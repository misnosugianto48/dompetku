<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlyFinancialReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $pdfContent,
        public string $periodString
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dompetku - Monthly Financial Report',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.monthly_report',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'Dompetku-Report-'.$this->periodString.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
