<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdviserRequestNotification extends Notification
{
    use Queueable;

    protected $programChair;
    protected $student;
    protected $appointmentType;

    public function __construct($programChair, $student, $appointmentType)
    {
        $this->programChair = $programChair;
        $this->student = $student;
        $this->appointmentType = $appointmentType;
    }

    public function via($notifiable)
    {
        return ['database'];  // We are using database notifications
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "You have a new adviser request for {$this->student->name} in the {$this->appointmentType} from {$this->programChair->name}.",
            'program_chair' => $this->programChair->name,
            'student' => $this->student->name,
            'appointment_type' => $this->appointmentType,
        ];
    }
}
