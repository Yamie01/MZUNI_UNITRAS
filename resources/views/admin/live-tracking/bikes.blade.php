@extends('layouts.admin')

@section('title', 'Live Bike Tracking - Admin')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 600px; border-radius: 12px; width: 100%; }
    .bike-list { max-height: 600px; overflow-y: auto; }
    .bike-item { 
        cursor: pointer; 
        transition: all 0.2s; 
        border-left: 3px solid transparent;
    }
    .bike-item:hover { 
        background-color: #f8f9fa; 
        transform: translateX(5px); 
    }
    .bike-item.active { 
        background-color: #e3f2fd; 
        border-left-color: #00529b; 
    }
    .timer-warning { color: #dc3545; font-weight: bold; }
    .timer-normal { color: #28a745; }
    .timer-overtime { color: #ff6b35; font-weight: bold; animation: pulse 1s infinite; }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.6; }
        100% { opacity: 1; }
    }
    .location-update { font-size: 0.7rem; color: #6c757d; }
    .bike-marker { background: none; border: none; }
    .bike-marker i { font-size: 1.5rem; color: #00529b; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Map Column -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Live Bike Tracking Map</h5>
                </div>
                <div class="card-body p-0">
                    <div id="map"></div>
                    <div class="p-2 bg-light text-center border-top">
                        <small class="text-muted">
                            <i class="fas fa-circle text-success me-1"></i> Green marker = Online 
                            <span class="mx-2">|</span>
                            <i class="fas fa-circle text-secondary me-1"></i> Gray marker = Offline
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Active Rentals List -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bicycle me-2"></i>Active Rentals</h5>
                </div>
                <div class="card-body p-0 bike-list">
                    @if($activeRentals->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($activeRentals as $rental)
                                <div class="list-group-item bike-item" 
                                     data-bike-id="{{ $rental->bike_id }}" 
                                     data-rental-id="{{ $rental->id }}"
                                     data-lat="{{ $rental->bike->latestLocation->latitude ?? '' }}"
                                     data-lng="{{ $rental->bike->latestLocation->longitude ?? '' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $rental->bike->brand }} {{ $rental->bike->model }}</strong>
                                            <div class="small text-muted">
                                                <i class="fas fa-user me-1"></i>{{ $rental->user->name }}<br>
                                                <i class="fas fa-clock me-1"></i>Rented: {{ $rental->created_at->format('d M Y, H:i') }}
                                            </div>
                                        </div>
                                        <span class="badge bg-success">Active</span>
                                    </div>
                                    <div class="row mt-2 small">
                                        <div class="col-6">
                                            <i class="fas fa-hourglass-half me-1"></i>Duration: 
                                            <strong>{{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</strong>
                                        </div>
                                        <div class="col-6" id="timer-{{ $rental->id }}">
                                            <i class="fas fa-stopwatch me-1"></i>
                                            <span class="timer-normal">Calculating...</span>
                                        </div>
                                    </div>
                                    <div class="mt-1 location-update" id="location-{{ $rental->bike_id }}">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <span id="location-text-{{ $rental->bike_id }}">Waiting for GPS...</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bicycle fa-3x text-muted mb-2"></i>
                            <p class="text-muted">No active bike rentals at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Initialize map centered on Mzuzu
    const map = L.map('map').setView([-11.45, 34.02], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> & CartoDB'
    }).addTo(map);
    
    // Store markers and bike data
    const markers = {};
    const bikeData = {};
    
    // Timer calculation function
    function updateTimer(rentalId, duration, durationType, startTimestamp) {
        const timerElement = document.getElementById(`timer-${rentalId}`);
        if (!timerElement) return;
        
        const startDate = new Date(startTimestamp * 1000);
        let durationHours = durationType === 'hour' ? duration : duration * 24;
        const endDate = new Date(startDate.getTime() + durationHours * 60 * 60 * 1000);
        const now = new Date();
        
        if (now > endDate) {
            const overtimeMs = now - endDate;
            const overtimeHours = Math.ceil(overtimeMs / (60 * 60 * 1000));
            timerElement.innerHTML = `<span class="timer-overtime"><i class="fas fa-exclamation-triangle me-1"></i>OVERTIME: +${overtimeHours} hour(s)</span>`;
        } else {
            const remainingMs = endDate - now;
            const remainingHours = Math.floor(remainingMs / (60 * 60 * 1000));
            const remainingMinutes = Math.floor((remainingMs % (60 * 60 * 1000)) / (60 * 1000));
            timerElement.innerHTML = `<span class="timer-normal"><i class="fas fa-clock me-1"></i>Remaining: ${remainingHours}h ${remainingMinutes}m</span>`;
        }
    }
    
    // Update bike marker on map
    function updateBikeMarker(bikeId, lat, lng, hasLocation = true) {
        if (markers[bikeId]) {
            markers[bikeId].setLatLng([lat, lng]);
        } else {
            // Create custom marker with bike icon
            const bikeIcon = L.divIcon({
                className: 'bike-marker',
                html: '<i class="fas fa-bicycle fa-2x text-primary"></i>',
                iconSize: [30, 30],
                popupAnchor: [0, -15]
            });
            markers[bikeId] = L.marker([lat, lng], { icon: bikeIcon }).addTo(map);
        }
        
        // Update location text in sidebar
        const locationText = document.getElementById(`location-text-${bikeId}`);
        if (locationText) {
            locationText.innerHTML = `Last update: ${new Date().toLocaleTimeString()}`;
        }
        
        bikeData[bikeId] = { lat, lng, lastUpdate: Date.now() };
    }
    
    // Remove marker (bike offline/returned)
    function removeBikeMarker(bikeId) {
        if (markers[bikeId]) {
            map.removeLayer(markers[bikeId]);
            delete markers[bikeId];
        }
        const locationSpan = document.getElementById(`location-text-${bikeId}`);
        if (locationSpan) {
            locationSpan.innerHTML = 'Bike returned/offline';
        }
    }
    
    // Initialize timers for all active rentals
    @foreach($activeRentals as $rental)
        updateTimer({{ $rental->id }}, {{ $rental->duration }}, '{{ $rental->duration_type }}', {{ $rental->created_at->timestamp }});
        
        // Set initial marker if location exists
        @if($rental->bike->latestLocation)
            updateBikeMarker({{ $rental->bike_id }}, {{ $rental->bike->latestLocation->latitude }}, {{ $rental->bike->latestLocation->longitude }});
        @endif
    @endforeach
    
    // Refresh timers every minute
    setInterval(() => {
        @foreach($activeRentals as $rental)
            updateTimer({{ $rental->id }}, {{ $rental->duration }}, '{{ $rental->duration_type }}', {{ $rental->created_at->timestamp }});
        @endforeach
    }, 60000);
    
    // Check for stale markers (no update > 5 minutes)
    setInterval(() => {
        const now = Date.now();
        Object.keys(bikeData).forEach(bikeId => {
            if (bikeData[bikeId] && (now - bikeData[bikeId].lastUpdate) > 300000) {
                // Marker is stale, change color or remove?
                if (markers[bikeId]) {
                    const grayIcon = L.divIcon({
                        className: 'bike-marker',
                        html: '<i class="fas fa-bicycle fa-2x text-secondary"></i>',
                        iconSize: [30, 30]
                    });
                    markers[bikeId].setIcon(grayIcon);
                }
            }
        });
    }, 60000);
    
    // Center map on bike when clicked
    document.querySelectorAll('.bike-item').forEach(item => {
        item.addEventListener('click', function() {
            const bikeId = this.dataset.bikeId;
            const lat = this.dataset.lat;
            const lng = this.dataset.lng;
            
            if (lat && lng && markers[bikeId]) {
                map.setView([parseFloat(lat), parseFloat(lng)], 15);
            } else if (bikeData[bikeId]) {
                map.setView([bikeData[bikeId].lat, bikeData[bikeId].lng], 15);
            }
            
            document.querySelectorAll('.bike-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Function to refresh bike locations from API
    function refreshBikeLocations() {
      fetch('/admin/active-bike-rentals')
            .then(response => response.json())
            .then(data => {
                data.forEach(bike => {
                    if (bike.location) {
                        updateBikeMarker(bike.bike_id, bike.location.latitude, bike.location.longitude);
                    }
                });
            })
            .catch(err => console.error('Error fetching locations:', err));
    }
    
    // Refresh locations every 30 seconds
    setInterval(refreshBikeLocations, 30000);
    
    // Initial load
    refreshBikeLocations();
    
    console.log('Admin tracking page loaded. Active rentals: {{ $activeRentals->count() }}');
</script>
@endpush