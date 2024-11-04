<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatisticianApprovalNotificationToRoles extends Notification
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

    public function toArray($notifiable)
    {
        return [
            'message' => "The student {$this->studentName} has been approved by the statistician for consultation.",
            'type' => 'statistician_approval', // Optional: specify a type if you need it for categorization
        ];
    }
}
