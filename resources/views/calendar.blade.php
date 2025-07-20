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

        .user-role {
            font-size: 0.85rem;
            color: #555;
            font-weight: 500;
        }

        .filter-container select {
            width: 200px;
            padding: 0.5rem;
            border-radius: 0.3rem;
            border: 1px solid #ccc;
            margin-right: 10px;
        }

        .filter-container .btn {
            padding: 0.5rem 1rem;
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
;
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

        .fc-event-confirmed {
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

        .modal {
            z-index: 1050;
        }

        .modal.hidden {
            display: none;
        }

        .modal-content {
            max-width: 600px;
        }

        #event-details {
            font-size: 0.9rem;
            color: #333;
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

            .modal-content {
                width: 90%;
            }

            .filter-container select {
                width: 100%;
                margin-bottom: 10px;
            }

            .filter-container .btn {
                width: 100%;
            }
        }
    </style>
@endpush

<div id="calendar-wrapper">
    <div class="calendar-header">
        <h3 class="calendar-title">Calendario de Citas</h3>
    </div>

    @if($is_admin)
        <div class="filter-container">
            <form method="GET" action="{{ route('platform.dashboard') }}" id="calendar-filter-form">
                <select name="professional_id" id="calendar_professional_id">
                    <option value="">Todos los Profesionales</option>
                    @foreach($professionals as $id => $name)
                        <option value="{{ $id }}" {{ $professional_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="status" id="calendar_status">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Todos los Estados</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmed" {{ $status == 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </form> 
        </div>
    @endif
    <div id="calendar"></div>

    <!-- Modal Banner -->
    <div id="event-modal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="modal-content relative p-5 bg-white w-11/12 md:w-1/2 mx-auto mt-20 rounded shadow-lg">
            <span class="modal-close absolute top-2 right-2 text-gray-600 cursor-pointer text-2xl">×</span>
            <h3 class="text-lg font-semibold mb-4">Detalles del Servicio</h3>
            <div id="event-details" class="mb-4"></div>
            <div class="flex justify-end gap-2">
                <button id="cancel-service-btn" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Cancelar</button>
                <button id="complete-service-btn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Completar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script>
        (function () {
            let calendar;
            let calendarInitialized = false;
            let currentEventId = null;

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
                    events: function (fetchInfo, successCallback, failureCallback) {
                        var url = '{{ route("platform.calendar.events") }}';
                        var urlParams = new URLSearchParams({
                            professional_id: document.getElementById('calendar_professional_id')?.value || '',
                            status: document.getElementById('calendar_status')?.value || 'all'
                        });
                        if (urlParams.toString()) {
                            url += '?' + urlParams.toString();
                        }

                        fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                            .then(response => {
                                if (!response.ok) {
                                    return response.text().then(text => {
                                        throw new Error('HTTP error ' + response.status + ': ' + text.substring(0, 100));
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Datos recibidos:', data); // Depuración
                                if (data.error) {
                                    console.error('Error en la respuesta del servidor:', data.error);
                                    failureCallback(new Error(data.error));
                                } else {
                                    successCallback(data);
                                }
                            })
                            .catch(error => {
                                console.error('Error al cargar eventos:', error.message);
                                failureCallback(error);
                                alert('No se pudieron cargar los eventos: ' + error.message);
                            });
                    },
                    eventClick: function (info) {
                        info.jsEvent.preventDefault();
                        if (!info.jsEvent.target.classList.contains('cancel-btn')) {
                            showEventModal(info.event);
                        }
                    },
                    eventContent: function (info) {
                        var cancelBtn = info.event.extendedProps.canCancel 
                        return {
                            html: '<div>' + info.event.title + cancelBtn + '</div>'
                        };
                    },
                    eventDidMount: function (info) {
                        var status = info.event.extendedProps.status;
                        var professional = info.event.extendedProps.professional;
                        var customer = info.event.extendedProps.customer;

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

                        if (status) {
                            var statusClass = 'fc-event-' + status.toLowerCase();
                            info.el.classList.add(statusClass);
                        }
                    },
                    dateClick: function (info) {
                        document.querySelectorAll('.fc-day-selected').forEach(function (el) {
                            el.classList.remove('fc-day-selected');
                        });

                        info.dayEl.classList.add('fc-day-selected');

                        var urlParams = new URLSearchParams({
                            selected_date: info.dateStr,
                            professional_id: document.getElementById('calendar_professional_id')?.value || '',
                            status: document.getElementById('calendar_status')?.value || 'all'
                        });

                        var newUrl = '{{ route("platform.dashboard") }}?' + urlParams.toString();
                        window.location.href = newUrl;
                    },
                    height: 'auto',
                    aspectRatio: 1.5,
                    dayMaxEvents: 3,
                    moreLinkText: 'más'
                });

                calendarEl.addEventListener('click', function (e) {
                    if (e.target.classList.contains('cancel-btn')) {
                        if (confirm('¿Estás seguro de que quieres cancelar esta cita?')) {
                            updateBookingStatus(e.target.dataset.id, 'cancelled');
                        }
                    }
                });

                calendar.render();

                setTimeout(function () {
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

            function showEventModal(event) {
                currentEventId = event.id;
                var modal = document.getElementById('event-modal');
                var details = document.getElementById('event-details');
                var status = event.extendedProps.status || 'Desconocido';
                var professional = event.extendedProps.professional || 'N/A';
                var customer = event.extendedProps.customer || 'N/A';
                var start = new Date(event.start).toLocaleString('es-ES');

                details.innerHTML = `
                    <p><strong>Título:</strong> ${event.title}</p>
                    <p><strong>Fecha y Hora:</strong> ${start}</p>
                    <p><strong>Profesional:</strong> ${professional}</p>
                    <p><strong>Cliente:</strong> ${customer}</p>
                    <p><strong>Estado:</strong> ${status}</p>
                `;

                var completeBtn = document.getElementById('complete-service-btn');
                completeBtn.style.display = (status.toLowerCase() === 'completed' || status.toLowerCase() === 'cancelled') ? 'none' : 'inline-block';
                modal.classList.remove('hidden');
            }

            function updateBookingStatus(bookingId, status) {
                if (!bookingId) {
                    alert('Error: ID de la cita no válido');
                    return;
                }
                fetch('{{ route("platform.dashboard.update-status", ":booking") }}'.replace(':booking', bookingId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: status })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP error ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert(`Cita ${status === 'cancelled' ? 'cancelada' : 'completada'} exitosamente`);
                            calendar.refetchEvents();
                            closeModal();
                        } else {
                            alert('Error: ' + (data.error || 'No se pudo actualizar la cita'));
                        }
                    })
                    .catch(error => {
                        alert('Error: ' + error.message);
                    });
            }

            function closeModal() {
                var modal = document.getElementById('event-modal');
                modal.classList.add('hidden');
                currentEventId = null;
            }

            document.addEventListener('DOMContentLoaded', function () {
                var modal = document.getElementById('event-modal');
                var closeBtn = document.querySelector('.modal-close');
                var cancelBtn = document.getElementById('cancel-service-btn');
                var completeBtn = document.getElementById('complete-service-btn');
                var professionalSelect = document.getElementById('calendar_professional_id');
                var statusSelect = document.getElementById('calendar_status');

                closeBtn.addEventListener('click', closeModal);
                cancelBtn.addEventListener('click', function () {
                    if (confirm('¿Estás seguro de que quieres cancelar esta cita?')) {
                        updateBookingStatus(currentEventId, 'cancelled');
                    }
                });
                completeBtn.addEventListener('click', function () {
                    if (confirm('¿Estás seguro de que quieres marcar esta cita como completada?')) {
                        updateBookingStatus(currentEventId, 'completed');
                    }
                });

                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        closeModal();
                    }
                });

                // Actualizar el calendario al cambiar los filtros
                if (professionalSelect) {
                    professionalSelect.addEventListener('change', function () {
                        calendar.refetchEvents();
                    });
                }
                if (statusSelect) {
                    statusSelect.addEventListener('change', function () {
                        calendar.refetchEvents();
                    });
                }
            });

            function checkAndInitialize() {
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initializeCalendar);
                } else {
                    initializeCalendar();
                }
            }

            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    setTimeout(initializeCalendar, 100);
                }
            });

            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
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

            checkAndInitialize();

            window.addEventListener('pagehide', function () {
                if (calendar) {
                    calendar.destroy();
                    calendarInitialized = false;
                }
            });

            window.dashboardCalendar = calendar;
        })();
    </script>
@endpush