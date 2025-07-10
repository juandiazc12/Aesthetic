<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserCredentialsLayout;
use App\Orchid\Layouts\User\UserRoleLayout;
use App\Orchid\Layouts\User\UserServiceLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Access\Impersonation;
use App\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserEditScreen extends Screen
{
    public $user;

    public function query(User $user): iterable
    {
        if (!$user->exists) {
            $user->roles()->detach();
            $user->servicesList()->detach();
        }

        return [
            'user' => $user,
            'permission' => $user->getStatusPermission(),
            'user.servicesList' => $user->servicesList->pluck('id')->toArray(),
        ];
    }

    public function name(): ?string
    {
        return $this->user->exists ? 'EDITAR USUARIO' : 'CREAR USUARIO';
    }

    public function description(): ?string
    {
        return 'Perfil y privilegios de usuario, incluido su rol y servicios asociados.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make(__('Eliminar'))
                ->icon('bs.trash3')
                ->confirm(__('Una vez que se elimine la cuenta, todos sus recursos y datos se eliminarán de forma permanente.'))
                ->method('remove')
                ->canSee($this->user->exists),

            Button::make(__('Guardar'))
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::block(UserEditLayout::class)
                ->title(__('Información del Perfil'))
                ->description(__('Actualice la foto y el nombre del usuario.')),

            Layout::block(UserCredentialsLayout::class)
                ->title(__('Credenciales'))
                ->description(__('Configure el correo electrónico y la contraseña del usuario.')),

            Layout::block([
                UserRoleLayout::class,
                UserServiceLayout::class,
            ])
                ->title(__('Rol y Servicios'))
                ->description(__('Asigne un rol y los servicios que el usuario ofrecerá.')),
        ];
    }

    public function save(User $user, Request $request)
    {
        $data = $request->validate([
            'user.email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'user.name' => 'required|string|max:255',
            'user.servicesList' => 'required|array',
            'user.servicesList.*' => 'exists:service_lists,id',
            'user.roles' => 'nullable|array',
            'user.roles.*' => 'exists:roles,id',
        ]);

        $permissions = collect($request->get('permissions'))
            ->map(fn($value, $key) => [base64_decode($key) => $value])
            ->collapse()
            ->toArray();

        $user->when($request->filled('user.password'), function (Builder $builder) use ($request) {
            $builder->getModel()->password = Hash::make($request->input('user.password'));
        });

        $user
            ->fill($request->collect('user')->except(['password', 'permissions', 'roles', 'servicesList'])->toArray())
            ->forceFill(['permissions' => $permissions])
            ->save();

        $user->replaceRoles($request->input('user.roles', []));
        $user->servicesList()->sync($request->input('user.servicesList', []));

        Toast::info(__('El usuario fue guardado.'));

        return redirect()->route('platform.systems.users');
    }

    public function remove(User $user)
    {
        $user->delete();

        Toast::info(__('El usuario fue eliminado'));

        return redirect()->route('platform.systems.users');
    }
}