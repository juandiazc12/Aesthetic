<?php
declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class BookingEditScreen extends Screen
{
    public $booking;

    public function query(Booking $booking): array
    {
        $this->booking = $booking;

        $scheduledAt = $booking->scheduled_at instanceof \Carbon\Carbon
            ? Carbon::parse($booking->scheduled_at, 'America/Bogota')
            : Carbon::now('America/Bogota');

        return [
            'booking' => [
                'id' => $booking->id,
                'service_id' => $booking->service_id,
                'professional_id' => $booking->professional_id,
                'customer_id' => $booking->customer_id,
                'scheduled_at' => $scheduledAt->format('Y-m-d H:i'),
                'status' => $booking->status,
                'total_amount' => $booking->total_amount !== null ? number_format((float)$booking->total_amount, 0) : '0',
                'service_name' => is_object($booking->service) ? $booking->service->name : 'Sin servicio',
                'professional_name' => is_object($booking->professional) ? $booking->professional->name : 'Sin profesional',
                'customer_name' => is_object($booking->customer) ? ($booking->customer->first_name ?? $booking->customer->name ?? 'Sin nombre') : 'Sin cliente',
                'duration' => is_object($booking->service) ? $booking->service->duration : null,
                'canCancel' => $booking->status === 'pending' && $scheduledAt->gt(Carbon::now('America/Bogota')->addHour()),
                'canComplete' => $booking->status === 'pending' && $scheduledAt->lte(Carbon::now('America/Bogota')),
            ],
        ];
    }

    public function name(): ?string
    {
        return 'Editar Cita';
    }

    public function description(): ?string
    {
        return 'Detalles y gestión de la cita';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Volver')
                ->icon('bs.arrow-left')
                ->method('goBack'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::view('booking-edit'),
        ];
    }

    public function updateBookingStatus(Booking $booking, Request $request): JsonResponse
    {
        try {
            $status = $request->input('status');

            if (!in_array($status, ['cancelled', 'completed'])) {
                return response()->json(['error' => 'Estado no válido.'], 400);
            }

            if ($status === 'cancelled') {
                $scheduledAt = Carbon::parse($booking->scheduled_at, 'America/Bogota');
                if ($scheduledAt instanceof \Carbon\Carbon && Carbon::now('America/Bogota')->gte($scheduledAt->subHour())) {
                    return response()->json(['error' => 'No se puede cancelar menos de 1 hora antes.'], 403);
                }
            }

            if ($status === 'completed') {
                $scheduledAt = Carbon::parse($booking->scheduled_at, 'America/Bogota');
                if ($scheduledAt instanceof \Carbon\Carbon && Carbon::now('America/Bogota')->lt($scheduledAt)) {
                    return response()->json(['error' => 'No se puede marcar como completada antes de la hora programada.'], 403);
                }
            }

            $booking->update([
                'status' => $status,
                $status === 'cancelled' ? 'cancelled_at' : 'completed_at' => Carbon::now('America/Bogota'),
            ]);

            // Notificar al cliente si la cita es cancelada por el profesional
            if ($status === 'cancelled' && $booking->customer) {
                $booking->customer->notify(new \App\Notifications\BookingCancelled($booking, 'La cita fue cancelada por el profesional.'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Cita actualizada exitosamente.',
                'redirect' => route('platform.dashboard')
            ]);
        } catch (\Exception $e) {
            Log::error('Error in updateBookingStatus: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error al actualizar la cita: ' . $e->getMessage()], 500);
        }
    }

    public function goBack()
    {
        return redirect()->route('platform.dashboard');
    }
}