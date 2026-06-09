@extends('layouts.app')

@section('title', 'Track Your Ride - Mzuni UNITRAS')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 500px; border-radius: 16px; margin-top: 1rem; }
    .info-card { background: white; border-radius: 16px; padding: 1rem; margin-bottom: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .driver-marker { background: none; border: none; }
    .driver-marker i { font-size: 2rem; color: #00529b; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    .status-badge { font-size: 0.85rem; padding: 0.25rem 0.75rem; border-radius: 2rem; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Live Tracking – Ride #{{ $booking->id }}</h5>
                </div>
                <div class="card-body p-0">
                    <div id="map"></div>
                    <div class="p-3 bg-light text-center border-top">
                        <span id="lastUpdate" class="text-muted small">
                            <i class="fas fa-clock me-1"></i> Waiting for location updates...
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <h6><i class="fas fa-info-circle text-primary me-2"></i>Trip Details</h6>
                <hr class="my-2">
                <p class="mb-1"><strong>From:</strong> {{ $booking->pickup_point }}</p>
                <p class="mb-1"><strong>To:</strong> {{ $booking->dropoff_point }}</p>
                <p class="mb-1"><strong>Date:</strong> {{ $booking->trip_date ? \Carbon\Carbon::parse($booking->trip_date)->format('d M Y, H:i') : 'Not set' }}</p>
                <p class="mb-0">
                    <strong>Status:</strong>
                    <span class="badge status-badge bg-{{ $booking->status === 'confirmed' ? 'warning' : ($booking->status === 'in_progress' ? 'info' : 'success') }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </p>
            </div>

            <div class="info-card">
                <h6><i class="fas fa-car-side text-primary me-2"></i>Driver / Vehicle</h6>
                <hr class="my-2">
                <p class="mb-1"><strong>Vehicle:</strong> {{ $vehicle->brand ?? '' }} {{ $vehicle->model ?? '' }} ({{ $vehicle->plate_number ?? 'N/A' }})</p>
                <p class="mb-0" id="driverStatus">
                    <i class="fas fa-spinner fa-pulse me-1"></i> Waiting for driver location...
                </p>
            </div>

            <div class="info-card">
                <h6><i class="fas fa-chart-line text-primary me-2"></i>Journey Snapshot</h6>
                <hr class="my-2">
                <p class="mb-1"><strong>Estimated distance:</strong> {{ $booking->estimated_distance ?? '—' }} km</p>
                <p class="mb-0"><strong>Estimated duration:</strong> {{ $booking->estimated_duration ?? '—' }} min</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    import Echo from 'laravel-echo';
    window.Pusher = Pusher;

    const echo = new Echo({
        broadcaster: 'reverb',
        key: '{{ env("REVERB_APP_KEY") }}',
        wsHost: '{{ env("REVERB_HOST") }}',
        wsPort: {{ env("REVERB_PORT") }},
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
    });

    // Initialize map (center on Mzuzu)
    const map = L.map('map').setView([-11.45, 34.02], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> & CartoDB'
    }).addTo(map);

    let driverMarker = null;
    let routeLine = null;
    let historyPoints = [];

    // Subscribe to the vehicle's channel
    const vehicleId = {{ $vehicle->id }};
    echo.channel(`tracking.vehicle.${vehicleId}`)
        .listen('location.updated', (e) => {
            const { latitude, longitude, speed, heading } = e.location;
            const coords = [latitude, longitude];

            if (!driverMarker) {
                // Custom marker with car icon
                const carIcon = L.divIcon({
                    className: 'driver-marker',
                    html: '<i class="fas fa-car-side fa-2x text-primary"></i>',
                    iconSize: [30, 30],
                    popupAnchor: [0, -15]
                });
                driverMarker = L.marker(coords, { icon: carIcon }).addTo(map)
                    .bindTooltip('Your driver', { permanent: false, direction: 'top' });
            } else {
                driverMarker.setLatLng(coords);
            }

            // Update map view to follow driver
            map.setView(coords);

            // Draw route history (polyline)
            historyPoints.push(coords);
            if (routeLine) map.removeLayer(routeLine);
            routeLine = L.polyline(historyPoints, { color: '#00529b', weight: 4, opacity: 0.6 }).addTo(map);

            // Update status texts
            const speedText = speed ? `${speed} km/h` : '—';
            document.getElementById('driverStatus').innerHTML = `
                <i class="fas fa-location-dot text-primary me-1"></i>
                Last update: ${new Date().toLocaleTimeString()}<br>
                Speed: ${speedText}
            `;
            document.getElementById('lastUpdate').innerHTML = `
                <i class="fas fa-clock me-1"></i> Last update: ${new Date().toLocaleTimeString()}
            `;
        });

    // Fetch initial location (if any)
    fetch('{{ route("api.vehicle.location", $vehicle->id) }}')
        .then(res => res.json())
        .then(data => {
            if (data.location) {
                const { latitude, longitude } = data.location;
                const coords = [latitude, longitude];
                const carIcon = L.divIcon({
                    className: 'driver-marker',
                    html: '<i class="fas fa-car-side fa-2x text-primary"></i>',
                    iconSize: [30, 30]
                });
                driverMarker = L.marker(coords, { icon: carIcon }).addTo(map);
                map.setView(coords);
                historyPoints.push(coords);
            }
        })
        .catch(err => console.warn('Initial location not available', err));
</script>
@endpush