<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\AdviserAppointment;

class SimilarityCertEXCStudent extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct(AdviserAppointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'A new similarity certificate has been uploaded for ' . $this->appointment->student->name,
            'appointment_id' => $this->appointment->id,
            'student_id' => $this->appointment->student_id,
            'uploaded_by' => auth()->user()->name,
            'created_at' => now(),
        ];
    }
}
