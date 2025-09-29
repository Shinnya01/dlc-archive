<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pending Approval</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.6; color:#333;">
    <h2>Hello {{ $userName }},</h2>

    <p>Your request is currently <strong>waiting for admin approval</strong>. You will be notified via email once it has been reviewed.</p>

    <p>If you have any questions, please contact our support team.</p>

    <p>Best regards,<br>{{ config('app.name') }} Team</p>
</body>
</html>
