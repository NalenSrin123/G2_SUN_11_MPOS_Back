<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Verify Email</title>
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
            background: #2b3440;
            color: #fff;
            text-decoration: none;
            border-radius: 6px
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Hello!</h2>
        <p>Please click the button below to verify your email address.</p>

        <p style="text-align:center;margin:26px 0;"><a class="btn" href="{{ $verifyUrl }}">Verify Email Address</a></p>

        <p>If you did not create an account, no further action is required.</p>

        <hr style="border:none;border-top:1px solid #eee;margin:18px 0">

        <p style="font-size:13px;color:#666">If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:</p>
        <p style="word-break:break-all"><a href="{{ $verifyUrl }}">{{ $verifyUrl }}</a></p>

        <p style="color:#888;margin-top:18px">Regards,<br>Laravel</p>
    </div>
</body>

</html>