<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProposalManuscriptUpdateNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $message;
    protected $type;

    // Constructor to accept user, message, and type
    public function __construct($user, $type = 'upload', $message = null)
    {
        $this->user = $user;
        $this->type = $type;

        // Define default messages based on type
        $this->message = $message ?? $this->getDefaultMessage();
    }

    // Specify database as the only channel
    public function via($notifiable)
    {
        return ['database'];
    }

    // Define the data that will be stored in the database
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'user' => $this->user->name,
            'user_id' => $this->user->id,
        ];
    }

    // Define default messages based on notification type
    protected function getDefaultMessage()
    {
        if ($this->type === 'deny') {
            return "Your adviser has denied your proposal manuscript update. Please consult with them to understand the required revisions.";
        }

        // Default message for upload type
        return "{$this->user->name} has uploaded a proposal manuscript update.";
    }
}
