<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudentScheduledNotification extends Notification
{
    use Queueable;

    private $scheduleType;
    private $date;
    private $time;

    public function __construct($scheduleType, $date, $time)
    {
        $this->scheduleType = $scheduleType;
        $this->date = $date;
        $this->time = $time;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "You have been scheduled for {$this->scheduleType}. Please check your calendar for details.",
            'date' => $this->date,
            'time' => $this->time,
        ];
    }
}
