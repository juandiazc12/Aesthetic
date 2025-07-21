@component('mail::message')
{{-- Header con logo --}}
<div style="text-align: center; margin-bottom: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 12px; color: white;">
    <img src="{{ asset('js/assets/logo.jpeg') }}" alt="Aesthectic" style="max-width: 120px; height: auto; margin-bottom: 10px;">
    <h1 style="margin: 0; font-size: 24px; color: white;">¡Cita Confirmada!</h1>
</div>

# ¡Hola {{ $notifiable->first_name }}! 👋

Tu cita ha sido **confirmada exitosamente**. ¡Nos emociona verte pronto!

---

## 📅 Detalles de tu Cita

<div style="background: #f8f9ff; padding: 20px; border-radius: 10px; border-left: 4px solid #667eea; margin: 20px 0;">

**🏥 Servicio:**  
{{ $booking->service->name }}

**📆 Fecha:**  
{{ \Carbon\Carbon::parse($booking->scheduled_at)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}

**🕐 Hora:**  
{{ \Carbon\Carbon::parse($booking->scheduled_at)->format('h:i A') }}

**⏱️ Duración:**  
{{ $booking->service->duration }} minutos

**👨‍⚕️ Profesional:**  
{{ $booking->professional->name }}

</div>

@component('mail::button', ['url' => route('booking.confirmation', ['booking' => $booking->id])])
📋 Ver Detalles Completos
@endcomponent

---

## 💡 Recordatorios Importantes

- Te enviaremos un recordatorio **24 horas antes** de tu cita
- Por favor llega **10 minutos antes** de la hora programada
- Para reagendar, contacta con **24 horas de anticipación**

---

<div style="text-align: center; background: #f0fff4; padding: 20px; border-radius: 10px; margin: 25px 0;">

**¡Gracias por confiar en nosotros!** 💙

*Estamos emocionados de cuidarte y hacerte sentir increíble.*

</div>

¡Te esperamos!

**✨ Equipo Aesthectic ✨**  
*"Tu belleza es nuestro arte"*

---

<div style="text-align: center; color: #666; font-size: 12px;">
¿Tienes preguntas? Contáctanos:<br>
📧 info@aesthectic.com | 📱 +57 XXX XXX XXXX
</div>

@endcomponent