<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Layouts\Rows;

class ProfilePasswordLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Password::make('old_password')
                ->placeholder(__('Ingrese la contraseña actual'))
                ->title(__('Contraseña actual'))
                ->help('Esta es su contraseña establecida en este momento.'),

            Password::make('password')
                ->placeholder(__('Introduzca la contraseña que desea configurar'))
                ->title(__('Nueva contraseña')),

            Password::make('password_confirmation')
                ->placeholder(__('Introduzca la contraseña que desea configurar'))
                ->title(__('Confirmar nueva contraseña'))
                ->help('Una buena contraseña tiene al menos 15 caracteres o al menos 8 caracteres de longitud, incluyendo un número y una letra minúscula.'),
        ];
    }
}
