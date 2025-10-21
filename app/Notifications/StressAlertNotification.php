<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioMessage;

class StressAlertNotification extends Notification
{
    use Queueable;

    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['twilio'];
    }

    public function toTwilio(object $notifiable): TwilioMessage
    {
        // Agora estamos especificando o número de origem do WhatsApp
        // que está no nosso arquivo .env
        return (new TwilioMessage())
            ->from(config('services.twilio.whatsapp_from'))
            ->content($this->message);
    }
}
