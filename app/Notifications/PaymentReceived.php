<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReceived extends Notification
{
    use Queueable;

    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('💰 Payment Received - Mzuni UNITRAS')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We have received your payment successfully.')
            ->line('💰 Amount: MWK ' . number_format($this->payment->amount, 2))
            ->line('📝 Transaction ID: ' . $this->payment->transaction_id)
            ->line('📅 Date: ' . $this->payment->payment_date->format('d M Y, H:i'))
            ->line('Thank you for using Mzuni UNITRAS!');
    }

    public function toArray($notifiable)
    {
        return [
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'message' => 'Payment received successfully.',
            'type' => 'payment_received'
        ];
    }
}