<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use App\Models\User;

class WelcomeMail extends Mailable
{
    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Benvenuto su FlightTracker!');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.welcome');
    }
}
