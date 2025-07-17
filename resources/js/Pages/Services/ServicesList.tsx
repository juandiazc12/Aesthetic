import { Link } from '@inertiajs/react';
import { Daum } from '@/Interfaces/Service';

interface Props {
    services: {
        data: Daum[];
    };
}

export default function ServicesList({ services }: Props) {
    // Filtrar servicios activos
    const activeServices = services.data?.filter((service: Daum) => service.status === 'active') || [];

    // Si no hay servicios activos, mostrar mensaje
    if (activeServices.length === 0) {
        return (
            <div className="text-center py-12">
                <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg className="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 className="text-lg font-semibold text-gray-600 mb-2">
                    No hay servicios disponibles
                </h3>
                <p className="text-gray-500">
                    Pronto tendremos más servicios disponibles para ti.
                </p>
            </div>
        );
    }

    return (
        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            {activeServices.map((service: Daum) => (
                <div
                    key={service.id}
                    className="flex flex-col bg-white border shadow-sm rounded-xl overflow-hidden hover:shadow-lg dark:bg-neutral-900 dark:border-neutral-700 dark:shadow-neutral-700/70 hover:scale-105 transition-transform duration-300"
                >
                    {/* Imagen del servicio */}
                    <Link href={`/service/${service.id}`}>
                        <img
                            className="w-full h-48 object-cover"
                            src={service.image}
                            alt={service.name}
                            loading="lazy"
                        />
                    </Link>

                    {/* Contenido de la tarjeta */}
                    <div className="flex flex-col p-4 md:p-5 h-full">
                        {/* Título del servicio */}
                        <Link className="underline" href={`/service/${service.id}`}>
                            <h3 className="text-lg font-bold text-gray-800 dark:text-white">
                                {service.name}
                            </h3>
                        </Link>

                        {/* Descripción del servicio */}
                        <p className="mt-1 text-gray-500 dark:text-neutral-400 flex-grow">
                            {service.description}
                        </p>

                        {/* Precio */}
                        <p className="mt-2 font-extrabold text-green-600 dark:text-green-400">
                            ${service.price?.toLocaleString('es-CO')}
                        </p>

                        {/* Botón en la parte inferior */}
                        <Link
                            href={`/service/${service.id}`}
                            className="mt-auto py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 hover:scale-105 disabled:opacity-50 disabled:pointer-events-none transition-transform duration-300"
                        >
                            Reservar ahora
                        </Link>
                    </div>  
                </div>
            ))}
        </div>
    );
}