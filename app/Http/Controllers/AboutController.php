<?php
namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class AboutController extends Controller
{
    public function index()
    {
        try {
            // Obtener administradores
            $admins = User::whereHas('roles', function ($query) {
                $query->where('slug', 'admin');
            })
            ->select('id', 'name', 'photo')
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'role' => 'Administrador',
                    'image' => $user->photo ? \Storage::url($user->photo) : null,
                    'experience' => 'Liderazgo en gestión clínica',
                ];
            });

            // Obtener profesionales con sus servicios
            $professionals = User::whereHas('roles', function ($query) {
                $query->where('slug', 'profesional');
            })
            ->with(['services' => function ($query) {
                $query->select('services.id', 'services.name');
            }])
            ->select('id', 'name', 'photo')
            ->get()
            ->map(function ($user) {
                $services = $user->services->pluck('name')->toArray();
                $averageRating = $user->receivedRatings()->avg('rating');
$ratingsCount = $user->receivedRatings()->count();
return [
    'name' => $user->name,
    'role' => 'Especialista',
    'image' => $user->photo,
    'experience' => 'Especialista en medicina estética',
    'services' => $services,
    'average_rating' => $averageRating ? round($averageRating, 1) : null,
    'ratings_count' => $ratingsCount,
];
            });

            // Combinar administradores y profesionales
            $team = $admins->merge($professionals)->toArray();

            return Inertia::render('Tools/About', [
                'team' => $team,
                'values' => [
                    [
                        'title' => 'Excelencia',
                        'description' => 'Comprometidos con los más altos estándares de calidad en cada tratamiento.',
                        'icon' => '⭐',
                    ],
                    [
                        'title' => 'Innovación',
                        'description' => 'Utilizamos las últimas tecnologías y técnicas en medicina estética.',
                        'icon' => '🔬',
                    ],
                    [
                        'title' => 'Confianza',
                        'description' => 'Construimos relaciones duraderas basadas en la transparencia y honestidad.',
                        'icon' => '🤝',
                    ],
                    [
                        'title' => 'Personalización',
                        'description' => 'Cada tratamiento se adapta a las necesidades únicas de nuestros pacientes.',
                        'icon' => '👤',
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading About page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar la página Quiénes Somos');
        }
    }
}
