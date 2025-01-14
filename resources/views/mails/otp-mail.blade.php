<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');

        body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #6C3E93;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .content {
            padding: 30px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 8px;
            margin: 30px 0;
            padding: 20px;
            background-color: #f0f8ff;
            border: 2px dashed #6C3E93;
            border-radius: 8px;
            color: #6C3E93;
        }
        .expiration {
            text-align: center;
            color: #e74c3c;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #e8f4fd;
            border-left: 4px solid #6C3E93;
            padding: 15px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4a90e2;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            background-color: #f8f8f8;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666666;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                margin: 0 !important;
            }
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
{{--        <img src="{{ $message->embed(public_path('assets/images/logo_long.png')) }}" alt="AIFUE Logo" style="max-width: 200px; height: auto;">--}}
    </div>
    <div class="content">
        <h1 style="text-align: center; color: #4a90e2;">Your One-Time Password (OTP)</h1>
        <p>Dear User,</p>
        <p>Your one-time password (OTP) for authentication. Please use the following code to complete your action:</p>
        <div class="otp-code">
            {{ $data['body'] }}
        </div>
        <p class="expiration">⏰ This code will expire in 10 minutes</p>
        <div class="info-box">
            <p><strong>Important:</strong></p>
            <ul>
                <li>If you did not request this OTP, please ignore this email and contact our support team immediately.</li>
                <li>For security reasons, please do not share this code with anyone.</li>
                <li>Our team will never ask for your OTP via phone or email.</li>
            </ul>
        </div>
        <p>If you're having trouble, please don't hesitate to reach out to our support team.</p>
        <p>Thank you for using our service.</p>
        <p>Best regards,<br>AIFUE Career Services Center</p>
        <a href="#" class="button">Visit Our Help Center</a>
    </div>
    <div class="footer">
        <p>This is an automated message, please do not reply to this email.</p>
        <p>© {{ date('Y') }} AIFUE Career Services Center. All rights reserved.</p>
        <p>If you have any questions, please contact our support at <a href="mailto:support@yourcompany.com" style="color: #4a90e2;">support@yourcompany.com</a></p>
    </div>
</div>
</body>
</html>

