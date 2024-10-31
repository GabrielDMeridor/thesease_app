<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\AdviserAppointment;

class OVPRINotification extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct(AdviserAppointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['database']; // Only store notification in the database
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => auth()->user()->name . ' has marked the research registration as responded.',
            'appointment_id' => $this->appointment->id,
            'adviser_name' => auth()->user()->name,
            'responded_at' => now(),
        ];
    }
}
