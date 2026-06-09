<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingStatusChanged extends Notification
{
    use Queueable;

    protected $booking;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Booking $booking, $oldStatus, $newStatus)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Booking Status Updated')
            ->greeting('Hello ' . $notifiable->name)
            ->line("Your booking #{$this->booking->id} status has been changed from {$this->oldStatus} to {$this->newStatus}.")
            ->action('View Booking', route('user.bookings.index'))
            ->line('Thank you for using Mzuni UNITRAS!');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Booking #{$this->booking->id} status updated to {$this->newStatus}."
        ];
    }
}