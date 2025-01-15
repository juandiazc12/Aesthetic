import React, { useState } from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { Daum } from '@/Interfaces/Service';
import Service from '../Services/Service';

interface Props {
  initialServices: Daum[];
}

export default function Booking({ initialServices }: Props) {
  const today = new Date();
  const currentDay = today.getDate();
  const currentMonth = today.getMonth();
  const currentYear = today.getFullYear();

  type TimeOfDay = 'DIA' | 'TARDE' | 'NOCHE';
  const [selectedDate, setSelectedDate] = useState(currentDay);
  const [timeOfDay, setTimeOfDay] = useState<TimeOfDay>('DIA');
  const [selectedTime, setSelectedTime] = useState('');
  const [selectedServices, setSelectedServices] = useState<Daum[]>([initialServices[0]]);
  const [showServiceList, setShowServiceList] = useState(false);

  const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  const days = Array.from({ length: daysInMonth - currentDay + 1 }, (_, i) => currentDay + i);

  const schedules: Record<TimeOfDay, string[]> = {
    DIA: ['7:00', '8:00', '9:00', '10:00', '11:00'],
    TARDE: ['12:00', '13:00', '14:00', '15:00', '16:00', '17:00'],
    NOCHE: ['18:00', '19:00', '20:00', '21:00', '22:00'],
  };

  const handleAddService = (service: Daum) => {
    if (!selectedServices.some((s) => s.name === service.name)) {
      setSelectedServices((prev) => [...prev, service]);
    }
  };

  const handleRemoveService = (index: number) => {
    setSelectedServices((prev) => prev.filter((_, i) => i !== index));
  };

  const formatDuration = (duration: number) => {
    const hours = Math.floor(duration / 60);
    const minutes = duration % 60;
    return `${hours > 0 ? `${hours}h ` : ''}${minutes}min`;
  };

  const totalPrice = selectedServices.reduce((total, service) => total + (Number(service.price) || 0), 0);
  const totalDuration = selectedServices.reduce((total, service) => total + Number(service.duration), 0);

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

      {/* Servicios seleccionados */}
      <div>
        <h2 className="text-lg font-bold mb-2">Servicios Seleccionados</h2>
        <ul className="mb-4">
          {selectedServices.map((service, index) => (
            <li key={index} className="flex justify-between items-center mb-2">
              <span>{service.name} <p> </p>${service.price} <p> </p>{service.duration}min</span>
              <button className="text-red-500 hover:underline" onClick={() => handleRemoveService(index)}>
                Eliminar
              </button>
            </li>
          ))}
        </ul>
        <p className="font-semibold">Total: ${totalPrice}</p>
        <p className="font-semibold">Duración total: {formatDuration(totalDuration)}</p>
      </div>

      {/* Botón para mostrar/ocultar lista de servicios */}
      <div>
        <button
          className="w-full bg-gray-100 py-2 text-gray-800 rounded-lg font-semibold hover:bg-gray-200"
          onClick={() => setShowServiceList(!showServiceList)}
        >
          {showServiceList ? 'Ocultar servicios' : 'Añadir servicios'}
        </button>
        {showServiceList && (
          <div className="grid grid-cols-2 gap-2 mt-4">
            {initialServices.map((service) => (
              <button
                key={service.name}
                className="py-2 px-3 rounded bg-gray-100 text-gray-800 hover:bg-blue-500 hover:text-white"
                onClick={() => handleAddService(service)}
              >
                {service.name} - ${service.price} - {formatDuration(service.duration)}
              </button>
            ))}
          </div>
        )}
      </div>

      {/* Botón confirmar */}
      <button
        className="w-full bg-blue-500 text-white py-3 rounded-lg font-semibold hover:bg-blue-600"
        onClick={() =>
          alert(
            `Reserva realizada para el ${selectedDate}/${currentMonth + 1}/${currentYear} a las ${selectedTime}. Servicios: ${selectedServices
              .map((s) => s.name)
              .join(', ')}.`
          )
        }
        disabled={!selectedTime || selectedServices.length === 0}
      >
        Confirmar Reserva
      </button>
    </div>
  );
}
