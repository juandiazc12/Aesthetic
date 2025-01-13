import { Daum } from '@/Interfaces/Service';

interface Props {
    service: Daum; // Usamos la interfaz 'Daum' para tipar el servicio
}

export default function Service({ service }: Props) {
    return (
        <div className="max-w-3xl mx-auto p-4">
            <h1 className="text-3xl font-bold mb-4">{service.name}</h1>
            <img
                src={service.image}
                alt={service.name}
                className="w-full h-auto mb-4 rounded-lg"
            />
            <p className="text-gray-700 mb-4">{service.description}</p>
            <p className="text-lg font-bold">Precio: ${service.price}</p>
            <p className={`mb-4 ${service.status === 'active' ? 'text-green-500' : 'text-red-500'}`}>
                Estado: {service.status === 'active' ? 'Activo' : 'Suspendido'}
            </p>
            <p className="text-sm text-gray-500">Creado el: {service.created_at}</p>
            <p className="text-sm text-gray-500">Actualizado el: {service.updated_at}</p>
        </div>
    );
}
