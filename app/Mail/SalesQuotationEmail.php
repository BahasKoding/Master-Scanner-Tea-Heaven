<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Backend\SalesManagement\SalesQuotation;
use Illuminate\Mail\Mailables\Attachment;
class SalesQuotationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $salesQuotation;
    public $pdfContent;

    public function __construct(SalesQuotation $salesQuotation, $pdfContent)
    {
        $this->salesQuotation = $salesQuotation;
        $this->pdfContent = $pdfContent;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Sales Quotation ' . $this->salesQuotation->quotation_number,
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.sales_quotation',
        );
    }

    public function attachments()
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'quotation.pdf')
                ->withMime('application/pdf'),
        ];
    }
}