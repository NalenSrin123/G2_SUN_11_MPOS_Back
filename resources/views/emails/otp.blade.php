<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>OTP Code</title>
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

        .otp {
            font-size: 28px;
            letter-spacing: 4px;
            background: #f5f5f5;
            padding: 10px 16px;
            display: inline-block;
            margin-top: 8px;
            border-radius: 6px
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Your verification code</h2>
        <p>Use the following one-time password (OTP) to verify your action. This code will expire in 2 minutes.</p>
        <div class="otp">{{ $otp }}</div>
        <p>If you did not request this, please ignore this email.</p>
    </div>
</body>

</html>