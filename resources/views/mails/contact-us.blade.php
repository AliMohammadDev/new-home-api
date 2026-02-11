<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رسالة جديدة - المنزل الحديث</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction: rtl;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding: 40px 10px;">

                <table role="presentation" width="100%"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #f0f0f0;">

                    <tr>
                        <td align="center" style="background-color: #025043; padding: 20px 20px;">
                            <img src="https://res.cloudinary.com/dzvrf9xe3/image/upload/v1770817128/home-logo-white_c2et5l_lng6kl.png"
                                alt="المنزل الحديث" width="200"
                                style="display: block; outline: none; border: none; text-decoration: none;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 30px; text-align: right;" dir="rtl">
                            <h1
                                style="color: #025043; font-size: 22px; margin: 0 0 15px 0; font-weight: bold; text-align: right;">
                                وصلتك رسالة جديدة</h1>

                            <p
                                style="color: #666; font-size: 15px; line-height: 1.6; margin-bottom: 25px; text-align: right;">
                                قام أحد العملاء بإرسال استفسار عبر نموذج الاتصال في المتجر.
                            </p>

                            <table role="presentation" width="100%"
                                style="background-color: #fcfcfc; border: 1px solid #f0f0f0; border-radius: 12px; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 20px; text-align: right;">
                                        <div style="margin-bottom: 15px;">
                                            <span
                                                style="font-size: 13px; color: #025043; display: block; margin-bottom: 4px;">الاسم
                                                الكامل</span>
                                            <strong
                                                style="font-size: 16px; color: #0a0a0a;">{{ $name }}</strong>
                                        </div>

                                        <div>
                                            <span
                                                style="font-size: 13px; color: #025043; display: block; margin-bottom: 4px;">البريد
                                                الإلكتروني</span>
                                            <a href="mailto:{{ $email }}"
                                                style="font-size: 16px; color: #0a0a0a; text-decoration: none; font-weight: 600;">{{ $email }}</a>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div style="margin-bottom: 30px;">
                                <span
                                    style="font-size: 14px; color: #025043; font-weight: bold; display: block; margin-bottom: 10px; border-right: 3px solid #025043; padding-right: 10px;">نص
                                    الرسالة:</span>
                                <div
                                    style="background-color: #ffffff; padding: 15px; border: 1px solid #f0f0f0; border-radius: 8px; color: #0a0a0a; font-size: 15px; line-height: 1.8; text-align: right;">
                                    {{ $messageText }}
                                </div>
                            </div>

                            <div style="text-align: center; margin-top: 35px;">
                                <a href="mailto:{{ $email }}"
                                    style="background-color: #025043; color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 15px; display: inline-block;">الرد
                                    المباشر</a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center"
                            style="padding: 25px; background-color: #fafafa; border-top: 1px solid #f0f0f0;">
                            <p style="margin: 0; font-size: 13px; color: #666;">© {{ date('Y') }} <strong>المنزل
                                    الحديث</strong>. جميع الحقوق محفوظة.</p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>

</html>
