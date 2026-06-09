@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tracking Your Bike</h3>
    <div id="map" style="height: 500px;"></div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([-11.45, 34.02], 14);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);
    
    let marker = null;
    const bikeId = {{ $bike->id }};
    
    // Echo setup (same as before)
    window.Echo.channel(`tracking.bike.${bikeId}`)
        .listen('location.updated', (e) => {
            const { latitude, longitude } = e.location;
            if (!marker) {
                marker = L.marker([latitude, longitude]).addTo(map);
            } else {
                marker.setLatLng([latitude, longitude]);
            }
            map.setView([latitude, longitude]);
        });
    
    // Fetch initial location
    fetch(`/api/bikes/${bikeId}/location`)
        .then(res => res.json())
        .then(data => {
            if (data.location) {
                marker = L.marker([data.location.latitude, data.location.longitude]).addTo(map);
                map.setView([data.location.latitude, data.location.longitude]);
            }
        });
</script>
@endsection