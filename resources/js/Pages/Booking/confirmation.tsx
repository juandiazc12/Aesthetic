import React, { useState } from 'react';
import { Calendar, Clock, User, CreditCard, CheckCircle, AlertCircle } from 'lucide-react';
import { PaymentSystem, PaymentDetails } from '../components/PaymentSystem';
import { router } from '@inertiajs/react';

interface BookingData {
  id: number;
  service: {
    name: string;
    description: string;
    price: number;
    duration: number;
  };
  professional: {
    name: string;
    email: string;
    photo: string;
  };
  customer: {
    name: string;
    email: string;
  };
  scheduled_at: string;
  scheduled_date: string;
  scheduled_time: string;
  scheduled_day: string;
  total_amount: number;
  payment_method: string;
  payment_status: string;
  status: string;
  created_at: string;
  confirmed_at?: string;
}

interface ConfirmationProps {
  booking: BookingData;
}

export default function Confirmation({ booking }: ConfirmationProps) {
  const [currentStep, setCurrentStep] = useState<'review' | 'payment' | 'success'>('review');
  const [isProcessing, setIsProcessing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handlePaymentComplete = async (paymentDetails: PaymentDetails) => {
  setIsProcessing(true);
  setError(null);
  
  try {
    // Si es pago en efectivo, la reserva ya está confirmada
    if (paymentDetails.method === 'cash') {
      setCurrentStep('success');
      // Redirigir después de un momento
      setTimeout(() => {
        router.visit('/mis-reservas');
      }, 3000);
    } else {
      // Para pagos con tarjeta/transferencia, confirmar la reserva
      const response = await fetch(`/booking/${booking.id}/confirm`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
          payment_details: paymentDetails
        })
      });
      
      const data = await response.json();
      
      if (data.success) {
        setCurrentStep('success');
        setTimeout(() => {
          router.visit('/mis-reservas');
        }, 3000);
      } else {
        throw new Error(data.error || 'Error al confirmar la reserva');
      }
    }
  } catch (err: any) {
    console.error('Error confirmando reserva:', err);
    setError(err.message || 'Error al confirmar la reserva');
  } finally {
    setIsProcessing(false);
  }
};

  const handleBackToReview = () => {
    setCurrentStep('review');
    setError(null);
  };

  const handleProceedToPayment = () => {
    setCurrentStep('payment');
    setError(null);
  };

  const handleCancelBooking = () => {
    // Navegar de vuelta a la página de reservas
    router.visit('/booking');
  };
