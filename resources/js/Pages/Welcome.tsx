import { usePage } from "@inertiajs/react";
import ServicesList from "./Services/ServicesList";
import { Services } from "../../Interfaces/Service";
import { MagnifyingGlassIcon, MapPinIcon } from "@heroicons/react/24/solid";

type PageProps = {
    services: Services;
}

export default function Welcome() {
    const props = usePage<PageProps>().props;

    return (
        <div className="container mx-auto relative">
            <div className="mb-5 relative">
                <img
                    src="https://i.pinimg.com/1200x/b6/19/2b/b6192b03134641e47fa92f7d8aba0e98.jpg"
                    className="w-full object-cover max-h-[300px] rounded-md"
                    alt="Avatar"
                />

                {/* Input doble encima de la imagen */}
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="bg-white bg-opacity-90 p-2 rounded-lg shadow-lg flex gap-1 max-w-[700px] w-full">
                        {/* Inputs y botón de búsqueda */}
                        <div className="relative w-full">
                            <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-500" />
                            <input
                                type="text"
                                placeholder="Buscar servicio, tratamiento o sitio"
                                className="form-input w-full pl-10 pr-4 py-2 rounded-l-md border-0 focus:ring-2 focus:ring-primary focus:outline-none"
                            />
                        </div>

                        <div className="relative w-full">
                            <MapPinIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-500" />
                            <input
                                type="text"
                                placeholder="Ubicación"
                                className="form-input w-full pl-10 pr-4 py-2 rounded-r-md border-0 focus:ring-2 focus:ring-primary focus:outline-none"
                            />
                        </div>

                        <button className="btn btn-primary flex items-center justify-center w-10 h-10 rounded-full">
                            <MagnifyingGlassIcon className="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>

            {/* Lista de servicios */}
            <ServicesList services={props.services} />
        </div>
    )
}
