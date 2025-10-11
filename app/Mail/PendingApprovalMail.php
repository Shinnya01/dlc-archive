<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PendingApprovalMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $userName;

    /**
     * Create a new message instance.
     */
    public function __construct($userName )
    {
        $this->userName = $userName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Request is Pending Admin Approval')
                    ->view('emails.pending-approval');
    }
}
