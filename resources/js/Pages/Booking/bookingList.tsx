import { usePage } from "@inertiajs/react";
import { Booking } from "@/Interfaces/Booking";
import { getInitials } from "@/Pages/utils/helpers";
import "@fortawesome/fontawesome-free/css/all.min.css";
import React, { useState } from "react";
import EditBookingBanner from "@/Pages/components/EditBookingBanner";
import { Link } from '@inertiajs/react';
import { Inertia } from '@inertiajs/inertia';

type Props = {
  bookings: Booking[];
};
export default function BookingList() {
  const { bookings, customer, professionals } = usePage<Props>().props;
  const [editBannerVisible, setEditBannerVisible] = useState(false);
  const [selectedBooking, setSelectedBooking] = useState<Booking | null>(null);

  const colors = [
    "bg-blue-200",
    "bg-green-200",
    "bg-yellow-200",
    "bg-red-200",
    "bg-pink-200",
    "bg-purple-200",
  ];

  const random = Math.floor(Math.random() * colors.length);

  const removeBoooking = (bookingId: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta reserva?')) {
      Inertia.delete(`/bookings/${bookingId}`, {
        onSuccess: () => {
          alert('Reserva eliminada exitosamente');
        },
        onError: () => {
          alert('Ocurrió un error al eliminar la reserva');
        }
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
    // Aquí implementar la lógica para guardar los cambios
    console.log("Guardando cambios:", updatedBooking);
    setEditBannerVisible(false);
    setSelectedBooking(null);
  };

  return (
    <div className="max-w-[85rem] mx-auto p-4">
      {/* Card */}
      <div className="flex flex-col">
        <div className="-m-1.5 overflow-x-auto">
          <div className="p-1.5 min-w-full inline-block align-middle">
            <div className="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
              {/* Header */}
              <div className="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
                <div>
                  <h2 className="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                    Reservas
                  </h2>
                  <p className="text-sm text-gray-600 dark:text-neutral-400"></p>
                </div>

                <div>
                  <div className="inline-flex gap-x-2">
                    <a
                      className="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                      href="#"
                    >
                      View all
                    </a>

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
              </div>
              {/* End Header */}

              {/* Table */}
              <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead className="bg-gray-50 dark:bg-neutral-800">
                  <tr>
                    <th
                      scope="col"
                      className="ps-6 lg:ps-3 xl:ps-0 pe-6 py-3 text-start"
                    >
                      <div className="flex items-center gap-x-2">
                        <span className="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                          Profesional
                        </span>
                      </div>
                    </th>

                    <th scope="col" className="px-6 py-3 text-start">
                      <div className="flex items-center gap-x-2">
                        <span className="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                          Servicio
                        </span>
                      </div>
                    </th>

                    <th scope="col" className="px-6 py-3 text-start">
                      <div className="flex items-center gap-x-2">
                        <span className="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                          Fecha
                        </span>
                      </div>
                    </th>

                    <th scope="col" className="px-6 py-3 text-start">
                      <div className="flex items-center gap-x-2">
                        <span className="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                          Created
                        </span>
                      </div>
                    </th>

                    <th scope="col" className="px-6 py-3 text-end"></th>
                  </tr>
                </thead>

                <tbody className="divide-y divide-gray-200 dark:divide-neutral-700">
                  {bookings.map((booking, index) => (
                    <tr key={index}>
                      <td className="size-px whitespace-nowrap">
                        <div className="ps-6 lg:ps-3 xl:ps-0 pe-6 py-3">
                          <div className="flex items-center gap-x-3">
                            <div
                              className={`w-10 h-10 rounded-full flex items-center justify-center  ${
                                colors[
                                  Math.floor(Math.random() * colors.length)
                                ]
                              }`}
                            >
                              {booking.professional.photo ? (
                                <img
                                  src={booking.professional.photo}
                                  alt={booking.professional.name}
                                />
                              ) : (
                                <span>
                                  {getInitials(booking.professional.name)}
                                </span>
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
                              <img src={booking.service.image} alt="" />
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
                          <span>{booking.scheduled_at}</span>
                        </div>
                      </td>
                      <td className="size-px whitespace-nowrap">
                        <div className="px-6 py-3">
                          <span className="text-sm text-gray-500 dark:text-neutral-500">
                            {booking.created_at}
                          </span>
                        </div>
                      </td>
                      <td className="size-px whitespace-nowrap">
                        <div className="px-6 py-1.5">
                          <a
                            className="inline-flex items-center gap-x-1 text-2xl text-blue-600 hover:text-blue-800 transition-all duration-300 transform hover:scale-110"
                            href="#"
                            onClick={() => handleEditClick(booking)}
                          >
                            {/* Ícono de editar (lapiz) de Font Awesome */}
                            <i className="fas fa-pencil-alt text-blue-600 hover:text-blue-800 transition-all duration-100 transform hover:scale-110"></i>
                          </a>
                        </div>
                      </td>
                      <td className="size-px whitespace-nowrap">
                        <div className="px-6 py-1.5">
                          <Link
                            className="inline-flex items-center gap-x-1 text-2xl text-red-600 hover:text-red-800 transition-all duration-100 transform hover:scale-110"
                            href="#"
                            onClick={() => removeBoooking(booking.id)}
                          >
                            {/* Ícono de eliminar (lapiz) de Font Awesome */}
                            <i className="fas fa-trash-alt text-red-600 hover:text-red-1000 transition-all duration-50 transform hover:scale-200"></i>
                          </Link>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
              {/* End Table */}

              {/* Footer */}
              <div className="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-neutral-700">
                <div>
                  <p className="text-sm text-gray-600 dark:text-neutral-400">
                    <span className="font-semibold text-gray-800 dark:text-neutral-200"></span>{" "}
                    results
                  </p>
                </div>

                <div>
                  <div className="inline-flex gap-x-2">
                    <button
                      type="button"
                      className="py-1.5 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
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
              {/* End Footer */}
            </div>
          </div>
        </div>
      </div>
      {/* End Card */}

      {/* Banner de edición */}
      {editBannerVisible && selectedBooking && (
        <EditBookingBanner
          booking={selectedBooking}
          onClose={handleCloseBanner}
          onSave={handleSaveBooking}
        />
      )}
    </div>
  );
}
