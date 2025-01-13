<?php
namespace App\Http\Controllers;

use App\Models\Service;
use Inertia\Inertia;

class Services extends Controller
{
    // Método para mostrar todos los servicios en la lista
    public function index(): \Inertia\Response
    {
        $services = Service::paginate(15)->through(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'image' => $service->image,
                'description' => $service->description,
                'price' => $service->price,
                'duration' => $duration->duration,
                'status' => $service->status,
                'created_at' => $service->created_at,
                'updated_at' => $service->updated_at,
            ];
        });

        return Inertia::render('Services/ServicesList', [
            'services' => $services,
        ]);
    }

    // Método para mostrar los detalles de un servicio específico
    public function show($id): \Inertia\Response
    {
        $service = Service::findOrFail($id);

        return Inertia::render('Services/Service', [
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'image' => $service->image,
                'description' => $service->description,
                'price' => $service->price,
                'status' => $service->status,
                'created_at' => $service->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $service->updated_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}
