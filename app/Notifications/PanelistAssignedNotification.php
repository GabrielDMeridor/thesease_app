<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PanelistAssignedNotification extends Notification
{
    use Queueable;

    private $studentName;
    private $scheduleType;
    private $date;
    private $time;

    public function __construct($studentName, $scheduleType, $date, $time)
    {
        $this->studentName = $studentName;
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
            'message' => "You have been assigned as a panelist for the {$this->scheduleType} of {$this->studentName}. Please check your calendar for details.",
            'date' => $this->date,
            'time' => $this->time,
        ];
    }
}
