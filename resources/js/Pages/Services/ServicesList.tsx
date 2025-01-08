import { Link } from '@inertiajs/react';
import { Daum } from '@/Interfaces/Service'; // Usamos 'Daum' como tipo de cada servicio

interface Props {
    services: {
        data: Daum[]; // Aquí definimos que `services` contiene un array de Daum
    }
}

export default function ServicesList(props: Props) {
    const { services } = props;
    
    return (
        <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            {services.data
                .map((service: Daum, i: number) => ( // Usamos Daum como tipo de cada servicio
                    <div
                        key={i}
                        className="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-700 dark:shadow-neutral-700/70"
                    >
                        <Link href={`/service/${service.id}`}> {/* Usamos id para el enlace */}
                            <img className="w-full h-auto rounded-t-xl"
                                 src={service.image}
                                 alt="Card Image"/>
                        </Link>

                        <div className="p-4 md:p-5">
                            <Link className="underline" href={`/service/${service.id}`}> {/* Usamos id aquí también */}
                                <h3 className="text-lg font-bold text-gray-800 dark:text-white">
                                    {service.name}
                                </h3>
                            </Link>
                            <p className="mt-1 text-gray-500 dark:text-neutral-400">
                                {service.description}
                            </p>
                            <p className="mt-2 font-extrabold dark:text-neutral-400">
                                {service.price}
                            </p>
                            <Link href={`/service/${service.id}`}>
                                <a className="mt-2 py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Reservar ahora
                                </a>
                            </Link>
                        </div>
                    </div>
                ))}
        </div>
    );
}
