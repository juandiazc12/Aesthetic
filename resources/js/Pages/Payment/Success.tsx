import React from 'react';
import { CheckCircle } from 'lucide-react';
import { Link, usePage } from '@inertiajs/react';

export default function Success() {
  const { booking_id, payment_id, status } = usePage().props as any;

  return (
    <div className="min-h-screen flex flex-col items-center justify-center bg-green-50 px-4">
      <CheckCircle className="w-20 h-20 text-green-600 mb-4" />
      <h1 className="text-3xl font-bold text-green-700 mb-2">¡Pago exitoso!</h1>
      <p className="text-gray-700 mb-4">Tu cita ha sido registrada correctamente.</p>

      <div className="bg-white rounded shadow p-4 w-full max-w-md">
        <p><strong>Estado:</strong> {status}</p>
        <p><strong>ID de transacción:</strong> {payment_id}</p>
        <p><strong>ID de reserva:</strong> {booking_id}</p>
      </div>

      <Link
        href="/bookings"
        className="mt-6 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
      >
        Ver mis citas
      </Link>
    </div>
  );
}
