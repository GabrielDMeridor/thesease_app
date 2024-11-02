<?php

// app/Notifications/ProposalSubmissionCompletedNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProposalSubmissionCompletedNotification extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['database']; // Store notification in the database
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'The proposal submission files of ' . $this->appointment->student->name . ' are now completed',
            'appointment_id' => $this->appointment->id,
            'student_name' => $this->appointment->student->name,
        ];
    }
}

