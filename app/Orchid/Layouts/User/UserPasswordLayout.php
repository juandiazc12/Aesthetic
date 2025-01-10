<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\User;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Layouts\Rows;

class UserPasswordLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        /** @var User $user */
        $user = $this->query->get('user');

        $exists = $user->exists;

        $placeholder = $exists
            ? __('Dejar vacío para mantener la contraseña actual')
            : __('Introduzca la contraseña que desea configurar');

        return [
            Password::make('user.password')
                ->placeholder($placeholder)
                ->title(__('Contraseña'))
                ->required(! $exists),
        ];
    }
}
