import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import moment from 'moment';
import axios from 'axios';
import { Loader2 } from 'lucide-react';

interface Props {
  booking: {
    id: string | number;
    scheduled_at: string;
    service: {
      name: string;
      price: number;
    };
    professional: {
      name: string;
    };
    payment_method: string;
    payment_status: string;
    total_amount?: number;
    is_confirmed: boolean;
    temp_id?: string;
    payment_id?: string;
    service_id?: number;
    professional_id?: number;
  };
  [key: string]: any;
}

export default function BookingConfirmation() {
  const { booking } = usePage<Props>().props;
  const [isProcessing, setIsProcessing] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [confirmedBooking, setConfirmedBooking] = useState<typeof booking | null>(null);

  const handleConfirm = async () => {
    setIsProcessing(true);
    setError(null);

    try {
      const response = await axios.post('/api/booking/confirm-store', {
        service_id: booking.service_id,
        professional_id: booking.professional_id,
        scheduled_at: booking.scheduled_at,
        payment_method: booking.payment_method,
        payment_transaction_id: booking.payment_id,
        temp_id: booking.temp_id,
        payment_amount: booking.total_amount,
      }, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });

      if (!response.data.success || !response.data.booking) {
        throw new Error(response.data.error || 'No se pudo confirmar la reserva');
      }

      setConfirmedBooking(response.data.booking);
      localStorage.removeItem(`booking_data_${booking.temp_id}`);
    } catch (error: any) {
      setError(error.response?.data?.error || error.message || 'Error al confirmar la reserva');
    } finally {
      setIsProcessing(false);
    }
  };

  const displayBooking = confirmedBooking || booking;

  return (
    <div className="max-w-md mx-auto p-6 bg-white rounded shadow text-center space-y-5">
      <h1 className="text-2xl font-bold text-green-600">
        {displayBooking.is_confirmed ? '¡Reserva confirmada!' : 'Confirmar Reserva'}
      </h1>
      <p>
      {displayBooking.is_confirmed
          ? '¡Tu cita ha sido agendada correctamente! Por favor revisa la bandeja de entrada de tu correo electrónico para ver los detalles de tu cita. Allí encontrarás un enlace para agregarla a tu calendario y así no olvidarla.'
          : 'Revisa los detalles de tu reserva y confírmala.'}

      </p>

      <div className="text-left border p-4 rounded bg-gray-50">
        <p><strong>Servicio:</strong> {displayBooking.service.name}</p>
        <p><strong>Profesional:</strong> {displayBooking.professional.name}</p>
        <p><strong>Fecha y Hora:</strong> {moment(displayBooking.scheduled_at).format("DD/MM/YYYY HH:mm")}</p>
        <p><strong>Método de pago:</strong> {displayBooking.payment_method}</p>
        <p><strong>Estado del pago:</strong> {displayBooking.payment_status}</p>
        <p><strong>Total:</strong> ${displayBooking.total_amount?.toLocaleString('es-CO')} COP</p>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 rounded-lg">
          <p className="text-red-800 text-sm">{error}</p>
        </div>
      )}

      {!displayBooking.is_confirmed && (
        <button
          onClick={handleConfirm}
          disabled={isProcessing}
          className={`w-full py-3 px-4 rounded-lg font-medium transition-all flex items-center justify-center gap-2
            ${isProcessing
              ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
              : 'bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800'}`}
        >
          {isProcessing ? (
            <>
              <Loader2 className="w-5 h-5 animate-spin" />
              Confirmando...
            </>
          ) : (
            'Confirmar Reserva'
          )}
        </button>
      )}

     
    </div>
  );
}