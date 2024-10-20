<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdviserResponseNotification extends Notification
{
    use Queueable;

    protected $status;
    protected $adviser;
    protected $appointmentType;

    public function __construct($status, $adviser, $appointmentType)
    {
        $this->status = $status;  // 'approved' or 'disapproved'
        $this->adviser = $adviser;  // The adviser who made the decision
        $this->appointmentType = $appointmentType;  // Project Study, Thesis, or Dissertation
    }

    public function via($notifiable)
    {
        return ['database'];  // Save the notification in the database
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Your adviser request has been {$this->status} by {$this->adviser->name} for {$this->appointmentType}.",
            'adviser' => $this->adviser->name,
            'status' => $this->status,
            'appointment_type' => $this->appointmentType,
        ];
    }
}
