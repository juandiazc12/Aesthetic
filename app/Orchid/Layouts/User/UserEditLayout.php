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
                ->targetRelativeUrl(),

            Input::make('user.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Nombres'))
                ->placeholder('Nombres y apellido'),
        ];
    }
}