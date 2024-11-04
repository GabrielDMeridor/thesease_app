<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PanelCommentNotification extends Notification
{
    use Queueable;

    protected $panelistName;

    public function __construct($panelistName)
    {
        $this->panelistName = $panelistName;
    }

    public function via($notifiable)
    {
        return ['database']; // Storing in database
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Your panelist {$this->panelistName} has added a comment in your proposal manuscript in your monitoring form.",
        ];
    }
}
