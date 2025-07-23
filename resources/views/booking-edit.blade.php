@push('styles')
    <style>
        .booking-edit-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 4rem);
            padding: 2rem;
            background: linear-gradient(180deg, #f1f5f9, #e2e8f0);
            font-family: 'Inter', 'Georgia', serif;
        }

        .booking-edit-container {
            background: linear-gradient(145deg, #ffffff, #f9fafb);
            border-radius: 1.25rem;
            padding: 2.5rem;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15), inset 0 1px 3px rgba(255, 255, 255, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .booking-edit-container:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.2);
        }

        .booking-edit-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #3b82f6, #10b981);
        }

        .booking-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
            letter-spacing: -0.025em;
            text-transform: uppercase;
            text-align: center;
        }

        .booking-details {
            font-size: 1rem;
            color: #475569;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .booking-details p {
            margin: 0.75rem 0;
        }

        .booking-details p strong {
            color: #1e293b;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-buttons button {
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

        .action-buttons button:disabled {
            background: #d1d5db;
            color: #6b7280;
            cursor: not-allowed;
            transform: none;
        }

        @media (max-width: 768px) {
            .booking-edit-wrapper {
                padding: 1.5rem;
            }

            .booking-edit-container {
                padding: 1.5rem;
            }

            .booking-title {
                font-size: 1.5rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.75rem;
            }

            .action-buttons button {
                width: 100%;
                padding: 0.75rem;
            }
        }

        @media (prefers-color-scheme: dark) {
            .booking-edit-wrapper {
                background: linear-gradient(180deg, #1e293b, #334155);
            }

            .booking-edit-container {
                background: linear-gradient(145deg, #2d3748, #4b5563);
                color: #f1f5f9;
            }

            .booking-edit-container::before {
                background: linear-gradient(90deg, #60a5fa, #34d399);
            }

            .booking-title {
                color: #f1f5f9;
            }

            .booking-details {
                color: #d1d5db;
            }

            .booking-details p strong {
                color: #e5e7eb;
            }
        }
    </style>
@endpush

<div class="booking-edit-wrapper">
    <div class="booking-edit-container">
        <h3 class="booking-title">Detalles de la Cita</h3>
        <div class="booking-details">
            <p><strong>Servicio:</strong> {{ $booking['service_name'] }}</p>
            <p><strong>Profesional:</strong> {{ $booking['professional_name'] }}</p>
            <p><strong>Cliente:</strong> {{ $booking['customer_name'] }}</p>
            <p><strong>Fecha y Hora:</strong> {{ $booking['scheduled_at'] ? \Carbon\Carbon::parse($booking['scheduled_at'], 'America/Bogota')->format('d/m/Y H:i') : 'No especificada' }}</p>
            <p><strong>Duración:</strong> {{ $booking['duration'] ?? 'No especificada' }} minutos</p>
            <p><strong>Estado:</strong> {{ $booking['status_spanish'] ?? $booking['status'] }}</p>
            <p><strong>Monto Total:</strong> ${{ $booking['total_amount'] }}</p>
        </div>
        <div class="action-buttons">
            <button id="cancel-service-btn" {{ $booking['canCancel'] ? '' : 'disabled' }}>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancelar
            </button>
            <button id="complete-service-btn" {{ $booking['canComplete'] ? '' : 'disabled' }}>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Completar
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cancelBtn = document.getElementById('cancel-service-btn');
            const completeBtn = document.getElementById('complete-service-btn');
            const bookingId = '{{ $booking['id'] }}';
            const updateStatusUrl = '{{ route("platform.bookings.update-status", $booking["id"]) }}';

            function updateStatus(status) {
                if (confirm(`¿Estás seguro de que quieres ${status === 'cancelled' ? 'cancelar' : 'completar'} esta cita?`)) {
                    console.log(`Sending ${status} request to:`, updateStatusUrl);
                    fetch(updateStatusUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: status })
                    })
                        .then(response => {
                            console.log('Response status:', response.status);
                            if (!response.ok) {
                                throw new Error('HTTP error ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Response data:', data);
                            if (data.success) {
                                alert(`Cita ${status === 'cancelled' ? 'cancelada' : 'completada'} exitosamente`);
                                window.location.href = data.redirect || '{{ route("platform.dashboard") }}';
                            } else {
                                alert('Error: ' + (data.error || `No se pudo ${status === 'cancelled' ? 'cancelar' : 'completar'} la cita`));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert(`Error al ${status === 'cancelled' ? 'cancelar' : 'completar'} la cita: ` + error.message);
                        });
                }
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => updateStatus('cancelled'));
            }

            if (completeBtn) {
                completeBtn.addEventListener('click', () => updateStatus('completed'));
            }
        });
    </script>
@endpush