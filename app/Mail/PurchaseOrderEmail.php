<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Backend\PurchaseManagement\PurchaseOrder;
use App\Models\Backend\PurchaseManagement\PurchaseRequest;
use App\Models\Backend\Supplier;
use App\Services\ImageService;

class PurchaseOrderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $ponumber;
    protected $imageService;
    public $subject;
    public $email_body;
    public $attachment;

    public function __construct($ponumber, ImageService $imageService, $subject, $email_body, $attachment)
    {
        $this->ponumber = $ponumber;
        $this->imageService = $imageService;
        $this->subject = $subject;
        $this->email_body = $email_body;
        $this->attachment = $attachment;
    }

    public function build()
    {
        $poNumber = $this->ponumber;
        $purchaseOrder = PurchaseOrder::where('po_number', $poNumber)->get();
        $purchaseRequest = PurchaseRequest::where('id', $purchaseOrder->first()->purchase_request_id)->get();
        $supplier = Supplier::where('id', $purchaseRequest->first()->supplier_id)->get();
        $supplierReference = $purchaseOrder->first()->supplier_reference ?? 'N/A';
        $email_body = $this->email_body;
        $attachment = $this->attachment;
        //$base64Image = $this->imageService->base64Image('img/logo-iaf.jpeg');


        $pdf = PDF::loadView('backend.purchase_management.pdf.purchase_order_pdf', compact('email_body', 'supplier', 'purchaseRequest', 'purchaseOrder', 'poNumber', 'supplierReference'));

        $email = $this->subject($this->subject)
            ->markdown('backend.purchase_management.mail.purchase_order', compact('email_body'))
            ->attachData($pdf->output(), 'purchase_order_' . $poNumber . '.pdf', [
                'mime' => 'application/pdf',
            ]);

        // Attach additional files if any
        if (!empty($this->attachment)) {
            foreach ($this->attachment as $file) {
                $email->attach($file);
            }
        }

        return $email;
    }
}
