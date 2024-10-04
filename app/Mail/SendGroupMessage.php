<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendGroupMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $group;
    public $member;

    /**
     * Create a new message instance.
     */
    public function __construct($group, $member)
    {
        $this->group = $group;
        $this->member = $member;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notification de Groupe sur FATE Companie.sa',
            from: new Address('accounts@unetah.net', 'FATE Companie.sa')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {

        // return $this->subject('Nouveau groupe créé : ' . $this->group->groupe_name)
        // ->view('emails.group_created');
        return new Content(
            view: 'mails.groupe_notification',
            with: [

                'groupe' => $this->group->groupe_name
                // 'name' => $this->name,
                // 'email' => $this->email,
                // 'code' => $this->otpCode
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
