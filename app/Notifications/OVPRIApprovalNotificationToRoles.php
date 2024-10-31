<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OVPRIApprovalNotificationToRoles extends Notification
{
    use Queueable;

    protected $adviserName;
    protected $studentName;

    public function __construct($adviserName, $studentName)
    {
        $this->adviserName = $adviserName;
        $this->studentName = $studentName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "OVPRI has approved the research registration of {$this->adviserName} for their advisee {$this->studentName}."
        ];
    }
}
