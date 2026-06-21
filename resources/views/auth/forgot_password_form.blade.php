<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Forgot Password</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            background: #f3f4f6;
            color: #374151;
            font-family: Arial, Helvetica, sans-serif;
        }

        .page {
            width: min(380px, calc(100vw - 32px));
        }

        .brand {
            width: 58px;
            height: 58px;
            margin: 0 auto 22px;
            border-radius: 999px;
            background: linear-gradient(135deg, #6366f1 0 55%, transparent 56%), #eef2ff;
            box-shadow: inset -10px -10px 0 #eef2ff;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .10);
            padding: 22px;
        }

        p {
            margin: 0 0 18px;
            font-size: 14px;
            line-height: 1.45;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 11px 12px;
            font-size: 14px;
        }

        button {
            display: block;
            margin: 14px 0 0 auto;
            border: 0;
            border-radius: 6px;
            background: #111827;
            color: #fff;
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .08em;
            padding: 12px 18px;
            text-transform: uppercase;
        }

        .status {
            margin-bottom: 16px;
            color: #047857;
            font-size: 14px;
            font-weight: 600;
        }

        .errors {
            margin-bottom: 18px;
            color: #b91c1c;
            font-size: 14px;
        }

        .errors strong {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .errors ul {
            margin: 0;
            padding-left: 18px;
        }
    </style>
</head>

<body>
    <main class="page">
        <section class="card">
            <p>Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="errors">
                    <strong>Whoops! Something went wrong.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" autofocus>
                <button type="submit">Email Password Reset Link</button>
            </form>
        </section>
    </main>
</body>

</html>
