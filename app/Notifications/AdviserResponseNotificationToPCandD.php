<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdviserResponseNotificationToPCandD extends Notification
{
    use Queueable;

    protected $status;
    protected $adviser;
    protected $appointmentType;
    protected $student;

    public function __construct($status, $adviser, $appointmentType, $student)
    {
        $this->status = $status;  // 'approved' or 'disapproved'
        $this->adviser = $adviser;  // The adviser who made the decision
        $this->appointmentType = $appointmentType;  // Project Study, Thesis, or Dissertation
        $this->student = $student; // The student associated with the appointment
    }

    public function via($notifiable)
    {
        return ['database'];  // Save the notification in the database
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "The adviser request for {$this->student->name} has been {$this->status} by {$this->adviser->name} for {$this->appointmentType}.",
            'adviser' => $this->adviser->name,
            'student' => $this->student->name,
            'status' => $this->status,
            'appointment_type' => $this->appointmentType,
        ];
    }
}
