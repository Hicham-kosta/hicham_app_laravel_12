<?php

namespace App\Mail;

use App\Models\VendorDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorDetailsApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $vendorDetail;

    /**
     * Create a new message instance.
     */
    public function __construct(VendorDetail $vendorDetail)
    {
        $this->vendorDetail = $vendorDetail;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Vendor Account Approved - ' . config('app.name'))
                    ->view('emails.approved')
                    ->with([
                        'vendorDetail' => $this->vendorDetail,
                    ]);
    }
}