<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AdviseeNotifStep2 extends Notification
{
    use Queueable;

    protected $adviser;
    protected $action;

    public function __construct($adviser)
    {
        $this->adviser = $adviser;
    }
    public function via($notifiable)
    {
        return ['database']; // Notify via email and store in the database
    }
    public function toArray($notifiable)
    {
        return [
            'message' => 'Your adviser, ' . $this->adviser->name . ', has signed the endorsement, you can now proceed to Step 3',
            'adviser_name' => $this->adviser->name,
        ];
    }
}
