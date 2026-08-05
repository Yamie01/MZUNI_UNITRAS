<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
<<<<<<< HEAD
=======
use App\Models\Vehicle;
use App\Observers\VehicleObserver;
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function boot(): void
<<<<<<< HEAD
    {
        //
    }
=======
{
    Vehicle::observe(VehicleObserver::class);
}
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
}