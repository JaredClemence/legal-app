<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OptInRequired extends Mailable
{
    use Queueable, SerializesModels;
    
    public $recipientName;
    public $confirmLink;
    public $unsubscribeNow;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('jclemence@ch-law.com', 'Jared R. Clemence'),
            replyTo: [
                new Address('jclemence@ch-law.com', 'Jared R. Clemence'),
                new Address('stevie@mclgattorneys.com', 'Stevie McDonald'),
                new Address('susie@mclgattorneys.com', 'Susie Coons')
            ],
            subject: '[Opt In Required] Kern County Bar: Probate Section Mail List',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'textmail.optin',    
            text: 'textmail.optin',
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
