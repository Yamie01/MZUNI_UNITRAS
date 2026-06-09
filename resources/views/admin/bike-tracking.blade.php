@extends('layouts.app')

@section('title', 'Live Bike Tracking')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 600px; border-radius: 16px; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Live Bike Tracking – Active Rentals</h5>
        </div>
        <div class="card-body">
            <div id="map"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([-11.45, 34.02], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { attribution: '&copy; OSM' }).addTo(map);
    
    const bikeMarkers = {};
    
    // Echo listener for each bike (you can broadcast bike locations dynamically)
    // For simplicity, we load all active bike rentals and then subscribe to their channels
    
    fetch('/api/admin/active-bike-rentals')
        .then(res => res.json())
        .then(rentals => {
            rentals.forEach(rental => {
                // Subscribe to each bike's channel
                window.Echo.channel(`tracking.bike.${rental.bike_id}`)
                    .listen('location.updated', (e) => {
                        const { latitude, longitude } = e.location;
                        const coords = [latitude, longitude];
                        if (bikeMarkers[rental.bike_id]) {
                            bikeMarkers[rental.bike_id].setLatLng(coords);
                        } else {
                            bikeMarkers[rental.bike_id] = L.marker(coords)
                                .bindPopup(`<b>Bike ${rental.bike_id}</b><br>Rented by: ${rental.user_name}`)
                                .addTo(map);
                        }
                    });
            });
        });
</script>
@endpush