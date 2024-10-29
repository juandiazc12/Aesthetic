<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\{Request, RedirectResponse};
use App\Models\Customer;

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
