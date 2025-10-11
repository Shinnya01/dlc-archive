<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AcmRequestApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $pdfPath; // path to the generated ACM PDF

    public function __construct($userName, $pdfPath)
    {
        $this->userName = $userName;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->subject('ACM Request Approved')
                    ->view('emails.acm-request-approved') // your Blade
                    ->with(['user' => $this->userName])
                    ->attach(storage_path('app/' . $this->pdfPath)); // attach the PDF
    }
}
