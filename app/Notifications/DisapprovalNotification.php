<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisapprovalNotification extends Notification
{
    use Queueable;

    protected $disapproveReason;

    public function __construct($disapproveReason)
    {
        $this->disapproveReason = $disapproveReason;
    }

    public function via($notifiable)
    {
        return ['database']; // Only storing notification in the database
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your account has been disapproved',
            'reason' => $this->disapproveReason,
        ];
    }


    public function toArray($notifiable)
    {
        return [
            'message' => 'Your account has been disapproved.',
            'reason' => $this->disapproveReason,
        ];
    }
}
