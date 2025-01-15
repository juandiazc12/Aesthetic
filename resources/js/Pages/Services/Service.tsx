import React from 'react';
import {Daum} from '@/Interfaces/Service';
import {Link, usePage} from '@inertiajs/react';
import {FaWhatsapp} from 'react-icons/fa';
import AddBooking from "@/Pages/components/add-booking";

interface ComponentProps {
  service: Daum;
}

export default function Service({service}: ComponentProps) {

  return (
    <div className="max-w-[85rem] mx-auto p-4">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
        {/* Column 1: Service Details */}
        <div className="space-y-4">
          <img
            src={service.image}
            alt={service.name}
            className="w-full h-64 object-cover rounded-lg"
          />

          <div className="space-y-2">
            <h1 className="text-2xl font-bold">{service.name}</h1>
            <p className="text-gray-600">{service.description}</p>

            <div className="flex items-center justify-between">
              <span className="font-bold text-xl">${service.price}</span>
            </div>
          </div>
        </div>

        {/* Column 2: Contact & Booking */}
        <div className="flex flex-col items-end space-y-3">

          {/* Reservation Button */}
          <Link
            href={`/bookings`}
            className="btn btn-primary"
          >
            Mis Reservas
          </Link>

          <AddBooking service={service}/>

          {/* WhatsApp Button */}
          <p className="text-gray-600 text-center">Contacto</p>
          <hr className="border-gray-300 w-1/2 my-2"/>

          <div className="flex flex-col items-end">
            <div className="flex items-center gap-2">
              <p className="text-gray-600">+57 321 770 6324</p>
              <a
                href="https://wa.me/573217706324"
                target="_blank"
                rel="noopener noreferrer"
                className="flex items-center justify-center bg-green-500 text-white p-2 rounded-full w-10 h-10 hover:bg-green-600 transition-colors"
              >
                <FaWhatsapp className="w-6 h-6"/>
              </a>
            </div>
          </div>
          <hr className="border-gray-300 w-1/2 my-2"/>
        </div>
      </div>
    </div>
  );
}
