<?php

namespace App\Mail;

use App\Models\VendorDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorDetailsRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $vendorDetail;
    public $rejectionReason;

    /**
     * Create a new message instance.
     */
    public function __construct(VendorDetail $vendorDetail, $rejectionReason = null)
    {
        $this->vendorDetail = $vendorDetail;
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Vendor Account Requires Revision - ' . config('app.name'))
                    ->view('emails.rejected')
                    ->with([
                        'vendorDetail' => $this->vendorDetail,
                        'rejectionReason' => $this->rejectionReason,
                    ]);
    }
}