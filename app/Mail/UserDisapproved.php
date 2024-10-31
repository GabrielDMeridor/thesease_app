<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserDisapproved extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reason; // Add reason as a public property

    public function __construct(User $user, $reason)
    {
        $this->user = $user;
        $this->reason = $reason; // Store the disapproval reason
    }

    public function build()
    {
        return $this->markdown('emails.disapproved')
                    ->subject('Account Disapproved')
                    ->with([
                        'user' => $this->user,
                        'reason' => $this->reason, // Pass reason to the view
                    ]);
    }
}
