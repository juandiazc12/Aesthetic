import React, { useState, useEffect } from "react";
import { ChevronLeft, ChevronRight, Clock, User, Calendar, X } from "lucide-react";
import { usePage, router } from "@inertiajs/react";
import moment from "moment";
import "moment/locale/es";
import { Customer } from "@/Interfaces/Customer";
import { Booking } from "@/Interfaces/Booking";
import { getInitials } from "@/Pages/utils/helpers";

moment.locale("es");

interface Service {
  id: number;
  name: string;
  description: string;
  price: number;
  duration: number;
  status: string;
  image: string;
}

interface Professional {
  id: number;
  photo: string;
  name: string;
  email: string;
}

interface TimeSlots {
  morning: string[];
  afternoon: string[];
  evening: string[];
}

interface WeekDay {
  date: string;
  day_name: string;
  day_name_es: string;
  day_number: number;
  month: number;
  year: number;
  formatted_date: string;
  is_available: boolean;
  is_today: boolean;
  is_past: boolean;
  booked_count: number;
  slots_available: number;
}

interface WeekData {
  week_days: WeekDay[];
  week_start: string;
  week_end: string;
  week_offset: number;
  professional_id: number;
  week_title: string;
  can_go_previous: boolean;
  can_go_next: boolean;
}

type TimeOfDay = "morning" | "afternoon" | "evening";

interface EditBookingBannerProps {
  booking: Booking;
  onClose: () => void;
  onSave: (booking: Booking) => void;
}

type Props = {
  customer: Customer;
  professionals: Professional[];
};

const TIME_PERIODS = {
  morning: { label: "MAÑANA", times: ["07:00", "08:00", "09:00", "10:00", "11:00"] },
  afternoon: { label: "TARDE", times: ["12:00", "13:00", "14:00", "15:00", "16:00", "17:00"] },
  evening: { label: "NOCHE", times: ["18:00", "19:00", "20:00", "21:00", "22:00"] },
};

