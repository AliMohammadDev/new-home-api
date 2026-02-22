<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - New Home</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction: ltr;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding: 40px 10px;">

                <table role="presentation" width="100%"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #f0f0f0;">

                    <tr>
                        <td align="center" style="background-color: #025043; padding: 40px 20px;">
                            <img src="https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770817128/home-logo-white_c2et5l_lng6kl.png"
                                alt="New Home Store" width="200"
                                style="display: block; outline: none; border: none; text-decoration: none;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 30px; text-align: left;">
                            <h1 style="color: #025043; font-size: 24px; margin: 0 0 20px 0; font-weight: bold;">Password
                                Reset Request</h1>

                            <p style="color: #555; font-size: 16px; line-height: 1.6; margin-bottom: 25px;">
                                Hello, <br>
                                We received a request to reset the password for your account. No changes have been made
                                yet.
                            </p>

                            <p style="color: #555; font-size: 16px; margin-bottom: 30px;">
                                You can reset your password by clicking the button below:
                            </p>

                            <div style="text-align: center; margin-bottom: 35px;">
                                <a href="https://almanzel-alhadith.com/reset-password?token={{ $token }}&email={{ $email }}"
                                    style="background-color: #025043; color: #ffffff; padding: 16px 35px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; display: inline-block; box-shadow: 0 4px 12px rgba(2, 80, 67, 0.2);">
                                    Reset Password
                                </a>
                            </div>

                            <div
                                style="background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
                                <p style="margin: 0 0 10px 0; font-size: 13px; color: #999;">If the button above doesn't
                                    work, copy and paste this URL into your browser:</p>
                                <p style="margin: 0; font-size: 13px; word-break: break-all; color: #025043;">
                                    https://almanzel-alhadith.com/reset-password?token={{ $token }}&email={{ $email }}
                                </p>
                            </div>

                            <p style="color: #777; font-size: 14px; border-top: 1px solid #f0f0f0; padding-top: 20px;">
                                <strong>Note:</strong> This link will expire in <strong>60 minutes</strong>. <br>
                                If you did not request this, please ignore this email.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center"
                            style="padding: 25px; background-color: #fafafa; border-top: 1px solid #f0f0f0;">
                            <p style="margin: 0; font-size: 13px; color: #666; font-weight: bold;">© {{ date('Y') }}
                                New Home Store. All rights reserved.</p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>

</html>
