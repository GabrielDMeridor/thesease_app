<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class SubmissionFilesRespondedNotification extends Notification
{
    use Queueable;

    protected $student;

    public function __construct(User $student)
    {
        $this->student = $student;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "{$this->student->name} has marked responded on the submission files.",
            'student_id' => $this->student->id,
        ];
    }
}
