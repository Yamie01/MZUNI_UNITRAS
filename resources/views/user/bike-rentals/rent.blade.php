@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="fas fa-bicycle text-primary"></i> Rent a Bike</h2>

    <div class="row">
        <div class="col-lg-8">
            <!-- Location filter (optional) -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="location_filter" class="form-label"><i class="fas fa-map-pin"></i> Filter by Location</label>
                            <select id="location_filter" class="form-select">
                                <option value="">All locations</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button class="btn btn-outline-primary w-100" id="filterBikesBtn"><i class="fas fa-filter"></i> Filter</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bike list -->
            <div class="row g-4" id="bikeList">
                @forelse($availableBikes as $bike)
                    <div class="col-md-6 col-lg-4 bike-item" data-bike-id="{{ $bike->id }}" data-location-id="{{ $bike->location_id }}" data-rate="{{ $bike->price_per_hour }}">
                        <div class="card h-100 shadow-sm bike-card @if(session('selected_bike_id') == $bike->id) border-primary @endif">
                            <div class="card-body">
                                <h5 class="card-title">{{ $bike->brand }} {{ $bike->model }}</h5>
                                <p class="card-text text-muted">{{ ucfirst($bike->type) }}</p>
                                <p class="card-text"><strong>MWK {{ number_format($bike->price_per_hour, 0) }}</strong> / hour</p>
                                <p class="card-text"><strong>MWK {{ number_format($bike->price_per_day, 0) }}</strong> / day</p>
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> Available</span>
                                @if(session('selected_bike_id') == $bike->id)
                                    <span class="badge bg-primary">Selected</span>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent">
                                <button class="btn btn-outline-primary select-bike-btn w-100" data-id="{{ $bike->id }}">Select</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5"><i class="fas fa-bicycle fa-3x text-muted"></i><p>No bikes available.</p></div>
                @endforelse
            </div>
        </div>

        <!-- Rental panel -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-play-circle"></i> Rental Controls</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="rentalDuration" class="form-label"><i class="fas fa-clock"></i> Duration (hours)</label>
                        <input type="number" id="rentalDuration" class="form-control" value="1" min="0.5" step="0.5">
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success" id="startRentalBtn" disabled><i class="fas fa-play"></i> Start Rental</button>
                        <button class="btn btn-danger" id="endRentalBtn" disabled><i class="fas fa-stop"></i> End Rental</button>
                    </div>

                    <hr>

                    <!-- Timer display -->
                    <div id="rentalTimer" style="display: none;">
                        <div class="text-center">
                            <h4 class="fw-bold" id="timerDisplay">00:00:00</h4>
                            <p class="text-muted small">Remaining / Overtime</p>
                        </div>
                        <div class="row text-center">
                            <div class="col-6">
                                <small>Base Fee</small><br>
                                <strong id="baseFeeDisplay">MWK 0</strong>
                            </div>
                            <div class="col-6">
                                <small>Overtime Fee</small><br>
                                <strong id="overtimeFeeDisplay" class="text-danger">MWK 0</strong>
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <h5>Total Due: <span id="totalDueDisplay" class="text-primary">MWK 0</span></h5>
                        </div>
                    </div>

                    <!-- Active rental info -->
                    <div id="rentalStatus"></div>

                    <!-- Late fee payment button (appears after end rental with overtime) -->
                    <div id="lateFeePayment" style="display: none;">
                        <hr>
                        <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> You have an overtime fee to pay.</p>
                        <a href="#" id="payLateFeeBtn" class="btn btn-warning w-100"><i class="fas fa-credit-card"></i> Pay Late Fee</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ─── Data ───
    const bikes = @json($availableBikes);
    let selectedBikeId = null;
    let rental = null; // { bikeId, startTime, durationHours, baseFee, ratePerHour, overtimeRate, timerInterval, totalOvertime }

    // ─── DOM refs ───
    const bikeItems = document.querySelectorAll('.bike-item');
    const selectBtns = document.querySelectorAll('.select-bike-btn');
    const startBtn = document.getElementById('startRentalBtn');
    const endBtn = document.getElementById('endRentalBtn');
    const durationInput = document.getElementById('rentalDuration');
    const timerDiv = document.getElementById('rentalTimer');
    const timerDisplay = document.getElementById('timerDisplay');
    const baseFeeDisplay = document.getElementById('baseFeeDisplay');
    const overtimeFeeDisplay = document.getElementById('overtimeFeeDisplay');
    const totalDueDisplay = document.getElementById('totalDueDisplay');
    const rentalStatus = document.getElementById('rentalStatus');
    const lateFeePayment = document.getElementById('lateFeePayment');
    const payLateFeeBtn = document.getElementById('payLateFeeBtn');

    // ─── Helper functions ───
    function formatCurrency(amount) {
        return 'MWK ' + Number(amount).toFixed(0);
    }

    function formatTime(seconds) {
        const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
        const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
        const s = String(Math.floor(seconds % 60)).padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    function getNow() { return new Date().getTime(); }

    // ─── Select bike ───
    function selectBike(bikeId) {
        selectedBikeId = bikeId;
        document.querySelectorAll('.bike-card').forEach(c => c.classList.remove('border-primary'));
        const card = document.querySelector(`.bike-item[data-bike-id="${bikeId}"] .bike-card`);
        if (card) card.classList.add('border-primary');
        startBtn.disabled = false;
        showNotification('Bike selected. Set duration and click Start.', 'info');
    }

    selectBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = parseInt(btn.dataset.id);
            selectBike(id);
        });
    });

    // Click on card also selects
    bikeItems.forEach(item => {
        item.addEventListener('click', (e) => {
            if (!e.target.closest('.select-bike-btn')) {
                const id = parseInt(item.dataset.bikeId);
                selectBike(id);
            }
        });
    });

    // ─── Filter bikes by location ───
    document.getElementById('filterBikesBtn').addEventListener('click', () => {
        const locId = document.getElementById('location_filter').value;
        bikeItems.forEach(item => {
            const itemLoc = item.dataset.locationId || '';
            item.style.display = (locId === '' || itemLoc === locId) ? '' : 'none';
        });
    });

    // ─── Start Rental ───
    startBtn.addEventListener('click', function() {
        if (!selectedBikeId) {
            showNotification('Please select a bike first.', 'error');
            return;
        }
        const bike = bikes.find(b => b.id === selectedBikeId);
        if (!bike) return;

        let hours = parseFloat(durationInput.value);
        if (!hours || hours <= 0) {
            showNotification('Enter a valid duration (≥ 0.5h).', 'error');
            return;
        }
        if (hours > 12) {
            showNotification('Maximum rental duration is 12 hours.', 'error');
            return;
        }

        // End existing rental if any
        if (rental) endRental(true);

        const startTime = getNow();
        const baseFee = bike.price_per_hour * hours;
        const overtimeRate = bike.price_per_hour * 1.5;

        rental = {
            bikeId: bike.id,
            bikeName: bike.brand + ' ' + bike.model,
            startTime,
            durationHours: hours,
            baseFee,
            ratePerHour: bike.price_per_hour,
            overtimeRate,
            timerInterval: null,
            totalOvertime: 0,
        };

        // UI
        timerDiv.style.display = 'block';
        baseFeeDisplay.textContent = formatCurrency(baseFee);
        overtimeFeeDisplay.textContent = formatCurrency(0);
        totalDueDisplay.textContent = formatCurrency(baseFee);
        startBtn.disabled = true;
        endBtn.disabled = false;
        lateFeePayment.style.display = 'none';

        showNotification(`✅ Rental started: ${rental.bikeName} for ${hours}h. Overtime: ${formatCurrency(overtimeRate)}/h after ${hours}h.`, 'success');

        // Start timer
        rental.timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
    });

    // ─── Update Timer ───
    function updateTimer() {
        if (!rental) return;
        const now = getNow();
        const elapsed = (now - rental.startTime) / 1000;
        const plannedSeconds = rental.durationHours * 3600;
        const remaining = plannedSeconds - elapsed;

        let overtimeSeconds = 0;
        let totalOvertimeFee = 0;

        if (remaining >= 0) {
            timerDisplay.textContent = formatTime(remaining);
            timerDisplay.style.color = '#28a745';
            overtimeFeeDisplay.textContent = formatCurrency(0);
            totalDueDisplay.textContent = formatCurrency(rental.baseFee);
        } else {
            overtimeSeconds = Math.abs(remaining);
            const overtimeHours = overtimeSeconds / 3600;
            totalOvertimeFee = overtimeHours * rental.overtimeRate;
            rental.totalOvertime = totalOvertimeFee;
            timerDisplay.textContent = '⏰ ' + formatTime(overtimeSeconds);
            timerDisplay.style.color = '#dc3545';
            overtimeFeeDisplay.textContent = formatCurrency(totalOvertimeFee);
            totalDueDisplay.textContent = formatCurrency(rental.baseFee + totalOvertimeFee);
        }
    }

    // ─── End Rental ───
    function endRental(silent = false) {
        if (!rental) return;
        clearInterval(rental.timerInterval);
        rental.timerInterval = null;

        const now = getNow();
        const elapsed = (now - rental.startTime) / 3600;
        const planned = rental.durationHours;
        const overtimeHours = Math.max(0, elapsed - planned);
        const overtimeFee = overtimeHours * rental.overtimeRate;
        const total = rental.baseFee + overtimeFee;

        if (!silent) {
            // Show summary
            let msg = `🏁 Rental ended: ${rental.bikeName}\n` +
                      `⏱ Duration: ${elapsed.toFixed(2)}h (planned ${planned}h)\n` +
                      `💰 Base fee: ${formatCurrency(rental.baseFee)}\n`;
            if (overtimeHours > 0) {
                msg += `⏰ Overtime: ${overtimeHours.toFixed(2)}h × ${formatCurrency(rental.overtimeRate)}/h = ${formatCurrency(overtimeFee)}\n` +
                       `💵 Total due: ${formatCurrency(total)}\n` +
                       `⚠️ Please pay the late fee to complete the rental.`;
                // Show late fee payment button
                lateFeePayment.style.display = 'block';
                // Store rental id for payment (you need to have created a rental record in DB)
                // For now, we simulate by setting a data attribute on the button
                payLateFeeBtn.href = "{{ route('rentals.pay-late-fee', '') }}/" + encodeURIComponent(rental.bikeId) + "?amount=" + overtimeFee;
                // In real scenario, you'd have a rental ID from DB
            } else {
                msg += `✅ Total due: ${formatCurrency(total)} – No overtime. Rental complete.`;
            }
            showNotification(msg, overtimeHours > 0 ? 'warning' : 'success');
        }

        // Reset UI
        timerDiv.style.display = 'none';
        startBtn.disabled = false;
        endBtn.disabled = true;
        timerDisplay.textContent = '00:00:00';
        overtimeFeeDisplay.textContent = formatCurrency(0);
        totalDueDisplay.textContent = formatCurrency(0);

        rental = null;
    }

    endBtn.addEventListener('click', () => endRental(false));

    // ─── Notification helper ───
    function showNotification(message, type = 'info') {
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        rentalStatus.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show mt-2" role="alert">
                <i class="fas ${icons[type] || icons.info}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        // Auto dismiss after 6s
        setTimeout(() => {
            const alert = rentalStatus.querySelector('.alert');
            if (alert) alert.remove();
        }, 6000);
    }

    // ─── Late fee payment (simulated redirect) ───
    payLateFeeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        // In real scenario, you'd create a PayChangu payment and redirect
        // For now, we just simulate
        alert('Redirecting to PayChangu to pay late fee...');
        // window.location.href = this.href;
    });

    // ─── Keyboard shortcut: Esc to end rental ───
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && rental) {
            if (confirm('End current rental?')) endRental(false);
        }
    });

    // ─── Preselect a bike if session has one ───
    @if(session('selected_bike_id'))
        selectBike({{ session('selected_bike_id') }});
    @endif
</script>
@endpush
