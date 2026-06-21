<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333
        }

        .card {
            max-width: 600px;
            margin: 24px auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 8px
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 6px
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Reset your password</h2>
        <p>We received a request to reset the password for your account. Open the secure form below to create a new password.</p>

        @php
        $base = rtrim(config('app.url'), '/');
        $resetUrl = $base . '/reset-password/' . $token . '?email=' . urlencode($user->email);
        @endphp

        <p style="text-align:center;margin:26px 0;"><a class="btn" href="{{ $resetUrl }}" style="background:#4f46e5;padding:14px 20px;border-radius:8px;color:#fff;display:inline-block;font-weight:600">Open Reset Form</a></p>

        <p>This link expires after the configured reset window. If you did not request a password reset, you can safely ignore this email and no changes will be made.</p>

        <hr style="border:none;border-top:1px solid #eee;margin:18px 0">

        <p style="font-size:13px;color:#666">If you're having trouble clicking the "Open Reset Form" button, copy and paste the URL below into your web browser:</p>
        <p style="word-break:break-all"><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>

        <p style="color:#888;margin-top:18px">Regards,<br>Your Team</p>
    </div>
</body>

</html>
