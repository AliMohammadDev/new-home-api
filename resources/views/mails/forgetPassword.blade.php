<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    {{-- style --}}
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        h2 {
            color: #025043;
            border-bottom: 2px solid #025043;
            padding-bottom: 10px;
        }

        p {
            line-height: 1.6;
            font-size: 15px;
        }

        .link-box {
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            word-break: break-all;
            font-size: 14px;
        }

        .button {
            display: inline-block;
            background-color: #025043;
            color: #ffffff !important;
            padding: 12px 22px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 10px;
        }

        .button:hover {
            opacity: 0.9;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Password Reset Request</h2>

        <p>
            We received a request to reset your password.
            Click the button below to proceed.
        </p>

        <div class="link-box">
            http://localhost:5173/reset-password?token={{ $token }}&email={{ $email }}
        </div>

        <a href="http://localhost:5173/reset-password?token={{ $token }}&email={{ $email }}" class="button">
            Reset Password
        </a>

        <p style="margin-top: 20px;">
            This link will expire in <strong>60 minutes</strong>.
            If you didn’t request a password reset, you can safely ignore this email.
        </p>

        <div class="footer">
            © {{ date('Y') }} New Home. All rights reserved.
        </div>
    </div>
</body>

</html>
