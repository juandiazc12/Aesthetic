// src/Pages/BookingList.tsx
import { usePage } from "@inertiajs/react";
import { Booking } from "@/Interfaces/Booking";
import { getInitials } from "@/Pages/utils/helpers";
import "@fortawesome/fontawesome-free/css/all.min.css";
import React, { useState, useEffect } from "react";
import EditBookingBanner from "@/Pages/components/EditBookingBanner";
import RatingBanner from "@/Pages/components/RatingBanner"; // Import RatingBanner
import { Link, router } from "@inertiajs/react";

type Props = {
  bookings: Booking[];
  flash?: { success?: string; error?: string };
};

export default function BookingList() {
  const { bookings: initialBookings, customer, professionals, flash } = usePage<Props>().props;
  const [bookings, setBookings] = useState<Booking[]>(initialBookings);
  const [editBannerVisible, setEditBannerVisible] = useState(false);
  const [ratingBannerBooking, setRatingBannerBooking] = useState<Booking | null>(null);
  const [selectedBooking, setSelectedBooking] = useState<Booking | null>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [filters, setFilters] = useState({
    professional: "",
    service: "",
    date: "",
    status: "",
    bookingId: "",
    paymentMethod: "",
  });
  const [isFilterOpen, setIsFilterOpen] = useState(false);
  const bookingsPerPage = 5;

  // Sync local bookings with server-side initialBookings
  useEffect(() => {
    setBookings(initialBookings);
  }, [initialBookings]);

  // Handle flash messages
  useEffect(() => {
    if (flash?.success) {
      alert(flash.success);
    } else if (flash?.error) {
      alert(flash.error);
    }
  }, [flash]);

  const colors = [
    "bg-blue-200",
    "bg-green-200",
    "bg-yellow-200",
    "bg-red-200",
    "bg-pink-200",
    "bg-purple-200",
  ];

  const isEditable = (scheduledAt: string) => {
    const scheduledDate = new Date(scheduledAt);
    const now = new Date();
    const oneHourBefore = new Date(scheduledDate.getTime() - 60 * 60 * 1000);
    return now < oneHourBefore;
  };

  const isActionDisabled = (scheduledAt: string, status: string) => {
    const isPastOneHour = !isEditable(scheduledAt);
    const isCancelledOrCompleted = status.toLowerCase() === "cancelled" || status.toLowerCase() === "completed";
    return isPastOneHour || isCancelledOrCompleted;
  };

  const filterBookings = (bookings: Booking[]) => {
    return bookings
      .filter((booking) => {
        const matchesProfessional = filters.professional
          ? booking.professional.name.toLowerCase() === filters.professional.toLowerCase()
          : true;
        const matchesService = filters.service
          ? booking.service.name.toLowerCase() === filters.service.toLowerCase()
          : true;
        const matchesDate = filters.date
          ? new Date(booking.scheduled_at).toISOString().split("T")[0] === filters.date
          : true;
        const matchesStatus = filters.status
          ? booking.status.toLowerCase() === filters.status.toLowerCase()
          : true;
        const matchesBookingId = filters.bookingId
          ? booking.id.toString() === filters.bookingId
          : true;
        const matchesPaymentMethod = filters.paymentMethod
          ? booking.payment_method.toLowerCase() === filters.paymentMethod.toLowerCase()
          : true;

        return (
          matchesProfessional &&
          matchesService &&
          matchesDate &&
          matchesStatus &&
          matchesBookingId &&
          matchesPaymentMethod
        );
      })
      .sort((a, b) => new Date(b.scheduled_at).getTime() - new Date(a.scheduled_at).getTime());
  };

  const uniqueProfessionals = Array.from(
    new Set(bookings.map((booking) => booking.professional.name))
  );
  const uniqueServices = Array.from(
    new Set(bookings.map((booking) => booking.service.name))
  );
  const uniquePaymentMethods = Array.from(
    new Set(bookings.map((booking) => booking.payment_method))
  );

  const filteredBookings = filterBookings(bookings);
  const indexOfLastBooking = currentPage * bookingsPerPage;
  const indexOfFirstBooking = indexOfLastBooking - bookingsPerPage;
  const currentBookings = filteredBookings.slice(indexOfFirstBooking, indexOfLastBooking);
  const totalPages = Math.ceil(filteredBookings.length / bookingsPerPage);

  const cancelBooking = (bookingId: number) => {
    if (confirm("¿Estás seguro de que deseas cancelar esta reserva?")) {
      router.delete(`/bookings/${bookingId}`, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          console.log("Reserva cancelada exitosamente");
        },
        onError: (errors) => {
          console.error("Error al cancelar la reserva:", errors);
          alert("Ocurrió un error al intentar cancelar la reserva: " + (errors.error || "Error desconocido"));
        },
      });
    }
  };

  const handleEditClick = (booking: Booking) => {
    setSelectedBooking(booking);
    setEditBannerVisible(true);
  };

  const handleCloseBanner = () => {
    setEditBannerVisible(false);
    setSelectedBooking(null);
  };

  const handleSaveBooking = (updatedBooking: Booking) => {
    setBookings((prevBookings) =>
      prevBookings.map((booking) =>
        booking.id === updatedBooking.id ? updatedBooking : booking
      )
    );
    setEditBannerVisible(false);
    setSelectedBooking(null);
  };

  const handleRateClick = (booking: Booking) => {
    setRatingBannerBooking(booking);
  };

  const handleCloseRatingBanner = () => {
    setRatingBannerBooking(null);
  };

  const paginate = (pageNumber: number) => setCurrentPage(pageNumber);

  const handleFilterChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFilters((prev) => ({ ...prev, [name]: value }));
    setCurrentPage(1);
  };

  const clearFilters = () => {
    setFilters({
      professional: "",
      service: "",
      date: "",
      status: "",
      bookingId: "",
      paymentMethod: "",
    });
    setCurrentPage(1);
  };

  return (
    <div className="max-w-[85rem] mx-auto p-4">
      <div className="flex flex-col">
        <div className="-m-1.5 overflow-x-auto">
          <div className="p-1.5 min-w-full inline-block align-middle">
            <div className="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
              <div className="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
                <div>
                  <h2 className="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                    Reservas
                  </h2>
                  <p className="text-sm text-gray-600 dark:text-neutral-400">
                    Total: {bookings.length} reservas (Filtradas: {filteredBookings.length})
                  </p>
                </div>
                <div className="flex gap-2">
                  <button
                    onClick={() => setIsFilterOpen(!isFilterOpen)}
                    className={`py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800 ${
                      isFilterOpen ? "bg-blue-600 text-white hover:bg-blue-700" : ""
                    }`}
                  >
                    <svg
                      className="shrink-0 size-4"
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    >
                      <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                    </svg>
                    Filtrar
                  </button>
                  <a
                    className="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                    href="/"
                  >
                    <svg
                      className="shrink-0 size-4"
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    >
                      <path d="M5 12h14" />
                      <path d="M12 5v14" />
                    </svg>
                    Agregar Servicio
                  </a>
                </div>
              </div>
              <div
                className={`px-6 py-4 overflow-hidden transition-all duration-300 ease-in-out ${
                  isFilterOpen ? "max-h-96 opacity-100" : "max-h-0 opacity-0"
                }`}
              >
                <div className="grid grid-cols-1 sm:grid-cols-6 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                      #
                    </label>
                    <input
                      type="number"
                      name="bookingId"
                      value={filters.bookingId}
                      onChange={handleFilterChange}
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white"
                      placeholder="Ej. 1"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                      Profesional
                    </label>
                    <select
                      name="professional"
                      value={filters.professional}
                      onChange={handleFilterChange}
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white"
                    >
                      <option value="">Todos</option>
                      {uniqueProfessionals.map((name) => (
                        <option key={name} value={name}>
                          {name}
                        </option>
                      ))}
                    </select>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                      Servicio
                    </label>
                    <select
                      name="service"
                      value={filters.service}
                      onChange={handleFilterChange}
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white"
                    >
                      <option value="">Todos</option>
                      {uniqueServices.map((name) => (
                        <option key={name} value={name}>
                          {name}
                        </option>
                      ))}
                    </select>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                      Cita
                    </label>
                    <input
                      type="date"
                      name="date"
                      value={filters.date}
                      onChange={handleFilterChange}
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                      Modalidad de Pago
                    </label>
                    <select
                      name="paymentMethod"
                      value={filters.paymentMethod}
                      onChange={handleFilterChange}
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white"
                    >
                      <option value="">Todos</option>
                      {uniquePaymentMethods.map((method) => (
                        <option key={method} value={method}>
                          {method}
                        </option>
                      ))}
                    </select>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                      Estado
                    </label>
                    <select
                      name="status"
                      value={filters.status}
                      onChange={handleFilterChange}
                      className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white"
                    >
                      <option value="">Todos</option>
                      <option value="pending">Pendiente</option>
                      <option value="confirmed">Confirmada</option>
                      <option value="completed">Completada</option>
                      <option value="cancelled">Cancelada</option>
                    </select>
                  </div>
                </div>
                <div className="mt-4 text-right">
                  <button
                    onClick={clearFilters}
                    className="py-2 px-4 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none dark:text-neutral-300 dark:bg-neutral-700 dark:hover:bg-neutral-600"
                  >
                    Limpiar Filtros
                  </button>
                </div>
              </div>
              <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead className="bg-gray-50 dark:bg-neutral-800">
                  <tr>
                    <th scope="col" className="ps-6 lg:ps-3 xl:ps-0 pe-6 py-3 text-start w-16">
                      <div className="flex items-center gap-x-2">
                        <span className="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                          #
                        </span>
                      </div>
                    </th>
                    <th scope="col" className="px-6 py-3 text-start w-40">
                      <div className="flex items-center gap-x-2">
                        <span className="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                          Profesional
                        </span>
                      </div>
                    </th>
                    <th scope="col" className="px-6 py-3 text-start w-40">
                      <div className="flex items-center gap-x-2">
                        <span className="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                          Servicio
                        </span>
                      </div>
                    </th>
                    <th scope="col" className="px-6 py-3 text-start w-40">
                      <div className="flex items-center gap-x-2">
                        <span className="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                          Cita
                        </span>
                      </div>
                    </th>
                    <th scope="col" className="px-6 py-3 text-start w-40">
                      <div className="flex items-center gap-x-2">
                        <span className="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                          Modalidad de Pago
                        </span>
                      </div>
                    </th>
                    <th scope="col" className="px-6 py-3 text-start w-40">
                      <div className="flex items-center gap-x-2">
                        <span className="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                          Estado
                        </span>
                      </div>
                    </th>
                    <th scope="col" className="px-6 py-3 text-end w-20"></th>
                    <th scope="col" className="px-6 py-3 text-end w-20"></th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200 dark:divide-neutral-700">
                  {currentBookings.map((booking, index) => {
                    console.log("Booking:", {
                      id: booking.id,
                      scheduled_at: booking.scheduled_at,
                      status: booking.status,
                      professionalPhoto: booking.professional.photo,
                      serviceImage: booking.service.image,
                    });
                    const canEditOrCancel = isActionDisabled(booking.scheduled_at, booking.status);
                    const isCompleted = booking.status.toLowerCase() === "completed";
                    const statusClass = {
                      pending: "bg-yellow-200 text-yellow-800",
                      confirmed: "bg-blue-200 text-blue-800",
                      completed: "bg-green-200 text-green-800",
                      cancelled: "bg-red-200 text-red-800",
                    }[booking.status.toLowerCase()] || "bg-gray-200 text-gray-800";

                    return (
                      <tr key={booking.id}>
                        <td className="size-px whitespace-nowrap">
                          <div className="ps-6 lg:ps-3 xl:ps-0 pe-6 py-3">
                            <span className="text-sm text-gray-500 dark:text-neutral-500">
                              {booking.id}
                            </span>
                          </div>
                        </td>
                        <td className="size-px whitespace-nowrap">
                          <div className="ps-6 lg:ps-3 xl:ps-0 pe-6 py-3">
                            <div className="flex items-center gap-x-3">
                              <div
                                className={`w-10 h-10 rounded-full flex items-center justify-center ${
                                  colors[Math.floor(Math.random() * colors.length)]
                                }`}
                              >
                                {booking.professional.photo ? (
                                  <img
                                    src={booking.professional.photo}
                                    alt={booking.professional.name}
                                    className="w-10 h-10 rounded-full object-cover"
                                  />
                                ) : (
                                  <span>{getInitials(booking.professional.name)}</span>
                                )}
                              </div>
                              <div className="grow">
                                <span className="block text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                  {booking.professional.name}
                                </span>
                                <span className="block text-sm text-gray-500 dark:text-neutral-500">
                                  {booking.professional.email}
                                </span>
                              </div>
                            </div>
                          </div>
                        </td>
                        <td className="h-px w-72 whitespace-nowrap">
                          <div className="ps-6 lg:ps-3 xl:ps-0 pe-6 py-3">
                            <div className="flex items-center gap-x-3">
                              <div className="w-16 h-16 rounded-md flex items-center justify-center bg-blue-200">
                                {booking.service.image ? (
                                  <img
                                    src={booking.service.image}
                                    alt={booking.service.name}
                                    className="w-16 h-16 rounded-md object-cover"
                                  />
                                ) : (
                                  <span>{getInitials(booking.service.name)}</span>
                                )}
                              </div>
                              <div className="grow">
                                <span className="block text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                  {booking.service.name}
                                </span>
                              </div>
                            </div>
                          </div>
                        </td>
                        <td className="size-px whitespace-nowrap">
                          <div className="px-6 py-3">
                            <span className="text-sm text-gray-500 dark:text-neutral-500">
                              {new Date(booking.scheduled_at).toLocaleString("es-CO", {
                                day: "2-digit",
                                month: "2-digit",
                                year: "numeric",
                                hour: "2-digit",
                                minute: "2-digit",
                                hour12: true,
                              })}
                            </span>
                          </div>
                        </td>
                        <td className="size-px whitespace-nowrap">
                          <div className="px-6 py-3">
                            <span className="text-sm text-gray-500 dark:text-neutral-500">
                              {booking.payment_method}
                            </span>
                          </div>
                        </td>
                        <td className="size-px whitespace-nowrap">
                          <div className="px-6 py-3">
                            <span
                              className={`inline-flex items-center px-2 py-1 rounded-full text-sm font-medium ${statusClass}`}
                            >
                              {booking.status_spanish}
                            </span>
                          </div>
                        </td>
                        <td className="size-px whitespace-nowrap">
                          <div className="px-6 py-1.5">
                            {!isCompleted && (
                              <a
                                className={`inline-flex items-center gap-x-1 text-2xl text-blue-600 hover:text-blue-800 transition-all duration-300 transform hover:scale-110 ${
                                  canEditOrCancel ? "opacity-50 pointer-events-none" : ""
                                }`}
                                href="#"
                                onClick={() => !canEditOrCancel && handleEditClick(booking)}
                                title={canEditOrCancel ? "No editable (hora o estado)" : "Editar"}
                              >
                                <i className="fas fa-pencil-alt text-blue-600 hover:text-blue-800 transition-all duration-100 transform hover:scale-110"></i>
                              </a>
                            )}
                          </div>
                        </td>
                        <td className="size-px whitespace-nowrap">
                          <div className="px-6 py-1.5">
                            {!isCompleted ? (
                              <Link
                                className={`inline-flex items-center gap-x-1 text-2xl text-red-600 hover:text-red-800 transition-all duration-100 transform hover:scale-110 ${
                                  canEditOrCancel ? "opacity-50 pointer-events-none" : ""
                                }`}
                                href="#"
                                onClick={(e) => {
                                  e.preventDefault();
                                  if (!canEditOrCancel) cancelBooking(booking.id);
                                }}
                                title={canEditOrCancel ? "No cancelable (hora o estado)" : "Cancelar"}
                              >
                                <i className="fas fa-trash-alt text-red-600 hover:text-red-800 transition-all duration-50 transform hover:scale-110"></i>
                              </Link>
                            ) : (
                              <button
                                className="inline-flex items-center gap-x-1 text-2xl text-yellow-500 hover:text-yellow-600 transition-all duration-100 transform hover:scale-110"
                                onClick={() => handleRateClick(booking)}
                                title="Calificar servicio"
                              >
                                ⭐
                              </button>
                            )}
                          </div>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
              <div className="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-neutral-700">
                <div>
                  <p className="text-sm text-gray-600 dark:text-neutral-400">
                    <span className="font-semibold text-gray-800 dark:text-neutral-200">
                      {indexOfFirstBooking + 1} -{" "}
                      {Math.min(indexOfLastBooking, filteredBookings.length)} de{" "}
                      {filteredBookings.length}
                    </span>{" "}
                    resultados
                  </p>
                </div>
                <div>
                  <div className="inline-flex gap-x-2">
                    <button
                      type="button"
                      className="py-1.5 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                      onClick={() => paginate(currentPage - 1)}
                      disabled={currentPage === 1}
                    >
                      <svg
                        className="shrink-0 size-4"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                      >
                        <path d="m15 18-6-6 6-6" />
                      </svg>
                      Prev
                    </button>
                    <button
                      type="button"
                      className="py-1.5 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                      onClick={() => paginate(currentPage + 1)}
                      disabled={currentPage === totalPages}
                    >
                      Next
                      <svg
                        className="shrink-0 size-4"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                      >
                        <path d="m9 18 6-6-6-6" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {editBannerVisible && selectedBooking && (
        <EditBookingBanner
          booking={selectedBooking}
          onClose={handleCloseBanner}
          onSave={handleSaveBooking}
        />
      )}
      {ratingBannerBooking && (
        <RatingBanner
          booking={ratingBannerBooking}
          onClose={handleCloseRatingBanner}
        />
      )}
    </div>
  );
}