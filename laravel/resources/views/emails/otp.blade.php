<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration OTP</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Registration OTP - Svasthya</h2>
        <p>Hello,</p>
        <p>Thank you for registering with Svasthya. Please use the following OTP to complete your registration:</p>
        <div style="background-color: #f4f4f4; padding: 20px; text-align: center; margin: 20px 0; border-radius: 5px;">
            <h1 style="color: #2c3e50; margin: 0; font-size: 32px; letter-spacing: 5px;">{{ $otp }}</h1>
        </div>
        <p>This OTP will expire in 10 minutes.</p>
        <p>If you did not request this OTP, please ignore this email.</p>
        <p style="margin-top: 30px; color: #7f8c8d; font-size: 12px;">
            Best regards,<br>
            Svasthya Team
        </p>
    </div>
</body>
</html>

