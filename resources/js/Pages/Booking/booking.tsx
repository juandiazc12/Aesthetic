import React, { useState } from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

export default function Booking() {
  const today = new Date();
  const currentDay = today.getDate();
  const currentMonth = today.getMonth();
  const currentYear = today.getFullYear();

  type TimeOfDay = 'DIA' | 'TARDE' | 'NOCHE';
  const [selectedDate, setSelectedDate] = useState(currentDay);
  const [timeOfDay, setTimeOfDay] = useState<TimeOfDay>('DIA');
  const [selectedTime, setSelectedTime] = useState('');
  const [selectedServices, setSelectedServices] = useState<{ name: string; price: number; duration: number }[]>([]);
  const [selectedProfessional, setSelectedProfessional] = useState('');

  const services = [
    { name: 'Corte de Cabello', price: 20000, duration: 30 },
    { name: 'Manicure', price: 15000, duration: 45 },
    { name: 'Masaje Relajante', price: 50000, duration: 60 },
    { name: 'Tratamiento Facial', price: 40000, duration: 50 },
  ];

  const professionals = ['Profesional 1', 'Profesional 2', 'Profesional 3'];

  const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  const days = Array.from({ length: daysInMonth - currentDay + 1 }, (_, i) => currentDay + i);

  const schedules: Record<TimeOfDay, string[]> = {
    DIA: ['7:00', '8:00', '9:00', '10:00', '11:00'],
    TARDE: ['12:00', '13:00', '14:00', '15:00', '16:00', '17:00'],
    NOCHE: ['18:00', '19:00', '20:00', '21:00', '22:00'],
  };

  const handleAddService = (service: { name: string; price: number; duration: number }) => {
    setSelectedServices((prev) => [...prev, service]);
  };

  const handleRemoveService = (index: number) => {
    setSelectedServices((prev) => prev.filter((_, i) => i !== index));
  };

  const totalPrice = selectedServices.reduce((total, service) => total + service.price, 0);
  const totalDuration = selectedServices.reduce((total, service) => total + service.duration, 0);

  return (
    <div className="max-w-md mx-auto p-4 space-y-6">
      <h1 className="text-2xl font-bold text-center">Reserva</h1>

      {/* Selector de fecha */}
      <div>
        <h2 className="text-lg font-bold mb-2">Selecciona una fecha</h2>
        <div className="flex items-center justify-between mb-4">
          <button className="p-2 hover:bg-gray-100 rounded-full" onClick={() => setSelectedDate(selectedDate - 1)}>
            <ChevronLeft className="w-6 h-6 text-gray-600" />
          </button>
          <div className="flex space-x-2 overflow-x-auto">
            {days.map((day) => (
              <button
                key={day}
                className={`flex flex-col items-center min-w-[45px] py-2 px-3 rounded-full ${
                  selectedDate === day ? 'bg-blue-500 text-white' : 'hover:bg-gray-100 text-gray-800'
                }`}
                onClick={() => setSelectedDate(day)}
              >
                <span className="text-xs">
                  {new Date(currentYear, currentMonth, day).toLocaleDateString('es-CO', { weekday: 'short' }).toUpperCase()}
                </span>
                <span className="font-semibold">{day}</span>
              </button>
            ))}
          </div>
          <button className="p-2 hover:bg-gray-100 rounded-full" onClick={() => setSelectedDate(selectedDate + 1)}>
            <ChevronRight className="w-6 h-6 text-gray-600" />
          </button>
        </div>
      </div>

      {/* Selector DIA/TARDE/NOCHE */}
      <div>
        <h2 className="text-lg font-bold mb-2">Selecciona un horario</h2>
        <div className="flex bg-gray-100 rounded-full p-1">
          {(['DIA', 'TARDE', 'NOCHE'] as const).map((time) => (
            <button
              key={time}
              className={`flex-1 py-2 text-sm rounded-full ${
                timeOfDay === time ? 'bg-blue-500 text-white' : 'text-gray-800'
              }`}
              onClick={() => setTimeOfDay(time)}
            >
              {time}
            </button>
          ))}
        </div>
      </div>

      {/* Selector de hora */}
      <div>
        <h2 className="text-lg font-bold mb-2">Elige una hora</h2>
        <div className="grid grid-cols-3 gap-2">
          {schedules[timeOfDay].map((hour) => (
            <button
              key={hour}
              className={`py-2 px-3 rounded ${
                selectedTime === hour ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-800'
              }`}
              onClick={() => setSelectedTime(hour)}
            >
              {hour}
            </button>
          ))}
        </div>
      </div>

      {/* Selección de servicios */}
      <div>
        <h2 className="text-lg font-bold mb-2">Selecciona un servicio</h2>
        <div className="grid grid-cols-2 gap-2">
          {services.map((service) => (
            <button
              key={service.name}
              className="py-2 px-3 rounded bg-gray-100 text-gray-800 hover:bg-blue-500 hover:text-white"
              onClick={() => handleAddService(service)}
            >
              {service.name} - ${service.price}
            </button>
          ))}
        </div>
      </div>

      {/* Servicios seleccionados */}
      {selectedServices.length > 0 && (
        <div className="border p-4 rounded-lg">
          <h3 className="text-lg font-bold mb-2">Servicios Seleccionados</h3>
          <ul>
            {selectedServices.map((service, index) => (
              <li key={index} className="flex justify-between items-center mb-2">
                <span>{service.name}</span>
                <button
                  className="text-red-500 hover:underline"
                  onClick={() => handleRemoveService(index)}
                >
                  Eliminar
                </button>
              </li>
            ))}
          </ul>
          <p className="font-semibold">Total: ${totalPrice}</p>
          <p className="font-semibold">Duración total: {totalDuration} minutos</p>
        </div>
      )}

      {/* Selección de profesional */}
      <div>
        <h2 className="text-lg font-bold mb-2">Selecciona un profesional</h2>
        <select
          className="w-full p-2 border rounded-lg"
          value={selectedProfessional}
          onChange={(e) => setSelectedProfessional(e.target.value)}
        >
          <option value="">Seleccione un profesional</option>
          {professionals.map((professional) => (
            <option key={professional} value={professional}>
              {professional}
            </option>
          ))}
        </select>
      </div>

      {/* Botón confirmar */}
      <button
        className="w-full bg-blue-500 text-white py-3 rounded-lg font-semibold hover:bg-blue-600"
        onClick={() =>
          alert(
            `Reserva realizada para el ${selectedDate}/${currentMonth + 1}/${currentYear} a las ${selectedTime} con ${selectedProfessional}. Servicios: ${selectedServices
              .map((s) => s.name)
              .join(', ')}.`
          )
        }
        disabled={!selectedTime || !selectedProfessional || selectedServices.length === 0}
      >
        Confirmar Reserva
      </button>
    </div>
  );
}
