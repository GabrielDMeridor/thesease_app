<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatisticianApprovalNotificationToStudent extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your consultation with the statistician has been approved.',
            'type' => 'statistician_approval', // Optional: specify a type if you need it for categorization
        ];
    }
}
