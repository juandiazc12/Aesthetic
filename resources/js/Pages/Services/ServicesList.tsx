import { Link } from '@inertiajs/react';
import { Daum } from '@/Interfaces/Service';

interface Props {
    services: {
        data: Daum[];
    };
}

export default function ServicesList({ services }: Props) {
    console.log('Servicios activos:', services.data.filter(service => service.status === 'active')); // Filtrar y mostrar solo los activos en la consola

    return (
        <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            {services.data
                .filter((service: Daum) => service.status === 'active') // Filtrar servicios activos
                .map((service: Daum) => (
                    <div
                        key={service.id}
                        className="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-700 dark:shadow-neutral-700/70"
                    >
                        <Link href={`/service/${service.id}`}>
                            <img
                                className="w-full h-auto rounded-t-xl"
                                src={service.image}
                                alt={service.name}
                            />
                        </Link>

                        <div className="p-4 md:p-5">
                            <Link className="underline" href={`/service/${service.id}`}>
                                <h3 className="text-lg font-bold text-gray-800 dark:text-white">
                                    {service.name}
                                </h3>
                            </Link>
                            <p className="mt-1 text-gray-500 dark:text-neutral-400">
                                {service.description}
                            </p>
                            <p className="mt-2 font-extrabold text-green-600 dark:text-green-400">
                                ${service.price}
                            </p>

                            <Link
                                href={`/service/${service.id}`}
                                className="mt-2 py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                            >
                                Reservar ahora
                            </Link>
                        </div>
                    </div>
                ))}
        </div>
    );
}
