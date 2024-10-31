<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OVPRIApprovalNotificationToAdviser extends Notification
{
    use Queueable;

    protected $studentName;

    public function __construct($studentName)
    {
        $this->studentName = $studentName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "OVPRI has approved your research registration for advisee {$this->studentName}."
        ];
    }
}
