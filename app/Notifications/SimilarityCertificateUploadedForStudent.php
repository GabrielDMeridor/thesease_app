<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\AdviserAppointment;

class SimilarityCertificateUploadedForStudent extends Notification
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
            'message' => 'The library has uploaded your similarity certificate.',
            'appointment_id' => $this->appointment->id,
            'uploaded_by' => auth()->user()->name,
            'created_at' => now(),
        ];
    }
}
