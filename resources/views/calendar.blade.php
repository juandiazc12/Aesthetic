@push('styles')
    <style>
        .calendar-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 4rem);
            padding: 2rem;
            background: linear-gradient(180deg, #f1f5f9, #e2e8f0);
            font-family: 'Inter', 'Georgia', serif;
        }

        #calendar-container {
            background: linear-gradient(145deg, #ffffff, #f9fafb);
            border-radius: 1.25rem;
            padding: 2.5rem;
            max-width: 960px;
            width: 100%;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15), inset 0 1px 3px rgba(255, 255, 255, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        #calendar-container:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.2);
        }

        #calendar-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #3b82f6, #10b981);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .calendar-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            letter-spacing: -0.025em;
            text-transform: uppercase;
        }

        .user-role {
            font-size: 1rem;
            color: #475569;
            font-weight: 500;
        }

        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-container select {
            padding: 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            background: #fff;
            font-size: 0.95rem;
            color: #1e293b;
            min-width: 200px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .filter-container select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
            outline: none;
        }

        #calendar {
            font-size: 0.9rem;
            color: #475569;
        }

        .fc {
            font-family: 'Inter', 'Georgia', serif;
        }

        .fc .fc-toolbar {
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .fc .fc-button {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            background: linear-gradient(90deg, #4ade80, #22c55e);
            border: none;
            color: #fff;
            transition: all 0.3s ease;
        }

        .fc .fc-button:hover {
            background: linear-gradient(90deg, #22c55e, #16a34a);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .fc .fc-button.fc-button-primary {
            background: linear-gradient(90deg, #3b82f6, #2563eb);
        }

        .fc .fc-button.fc-button-primary:hover {
            background: linear-gradient(90deg, #2563eb, #1e40af);
        }

        .fc .fc-daygrid-day {
            padding: 6px;
            transition: all 0.2s ease;
        }

        .fc .fc-daygrid-day:hover {
            background-color: #f1f5f9;
        }

        .fc .fc-daygrid-event {
            font-size: 0.85rem;
            padding: 5px 8px;
            border-radius: 6px;
            margin: 3px 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }

        .fc-event-pending {
            background: linear-gradient(90deg, #f59e0b, #d97706);
            color: #fff;
        }

        .fc-event-confirmed {
            background: linear-gradient(90deg, #3b82f6, #2563eb);
            color: #fff;
        }

        .fc-event-completed {
            background: linear-gradient(90deg, #4ade80, #22c55e);
            color: #fff;
        }

        .fc-event-cancelled {
            background: linear-gradient(90deg, #f87171, #dc2626);
            color: #fff;
        }

        .fc .fc-event-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex-grow: 1;
            font-weight: 500;
        }

        .fc-day-selected {
            background: #e0f2fe !important;
            border: 3px solid #3b82f6 !important;
            border-radius: 6px;
        }

        .modal {
            z-index: 1050;
            background: rgba(0, 0, 0, 0.6);
        }

        .modal.hidden {
            display: none;
        }

        .modal-content {
            max-width: 600px;
            background: linear-gradient(145deg, #ffffff, #f9fafb);
            border-radius: 1rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            position: relative;
        }

        .modal-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #10b981);
        }

        .modal-close {
            font-size: 1.5rem;
            color: #475569;
            transition: color 0.3s ease;
        }

        .modal-close:hover {
            color: #1e293b;
        }

        #event-details {
            font-size: 1rem;
            color: #475569;
            line-height: 1.8;
        }

        #event-details p {
            margin: 0.75rem 0;
        }

        #event-details p strong {
            color: #1e293b;
            font-weight: 600;
        }

        .modal .flex button {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        #cancel-service-btn {
            background: linear-gradient(90deg, #f87171, #dc2626);
            color: #fff;
        }

        #cancel-service-btn:hover {
            background: linear-gradient(90deg, #ef4444, #b91c1c);
            transform: translateY(-2px);
        }

        #complete-service-btn {
            background: linear-gradient(90deg, #4ade80, #22c55e);
            color: #fff;
        }

        #complete-service-btn:hover {
            background: linear-gradient(90deg, #22c55e, #16a34a);
            transform: translateY(-2px);
        }

        .modal .flex button:disabled {
            background: #d1d5db;
            color: #6b7280;
            cursor: not-allowed;
            transform: none;
        }

        .loading {
            opacity: 0.5;
            transition: opacity 0.3s;
        }

        @media (max-width: 768px) {
            .calendar-wrapper {
                padding: 1.5rem;
            }

            #calendar-container {
                padding: 1.5rem;
            }

            .calendar-header {
                flex-direction: column;
                gap: 0.75rem;
                align-items: flex-start;
            }

            .calendar-title {
                font-size: 1.5rem;
            }

            .filter-container {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-container select {
                width: 100%;
                margin-bottom: 0.75rem;
            }

            .fc .fc-button {
                padding: 0.4rem 0.8rem;
                font-size: 0.85rem;
            }

            .fc .fc-daygrid-event {
                font-size: 0.75rem;
                padding: 4px 6px;
            }

            .modal-content {
                width: 90%;
                padding: 1.5rem;
            }
        }

        @media (prefers-color-scheme: dark) {
            .calendar-wrapper {
                background: linear-gradient(180deg, #1e293b, #334155);
            }

            #calendar-container {
                background: linear-gradient(145deg, #2d3748, #4b5563);
                color: #f1f5f9;
            }

            #calendar-container::before {
                background: linear-gradient(90deg, #60a5fa, #34d399);
            }

            .calendar-header {
                border-bottom: 2px solid #4b5563;
            }

            .calendar-title {
                color: #f1f5f9;
            }

            .user-role {
                color: #d1d5db;
            }

            .filter-container select {
                background: #374151;
                color: #f1f5f9;
                border-color: #4b5563;
            }

            .filter-container select:focus {
                border-color: #60a5fa;
                box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.3);
            }

            .fc .fc-daygrid-day:hover {
                background-color: #4b5563;
            }

            .fc-day-selected {
                background: #1e40af !important;
                border-color: #60a5fa !important;
            }

            .modal-content {
                background: linear-gradient(145deg, #2d3748, #4b5563);
                color: #f1f5f9;
            }

            .modal-content::before {
                background: linear-gradient(90deg, #60a5fa, #34d399);
            }

            #event-details {
                color: #d1d5db;
            }

            #event-details p strong {
                color: #e5e7eb;
            }

            .modal-close {
                color: #d1d5db;
            }

            .modal-close:hover {
                color: #f1f5f9;
            }
        }
    </style>
@endpush

<div class="calendar-wrapper">
    <div id="calendar-container">
        <div class="calendar-header">
            <h3 class="calendar-title">Calendario de Citas</h3>
            <span class="user-role">Rol: {{ $role_name }}</span>
        </div>

        <div class="filter-container">
            @if($is_admin)
                <select name="professional_id" id="calendar_professional_id">
                    <option value="">Todos los Profesionales</option>
                    @foreach($professionals as $id => $name)
                        <option value="{{ $id }}" {{ $professional_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            @endif
            <select name="status" id="calendar_status">
                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Todos los Estados</option>
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                <option value="confirmed" {{ $status == 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completada</option>
                <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
            </select>
        </div>
        <div id="calendar"></div>

        <!-- Modal Banner -->
        <div id="event-modal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
            <div class="modal-content relative mx-auto mt-20 rounded">
                <span class="modal-close absolute top-2 right-2 cursor-pointer text-2xl">×</span>
                <h3 class="text-lg font-semibold mb-4">Detalles del Servicio</h3>
                <div id="event-details" class="mb-4"></div>
                <div class="flex justify-end gap-2">
                    <button id="cancel-service-btn" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancelar
                    </button>
                    <button id="complete-service-btn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Completar
                    </button>
                </div>
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
                                console.log('Datos recibidos:', data);
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
                        console.log('Fecha seleccionada:', info.dateStr);
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
                        window.history.pushState({}, '', newUrl);
                        window.location.href = newUrl;
                    },
                    height: 'auto',
                    aspectRatio: 1.5,
                    dayMaxEvents: 3,
                    moreLinkText: 'más'
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
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        closeModal();
                    }
                });

                if (cancelBtn) {
                    cancelBtn.addEventListener('click', function () {
                        if (confirm('¿Estás seguro de que quieres cancelar esta cita?')) {
                            fetch('{{ route("platform.bookings.update-status", ":id") }}'.replace(':id', currentEventId), {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ status: 'cancelled' })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        alert('Cita cancelada exitosamente');
                                        closeModal();
                                        calendar.refetchEvents();
                                    } else {
                                        alert('Error: ' + (data.error || 'No se pudo cancelar la cita'));
                                    }
                                })
                                .catch(error => alert('Error: ' + error.message));
                        }
                    });
                }

                if (completeBtn) {
                    completeBtn.addEventListener('click', function () {
                        if (confirm('¿Estás seguro de que quieres marcar esta cita como completada?')) {
                            fetch('{{ route("platform.bookings.update-status", ":id") }}'.replace(':id', currentEventId), {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ status: 'completed' })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        alert('Cita completada exitosamente');
                                        closeModal();
                                        calendar.refetchEvents();
                                    } else {
                                        alert('Error: ' + (data.error || 'No se pudo completar la cita'));
                                    }
                                })
                                .catch(error => alert('Error: ' + error.message));
                        }
                    });
                }

                if (professionalSelect) {
                    professionalSelect.addEventListener('change', function () {
                        var urlParams = new URLSearchParams({
                            selected_date: new URLSearchParams(window.location.search).get('selected_date') || '{{ Carbon\Carbon::today("America/Bogota")->format("Y-m-d") }}',
                            professional_id: professionalSelect.value || '',
                            status: document.getElementById('calendar_status')?.value || 'all'
                        });
                        window.location.href = '{{ route("platform.dashboard") }}?' + urlParams.toString();
                    });
                }

                if (statusSelect) {
                    statusSelect.addEventListener('change', function () {
                        var urlParams = new URLSearchParams({
                            selected_date: new URLSearchParams(window.location.search).get('selected_date') || '{{ Carbon\Carbon::today("America/Bogota")->format("Y-m-d") }}',
                            professional_id: document.getElementById('calendar_professional_id')?.value || '',
                            status: statusSelect.value || 'all'
                        });
                        window.location.href = '{{ route("platform.dashboard") }}?' + urlParams.toString();
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