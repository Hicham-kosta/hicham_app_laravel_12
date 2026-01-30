<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vendor Account Requires Revision</title>
</head>
<body>
    <h2>Vendor Account Requires Revision</h2>
    
    <p>Dear Vendor,</p>
    
    <p>Your vendor account for <strong>{{ $vendorDetail->shop_name ?? 'Your Shop' }}</strong> requires some revisions.</p>
    
    @if($rejectionReason)
    <p><strong>Reason:</strong> {{ $rejectionReason }}</p>
    @endif
    
    <p>Please update your details and resubmit for approval.</p>
    
    <p>Thank you,<br>
    {{ config('app.name') }} Support Team</p>
</body>
</html>