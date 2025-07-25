<?php
namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceList;

use function Laravel\Prompts\error;

class Services extends Controller
{
    // Método para mostrar todos los servicios en la lista
    public function index(): \Inertia\Response
    {
        
        $services = Service::paginate(15)->through(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'image' => $service->image ? \Storage::url($service->image) : null,
                'description' => $service->description,
                'price' => number_format((float)$service->price, 0),
                'duration' => $service->duration,
                'status' => $service->status,
                'created_at' => $service->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $service->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return Inertia::render('Services/ServicesList', [
            'services' => $services,
        ]);
    }

    // Método para mostrar los detalles de un servicio específico
    public function show($id)
    {
        $customer = Auth::guard('customer');
        if (!$customer->check()) {  
            return redirect()->route('customer.login');
        }

        $service = Service::findOrFail($id);
        $serviceList = ServiceList::where('name', $service->name)->first();
        $professionals = collect();

        if ($serviceList) {
            $professionals = $serviceList->servicesList()
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'profesional');
                })
                ->select('users.id', 'users.name', 'users.email', 'users.photo')
                ->get();
        }

        return Inertia::render('Services/Service', [
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'image' => $service->image ,
                'description' => $service->description,
                'price' => number_format((float)$service->price, 0),
                'status' => $service->status,
                'created_at' => $service->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $service->updated_at->format('Y-m-d H:i:s'),
            ],
            'professionals' => $professionals->map(function ($professional) {
                $averageRating = $professional->receivedRatings()->avg('rating');
                $ratingsCount = $professional->receivedRatings()->count();
                return [
                    'id' => $professional->id,
                    'name' => $professional->name,
                    'email' => $professional->email,
                    'photo' => $professional->photo,
                    'average_rating' => $averageRating ? round($averageRating, 1) : null,
                    'ratings_count' => $ratingsCount,
                ];
            }), 
        ]);
    }
}
