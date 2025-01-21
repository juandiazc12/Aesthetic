<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Layouts\Rows;

class UserEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Picture::make('user.photo')
            ->title(__('Foto de Perfil'))
            ->targetRelativeUrl() // Guarda solo la URL relativa
            ->help(__('Sube una imagen para el perfil')),
           
            
            Input::make('user.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Nombre'))
                ->placeholder(__('Nombre')),    

            Input::make('user.email')
                ->type('email')
                ->required()
                ->title(__('Correo'))
                ->placeholder(__('Correo')),
        ];
    }
}
