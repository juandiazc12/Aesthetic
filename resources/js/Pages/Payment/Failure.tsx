import React from 'react';
import { XCircle } from 'lucide-react';
import { Link, usePage } from '@inertiajs/react';

export default function Failure() {
  const { booking_id } = usePage().props as any;

  return (
    <div className="min-h-screen flex flex-col items-center justify-center bg-red-50 px-4">
      <XCircle className="w-20 h-20 text-red-600 mb-4" />
      <h1 className="text-3xl font-bold text-red-700 mb-2">Pago rechazado</h1>
      <p className="text-gray-700 mb-4">Hubo un problema con tu pago.</p>

      <div className="bg-white rounded shadow p-4 w-full max-w-md">
        <p><strong>ID de reserva:</strong> {booking_id}</p>
        <p>Te recomendamos intentar nuevamente.</p>
      </div>

      <Link
        href={`/booking/${booking_id}`}
        className="mt-6 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition"
      >
        Reintentar pago
      </Link>
    </div>
  );
}
