<?php

declare(strict_types=1);

namespace App\Mail;

use App\Enums\MembershipRole;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The invitation link. Carries plain values only: the token is never stored, so the
 * queued mail is the one place it exists until the person opens the link.
 */
final class InvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $schoolName,
        public readonly MembershipRole $role,
        public readonly string $url,
        public readonly CarbonInterface $expiresAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('mail.invitation.subject', ['school' => $this->schoolName]));
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.invitation',
            text: 'mail.invitation-text',
            with: [
                'roleLabel' => $this->role->label(),
                'expiresOn' => $this->expiresAt->timezone('Asia/Jakarta')->locale('id')->translatedFormat('j F Y, H.i'),
            ],
        );
    }
}
