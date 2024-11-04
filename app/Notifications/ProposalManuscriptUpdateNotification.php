<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProposalManuscriptUpdateNotification extends Notification
{
    use Queueable;

    protected $student;

    public function __construct($student)
    {
        $this->student = $student;
    }

    public function via($notifiable)
    {
        return ['database']; // or ['mail', 'database'] if you want email as well
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "A new update of the proposal manuscript has been uploaded by the student {$this->student->name}.",
            'student_id' => $this->student->id
        ];
    }
}
