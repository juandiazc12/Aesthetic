import React, { useState, useEffect } from "react";
import { ChevronLeft, ChevronRight, Clock, User, Calendar } from "lucide-react";
import { router, usePage } from "@inertiajs/react";
import { Customer } from "@/Interfaces/Customer";
import { getInitials } from "@/Pages/utils/helpers";
import moment from "moment";
import "moment/locale/es"; // Importar localización en español
import { PaymentSystem, PaymentDetails } from "../components/PaymentSystem";

interface Service {
  id: number;
  name: string;
  description: string;
  price: number;
  duration: number;
  status: string;
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

interface ComponentProps {
  service: Service;
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

// Configurar moment para español
moment.locale("es");

export default function AddBooking({ service }: ComponentProps) {
  const { customer, professionals } = usePage<Props>().props;

  const [selectedDate, setSelectedDate] = useState<string | null>(null);
  const [timeOfDay, setTimeOfDay] = useState<TimeOfDay>("morning");
  const [selectedTime, setSelectedTime] = useState<string | null>(null);
  const [professionalId, setProfessionalId] = useState<number | null>(
    professionals.length === 1 ? professionals[0].id : null
  );
  const [availableSlots, setAvailableSlots] = useState<TimeSlots>({
    morning: [],
    afternoon: [],
    evening: [],
  });
  const [weekData, setWeekData] = useState<WeekData | null>(null);
  const [weekOffset, setWeekOffset] = useState(0);
  const [showPayment, setShowPayment] = useState(false);
  const [loading, setLoading] = useState(false);
  const [loadingDates, setLoadingDates] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // Obtener fechas disponibles cuando cambia el profesional o el offset de semana
  useEffect(() => {
    if (professionalId) {
      fetchAvailableDates();
    }
  }, [professionalId, weekOffset]);

  // Obtener horarios disponibles cuando cambia la fecha o el profesional
  useEffect(() => {
    if (professionalId && selectedDate) {
      fetchAvailableSlots();
    }
  }, [professionalId, selectedDate]);

  const fetchAvailableDates = async () => {
    if (!professionalId) return;

    setLoadingDates(true);
    setError(null);

    try {
      const response = await fetch('/api/booking/available-dates', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
          professional_id: professionalId,
          week_offset: weekOffset,
        }),
      });

      if (!response.ok) {
        throw new Error('Error al obtener fechas disponibles');
      }

      const data = await response.json();
      setWeekData(data);
      
      // Limpiar selección de fecha si no está disponible en la nueva semana
      if (selectedDate && !data.week_days.some((day: WeekDay) => day.date === selectedDate)) {
        setSelectedDate(null);
        setSelectedTime(null);
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error desconocido');
      console.error('Error fetching available dates:', err);
    } finally {
      setLoadingDates(false);
    }
  };

