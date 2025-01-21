// customer/Settings.tsx

import React, { useState } from 'react';
import { Customer } from '@/Interfaces/Customer'; // Asegúrate de tener la ruta correcta
import { usePage } from '@inertiajs/react';

type Props = {
  customer: Customer;
}

const Settings = ({ customer }: Props) => {
  const { flash } = usePage().props; // Esto es para obtener mensajes flash si los hay
  const [formData, setFormData] = useState<Customer>(customer);

  // Manejador de cambios en el formulario
  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value,
    }));
  };

  // Simulación de envío de datos
  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // Aquí podrías hacer una llamada a tu API para actualizar la información del cliente
    console.log("Información actualizada:", formData);
  };

  return (
    <div className="max-w-[85rem] mx-auto p-4">
      {/* Formulario de Edición */}
      <div className="flex flex-col bg-white shadow-md p-6 rounded-lg">
        <h2 className="text-xl font-semibold mb-4">Editar Información Personal</h2>
        
        
        
        <form onSubmit={handleSubmit}>
          {/* Campos de formulario */}
          <div className="grid gap-4 md:grid-cols-2">
            <div>
              <label className="block text-sm font-medium text-gray-700">Primer Nombre</label>
              <input
                type="text"
                name="first_name"
                value={formData.first_name}
                onChange={handleChange}
                className="mt-1 p-2 w-full border border-gray-300 rounded-md"
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700">Apellido</label>
              <input
                type="text"
                name="last_name"
                value={formData.last_name}
                onChange={handleChange}
                className="mt-1 p-2 w-full border border-gray-300 rounded-md"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">Correo Electrónico</label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                className="mt-1 p-2 w-full border border-gray-300 rounded-md"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">Número de Teléfono</label>
              <input
                type="tel"
                name="phone_number"
                value={formData.phone_number}
                onChange={handleChange}
                className="mt-1 p-2 w-full border border-gray-300 rounded-md"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">Dirección</label>
              <textarea
                name="address"
                value={formData.address}
                onChange={handleChange}
                className="mt-1 p-2 w-full border border-gray-300 rounded-md"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">Ciudad</label>
              <input
                type="text"
                name="city"
                value={formData.city}
                onChange={handleChange}
                className="mt-1 p-2 w-full border border-gray-300 rounded-md"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">Estado</label>
              <input
                type="text"
                name="state"
                value={formData.state}
                onChange={handleChange}
                className="mt-1 p-2 w-full border border-gray-300 rounded-md"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">Código Postal</label>
              <input
                type="text"
                name="postal_code"
                value={formData.postal_code}
                onChange={handleChange}
                className="mt-1 p-2 w-full border border-gray-300 rounded-md"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">País</label>
              <input
                type="text"
                name="country"
                value={formData.country}
                onChange={handleChange}
                className="mt-1 p-2 w-full border border-gray-300 rounded-md"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
              <input
                type="date"
                name="birth_date"
                value={formData.birth_date}
                onChange={handleChange}
                className="mt-1 p-2 w-full border border-gray-300 rounded-md"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">Foto de Perfil</label>
              <input
                type="file"
                name="profile_picture"
                onChange={handleChange}
                className="mt-1 p-2 w-full border border-gray-300 rounded-md"
              />
            </div>
          </div>

          {/* Botón de Guardar */}
          <div className="mt-6">
            <button
              type="submit"
              className="py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none"
            >
              Guardar Cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default Settings;
