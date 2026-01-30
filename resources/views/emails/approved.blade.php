<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vendor Account Approved</title>
</head>
<body>
    <h2>Vendor Account Approved</h2>
    
    <p>Dear Vendor,</p>
    
    <p>Your vendor account for <strong>{{ $vendorDetail->shop_name ?? 'Your Shop' }}</strong> has been approved!</p>
    
    <p>You can now start selling on our platform.</p>
    
    <p>Thank you,<br>
    {{ config('app.name') }}</p>
</body>
</html>