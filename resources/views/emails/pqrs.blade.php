@component('mail::message')
# Nuevo PQRS

**Nombre:** {{ $data['nombre'] ?? '—' }}  
**Email:** {{ $data['email'] ?? '—' }}  
**Tipo:** {{ $data['tipo'] ?? 'No especificado' }}

---

{{ $data['mensaje'] ?? '' }}


Gracias,<br>
{{ config('app.name') }}
@endcomponent
