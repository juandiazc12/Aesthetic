<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\{Request, RedirectResponse};
use App\Models\Customer;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('customer')->attempt($credentials)) {
            return redirect()->intended(route('welcome'));
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }


    public function logout(): RedirectResponse
    {
        Auth::guard('customer')->logout();
        return redirect('/');
    }

    /**
     * Send a password reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Reset the given user's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Redirigir con mensaje de éxito
            return redirect('/customer/login')
                ->with('status', 'Tu contraseña ha sido restablecida exitosamente. Por favor inicia sesión con tu nueva contraseña.');
        }

        // Si hay un error, redirigir de vuelta con el error
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }


    public function register(Request $request): RedirectResponse
    {
        //Validacion de datos
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'password' => 'required|string|min:8|confirmed',
        ]);

        //Creacin del cliente si comple con la validacion
        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'status' => true,
            'password' => Hash::make($request->password)
        ]);


        //Crear la sesion del clinete registrado
        Auth::guard('customer')->login($customer);
        return redirect()->route('welcome');
    }


}
