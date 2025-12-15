<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Contact Message</title>
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
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        h2 {
            color: #025043;
            border-bottom: 2px solid #025043;
            padding-bottom: 8px;
        }

        p {
            line-height: 1.6;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .message-box {
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            white-space: pre-wrap;
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
        <h2>New Contact Message</h2>

        <p><span class="label">Name:</span> {{ $name }}</p>
        <p><span class="label">Email:</span> {{ $email }}</p>

        <p class="label">Message:</p>
        <div class="message-box">{{ $messageText }}</div>

        <div class="footer">
            This email was sent from your website contact form.
        </div>
    </div>
</body>

</html>
