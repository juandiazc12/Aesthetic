import React from 'react';
import { Daum } from '@/Interfaces/Service';
import { Link } from '@inertiajs/react';
import { FaWhatsapp } from 'react-icons/fa';

interface Props {
    service: Daum;
}

export default function Service({ service }: Props) {
    return (
        <div className="max-w-4xl mx-auto p-4">
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
                    <br />
                    {/* Reservation Button */}
                    <Link
                        href={`/booking/${service.id}`}
                        className="mt-2 py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                    >
                        Reservar ahora
                    </Link>
                    <br />
                    <br />

                    {/* WhatsApp Button */}
                    <p className="text-gray-600 text-center">Contacto</p>
                    <hr className="border-gray-300 w-1/2 my-2" />
                    
                    <div className="flex flex-col items-end">
                        <div className="flex items-center gap-2">
                            <p className="text-gray-600">+57 321 770 6324</p>
                            <a
                                href="https://wa.me/573217706324"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="flex items-center justify-center bg-green-500 text-white p-2 rounded-full w-10 h-10 hover:bg-green-600 transition-colors"
                            >
                                <FaWhatsapp className="w-6 h-6" />
                            </a>
                        </div>
                    </div>
                    <hr className="border-gray-300 w-1/2 my-2" />
                </div>
            </div>
        </div>
    );
}
