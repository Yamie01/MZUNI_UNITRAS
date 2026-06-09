<div class="card vehicle-card h-100">
    <div class="position-relative">
        @php
            // Safely handle images
            $imageUrl = 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
            if ($advertisement->images) {
                $images = is_array($advertisement->images) ? $advertisement->images : json_decode($advertisement->images, true);
                if (is_array($images) && count($images) > 0) {
                    $imageUrl = asset('storage/' . $images[0]);
                }
            }
        @endphp
        <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $advertisement->title }}" style="height: 200px; object-fit: cover;">

        <!-- Vehicle Type Badge -->
        <span class="badge-type badge bg-{{ $advertisement->ad_type == 'bus' ? 'danger' : ($advertisement->ad_type == 'taxi' ? 'warning' : 'success') }}">
            {{ ucfirst(str_replace('_', ' ', $advertisement->ad_type)) }}
        </span>

        <!-- Featured Badge -->
        @if($advertisement->is_featured)
            <span class="badge bg-danger" style="position: absolute; top: 10px; left: 10px;">Featured</span>
        @endif
    </div>

    <div class="card-body">
        <h5 class="card-title">{{ Str::limit($advertisement->title, 40) }}</h5>

        <div class="mb-2">
            <small class="text-muted">
                <i class="fas fa-map-marker-alt text-danger"></i> 
                {{ Str::limit($advertisement->from_location, 20) }} 
                <i class="fas fa-arrow-right mx-1"></i> 
                {{ Str::limit($advertisement->to_location, 20) }}
            </small>
        </div>

        <div class="mb-3">
            <small class="text-muted">
                <i class="far fa-calendar-alt text-primary"></i> 
                {{ \Carbon\Carbon::parse($advertisement->departure_time)->format('M d, Y H:i') }}
            </small>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <span class="h5 mb-0 text-primary">MWK {{ number_format($advertisement->price, 2) }}</span>
                <small class="text-muted d-block">per seat</small>
            </div>
            <div class="text-end">
                <div>
                    <i class="fas fa-users text-muted"></i>
                    <small class="text-muted">{{ $advertisement->available_seats }} seats left</small>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <span class="badge bg-info">
                <i class="fas fa-car me-1"></i> {{ $advertisement->vehicle->model ?? 'N/A' }}
            </span>
            <span class="text-muted">
                <i class="fas fa-user me-1"></i> {{ Str::limit($advertisement->owner->name, 15) }}
            </span>
        </div>
    </div>

    <div class="card-footer bg-transparent border-top-0">
        <div class="d-grid">
            @auth
                <a href="{{ route('user.bookings.create', $advertisement) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-calendar-check me-1"></i> Book Now
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-sign-in-alt me-1"></i> Login to Book
                </a>
            @endauth
        </div>
    </div>
</div>