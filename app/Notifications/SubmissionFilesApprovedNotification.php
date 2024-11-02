<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class SubmissionFilesApprovedNotification extends Notification
{
    use Queueable;

    protected $superAdmin;

    public function __construct(User $superAdmin)
    {
        $this->superAdmin = $superAdmin;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Your submission files have been approved by {$this->superAdmin->name}.",
        ];
    }
}
