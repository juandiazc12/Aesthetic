@push('styles')
<style>
    #calendar-wrapper {
        background-color: #fff;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        max-width: 100%;
        overflow-x: auto;
    }
    #calendar {
        max-height: 400px;
        font-size: 0.85rem;
    }
    .fc {
        font-size: 0.85rem;
    }
    .fc .fc-toolbar {
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }
    .fc .fc-button {
        padding: 0.3rem 0.5rem;
        font-size: 0.8rem;
    }
    .fc .fc-daygrid-day {
        padding: 2px;
    }
    .fc .fc-daygrid-event {
        font-size: 0.75rem;
        padding: 2px 4px;
    }
    .cancel-btn {
        margin-left: 5px;
        color: red;
        cursor: pointer;
    }
    @media (max-width: 768px) {
        #calendar {
            max-height: 300px;
            font-size: 0.75rem;
        }
        .fc .fc-button {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }
    }
</style>
@endpush

<div id="calendar-wrapper">
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
        events: '{{ route("platform.calendar.events") }}' + (new URLSearchParams(window.location.search).toString() ? '?' + new URLSearchParams(window.location.search).toString() : ''),
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url && !info.jsEvent.target.classList.contains('cancel-btn')) {
                window.location.href = info.event.url;
            }
        },
        eventContent: function(info) {
            var cancelBtn = info.event.extendedProps.canCancel ? '<span class="cancel-btn" data-id="' + info.event.id + '">✖</span>' : '';
            return {
                html: '<div>' + info.event.title + cancelBtn + '</div>'
            };
        },
        eventDidMount: function(info) {
            info.el.title = info.event.title;
        },
        height: 'auto',
        aspectRatio: 1.5,
        dayMaxEvents: 3,
        moreLinkText: 'más',
        eventsFetch: function(fetchInfo, successCallback, failureCallback) {
            fetch('{{ route("platform.calendar.events") }}' + (new URLSearchParams(window.location.search).toString() ? '?' + new URLSearchParams(window.location.search).toString() : ''), {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error('Error en la respuesta del servidor:', data.error);
                    failureCallback(new Error(data.error));
                } else {
                    successCallback(data);
                }
            })
            .catch(error => {
                console.error('Error al cargar eventos:', error);
                failureCallback(error);
            });
        }
    });

    calendarEl.addEventListener('click', function(e) {
        if (e.target.classList.contains('cancel-btn')) {
            if (confirm('¿Estás seguro de que quieres cancelar esta cita?')) {
                fetch('{{ route("platform.example.cancel", ":id") }}'.replace(':id', e.target.dataset.id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cita cancelada exitosamente');
                        calendar.refetchEvents();
                    } else {
                        alert('Error: ' + (data.error || 'No se pudo cancelar la cita'));
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        }
    });

    calendar.render();
});
</script>
@endpush