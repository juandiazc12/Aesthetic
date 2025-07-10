<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Platform\Models\Role;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class UserRoleLayout extends Rows
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
            ? __('')
            : __('Especifique a qué grupos debe pertenecer esta cuenta');

        return [
            Select::make('user.roles.')
                ->fromModel(Role::class, 'name')
                ->title('Rol')
                ->allowEmpty(true) // Esto permite que inicie vacío (sin preselección)
                ->required(),      // Esto obliga a seleccionar uno antes de guardar
        ];
    }
}
