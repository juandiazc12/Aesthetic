<div class="card border-0 mt-3 shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="card-title mb-0">Ranking de Servicios</h5>
    </div>
    <ul class="list-group list-group-flush">
        @forelse ($service_ranking as $item)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $item['service_name'] }}
                <span class="badge bg-primary rounded-pill">{{ $item['count'] }}</span>
            </li>
        @empty
            <li class="list-group-item text-center text-muted">No hay datos para este mes.</li>
        @endforelse
    </ul>
</div>
