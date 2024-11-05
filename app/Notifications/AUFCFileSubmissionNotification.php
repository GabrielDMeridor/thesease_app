<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AUFCFileSubmissionNotification extends Notification
{
    use Queueable;

    protected $studentName;
    protected $userAccountType;

    /**
     * Create a new notification instance.
     *
     * @param string $studentName
     * @param int $userAccountType
     */
    public function __construct($studentName, $userAccountType)
    {
        $this->studentName = $studentName;
        $this->userAccountType = $userAccountType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // Store notification in the database
    }

    /**
     * Get the database representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // Set message based on account type
        if ($this->userAccountType === 6) {
            $message = "{$this->studentName} has sent their files, awaiting for approval.";
        } else {
            $message = "The student {$this->studentName} has sent their files for AUFC.";
        }

        return [
            'message' => $message,
            'student_name' => $this->studentName,
        ];
    }
}
