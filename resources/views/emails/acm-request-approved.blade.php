<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ACM Request Approved</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.6; color:#333;">
    <h2>Hello {{ $user }},</h2>

    <p>We’re happy to inform you that your <strong>ACM request has been approved by the admin</strong>.</p>

    <p>The ACM PDF has been generated and is attached to this email for your records.</p>

    <p>
        <a href="{{ url('/') }}" style="display:inline-block; padding:10px 20px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:5px;">
            Login
        </a>
    </p>

    <p>Best regards,<br>{{ config('app.name') }} Team</p>
</body>
</html>
