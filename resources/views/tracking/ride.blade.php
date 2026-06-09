@extends('layouts.app')

@section('title', 'Track Your Ride')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 500px; border-radius: 16px; }
    .info-card { background: white; border-radius: 16px; padding: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .driver-marker i { font-size: 2rem; color: #00529b; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Live Tracking – Ride #{{ $booking->id }}</h5>
                </div>
                <div class="card-body p-0">
                    <div id="map"></div>
                    <div class="p-3 bg-light text-center border-top">
                        <span id="lastUpdate" class="text-muted small">
                            <i class="fas fa-clock me-1"></i> Waiting for driver location...
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <h6><i class="fas fa-info-circle text-primary me-2"></i>Trip Details</h6>
                <hr>
                <p><strong>From:</strong> {{ $booking->pickup_point }}</p>
                <p><strong>To:</strong> {{ $booking->dropoff_point }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $booking->status === 'confirmed' ? 'warning' : 'success' }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </p>
            </div>
            <div class="info-card">
                <h6><i class="fas fa-car-side text-primary me-2"></i>Driver Location</h6>
                <p id="driverStatus"><i class="fas fa-spinner fa-pulse me-1"></i> Waiting for signal...</p>
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
    });
    
    const map = L.map('map').setView([-11.45, 34.02], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OSM & CartoDB'
    }).addTo(map);
    
    let driverMarker = null;
    let historyPoints = [];
    let routeLine = null;
    
    // Subscribe to vehicle channel
    const vehicleId = {{ $vehicle->id }};
    
    echo.channel(`tracking.vehicle.${vehicleId}`)
        .listen('location.updated', (e) => {
            const { latitude, longitude, speed } = e;
            const coords = [latitude, longitude];
            
            if (!driverMarker) {
                const carIcon = L.divIcon({
                    className: 'driver-marker',
                    html: '<i class="fas fa-car-side fa-2x text-primary"></i>',
                    iconSize: [30, 30]
                });
                driverMarker = L.marker(coords, { icon: carIcon }).addTo(map);
            } else {
                driverMarker.setLatLng(coords);
            }
            
            map.setView(coords);
            historyPoints.push(coords);
            if (routeLine) map.removeLayer(routeLine);
            routeLine = L.polyline(historyPoints, { color: '#00529b', weight: 4 }).addTo(map);
            
            const speedText = speed ? `${speed} km/h` : '—';
            document.getElementById('driverStatus').innerHTML = `
                <i class="fas fa-location-dot text-primary me-1"></i>
                Last update: ${new Date().toLocaleTimeString()}<br>
                Speed: ${speedText}
            `;
            document.getElementById('lastUpdate').innerHTML = `Last update: ${new Date().toLocaleTimeString()}`;
        });
    
    // Fetch initial location
    fetch('/api/vehicles/{{ $vehicle->id }}/location')
        .then(res => res.json())
        .then(data => {
            if (data.location) {
                const { latitude, longitude } = data.location;
                driverMarker = L.marker([latitude, longitude]).addTo(map);
                map.setView([latitude, longitude]);
                historyPoints.push([latitude, longitude]);
            }
        });
</script>
@endpush