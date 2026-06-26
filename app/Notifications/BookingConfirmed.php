<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingConfirmed extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('✅ Booking Confirmed - Mzuni UNITRAS')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your booking has been confirmed successfully.')
            ->line('📋 Booking Reference: ' . $this->booking->booking_reference)
            ->line('📍 Route: ' . $this->booking->pickup_point . ' → ' . $this->booking->dropoff_point)
            ->line('📅 Date: ' . $this->booking->trip_date->format('d M Y, H:i'))
            ->line('💰 Amount: MWK ' . number_format($this->booking->total_price, 2))
            ->action('View Booking', route('user.bookings.show', $this->booking))
            ->line('Thank you for using Mzuni UNITRAS!');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'message' => 'Your booking has been confirmed.',
            'type' => 'booking_confirmed'
        ];
    }
}