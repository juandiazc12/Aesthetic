<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\ServiceList;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\TD;

class ServiceListScreen extends Screen
{
    public $name = 'Gestión de Lista de Servicios';
    public $description = 'Crear, editar y eliminar nombres de servicios';

    public function query(): iterable
    {
        return [
            'service_lists' => ServiceList::all(),
        ];
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Crear Servicio')
                ->icon('bs.plus-circle')
                ->modal('createServiceModal')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::modal('createServiceModal', [
                Layout::rows([
                    Input::make('service_list.name')
                        ->title('Nombre del Servicio')
                        ->required()
                        ->placeholder('Ej: Corte de cabello'),
                ]),
            ])->title('Crear Servicio')->applyButton('Guardar'),

            Layout::modal('editServiceModal', [
                Layout::rows([
                    Input::make('service_list.name')
                        ->title('Nombre del Servicio')
                        ->required()
                        ->placeholder('Ej: Corte de cabello'),
                ]),
            ])->title('Editar Servicio')->applyButton('Guardar')->async('asyncGetService'),

            Layout::table('service_lists', [
                TD::make('name', 'Nombre')->sort()->filter(Input::make()),
                TD::make('actions', 'Acciones')
                    ->align(TD::ALIGN_CENTER)
                    ->render(function (ServiceList $serviceList) {
                        return DropDown::make()
                            ->icon('bs.three-dots-vertical')
                            ->list([
                                ModalToggle::make('Editar')
                                    ->icon('bs.pencil')
                                    ->modal('editServiceModal')
                                    ->method('save')
                                    ->asyncParameters(['id' => $serviceList->id]),
                                Button::make('Eliminar')
                                    ->icon('bs.trash3')
                                    ->confirm('¿Estás seguro de que deseas eliminar este servicio?')
                                    ->method('delete', ['id' => $serviceList->id]),
                            ]);
                    }),
            ])->title('Lista de Servicios'),
        ];
    }

    public function asyncGetService(int $id = 0): array
    {
        $serviceList = ServiceList::findOrFail($id);
        return [
            'service_list' => [
                'name' => $serviceList->name,
            ],
        ];
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'service_list.name' => 'required|string|max:255|unique:service_lists,name,' . $request->input('id'),
        ]);

        $serviceList = $request->has('id')
            ? ServiceList::findOrFail($request->input('id'))
            : new ServiceList();

        $serviceList->fill($data['service_list'])->save();

        Toast::info($request->has('id') ? 'Servicio actualizado correctamente.' : 'Servicio creado correctamente.');

        return redirect()->route('platform.services');
    }

    public function delete(Request $request)
    {
        ServiceList::findOrFail($request->input('id'))->delete();

        Toast::info('Servicio eliminado correctamente.');

        return redirect()->route('platform.services');
    }
}