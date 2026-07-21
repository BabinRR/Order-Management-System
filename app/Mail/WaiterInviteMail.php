<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WaiterInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $changeCode,
        public string $defaultPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name').' — your waiter account',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.waiter-invite',
            with: [
                'user' => $this->user,
                'changeCode' => $this->changeCode,
                'defaultPassword' => $this->defaultPassword,
                'loginUrl' => route('login'),
            ],
        );
    }
}
