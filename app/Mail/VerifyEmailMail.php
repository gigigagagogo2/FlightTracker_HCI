<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class VerifyEmailMail extends Mailable
{
    public function __construct(public string $verifyUrl) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Verifica la tua email - FlightTracker');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.verify-email');
    }
}