  const fetchAvailableSlots = async () => {
  if (!professionalId || !selectedDate) return;

  setLoading(true);
  setError(null);

  try {
    const response = await fetch('/api/booking/available-slots', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        professional_id: professionalId,
        date: selectedDate,
        service_id: service.id, // ¡Agregar esta línea!
      }),
    });

    if (!response.ok) {
      throw new Error('Error al obtener horarios disponibles');
    }

    const data = await response.json();
    setAvailableSlots(data);
    
    // Limpiar selección de hora si no está disponible
    if (selectedTime && !data[timeOfDay]?.includes(selectedTime)) {
      setSelectedTime(null);
    }
  } catch (err) {
    setError(err instanceof Error ? err.message : 'Error desconocido');
    console.error('Error fetching available slots:', err);
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

  const handleProfessionalSelection = (professionalId: number) => {
    setProfessionalId(professionalId);
    setSelectedDate(null);
    setSelectedTime(null);
    setWeekOffset(0); // Resetear a la semana actual
  };

  const handleWeekNavigation = (direction: 'prev' | 'next') => {
    if (direction === 'prev' && weekOffset > 0) {
      setWeekOffset(weekOffset - 1);
    } else if (direction === 'next' && weekOffset < 12) {
      setWeekOffset(weekOffset + 1);
    }
    setSelectedDate(null);
    setSelectedTime(null);
  };

  const handlePaymentComplete = async (paymentDetails: PaymentDetails) => {
    if (!selectedTime || !professionalId || !selectedDate) return;

    const scheduledAt = new Date(selectedDate + ' ' + selectedTime);

    try {
      const response = await fetch('/api/booking/create', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
          service_id: service.id,
          professional_id: professionalId,
          scheduled_at: moment(scheduledAt).format('YYYY-MM-DD HH:mm:ss'),
          payment_method: paymentDetails.method,
          payment_status: paymentDetails.status,
          payment_transaction_id: paymentDetails.transactionId,
          payment_amount: paymentDetails.amount,
        }),
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Error al crear la reserva');
      }

      // Redirigir a página de éxito
      router.visit('/booking/success');
    } catch (error) {
      console.error('Error creating booking:', error);
      setError(error instanceof Error ? error.message : 'Error al procesar la reserva');
    }
  };

  const selectedProfessional = professionals.find(p => p.id === professionalId);
  const selectedDay = weekData?.week_days.find(day => day.date === selectedDate);

  if (showPayment) {
    if (!selectedDate || !selectedTime || !professionalId) {
      return (
        <div className="w-full max-w-[30rem] text-red-500">
          Faltan datos de la reserva (fecha u horario)
        </div>
      );
    }

    const scheduledAt = new Date(`${selectedDate} ${selectedTime}`);
    return (
      <div className="w-full max-w-[30rem]">
        <PaymentSystem
          amount={service.price}
          serviceId={service.id}
          serviceName={service.name}
          bookingId={0} // Opcional, si no lo necesitas
          customerEmail={customer.email}
          customerName={customer.last_name}
          scheduledAt={moment(scheduledAt).format('YYYY-MM-DD HH:mm:ss')}
          professionalId={professionalId}
          onPaymentComplete={handlePaymentComplete}
          onCancel={() => setShowPayment(false)}
        />
      </div>
    );
  }

  return (
    <div className="w-full max-w-[30rem] space-y-6">
      {error && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {error}
        </div>
      )}

      {/* Selector de Profesional */}
      <div>
        <h2 className="text-lg font-bold mb-3 flex items-center gap-2">
          <User className="w-5 h-5" />
          Selecciona un profesional
        </h2>
        {professionals.length === 0 ? (
          <div className="text-center text-gray-500">
            No hay profesionales disponibles para este servicio
          </div>
        ) : (
          <div className="grid gap-3">
            {professionals.map((professional) => (
              <button
                key={professional.id}
                className={`flex items-center gap-3 p-3 rounded-lg border transition-colors ${
                  professionalId === professional.id
                    ? "bg-blue-500 text-white border-blue-500"
                    : "bg-white hover:bg-gray-50 border-gray-200"
                }`}
                onClick={() => handleProfessionalSelection(professional.id)}
              >
                <div
                  className={`w-10 h-10 rounded-full flex items-center justify-center ${
                    professionalId === professional.id
                      ? "bg-blue-700 text-white"
                      : "bg-gray-100"
                  }`}
                >
                  {professional.photo ? (
                    <img
                      src={professional.photo}
                      alt={professional.name}
                      className="w-10 h-10 rounded-full object-cover"
                    />
                  ) : (
                    <span className="text-sm font-medium">
                      {getInitials(professional.name)}
                    </span>
                  )}
                </div>
                <div className="text-left">
                  <div className="font-medium">{professional.name}</div>
                  <div className="text-sm opacity-75">{professional.email}</div>
                </div>
              </button>
            ))}
          </div>
        )}
      </div>

      {/* Selector de Fecha - Barra de días de la semana */}
      {professionalId && (
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
              {/* Navegación de semanas */}
              <div className="flex items-center justify-between mb-4">
                <button
                  onClick={() => handleWeekNavigation('prev')}
                  disabled={!weekData.can_go_previous}
                  className={`p-2 rounded-full ${
                    weekData.can_go_previous
                      ? 'hover:bg-gray-100 text-gray-600'
                      : 'text-gray-300 cursor-not-allowed'
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
                          ? 'bg-gray-50 text-gray-400 cursor-not-allowed'
                          : selectedDate === day.date
                          ? 'bg-blue-500 text-white'
                          : day.is_today
                          ? 'bg-blue-100 text-blue-600 hover:bg-blue-200'
                          : 'bg-gray-50 text-gray-700 hover:bg-gray-100'
                      }`}
                    >
                      <div className="text-xs font-medium mb-1">{day.day_name_es.slice(0, 3)}</div>
                      <div className="text-lg font-bold">{day.day_number}</div>
                      <div className="text-sm font-medium">{moment(day.date).format('MMM')}</div>
                    </button>
                  ))}
                </div>
                
                <button
                  onClick={() => handleWeekNavigation('next')}
                  disabled={!weekData.can_go_next}
                  className={`p-2 rounded-full ${
                    weekData.can_go_next
                      ? 'hover:bg-gray-100 text-gray-600'
                      : 'text-gray-300 cursor-not-allowed'
                  }`}
                >
                  <ChevronRight className="w-5 h-5" />
                </button>
              </div>
            </div>
          ) : null}
        </div>
      )}

      {/* Selector de Horario */}
      {professionalId && selectedDate && (
        <div>
          <h2 className="text-lg font-bold mb-3 flex items-center gap-2">
            <Clock className="w-5 h-5" />
            Selecciona un horario
          </h2>
          
          {/* Selector de período */}
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
              >
                {label}
              </button>
            ))}
          </div>

          {/* Horarios disponibles */}
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

      {/* Botón de Continuar */}
      {selectedTime && professionalId && selectedDate && (
        <button
          onClick={() => setShowPayment(true)}
          className="w-full bg-blue-500 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-600 transition-colors"
        >
          Continuar al pago ({service.price} $)
        </button>
      )}
    </div>
  );
}