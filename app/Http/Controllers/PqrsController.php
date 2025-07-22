<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PqrsMail;

class PqrsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'type'    => 'required|string|max:50',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'phone'   => 'nullable|string|max:50',
        ]);

        // Normaliza al esquema que usa tu vista Mailable
        $data = [
            'nombre'  => $validated['name'],
            'email'   => $validated['email'],
            'tipo'    => $validated['type'],
            'asunto'  => $validated['subject'],
            'mensaje' => $validated['message'],
            'telefono'=> $validated['phone'] ?? null,
        ];

        // Enviar correo (usa replyTo para poder responder al usuario)
        Mail::to('aesthecticagenda@gmail.com')
            ->send((new PqrsMail($data))->replyTo($data['email'], $data['nombre']));

        return back()->with('status', 'PQRS enviado correctamente.');
    }
}
