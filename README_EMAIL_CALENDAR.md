# Configuración de Notificaciones por Correo con Integración de Calendario

Este documento explica cómo se ha implementado la funcionalidad de envío de correos electrónicos con integración de calendario cuando un cliente agenda una cita en Aesthectic.

## Características Implementadas

- Envío automático de correo electrónico al cliente cuando se confirma una cita
- Inclusión de un archivo ICS (iCalendar) que permite al cliente agregar la cita a su calendario personal
- Información detallada de la cita en el correo electrónico

## Configuración del Entorno

Para que el envío de correos funcione correctamente, es necesario configurar las siguientes variables en el archivo `.env` de la aplicación:

```
MAIL_MAILER=smtp
MAIL_HOST=tu-servidor-smtp.com
MAIL_PORT=587
MAIL_USERNAME=tu-usuario
MAIL_PASSWORD=tu-contraseña
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@aesthectic.com
MAIL_FROM_NAME="Aesthectic"
```

Puedes utilizar servicios como:
- Gmail SMTP
- Amazon SES
- Mailgun
- Postmark
- SendGrid

## Cómo Funciona

1. Cuando un cliente agenda una cita, se crea un registro en la base de datos.
2. Al confirmar la cita (ya sea inmediatamente o posteriormente), se envía una notificación por correo electrónico.
3. La notificación incluye un archivo adjunto en formato ICS que contiene:
   - Fecha y hora de inicio de la cita
   - Duración de la cita
   - Nombre del servicio
   - Nombre del profesional
   - Ubicación (Aesthectic)

## Clases Implementadas

### BookingConfirmation

Se ha creado una clase de notificación `BookingConfirmation` en `app/Notifications/BookingConfirmation.php` que:

- Extiende la clase `Notification` de Laravel
- Implementa la interfaz `ShouldQueue` para procesamiento asíncrono
- Genera el contenido del correo electrónico
- Crea el archivo ICS para la integración con calendarios

### Modificaciones en el Modelo Customer

Se ha añadido el trait `Notifiable` al modelo `Customer` para permitir el envío de notificaciones.

### Modificaciones en BookingController

Se ha modificado el controlador de reservas para enviar la notificación en dos puntos:

1. Cuando se crea una nueva reserva (`confirmStore`)
2. Cuando se confirma una reserva existente (`confirmBooking`)

## Pruebas

Para probar esta funcionalidad:

1. Configura las variables de entorno para el correo electrónico
2. Crea una cuenta de cliente con un correo electrónico válido
3. Agenda una cita
4. Verifica la bandeja de entrada del correo electrónico
5. Abre el archivo ICS adjunto para comprobar que se añade correctamente al calendario

## Solución de Problemas

Si los correos no se envían:

1. Verifica la configuración en el archivo `.env`
2. Revisa los logs de la aplicación en `storage/logs/laravel.log`
3. Asegúrate de que el servicio de correo esté funcionando correctamente
4. Comprueba que no haya restricciones de seguridad en el servidor SMTP

## Personalización

Puedes personalizar el contenido del correo electrónico y del archivo ICS modificando la clase `BookingConfirmation`.