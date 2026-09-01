<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $replySubject }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1f2937;">
    <p>Dear {{ $contactMessage->name }},</p>

    <p>{!! nl2br(e($replyMessage)) !!}</p>

    <p style="margin-top: 24px;">Best regards,<br>{{ config('app.name', 'GIHQS Team') }}</p>
</body>
</html>
