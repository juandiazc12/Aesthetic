<?php
namespace App\Orchid\Resources;

use App\Models\Service;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\TD;

class PostService extends Resource
{
    public static $model = Service::class;

    public static $name = 'Services';
    public static $description = 'Manage your services';

    public function fields(): array
    {
        return [
            Input::make('name')
                ->title('Nombre')
                ->placeholder('Ingrese el nombre del servicio')
                ->required(),

            TextArea::make('description')
                ->title('Descripción')
                ->placeholder('ingrese la descripción'),

            Picture::make('image')
                ->title('Imagen del servicio')
                ->required(),

            Input::make('price')
                ->type('number')
                ->title('Precio')
                ->placeholder('Precio del servicio')
                ->required(),

            Input::make('duration')
                ->type('text')
                ->title('Duracion')
                ->placeholder('Duración del servicio'),

            Select::make('status')
                ->title('Modo')
                ->options([
                    'active' => 'Activo',
                    'suspended' => 'Suspendido',
                ])
                ->required(),

            Relation::make('user_id')
                ->title('Usuario')
                ->fromModel(\App\Models\User::class, 'name'),
        ];
    }

    public function columns(): array
    {
        return [
            TD::make('name', 'Name')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function ($service) {
                    return $service->name;
                }),

            TD::make('price', 'Price')
                ->sort()
                ->render(function ($service) {
                    return '$' . number_format($service->price, 2);
                }),

            TD::make('status', 'Status')
                ->sort()
                ->render(function ($service) {
                    return ucfirst($service->status);
                }),

            TD::make('created_at', 'Created At')
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
}
