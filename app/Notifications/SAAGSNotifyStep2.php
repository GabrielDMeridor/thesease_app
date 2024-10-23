<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SAAGSNotifyStep2 extends Notification
{
    use Queueable;

    protected $adviser;
    protected $advisee;

    public function __construct($adviser, $advisee)
    {
        $this->adviser = $adviser;
        $this->advisee = $advisee;
    }

    public function via($notifiable)
    {
        return ['database']; // We are only using database notifications
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'The adviser ' . $this->adviser->name . ' has signed the endorsement for advisee ' . $this->advisee->name . ' in Step 2.',
            'adviser_name' => $this->adviser->name,
            'advisee_name' => $this->advisee->name,
        ];
    }
}
