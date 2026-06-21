<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendVerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verifyUrl;

    public function __construct($user, string $verifyUrl)
    {
        $this->user = $user;
        $this->verifyUrl = $verifyUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Email Address',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify_email',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
