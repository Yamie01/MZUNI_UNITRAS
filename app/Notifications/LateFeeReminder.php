<?php

namespace App\Notifications;

use App\Models\BikeRental;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LateFeeReminder extends Notification
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
            ->subject('⚠️ Late Fee Reminder - Mzuni UNITRAS')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have an unpaid late fee for a bike rental.')
            ->line('🚲 Bike: ' . $this->rental->bike->brand . ' ' . $this->rental->bike->model)
            ->line('💰 Late Fee: MWK ' . number_format($this->rental->late_fee, 2))
            ->line('📅 Due Date: Please pay as soon as possible.')
            ->action('Pay Late Fee', route('user.bike-rentals.show', $this->rental))
            ->line('Please pay the late fee to continue renting bikes.');
    }

    public function toArray($notifiable)
    {
        return [
            'rental_id' => $this->rental->id,
            'late_fee' => $this->rental->late_fee,
            'message' => 'You have an unpaid late fee.',
            'type' => 'late_fee_reminder'
        ];
    }
}