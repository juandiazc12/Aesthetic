@component('mail::message')
# Restablecimiento de Contraseña

¡Hola! Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.

@component('mail::button', ['url' => $url, 'color' => 'primary'])
Restablecer Contraseña
@endcomponent

Este enlace de restablecimiento de contraseña expirará en {{ config('auth.passwords.customers.expire') }} minutos.

Si no solicitaste un restablecimiento de contraseña, puedes ignorar este mensaje.

Saludos,<br>
El equipo de {{ config('app.name') }}

---

Si tienes problemas para hacer clic en el botón, copia y pega la siguiente URL en tu navegador:
{{ $url }}
@endcomponent