export default function EditBookingBanner({ booking, onClose, onSave }: EditBookingBannerProps) {
  const { customer } = usePage<Props>().props;

  if (!booking || !booking.professional || !booking.professional.id || !booking.service) {
    console.error("Invalid booking or professional data", { booking });
    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-lg p-6 max-w-2xl w-full">
          <div className="text-red-600 text-center">
            Error: No se puede editar la reserva porque falta información.
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

  const bookingDate = new Date(booking.scheduled_at);
  const [selectedDate, setSelectedDate] = useState<string>(
    moment(bookingDate).format("YYYY-MM-DD")
  );
  const [timeOfDay, setTimeOfDay] = useState<TimeOfDay>(() => {
    const hour = bookingDate.getHours();
    if (hour >= 7 && hour < 12) return "morning";
    if (hour >= 12 && hour < 18) return "afternoon";
    return "evening";
  });
  const [selectedTime, setSelectedTime] = useState<string>(
    moment(bookingDate).format("HH:mm")
  );
  const [availableSlots, setAvailableSlots] = useState<TimeSlots>({
    morning: [],
    afternoon: [],
    evening: [],
  });
  const [weekData, setWeekData] = useState<WeekData | null>(null);
  const [weekOffset, setWeekOffset] = useState(0);
  const [loading, setLoading] = useState(false);
  const [loadingDates, setLoadingDates] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // Obtener fechas disponibles cuando cambia el offset de semana
  useEffect(() => {
    if (booking.professional.id) {
      fetchAvailableDates();
    }
  }, [weekOffset, booking.professional.id]);

  // Obtener horarios disponibles cuando cambia la fecha
  useEffect(() => {
    if (booking.professional.id && selectedDate) {
      fetchAvailableSlots();
    }
  }, [selectedDate, booking.professional.id]);

  const fetchAvailableDates = async () => {
    if (!booking.professional.id) return;
    setLoadingDates(true);
    setError(null);
    try {
      const response = await fetch("/api/booking/available-dates", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
        },
        body: JSON.stringify({
          professional_id: booking.professional.id,
          week_offset: weekOffset,
        }),
      });
      if (!response.ok) {
        throw new Error("Error al obtener fechas disponibles");
      }
      const data = await response.json();
      setWeekData(data);
      if (selectedDate && !data.week_days.some((day: WeekDay) => day.date === selectedDate)) {
        setSelectedDate(null);
        setSelectedTime(null);
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : "Error desconocido");
      console.error("Error fetching available dates:", err);
    } finally {
      setLoadingDates(false);
    }
  };

  const fetchAvailableSlots = async () => {
    if (!booking.professional.id || !selectedDate) return;
    setLoading(true);
    setError(null);
    try {
      const response = await fetch("/api/booking/available-slots", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
        },
        body: JSON.stringify({
          professional_id: booking.professional.id,
          date: selectedDate,
          service_id: booking.service.id,
        }),
      });
      if (!response.ok) {
        throw new Error("Error al obtener horarios disponibles");
      }
      const data = await response.json();
      setAvailableSlots(data);
      if (selectedTime && !data[timeOfDay]?.includes(selectedTime)) {
        setSelectedTime(null);
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : "Error desconocido");
      console.error("Error fetching available slots:", err);
    } finally {
      setLoading(false);
    }
  };

  const handleDateSelection = (date: string) => {
    setSelectedDate(date);
    setSelectedTime(null);
  };

  const handleTimeSelection = (time: string) => {
    setSelectedTime(time);
  };

  const handleWeekNavigation = (direction: "prev" | "next") => {
    if (direction === "prev" && weekOffset > 0) {
      setWeekOffset(weekOffset - 1);
    } else if (direction === "next" && weekOffset < 12) {
      setWeekOffset(weekOffset + 1);
    }
    setSelectedDate(null);
    setSelectedTime(null);
  };

  const handleSave = async () => {
    if (!selectedTime || !selectedDate) {
      setError("Por favor selecciona una fecha y un horario");
      return;
    }

    if (!availableSlots[timeOfDay]?.includes(selectedTime)) {
      setError("El horario seleccionado no está disponible");
      return;
    }

    setLoading(true);
    setError(null);

    try {
      const formattedDateTime = `${selectedDate} ${selectedTime}:00`;
      console.log("Sending PUT request to update booking:", {
        booking_id: booking.id,
        service_id: booking.service.id,
        scheduled_at: formattedDateTime,
      });

      router.put(
        `/bookings/${booking.id}`,
        {
          service_id: booking.service.id,
          scheduled_at: moment(formattedDateTime).format("YYYY-MM-DD HH:mm:ss"),
        },
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            const updatedBooking = {
              ...booking,
              scheduled_at: formattedDateTime,
              status: "pending",
              scheduled_date: moment(formattedDateTime).format("DD/MM/YYYY"),
              scheduled_time: selectedTime,
              status_spanish: "Pendiente",
            };
            onSave(updatedBooking);
            setLoading(false);
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
            setLoading(false);
          },
        }
      );
    } catch (err) {
      console.error("Unexpected error in handleSave:", err);
      setError("Error inesperado al guardar los cambios");
      setLoading(false);
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
                disabled={loading}
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
              <h2 className="text-lg font-bold mb-3 flex items-center gap-2">
                <User className="w-5 h-5" />
                Profesional
              </h2>
              <div className="flex items-center gap-3 p-3 rounded-lg border bg-gray-50">
                <div
                  className="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100"
                >
                  {booking.professional.photo ? (
                    <img
                      src={booking.professional.photo}
                      alt={booking.professional.name}
                      className="w-10 h-10 rounded-full object-cover"
                    />
                  ) : (
                    <span className="text-sm font-medium">
                      {getInitials(booking.professional.name)}
                    </span>
                  )}
                </div>
                <div className="text-left">
                  <div className="font-medium">{booking.professional.name}</div>
                  <div className="text-sm opacity-75">{booking.professional.email}</div>
                </div>
              </div>
            </div>

            {/* Selector de Fecha */}
            <div>
              <h2 className="text-lg font-bold mb-3 flex items-center gap-2">
                <Calendar className="w-5 h-5" />
                Selecciona una fecha
              </h2>
              {loadingDates ? (
                <div className="flex justify-center py-8">
                  <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                </div>
              ) : weekData ? (
                <div className="bg-white border border-gray-200 rounded-lg p-4">
                  <div className="flex items-center justify-between mb-4">
                    <button
                      onClick={() => handleWeekNavigation("prev")}
                      disabled={!weekData.can_go_previous}
                      className={`p-2 rounded-full ${
                        weekData.can_go_previous
                          ? "hover:bg-gray-100 text-gray-600"
                          : "text-gray-300 cursor-not-allowed"
                      }`}
                    >
                      <ChevronLeft className="w-5 h-5" />
                    </button>
                    <div className="grid grid-cols-7 gap-2">
                      {weekData.week_days.map((day) => (
                        <button
                          key={day.date}
                          onClick={() => day.is_available && handleDateSelection(day.date)}
                          disabled={!day.is_available}
                          className={`p-3 rounded-lg text-center transition-colors ${
                            !day.is_available
                              ? "bg-gray-50 text-gray-400 cursor-not-allowed"
                              : selectedDate === day.date
                              ? "bg-blue-500 text-white"
                              : day.is_today
                              ? "bg-blue-100 text-blue-600 hover:bg-blue-200"
                              : "bg-gray-50 text-gray-700 hover:bg-gray-100"
                          }`}
                        >
                          <div className="text-xs font-medium mb-1">{day.day_name_es.slice(0, 3)}</div>
                          <div className="text-lg font-bold">{day.day_number}</div>
                          <div className="text-sm font-medium">{moment(day.date).format("MMM")}</div>
                        </button>
                      ))}
                    </div>
                    <button
                      onClick={() => handleWeekNavigation("next")}
                      disabled={!weekData.can_go_next}
                      className={`p-2 rounded-full ${
                        weekData.can_go_next
                          ? "hover:bg-gray-100 text-gray-600"
                          : "text-gray-300 cursor-not-allowed"
                      }`}
                    >
                      <ChevronRight className="w-5 h-5" />
                    </button>
                  </div>
                </div>
              ) : null}
            </div>

            {/* Selector de Horario */}
            {selectedDate && (
              <div>
                <h2 className="text-lg font-bold mb-3 flex items-center gap-2">
                  <Clock className="w-5 h-5" />
                  Selecciona un horario
                </h2>
                <div className="flex bg-gray-100 rounded-lg p-1 mb-4">
                  {Object.entries(TIME_PERIODS).map(([key, { label }]) => (
                    <button
                      key={key}
                      className={`flex-1 py-2 px-3 text-sm rounded-md transition-colors ${
                        timeOfDay === key
                          ? "bg-blue-500 text-white"
                          : "text-gray-700 hover:bg-gray-200"
                      }`}
                      onClick={() => setTimeOfDay(key as TimeOfDay)}
                      disabled={loading}
                    >
                      {label}
                    </button>
                  ))}
                </div>
                {loading ? (
                  <div className="flex justify-center py-8">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                  </div>
                ) : (
                  <div className="grid grid-cols-3 gap-2">
                    {availableSlots[timeOfDay]?.map((time) => (
                      <button
                        key={time}
                        className={`py-2 px-3 rounded-md text-sm font-medium transition-colors ${
                          selectedTime === time
                            ? "bg-blue-500 text-white"
                            : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                        }`}
                        onClick={() => handleTimeSelection(time)}
                        disabled={loading}
                      >
                        {time}
                      </button>
                    ))}
                  </div>
                )}
                {availableSlots[timeOfDay]?.length === 0 && !loading && (
                  <div className="text-center py-8 text-gray-500">
                    No hay horarios disponibles para este período
                  </div>
                )}
              </div>
            )}

            {/* Botones */}
            <div className="sticky bottom-0 bg-white pt-4 mt-6 border-t">
              <div className="flex justify-end gap-3">
                <button
                  onClick={onClose}
                  className="px-4 py-2 border rounded-lg hover:bg-gray-50"
                  disabled={loading}
                >
                  Cancelar
                </button>
                <button
                  onClick={handleSave}
                  className={`px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 ${
                    loading || !selectedTime || !selectedDate ? "opacity-50 cursor-not-allowed" : ""
                  }`}
                  disabled={loading || !selectedTime || !selectedDate}
                >
                  {loading ? "Guardando..." : "Guardar Cambios"}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}