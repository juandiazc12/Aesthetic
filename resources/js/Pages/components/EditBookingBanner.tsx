import { Booking } from "@/Interfaces/Booking";
import { X, ChevronLeft, ChevronRight } from "lucide-react";
import React, { useState, useEffect } from "react";
import { Customer } from "@/Interfaces/Customer";
import { usePage, router } from "@inertiajs/react";
import axios from "axios";

interface EditBookingBannerProps {
  booking: Booking;
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
  MAÑANA: ["07:00", "08:00", "09:00", "10:00", "11:00"],
  TARDE: ["12:00", "13:00", "14:00", "15:00", "16:00", "17:00"],
  NOCHE: ["18:00", "19:00", "20:00", "21:00", "22:00"],
};

const timeOfDayMap: Record<TimeOfDay, string> = {
  MAÑANA: "morning",
  TARDE: "afternoon",
  NOCHE: "evening",
};

export default function EditBookingBanner({
  booking,
  onClose,
  onSave,
}: EditBookingBannerProps) {
  const { customer } = usePage<Props>().props;
  console.log("Booking data received:", { booking });

  if (!booking || !booking.professional || !booking.professional.id) {
    console.error("Invalid booking or professional data", { booking });
    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-lg p-6 max-w-2xl w-full">
          <div className="text-red-600 text-center">
            Error: No se puede editar la reserva porque falta información del profesional.
          </div>
          <button
            onClick={onClose}
            className="mt-4 px-4 py-2 border rounded-lg hover:bg-gray-50"
          >
            Cerrar
          </button>
        </div>
      </div>
    );
  }

  const today = new Date();
  const currentDay = today.getDate();
  const currentMonth = today.getMonth();
  const currentYear = today.getFullYear();

  const bookingDate = new Date(booking.scheduled_at);
  const [selectedDate, setSelectedDate] = useState(bookingDate.getDate());
  const [timeOfDay, setTimeOfDay] = useState<TimeOfDay>(() => {
    const hour = bookingDate.getHours();
    if (hour >= 7 && hour < 12) return "MAÑANA";
    if (hour >= 12 && hour < 18) return "TARDE";
    return "NOCHE";
  });
  const [selectedTime, setSelectedTime] = useState<string>(
    bookingDate.toLocaleTimeString("en-US", {
      hour: "2-digit",
      minute: "2-digit",
      hour12: false,
    })
  );
  const [availableTimes, setAvailableTimes] = useState<string[]>([]);
  const [error, setError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  const days = Array.from(
    { length: Math.min(daysInMonth - currentDay + 1, daysInMonth) },
    (_, i) => currentDay + i
  );

  useEffect(() => {
    const fetchAvailableSlots = async () => {
      if (!booking.professional.id) {
        setError("No se encontró el profesional de la reserva");
        setIsLoading(false);
        return;
      }

      try {
        setIsLoading(true);
        setError(null);
        const formattedDate = `${currentYear}-${(currentMonth + 1)
          .toString()
          .padStart(2, "0")}-${selectedDate.toString().padStart(2, "0")}`;
        console.log("Fetching slots with params:", {
          professional_id: booking.professional.id,
          date: formattedDate,
          timeOfDay,
        });

        // Validar formato de fecha
        if (!/^\d{4}-\d{2}-\d{2}$/.test(formattedDate)) {
          throw new Error("Formato de fecha inválido");
        }

        const response = await axios.get("/api/available-slots", {
          params: {
            professional_id: booking.professional.id,
            date: formattedDate,
          },
        });

        console.log("Available slots response:", response.data);

        // Validar que response.data sea un objeto
        if (!response.data || typeof response.data !== "object") {
          throw new Error("Respuesta del servidor inválida");
        }

        const period = timeOfDayMap[timeOfDay];
        const times = response.data[period] || [];
        if (!Array.isArray(times)) {
          console.warn(`No times found for period: ${period}`, response.data);
          setError(`No hay horarios disponibles para ${timeOfDay.toLowerCase()}`);
          setAvailableTimes([]);
          return;
        }

        setAvailableTimes(times);

        if (times.length > 0 && !times.includes(selectedTime)) {
          setSelectedTime(times[0]);
        } else if (times.length === 0) {
          setError(`No hay horarios disponibles para ${timeOfDay.toLowerCase()}`);
        }
      } catch (err: any) {
        console.error("Error fetching available slots:", {
          message: err.message,
          response: err.response?.data,
          status: err.response?.status,
        });
        const errorMessage =
          err.response?.data?.details?.professional_id?.[0] ||
          err.response?.data?.details?.date?.[0] ||
          err.response?.data?.error ||
          err.message ||
          "Error al obtener horarios disponibles";
        setError(errorMessage);
      } finally {
        setIsLoading(false);
      }
    };
    fetchAvailableSlots();
  }, [selectedDate, timeOfDay, booking.professional.id]);

  const isTimePassed = (time: string): boolean => {
    const [hour, minute] = time.split(":").map(Number);
    const selectedDateObj = new Date(
      currentYear,
      currentMonth,
      selectedDate,
      hour,
      minute
    );
    const now = new Date();
    return selectedDateObj < now;
  };

  const handleSave = async () => {
    if (!selectedTime) {
      setError("Por favor selecciona un horario");
      return;
    }

    if (!availableTimes.includes(selectedTime)) {
      setError("El horario seleccionado no está disponible");
      return;
    }

    setIsLoading(true);
    setError(null);

    try {
      const formattedDateTime = `${currentYear}-${(currentMonth + 1)
        .toString()
        .padStart(2, "0")}-${selectedDate
        .toString()
        .padStart(2, "0")} ${selectedTime}:00`;

      console.log("Sending PUT request to update booking:", {
        booking_id: booking.id,
        service_id: booking.service.id,
        scheduled_at: formattedDateTime,
      });

      router.put(
        `/bookings/${booking.id}`,
        {
          service_id: booking.service.id,
          scheduled_at: formattedDateTime,
        },
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: (page) => {
            console.log("Booking update successful:", page);
            const updatedBooking = {
              ...booking,
              scheduled_at: formattedDateTime,
              status: "pending",
              scheduled_date: new Date(formattedDateTime).toLocaleDateString(
                "es-CO",
                { day: "2-digit", month: "2-digit", year: "numeric" }
              ),
              scheduled_time: selectedTime,
              status_spanish: "Pendiente",
            };
            onSave(updatedBooking);
            setIsLoading(false);
            onClose();
          },
          onError: (errors) => {
            console.error("Error updating booking:", errors);
            setError(
              errors.scheduled_at ||
              errors.service_id ||
              errors.error ||
              "Error al actualizar la reserva. Inténtalo de nuevo."
            );
            setIsLoading(false);
          },
          onFinish: () => {
            console.log("PUT request finished");
          },
        }
      );
    } catch (err: any) {
      console.error("Unexpected error in handleSave:", {
        message: err.message,
        stack: err.stack,
      });
      setError("Error inesperado al guardar los cambios");
      setIsLoading(false);
    }
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
                disabled={isLoading}
              >
                <X className="w-6 h-6" />
              </button>
            </div>
            {error && (
              <div className="mt-2 text-sm text-red-600">{error}</div>
            )}
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

            {/* Profesional (solo mostrar, no editable) */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Profesional
              </label>
              <p className="text-gray-800">{booking.professional.name}</p>
            </div>

            {/* Fecha */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Fecha
              </label>
              <div className="flex items-center justify-between mb-4">
                <button
                  className="p-2 hover:bg-gray-200 rounded-full"
                  onClick={() => setSelectedDate(selectedDate - 1)}
                  disabled={selectedDate <= currentDay}
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
                  disabled={selectedDate >= days[days.length - 1]}
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
                    disabled={isLoading}
                  >
                    {time}
                  </button>
                ))}
              </div>
            </div>

            {/* Selector de hora */}
            <div>
              <h2 className="text-lg font-bold mb-2">Elige una hora</h2>
              {isLoading ? (
                <p className="text-gray-600">Cargando horarios...</p>
              ) : availableTimes.length === 0 ? (
                <p className="text-gray-600">No hay horarios disponibles para este día y turno</p>
              ) : (
                <div className="grid grid-cols-3 gap-2">
                  {availableTimes.map((hour) => (
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
                      disabled={isTimePassed(hour) || isLoading}
                    >
                      {hour}
                    </button>
                  ))}
                </div>
              )}
            </div>
          </div>

          {/* Botones */}
          <div className="sticky bottom-0 bg-white pt-4 mt-6 border-t">
            <div className="flex justify-end gap-3">
              <button
                onClick={onClose}
                className="px-4 py-2 border rounded-lg hover:bg-gray-50"
                disabled={isLoading}
              >
                Cancelar
              </button>
              <button
                onClick={handleSave}
                className={`px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 ${
                  isLoading || !selectedTime || availableTimes.length === 0
                    ? "opacity-50 cursor-not-allowed"
                    : ""
                }`}
                disabled={isLoading || !selectedTime || availableTimes.length === 0}
              >
                {isLoading ? "Guardando..." : "Guardar Cambios"}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}