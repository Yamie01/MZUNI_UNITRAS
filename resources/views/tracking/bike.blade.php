@extends('layouts.app')

@section('title', 'Track Your Bike')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { 
        height: 500px; 
        width: 100%; 
        border-radius: 16px; 
        background-color: #e9ecef;
        position: relative;
        z-index: 1;
    }
    .info-card { 
        background: white; 
        border-radius: 16px; 
        padding: 1rem; 
        margin-bottom: 1rem; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
    }
    .status-badge {
        background-color: #28a745;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Live Bike Tracking</h5>
                </div>
                <div class="card-body p-0">
                    <div id="map"></div>
                    <div class="p-3 bg-light text-center border-top">
                        <span id="lastUpdate" class="text-muted small">
                            <i class="fas fa-spinner fa-pulse me-1"></i> Waiting for GPS signal...
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <h6><i class="fas fa-info-circle text-primary me-2"></i>Rental Details</h6>
                <hr>
                <p><strong>Bike:</strong> {{ $bike->brand }} {{ $bike->model }}</p>
                <p><strong>Rental Code:</strong> {{ $rental->rental_code }}</p>
                <p><strong>Status:</strong> <span class="status-badge">Active</span></p>
                <p><strong>Location:</strong> <span id="locationStatus">Waiting for signal...</span></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    // Wait for DOM to load completely
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map with Mzuzu coordinates
        var map = L.map('map').setView([-11.45, 34.02], 14);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> & CartoDB',
            subdomains: 'abcd',
            maxZoom: 19,
            minZoom: 4
        }).addTo(map);
        
        console.log('Map initialized');
        
        var marker = null;
        var bikeId = {{ $bike->id }};
        
        // Try to load Echo if available
        if (typeof Echo !== 'undefined') {
            console.log('Echo is available');
            Echo.channel(`tracking.bike.${bikeId}`)
                .listen('location.updated', (e) => {
                    console.log('Location update received:', e);
                    var coords = [e.latitude, e.longitude];
                    
                    if (!marker) {
                        marker = L.marker(coords).addTo(map);
                    } else {
                        marker.setLatLng(coords);
                    }
                    map.setView(coords);
                    document.getElementById('locationStatus').innerHTML = `Last update: ${new Date().toLocaleTimeString()}`;
                    document.getElementById('lastUpdate').innerHTML = `<i class="fas fa-check-circle text-success me-1"></i> Last update: ${new Date().toLocaleTimeString()}`;
                });
        } else {
            console.log('Echo not available - WebSocket not configured');
            document.getElementById('lastUpdate').innerHTML = '<i class="fas fa-exclamation-triangle text-warning me-1"></i> Live updates not available';
        }
        
        // Fetch initial location
        fetch(`/api/bikes/${bikeId}/location`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Initial location:', data);
            if (data.location && data.location.latitude && data.location.longitude) {
                var coords = [data.location.latitude, data.location.longitude];
                marker = L.marker(coords).addTo(map);
                map.setView(coords);
                document.getElementById('locationStatus').innerHTML = `Last known: ${new Date().toLocaleTimeString()}`;
                document.getElementById('lastUpdate').innerHTML = `<i class="fas fa-check-circle text-success me-1"></i> Location loaded`;
            }
        })
        .catch(err => {
            console.error('Error fetching location:', err);
            document.getElementById('lastUpdate').innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-1"></i> Unable to fetch location';
        });
    });
</script>
@endpush