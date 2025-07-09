@push('styles')
<style>
    #calendar {
        max-height: 600px; /* Ajusta esta altura según tu preferencia */
    }
</style>
@endpush

<div id="calendar-wrapper" class="bg-white rounded-lg p-4 mb-4 h-100">
    <div id="calendar"></div>
</div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '{{ route("platform.calendar.events") }}',
        eventClick: function(info) {
            info.jsEvent.preventDefault(); // previene la navegación por defecto del navegador
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        eventDidMount: function(info) {
            // Opcional: Añadir un tooltip con el nombre del servicio
            // Requiere una librería como Tippy.js o Bootstrap's Popover
            // Por ahora, el título del evento será suficiente.
        }
    });
    calendar.render();
});
</script>
@endpush