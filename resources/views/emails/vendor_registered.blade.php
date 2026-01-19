<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
            text-align: left;
        }
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 14px;
            background: #fafafa;
            color: #666;
        }
        a.button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4f46e5;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 15px;
        }
        a.button:hover {
            background-color: #3730a3;
        }
    </style>
</head>
<body>
<h2>Hello {{ $vendor->name }}</h2>

<p>Thanks for registering as Vendor at {{ config('app.name') }}. 
    Your account is pending for approval. Please confirm your email to activate your account.</p>

<a href="{{ route('vendor.confirm', $code) }}" class="btn btn-primary">Confirm Email</a>
    </body>
    </html>