<?php

namespace App\Notifications;

use App\Models\BikeRental;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RentalStarted extends Notification
{
    use Queueable;

    protected $rental;

    public function __construct(BikeRental $rental)
    {
        $this->rental = $rental;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🚲 Bike Rental Started - Mzuni UNITRAS')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your bike rental has started successfully.')
            ->line('🚲 Bike: ' . $this->rental->bike->brand . ' ' . $this->rental->bike->model)
            ->line('📅 Started: ' . $this->rental->start_time->format('d M Y, H:i'))
            ->line('⏰ Duration: ' . $this->rental->duration . ' ' . ucfirst($this->rental->duration_type) . '(s)')
            ->line('💰 Total: MWK ' . number_format($this->rental->total_amount, 2))
            ->action('View Rental', route('user.bike-rentals.show', $this->rental))
            ->line('Enjoy your ride! 🚲');
    }

    public function toArray($notifiable)
    {
        return [
            'rental_id' => $this->rental->id,
            'rental_code' => $this->rental->rental_code,
            'message' => 'Your bike rental has started.',
            'type' => 'rental_started'
        ];
    }
}