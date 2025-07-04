<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Resources\Preference;
use MercadoPago\Exceptions\MPApiException;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Customer;

class MercadoPagoController extends Controller
{
    public function __construct()
    {
        // Configurar el token de acceso
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        
        // Configurar el entorno
        if (config('services.mercadopago.environment') === 'sandbox') {
            MercadoPagoConfig::setEnvironment(MercadoPagoConfig::SANDBOX);
        } else {
            MercadoPagoConfig::setEnvironment(MercadoPagoConfig::PRODUCTION);
        }
    }

    public function createPreference(Request $request)
    {
        Log::info('Datos recibidos en createPreference', $request->all());

        try {
            // Validar solo los datos básicos que vienen del frontend
            $request->validate([
                'booking_id' => 'required|integer|exists:bookings,id',
                'payment_type' => 'required|string|in:card,transfer'
            ]);

            // Obtener la reserva con sus relaciones
            $booking = Booking::with(['service', 'customer'])->findOrFail($request->booking_id);
            
            // Verificar que la reserva existe y está en estado válido
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'error' => 'Reserva no encontrada'
                ], 404);
            }

            // Verificar que no esté ya pagada
            if ($booking->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'error' => 'Esta reserva ya ha sido pagada'
                ], 400);
            }

            // Obtener datos de la reserva
            $service = $booking->service;
            $customer = $booking->customer;
            
            // Calcular el monto total
            $amount = $booking->total_amount ?? $service->price;
            
            // Actualizar el monto en la reserva si no existe
            if (!$booking->total_amount) {
                $booking->update(['total_amount' => $amount]);
            }

            $client = new PreferenceClient();
            
            // Crear el item
            $item = [
                "id" => "service_" . $service->id,
                "title" => "Servicio de Estética - " . $service->name,
                "description" => "Pago por servicio de estética: " . $service->description,
                "quantity" => 1,
                "currency_id" => "COP", // Peso colombiano
                "unit_price" => (float) $amount,
            ];

            // Configurar URLs de retorno
            $backUrls = [
                "success" => route('payment.success') . "?booking_id=" . $booking->id,
                "failure" => route('payment.failure') . "?booking_id=" . $booking->id,
                "pending" => route('payment.pending') . "?booking_id=" . $booking->id
            ];

            // Configurar datos del pagador
            $payer = [
                "name" => $customer->name,
                "email" => $customer->email,
            ];

            // Agregar teléfono si existe
            if ($customer->phone) {
                $payer["phone"] = [
                    "area_code" => "57", // Código de Colombia
                    "number" => $customer->phone
                ];
            }

            // Agregar identificación si existe
            if ($customer->identification) {
                $payer["identification"] = [
                    "type" => "CC", // Cédula de ciudadanía
                    "number" => $customer->identification
                ];
            }

            // Agregar dirección si existe
            if ($customer->address) {
                $payer["address"] = [
                    "street_name" => $customer->address,
                    "street_number" => "",
                    "zip_code" => $customer->postal_code ?? ""
                ];
            }

            // Configurar métodos de pago según el tipo seleccionado
            $paymentMethods = [
                "excluded_payment_methods" => [],
                "excluded_payment_types" => [],
                "installments" => 12
            ];

            // Personalizar métodos según el tipo de pago
            if ($request->payment_type === 'transfer') {
                // Solo PSE para transferencias
                $paymentMethods["included_payment_methods"] = [
                    ["id" => "pse"]
                ];
            } elseif ($request->payment_type === 'card') {
                // Excluir PSE para pagos con tarjeta
                $paymentMethods["excluded_payment_methods"] = [
                    ["id" => "pse"]
                ];
            }

            // Datos para crear la preferencia
            $preferenceData = [
                "items" => [$item],
                "back_urls" => $backUrls,
                "auto_return" => "approved",
                "payer" => $payer,
                "payment_methods" => $paymentMethods,
                "notification_url" => route('payment.webhook'),
                "statement_descriptor" => "AESTHETIC SPA",
                "external_reference" => "booking_" . $booking->id,
                "expires" => true,
                "expiration_date_from" => now()->toISOString(),
                "expiration_date_to" => now()->addHours(24)->toISOString(),
                "metadata" => [
                    "booking_id" => $booking->id,
                    "service_id" => $service->id,
                    "customer_id" => $customer->id,
                    "payment_type" => $request->payment_type
                ]
            ];

            Log::info('Creando preferencia con datos:', $preferenceData);

            // Crear la preferencia
            $preference = $client->create($preferenceData);

            // Actualizar la reserva con el ID de preferencia
            $booking->update([
                'payment_preference_id' => $preference->id,
                'payment_status' => 'pending',
                'payment_method' => $request->payment_type
            ]);

            Log::info('Preferencia creada exitosamente', [
                'preference_id' => $preference->id,
                'booking_id' => $booking->id,
                'amount' => $amount,
                'init_point' => $preference->init_point
            ]);

            return response()->json([
                'success' => true,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
                'preference_id' => $preference->id,
                'booking_id' => $booking->id
            ]);

        } catch (MPApiException $e) {
            Log::error('MercadoPago API Error: ' . $e->getMessage(), [
                'status_code' => $e->getApiResponse()->getStatusCode(),
                'content' => $e->getApiResponse()->getContent()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al crear la preferencia de pago',
                'details' => $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Payment preference creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('MercadoPago Webhook received:', $data);

            // Procesar la notificación según el tipo
            if (isset($data['type']) && $data['type'] === 'payment') {
                $paymentId = $data['data']['id'];
                $this->updatePaymentStatus($paymentId);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Webhook processing error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    private function updatePaymentStatus($paymentId)
    {
        try {
            // Configurar el cliente de MercadoPago
            $client = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $client->get($paymentId);

            // Obtener el booking desde la external_reference
            $externalReference = $payment->external_reference;
            $bookingId = str_replace('booking_', '', $externalReference);
            
            $booking = Booking::find($bookingId);
            
            if ($booking) {
                // Actualizar el estado del pago según el status de MercadoPago
                $paymentStatus = match($payment->status) {
                    'approved' => 'paid',
                    'pending' => 'pending',
                    'rejected' => 'failed',
                    'cancelled' => 'cancelled',
                    default => 'pending'
                };

                $updateData = [
                    'payment_status' => $paymentStatus,
                    'payment_id' => $paymentId,
                    'payment_details' => [
                        'payment_method' => $payment->payment_method_id,
                        'payment_type' => $payment->payment_type_id,
                        'status' => $payment->status,
                        'status_detail' => $payment->status_detail,
                        'amount' => $payment->transaction_amount,
                        'paid_at' => $payment->date_approved
                    ]
                ];

                // Si el pago fue aprobado, marcar como pagado y actualizar timestamp
                if ($paymentStatus === 'paid') {
                    $updateData['payment_completed_at'] = now();
                    $updateData['status'] = 'active'; // Activar la reserva
                }

                $booking->update($updateData);

                Log::info('Payment status updated', [
                    'booking_id' => $booking->id,
                    'payment_id' => $paymentId,
                    'status' => $paymentStatus
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error updating payment status: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $paymentId = $request->query('payment_id');
        $status = $request->query('status');

        // Obtener detalles de la reserva
        $booking = Booking::with(['service', 'customer'])->find($bookingId);

        return inertia('Payment/Success', [
            'booking' => $booking,
            'payment_id' => $paymentId,
            'status' => $status
        ]);
    }

    public function failure(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $booking = Booking::with(['service', 'customer'])->find($bookingId);
        
        return inertia('Payment/Failure', [
            'booking' => $booking
        ]);
    }

    public function pending(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $booking = Booking::with(['service', 'customer'])->find($bookingId);
        
        return inertia('Payment/Pending', [
            'booking' => $booking
        ]);
    }

    public function checkPaymentStatus(Request $request)
    {
        try {
            $bookingId = $request->query('booking_id');
            $booking = Booking::find($bookingId);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'error' => 'Reserva no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'payment_status' => $booking->payment_status,
                'payment_id' => $booking->payment_id,
                'booking_status' => $booking->status
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking payment status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al verificar el estado del pago'
            ], 500);
        }
    }
}