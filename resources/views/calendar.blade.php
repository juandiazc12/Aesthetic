@push('styles')
<style>
    #calendar-wrapper {
        background-color: #fff;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        max-width: 100%;
        overflow-x: auto;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding: 0.75rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
    }
    
    .calendar-title {
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    
    .filter-info {
        font-size: 0.85rem;
        color: #666;
        font-style: italic;
    }
    
    #calendar {
        max-height: 450px;
        font-size: 0.9rem;
    }
    
    .fc {
        font-size: 0.9rem;
        font-family: 'Arial', sans-serif;
    }
    
    .fc .fc-toolbar {
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    
    .fc .fc-button {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
        border-radius: 0.3rem;
        background-color: #1976d2;
        border-color: #1976d2;
        color: #fff;
        transition: background-color 0.2s;
    }
    
    .fc .fc-button:hover {
        background-color: #1565c0;
    }
    
    .fc .fc-daygrid-day {
        padding: 4px;
        transition: all 0.2s ease;
    }
    
    .fc .fc-daygrid-day:hover {
        background-color: #f5f5f5;
    }
    
    .fc .fc-daygrid-event {
        font-size: 0.8rem;
        padding: 4px 6px;
        border-radius: 4px;
        margin: 2px 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
    }
    
    .fc-event-pending {
        background-color: #ff9800;
        border-color: #f57c00;
        color: #fff;
    }
    
    .fc-event-active {
        background-color: #2196f3;
        border-color: #1976d2;
        color: #fff;
    }
    
    .fc-event-completed {
        background-color: #4caf50;
        border-color: #388e3c;
        color: #fff;
    }
    
    .fc-event-cancelled {
        background-color: #f44336;
        border-color: #d32f2f;
        color: #fff;
    }
    
    .fc .fc-event-title {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex-grow: 1;
        font-weight: 500;
    }
    
    .cancel-btn {
        margin-left: 8px;
        color: #fff;
        font-size: 0.9rem;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .cancel-btn:hover {
        color: #ff1744;
    }
    
    .fc-day-selected {
        background-color: #bbdefb !important;
        border: 3px solid #1976d2 !important;
        border-radius: 4px;
    }
    
    @media (max-width: 768px) {
        #calendar {
            max-height: 350px;
            font-size: 0.8rem;
        }
        
        .fc .fc-button {
            padding: 0.3rem 0.6rem;
            font-size: 0.75rem;
        }
        
        .fc .fc-daygrid-event {
            font-size: 0.7rem;
            padding: 3px 5px;
        }
        
        .calendar-header {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
@endpush

<div id="calendar-wrapper">
    <div class="calendar-header">
        <h3 class="calendar-title">Calendario de Citas</h3>
        <div class="filter-info">
            Mostrando: {{ $selected_professional_name ?? 'Todas las citas' }}
        </div>
    </div>
    <div id="calendar"></div>
</div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
(function() {
    let calendar;
    let calendarInitialized = false;

    function initializeCalendar() {
        if (calendarInitialized && calendar) {
            calendar.destroy();
        }

        var calendarEl = document.getElementById('calendar');
        if (!calendarEl) {
            console.error('Calendar element not found');
            return;
        }

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                var url = '{{ route("platform.calendar.events") }}';
                var urlParams = new URLSearchParams(window.location.search);
                if (urlParams.toString()) {
                    url += '?' + urlParams.toString();
                }
                
                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                if (info.event.url && !info.jsEvent.target.classList.contains('cancel-btn')) {
                    window.location.href = info.event.url;
                }
            },
            eventContent: function(info) {
                var cancelBtn = info.event.extendedProps.canCancel ? 
                    '<span class="cancel-btn" data-id="' + info.event.id + '">✖</span>' : '';
                return {
                    html: '<div>' + info.event.title + cancelBtn + '</div>'
                };
            },
            eventDidMount: function(info) {
                var status = info.event.extendedProps.status;
                var professional = info.event.extendedProps.professional;
                var customer = info.event.extendedProps.customer;
                
                // Crear tooltip con información adicional
                var tooltipText = info.event.title;
                if (professional) {
                    tooltipText += '\nProfesional: ' + professional;
                }
                if (customer) {
                    tooltipText += '\nCliente: ' + customer;
                }
                if (status) {
                    tooltipText += '\nEstado: ' + status;
                }
                
                info.el.title = tooltipText;
                
                // Add class based on event status
                if (status) {
                    var statusClass = 'fc-event-' + status.toLowerCase();
                    info.el.classList.add(statusClass);
                }
            },
            dateClick: function(info) {
                document.querySelectorAll('.fc-day-selected').forEach(function(el) {
                    el.classList.remove('fc-day-selected');
                });
                
                info.dayEl.classList.add('fc-day-selected');
                
                var urlParams = new URLSearchParams(window.location.search);
                urlParams.set('selected_date', info.dateStr);
                
                var newUrl = window.location.pathname + '?' + urlParams.toString();
                window.location.href = newUrl;
            },
            height: 'auto',
            aspectRatio: 1.5,
            dayMaxEvents: 3,
            moreLinkText: 'más'
        });

        // Event handler for cancel buttons
        calendarEl.addEventListener('click', function(e) {
            if (e.target.classList.contains('cancel-btn')) {
                if (confirm('¿Estás seguro de que quieres cancelar esta cita?')) {
                    fetch('{{ route("platform.dashboard.cancel", ":id") }}'.replace(':id', e.target.dataset.id), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP error ' + response.status);
                        }
                        return response.json();
                    })
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

        // Highlight the selected date on page load
        setTimeout(function() {
            var urlParams = new URLSearchParams(window.location.search);
            var selectedDate = urlParams.get('selected_date');
            if (selectedDate) {
                calendar.gotoDate(selectedDate);
                var dayEl = calendarEl.querySelector('[data-date="' + selectedDate + '"]');
                if (dayEl) {
                    dayEl.classList.add('fc-day-selected');
                }
            }
        }, 100);

        calendarInitialized = true;
    }

    // Función para manejar el cambio de filtro
    function handleFilterChange() {
        if (calendar) {
            calendar.refetchEvents();
        }
    }

    // Escuchar cambios en los filtros
    document.addEventListener('DOMContentLoaded', function() {
        var filterSelect = document.querySelector('select[name="professional_id"]');
        if (filterSelect) {
            filterSelect.addEventListener('change', handleFilterChange);
        }
    });

    // Inicializar cuando el DOM esté listo
    function checkAndInitialize() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeCalendar);
        } else {
            initializeCalendar();
        }
    }

    // Reinicializar cuando la página se muestre (incluyendo cuando se navega hacia atrás)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            setTimeout(initializeCalendar, 100);
        }
    });

    // Reinicializar cuando se detecte que estamos en la página del dashboard
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                var calendarEl = document.getElementById('calendar');
                if (calendarEl && !calendarInitialized) {
                    initializeCalendar();
                }
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Inicializar inmediatamente
    checkAndInitialize();

    // Limpiar cuando la página se oculte
    window.addEventListener('pagehide', function() {
        if (calendar) {
            calendar.destroy();
            calendarInitialized = false;
        }
    });

    // Hacer el calendar accesible globalmente para debugging
    window.dashboardCalendar = calendar;
})();
</script>
@endpush