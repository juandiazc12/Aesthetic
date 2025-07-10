<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\ServiceList;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class UserServiceLayout extends Rows
{
    public function fields(): array
    {

        return [
            Select::make('user.servicesList.')
                ->fromModel(ServiceList::class, 'name')
                ->multiple()
                ->title('Servicios')
                ->required(),
        ];
    }
}