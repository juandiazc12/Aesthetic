import React from 'react';
import { Clock } from 'lucide-react';
import { Link, usePage } from '@inertiajs/react';

export default function Pending() {
  const { booking_id } = usePage().props as any;

  return (
    <div className="min-h-screen flex flex-col items-center justify-center bg-yellow-50 px-4">
      <Clock className="w-20 h-20 text-yellow-600 mb-4" />
      <h1 className="text-3xl font-bold text-yellow-700 mb-2">Pago pendiente</h1>
      <p className="text-gray-700 mb-4">Estamos esperando la confirmación de tu pago.</p>

      <div className="bg-white rounded shadow p-4 w-full max-w-md">
        <p><strong>ID de reserva:</strong> {booking_id}</p>
        <p>Te notificaremos una vez se confirme.</p>
      </div>

      <Link
        href="/bookings"
        className="mt-6 bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 transition"
      >
        Volver al panel
      </Link>
    </div>
  );
}
