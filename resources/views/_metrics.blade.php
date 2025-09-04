<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title text-muted mb-2">Total de Citas</h5>
                <p class="h2 font-weight-bold mb-0" id="total_bookings_value">{{ $metrics['total_bookings']['value'] ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title text-muted mb-2">Citas de Hoy</h5>
                <p class="h2 font-weight-bold mb-0" id="bookings_today_value">{{ $metrics['bookings_today']['value'] ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title text-muted mb-2">Ingresos Mensuales</h5>
                <p class="h2 font-weight-bold mb-0" id="monthly_revenue_value">${{ $metrics['monthly_revenue']['value'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
