import { Daum } from "@/Interfaces/Service";
import React, { useState } from "react";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { router, usePage } from "@inertiajs/react";
import { Customer } from "@/Interfaces/Customer";
import { getInitials } from "@/Pages/utils/helpers";
import moment from "moment";

interface ComponentProps {
  service: Daum;
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

export default function AddBooking({ service }: ComponentProps) {
  const { customer, professionals } = usePage<Props>().props;

  const today = new Date();
  const currentDay = today.getDate();
  const currentMonth = today.getMonth();
  const currentYear = today.getFullYear();
  const currentHour = today.getHours(); // Hora actual

  type TimeOfDay = "MAÑANA" | "TARDE" | "NOCHE";
  const [selectedDate, setSelectedDate] = useState(currentDay);
  const [timeOfDay, setTimeOfDay] = useState<TimeOfDay>("MAÑANA");
  const [selectedTime, setSelectedTime] = useState<null | string>(null);
  const [professionalId, setProfessionalId] = useState<null | number>(
    professionals.length === 1 ? professionals[0].id : null
  );

  const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  const days = Array.from({ length: daysInMonth - currentDay + 1 }, (_, i) => currentDay + i);

  const schedules: Record<TimeOfDay, string[]> = {
    MAÑANA: ["7:00", "8:00", "9:00", "10:00", "11:00"],
    TARDE: ["12:00", "13:00", "14:00", "15:00", "16:00", "17:00"],
    NOCHE: ["18:00", "19:00", "20:00", "21:00", "22:00"],
  };

  const onSubmit = (e: React.FormEvent<HTMLButtonElement>) => {
    e.preventDefault();

    const scheduled_at = new Date(currentYear, currentMonth, selectedDate);
    if (selectedTime) {
      scheduled_at.setHours(parseInt(selectedTime.split(":")[0]));
      scheduled_at.setMinutes(parseInt(selectedTime.split(":")[1]));
    }

    const dateFormat = moment(scheduled_at).format("YYYY-MM-DD HH:mm:ss");

    router.post("/booking", {
      service_id: service.id,
      customer_id: customer.id,
      professional_id: professionalId,
      scheduled_at: dateFormat,
    });
  };

  const isTimePassed = (time: string): boolean => {
    const [hour, minute] = time.split(":").map(Number);
    const timeInMinutes = hour * 60 + minute;
    const currentTimeInMinutes = currentHour * 60 + today.getMinutes();
    return timeInMinutes < currentTimeInMinutes;
  };

  return (
    <div>
      <div className="max-w-[30rem] flex flex-col gap-5">
        {/* Selector de fecha */}
        <div>
          <h2 className="text-lg font-bold mb-2">Selecciona una fecha:</h2>
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
                  timeOfDay === time ? "bg-blue-500 text-white" : "text-gray-800 hover:bg-blue-100"
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

        {/* Selector de profesionales */}
        <div>
          <h2 className="text-lg font-bold mb-2">Selecciona un profesional</h2>
          <div className="flex flex-col gap-2">
            {professionals.map((professional) => (
              <div
                key={professional.id}
                className={`flex gap-3 items-start border p-2 rounded-md cursor-pointer ${
                  professionalId === professional.id ? "bg-blue-500 text-white " : "hover:bg-gray-200"
                }`}
                onClick={() => setProfessionalId(professional.id)}
              >
                <div
                  className={`w-10 h-10 rounded-full flex items-center justify-center ${
                    professionalId === professional.id ? "bg-blue-700 text-white" : "bg-gray-100"
                  }`}
                >
                  {professional.photo ? (
                    <img src={professional.photo} alt={professional.name} />
                  ) : (
                    <span>{getInitials(professional.name)}</span>
                  )}
                </div>
                <div className="flex flex-col">
                  <span>{professional.name}</span>
                  <strong className="text-sm">{professional.email}</strong>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
      <button
        type="button"
        onClick={onSubmit}
        className="btn-primary"
        disabled={!selectedTime || !professionalId}
      >
        Reservar ahora
      </button>
    </div>
  );
}
