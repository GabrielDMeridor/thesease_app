<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdviserRequestNotification extends Notification
{
    use Queueable;

    protected $student;
    protected $appointmentType;

    public function __construct($student, $appointmentType)
    {
        $this->student = $student;
        $this->appointmentType = $appointmentType;
    }

    public function via($notifiable)
    {
        return ['database']; // Store the notification in the database
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->student->name . ' has requested you to be their ' . $this->appointmentType . '.',
            'student_id' => $this->student->id,
            'appointment_type' => $this->appointmentType
        ];
    }
}
