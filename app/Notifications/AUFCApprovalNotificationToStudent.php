<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AUFCApprovalNotificationToStudent extends Notification
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
            'message' => "AUFC has approved your files. You are now ready to proceed to data gathering and analysis. Maintain communication with your adviser throughout this process to be guided. You are now able to access Route 2.",
        ];
    }
}
