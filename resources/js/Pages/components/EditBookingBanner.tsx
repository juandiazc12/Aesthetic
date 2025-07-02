import { Booking } from "@/Interfaces/Booking";
import { X, ChevronLeft, ChevronRight } from "lucide-react";
import React, { useState } from "react";
import { Customer } from "@/Interfaces/Customer";
import { usePage } from "@inertiajs/react";

interface EditBookingBannerProps {
  booking: Booking | null;
  onClose: () => void;
  onSave: (booking: Booking) => void;
}

type Props = {
  customer: Customer;
  professionals: Professional[];
};

interface Professional {
  id: number;
  photo: string;
  name: string;
  email: string;
  email_verified_at: any;
  created_at: string;
  updated_at: string;
}

type TimeOfDay = "MAÑANA" | "TARDE" | "NOCHE";

const schedules: Record<TimeOfDay, string[]> = {
  MAÑANA: ["7:00", "8:00", "9:00", "10:00", "11:00"],
  TARDE: ["12:00", "13:00", "14:00", "15:00", "16:00", "17:00"],
  NOCHE: ["18:00", "19:00", "20:00", "21:00", "22:00"]
};

export default function EditBookingBanner({
  booking,
  onClose,
  onSave,
}: EditBookingBannerProps) {
  const { customer, professionals } = usePage<Props>().props;
  if (!booking) return null;

  const today = new Date();
  const currentDay = today.getDate();
  const currentMonth = today.getMonth();
  const currentYear = today.getFullYear();
  const currentHour = today.getHours();

  const [selectedDate, setSelectedDate] = useState(
    new Date(booking.scheduled_at).getDate()
  );
  const [timeOfDay, setTimeOfDay] = useState<TimeOfDay>("MAÑANA");
  const [selectedTime, setSelectedTime] = useState<string | null>(
    new Date(booking.scheduled_at).toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    })
  );
  const [professionalId, setProfessionalId] = useState<number | null>(
    booking.professional.id
  );
  const [showPayment, setShowPayment] = useState(false);

  const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  const days = Array.from(
    { length: daysInMonth - currentDay + 1 },
    (_, i) => currentDay + i
  );

  const isTimePassed = (time: string): boolean => {
    const [hour, minute] = time.split(":").map(Number);
    const selectedDateObj = new Date(currentYear, currentMonth, selectedDate, hour, minute);
    const now = new Date();
    return selectedDateObj < now;
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
      <div className="bg-white rounded-lg p-6 max-w-2xl w-full my-8">
        <div className="max-h-[90vh] overflow-y-auto">
          {/* Header */}
          <div className="sticky top-0 bg-white z-10 pb-4 mb-6 border-b">
            <div className="flex justify-between items-center">
              <h2 className="text-2xl font-bold text-gray-800">Editar Reserva</h2>
              <button
                onClick={onClose}
                className="p-2 hover:bg-gray-100 rounded-full transition-colors"
              >
                <X className="w-6 h-6" />
              </button>
            </div>
          </div>

          {/* Contenido */}
          <div className="space-y-6">
            {/* Detalles del servicio */}
            <div className="flex gap-4 items-start">
              <img
                src={booking.service.image}
                alt={booking.service.name}
                className="w-24 h-24 rounded-lg object-cover"
              />
              <div>
                <h3 className="font-semibold text-lg">{booking.service.name}</h3>
                <p className="text-gray-600">${booking.service.price}</p>
              </div>
            </div>

            {/* Fecha y hora */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Fecha y hora
              </label>
              <div className="flex items-center justify-between mb-4 ">
                <button
                  className="p-2 hover:bg-gray-200 rounded-full"
                  onClick={() => setSelectedDate(selectedDate - 1)}
                >
                  <ChevronLeft className="w-6 h-6 text-gray-600" />
                </button>
                <div className="flex space-x-2 overflow-x-auto">
                  {days.map((day) => (
                    <button
                      key={day}
                      className={`flex flex-col items-center min-w-[45px] py-2 px-3 rounded-full ${
                        selectedDate === day
                          ? "bg-blue-500 text-white"
                          : "hover:bg-gray-200 text-gray-800"
                      }`}
                      onClick={() => setSelectedDate(day)}
                    >
                      <span className="text-xs">
                        {new Date(currentYear, currentMonth, day)
                          .toLocaleDateString("es-CO", { weekday: "short" })
                          .toUpperCase()}
                      </span>
                      <span className="font-semibold">{day}</span>
                    </button>
                  ))}
                </div>
                <button
                  className="p-2 hover:bg-gray-200 rounded-full"
                  onClick={() => setSelectedDate(selectedDate + 1)}
                >
                  <ChevronRight className="w-6 h-6 text-gray-600" />
                </button>
              </div>
            </div>

            {/* Selector MAÑANA/TARDE/NOCHE */}
            <div>
              <h2 className="text-lg font-bold mb-2">Selecciona un horario</h2>
              <div className="flex bg-gray-100 rounded-full p-1">
                {(["MAÑANA", "TARDE", "NOCHE"] as const).map((time) => (
                  <button
                    key={time}
                    className={`flex-1 py-2 text-sm rounded-full ${
                      timeOfDay === time
                        ? "bg-blue-500 text-white"
                        : "text-gray-800 hover:bg-blue-100"
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
                      selectedTime === hour
                        ? "bg-blue-500 text-white"
                        : isTimePassed(hour)
                        ? "bg-gray-300 text-gray-600 cursor-not-allowed"
                        : "bg-gray-100 text-gray-800 hover:bg-blue-100"
                    }`}
                    onClick={() => !isTimePassed(hour) && setSelectedTime(hour)}
                    disabled={isTimePassed(hour)}
                  >
                    {hour}
                  </button>
                ))}
              </div>
            </div>

            {/* Profesional */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Profesional
              </label>
              <div className="flex items-center gap-3 p-3 border rounded-lg">
                <div className="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                  {booking.professional.photo ? (
                    <img
                      src={booking.professional.photo}
                      alt={booking.professional.name}
                      className="w-full h-full rounded-full"
                    />
                  ) : (
                    <span className="text-blue-600 font-semibold">
                      {booking.professional.name.charAt(0)}
                    </span>
                  )}
                </div>
                <div>
                  <p className="font-medium">{booking.professional.name}</p>
                  <p className="text-sm text-gray-600">
                    {booking.professional.email}
                  </p>
                </div>
              </div>
            </div>
          </div>

          

          {/* Botones */}
          <div className="sticky bottom-0 bg-white pt-4 mt-6 border-t">
            <div className="flex justify-end gap-3">
              <button
                onClick={onClose}
                className="px-4 py-2 border rounded-lg hover:bg-gray-50"
              >
                Cancelar
              </button>
              <button
                onClick={() => onSave(booking)}
                className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
              >
                Guardar Cambios
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
