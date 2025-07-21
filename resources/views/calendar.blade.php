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
            min-height: 20px;
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
    </div>
</div>

@push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script>
        (function () {
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
                    initialDate: '{{ Carbon\Carbon::today("America/Bogota")->format("Y-m-d") }}', // 2025-07-21
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: function (fetchInfo, successCallback, failureCallback) {
                        var url = '{{ route("platform.calendar.events") }}';
                        var currentDate = new Date('{{ Carbon\Carbon::today("America/Bogota")->format("Y-m-d") }}'); // Forzar fecha actual
                        var urlParams = new URLSearchParams({
                            professional_id: document.getElementById('calendar_professional_id')?.value || '',
                            status: document.getElementById('calendar_status')?.value || 'all',
                            month: currentDate.getMonth() + 1, // Usar mes actual (7)
                            year: currentDate.getFullYear(), // Usar año actual (2025)
                            nocache: new Date().getTime()
                        });

                        console.log('Fecha forzada:', currentDate);
                        console.log('Mes calculado:', currentDate.getMonth() + 1);
                        console.log('Año calculado:', currentDate.getFullYear());
                        console.log('Solicitando eventos para:', url + '?' + urlParams.toString());

                        fetch(url + '?' + urlParams.toString(), {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Cache-Control': 'no-cache'
                            }
                        })
                            .then(response => {
                                console.log('Respuesta recibida:', response.status, response.url);
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
                        console.log('Renderizando evento:', info.event.title, info.event.start);
                        return {
                            html: `<div class="fc-event-title">${info.event.title || 'Sin título'}</div>`
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
                    eventClick: function (info) {
                        window.location.href = info.event.url;
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
                calendarInitialized = true;
            }

            document.addEventListener('DOMContentLoaded', function () {
                var professionalSelect = document.getElementById('calendar_professional_id');
                var statusSelect = document.getElementById('calendar_status');

                function updateCalendar() {
                    if (calendar) {
                        var urlParams = new URLSearchParams(window.location.search);
                        var selectedDate = urlParams.get('selected_date');
                        if (selectedDate) {
                            calendar.gotoDate(selectedDate);
                        } else {
                            calendar.gotoDate('{{ Carbon\Carbon::today("America/Bogota")->format("Y-m-d") }}'); // 2025-07-21
                        }
                        calendar.refetchEvents();
                    }
                }

                if (professionalSelect) {
                    professionalSelect.addEventListener('change', function () {
                        updateCalendar();
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
                        updateCalendar();
                        var urlParams = new URLSearchParams({
                            selected_date: new URLSearchParams(window.location.search).get('selected_date') || '{{ Carbon\Carbon::today("America/Bogota")->format("Y-m-d") }}',
                            professional_id: document.getElementById('calendar_professional_id')?.value || '',
                            status: statusSelect.value || 'all'
                        });
                        window.location.href = '{{ route("platform.dashboard") }}?' + urlParams.toString();
                    });
                }

                updateCalendar();
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