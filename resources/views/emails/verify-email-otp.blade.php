<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your IAF Apps Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px 0;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Verify Your IAF Apps Account</h2>
        </div>
        <div class="content">
            <p>Dear {{ $notifiable->name }},</p>
            <p>Thank you for registering with IAF apps! To complete the verification process, please use the One-Time Password (OTP) provided below:</p>
            <div class="otp">{{ $notifiable->otp }}</div>
            <p>This OTP is valid for a limited time. Please enter it on the verification page to confirm your account.</p>
            <p>If you did not request this verification, please ignore this email or contact our support team for assistance.</p>
            <p>Best regards,<br>The IAF Apps Team</p>
        </div>
        <div class="footer">
            <p>This is an automated message, please do not reply directly to this email.</p>
            <p>&copy; {{ date('Y') }} IAF Apps. All rights reserved.</p>
        </div>
    </div>
</body>
</html>