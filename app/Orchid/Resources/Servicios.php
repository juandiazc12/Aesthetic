<?php
namespace App\Orchid\Resources;

use App\Models\Service;
use App\Models\ServiceList;
use Illuminate\Support\Facades\Auth;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\TD;

class Servicios extends Resource
{
    public static $model = Service::class;

    public static $name = 'Servicios';
    public static $description = 'Gestiona tus servicios';

    public function fields(): array
    {
        return [
            Picture::make('image')
                ->title('Imagen del servicio')
                ->targetRelativeUrl()
                ->required(),
                
            Select::make('name')
                ->fromModel(ServiceList::class, 'name', 'name')
                ->title('Nombre')
                ->required()
                ->empty('Seleccione un servicio'),

            TextArea::make('description')
                ->title('Descripción')
                ->placeholder('Ingrese la descripción'),

            Input::make('price')
                ->type('number')
                ->title('Precio')
                ->placeholder('Precio del servicio')
                ->required(),

            Input::make('duration')
                ->type('number')
                ->title('Duración')
                ->placeholder('Duración del servicio en minutos'),

            Select::make('status')
                ->title('Modo')
                ->options([
                    'active' => 'Activo',
                    'suspended' => 'Suspendido',
                ])
                ->required(),

            Input::make('user_id')
                ->type('number')
                ->hidden()
                ->value(Auth::id())
        ];
    }

    public function columns(): array
    {
        return [
            TD::make('name', 'Nombre')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function ($service) {
                    return $service->name ?: 'Sin nombre';
                }),

            TD::make('duration', 'Duración')
                ->sort()
                ->render(function ($service) {
                    return number_format($service->duration) . ' min';
                }),

            TD::make('price', 'Precio')
                ->sort()
                ->render(function ($service) {
                    return '$' . number_format($service->price, 0);
                }),

            TD::make('status', 'Modo')
                ->sort()
                ->render(function ($service) {
                    return ucfirst($service->status);
                }),

            TD::make('created_at', 'Creado')
                ->sort()
                ->render(function ($service) {
                    return $service->created_at->format('Y-m-d H:i:s');
                }),
        ];
    }

    public function legend(): array
    {
        return [
            'name' => 'Service Name',
            'price' => 'Price',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public function actions(): array
    {
        return [];
    }

    public static function permission(): ?string
    {
        return 'manage-services';
    }
   public function store(Request $request)
    {
        // Validar los datos de entrada
        $data = $request->validate([
            'name' => 'required|string|exists:service_lists,name',
            'image' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,suspended',
            'user_id' => 'required|exists:users,id',
        ]);

        // Procesar la ruta de la imagen para guardar solo la parte relativa
        if (isset($data['image'])) {
            $data['image'] = str_replace(url(''), '', $data['image']);
            if (strpos($data['image'], '/storage/') !== 0) {
                $data['image'] = '/storage/' . ltrim($data['image'], '/');
            }
        }

        // Crear el servicio
        $service = Service::create($data);

        // Mostrar mensaje de éxito
        Toast::success('Servicio creado correctamente.');

        return redirect()->route('platform.servicios');
    }
}