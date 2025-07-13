<?php
declare(strict_types=1);

namespace App\Orchid;

use App\Models\Booking;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
    }

    public function menu(): array
    {
        return [
            Menu::make('Dashboard')
                ->icon('bs.collection')
                ->route('platform.dashboard')
                ->badge(function () {
                    $user = auth()->user();
                    $isAdmin = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin']);
                    $professionalId = $isAdmin ? null : ($user ? $user->id : null);

                    $query = Booking::where('status', 'pending');
                    if (!$isAdmin && $professionalId) {
                        $query->where('professional_id', $professionalId);
                    }
                    return $query->count();
                }),

            Menu::make('Cards')
                ->icon('bs.card-text')
                ->route('platform.example.cards')
                ->divider(),

            Menu::make(__('Usuarios'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),

            Menu::make(__('Lista de servicios'))
                ->icon('bs.gear')
                ->route('platform.services')
                ->permission('manage-services'),
        ];
    }

    public function permissions(): array
    {
        return [
            ItemPermission::group(__('Permisos'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Usuarios'))
                ->addPermission('manage-services', __('Servicios')),
        ];
    }
}