const shouldShowPaymentStep = booking.payment_method !== 'cash' && booking.status !== 'confirmed';
  if (currentStep === 'success') {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center p-4">
        <div className="bg-white rounded-lg shadow-lg p-8 max-w-md w-full text-center">
          <div className="mb-6">
            <CheckCircle className="w-16 h-16 text-green-500 mx-auto mb-4" />
            <h1 className="text-2xl font-bold text-gray-800 mb-2">¡Reserva Confirmada!</h1>
            <p className="text-gray-600">
              Tu reserva ha sido confirmada exitosamente. Pronto recibirás un email con los detalles.
            </p>
          </div>
          
          <div className="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 className="font-semibold text-gray-800 mb-2">Detalles de tu reserva:</h3>
            <div className="space-y-2 text-sm">
              <p><strong>Servicio:</strong> {booking.service.name}</p>
              <p><strong>Profesional:</strong> {booking.professional.name}</p>
              <p><strong>Fecha:</strong> {booking.scheduled_date}</p>
              <p><strong>Hora:</strong> {booking.scheduled_time}</p>
              <p><strong>Total:</strong> ${booking.total_amount.toLocaleString('es-CO')} COP</p>
            </div>
          </div>

          <div className="space-y-3">
            <button
              onClick={() => router.visit('/mis-reservas')}
              className="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors"
            >
              Ver mis reservas
            </button>
            <button
              onClick={() => router.visit('/booking')}
              className="w-full border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Hacer otra reserva
            </button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-8 px-4">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="text-center mb-8">
          <h1 className="text-3xl font-bold text-gray-800 mb-2">
            {currentStep === 'review' ? 'Confirma tu Reserva' : 'Finalizar Pago'}
          </h1>
          <p className="text-gray-600">
            {currentStep === 'review' 
              ? 'Revisa los detalles de tu reserva antes de proceder al pago'
              : 'Selecciona tu método de pago preferido'
            }
          </p>
        </div>

        {/* Progress Indicator */}
        <div className="flex justify-center mb-8">
          <div className="flex items-center space-x-4">
            <div className={`flex items-center ${currentStep === 'review' ? 'text-blue-600' : 'text-green-600'}`}>
              <div className={`w-8 h-8 rounded-full border-2 flex items-center justify-center ${
                currentStep === 'review' ? 'border-blue-600 bg-blue-50' : 'border-green-600 bg-green-50'
              }`}>
                {currentStep === 'review' ? '1' : <CheckCircle className="w-5 h-5" />}
              </div>
              <span className="ml-2 font-medium">Revisar</span>
            </div>
            <div className={`w-8 h-0.5 ${currentStep === 'payment' ? 'bg-blue-600' : 'bg-gray-300'}`}></div>
            <div className={`flex items-center ${currentStep === 'payment' ? 'text-blue-600' : 'text-gray-400'}`}>
              <div className={`w-8 h-8 rounded-full border-2 flex items-center justify-center ${
                currentStep === 'payment' ? 'border-blue-600 bg-blue-50' : 'border-gray-300'
              }`}>
                2
              </div>
              <span className="ml-2 font-medium">Pagar</span>
            </div>
          </div>
        </div>

        {error && (
          <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
            <AlertCircle className="w-5 h-5 text-red-500 mr-3" />
            <p className="text-red-800">{error}</p>
          </div>
        )}

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Booking Details */}
          <div className="bg-white rounded-lg shadow-lg p-6">
            <h2 className="text-xl font-bold text-gray-800 mb-6">Detalles de la Reserva</h2>
            
            <div className="space-y-6">
              {/* Service Info */}
              <div className="border-b pb-4">
                <h3 className="font-semibold text-gray-800 mb-2">Servicio</h3>
                <p className="text-lg font-medium text-blue-600">{booking.service.name}</p>
                <p className="text-sm text-gray-600">{booking.service.description}</p>
                <p className="text-sm text-gray-600 mt-1">
                  Duración: {booking.service.duration} minutos
                </p>
              </div>

              {/* Professional Info */}
              <div className="border-b pb-4">
                <h3 className="font-semibold text-gray-800 mb-2">Profesional</h3>
                <div className="flex items-center">
                  {booking.professional.photo && (
                    <img 
                      src={booking.professional.photo} 
                      alt={booking.professional.name}
                      className="w-12 h-12 rounded-full mr-3"
                    />
                  )}
                  <div>
                    <p className="font-medium text-gray-800">{booking.professional.name}</p>
                    <p className="text-sm text-gray-600">{booking.professional.email}</p>
                  </div>
                </div>
              </div>

              {/* Date & Time */}
              <div className="border-b pb-4">
                <h3 className="font-semibold text-gray-800 mb-2">Fecha y Hora</h3>
                <div className="flex items-center space-x-4">
                  <div className="flex items-center">
                    <Calendar className="w-5 h-5 text-blue-600 mr-2" />
                    <span>{booking.scheduled_date}</span>
                  </div>
                  <div className="flex items-center">
                    <Clock className="w-5 h-5 text-blue-600 mr-2" />
                    <span>{booking.scheduled_time}</span>
                  </div>
                </div>
                <p className="text-sm text-gray-600 mt-1">{booking.scheduled_day}</p>
              </div>

              {/* Customer Info */}
              <div className="border-b pb-4">
                <h3 className="font-semibold text-gray-800 mb-2">Cliente</h3>
                <div className="flex items-center">
                  <User className="w-5 h-5 text-blue-600 mr-2" />
                  <div>
                    <p className="font-medium text-gray-800">{booking.customer.name}</p>
                    <p className="text-sm text-gray-600">{booking.customer.email}</p>
                  </div>
                </div>
              </div>

              {/* Total */}
              <div className="pt-4">
                <div className="flex justify-between items-center">
                  <span className="text-lg font-semibold text-gray-800">Total:</span>
                  <span className="text-2xl font-bold text-blue-600">
                    ${booking.total_amount.toLocaleString('es-CO')} COP
                  </span>
                </div>
              </div>
            </div>
          </div>

          {/* Payment or Actions */}
          <div className="bg-white rounded-lg shadow-lg p-6">
            {currentStep === 'review' ? (
  <div>
    <h2 className="text-xl font-bold text-gray-800 mb-6">
      {booking.payment_method === 'cash' ? 'Reserva Confirmada' : 'Confirmar Reserva'}
    </h2>
    <p className="text-gray-600 mb-6">
      {booking.payment_method === 'cash' 
        ? 'Tu reserva ha sido confirmada. El pago se realizará en efectivo en el establecimiento.'
        : 'Revisa todos los detalles de tu reserva. Una vez que procedas al pago, se enviará una confirmación a tu email.'
      }
    </p>
    
    <div className="space-y-4">
      {booking.payment_method === 'cash' ? (
        <button
          onClick={() => router.visit('/mis-reservas')}
          className="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium"
        >
          Ver mis reservas
        </button>
      ) : (
        <>
          <button
            onClick={handleProceedToPayment}
            disabled={isProcessing}
            className="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed font-medium"
          >
            {isProcessing ? 'Procesando...' : 'Proceder al Pago'}
          </button>
          
          <button
            onClick={handleCancelBooking}
            disabled={isProcessing}
            className="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed font-medium"
          >
            Cancelar Reserva
          </button>
        </>
      )}
    </div>
  </div>
) : (
  // Solo mostrar PaymentSystem si no es pago en efectivo
  shouldShowPaymentStep && (
    <PaymentSystem
      amount={booking.total_amount}
      serviceId={booking.service.id || 0}
      serviceName={booking.service.name}
      bookingId={booking.id}
      customerEmail={booking.customer.email}
      customerName={booking.customer.name}
      onPaymentComplete={handlePaymentComplete}
      onCancel={handleBackToReview}
    />
  )
)}
          </div>
        </div>
      </div>
    </div>
  );
}