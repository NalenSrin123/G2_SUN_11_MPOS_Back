<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f7fafc
        }

        .wrap {
            max-width: 480px;
            margin: 40px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06)
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            background: #4f46e5;
            color: #fff;
            border-radius: 6px;
            text-decoration: none
        }

        label {
            display: block;
            margin-top: 12px;
            font-size: 14px
        }

        input {
            width: 100%;
            padding: 9px;
            margin-top: 6px;
            border: 1px solid #e6eef8;
            border-radius: 6px;
            box-sizing: border-box
        }

        .note {
            font-size: 13px;
            color: #6b7280;
            margin-top: 10px
        }

        .success {
            color: green
        }

        .error {
            color: #b91c1c
        }
    </style>
</head>

<body>
    <div class="wrap">
        <h2>Reset password</h2>

        <div id="messages"></div>

        <div>
            <label>Email</label>
            <input id="email" type="text" readonly value="{{ $email ?? '' }}">
        </div>

        <form id="resetForm" style="margin-top:12px;">
            <input id="token" type="hidden" value="{{ $token ?? '' }}">
            <label>New password</label>
            <input id="password" type="password" autocomplete="new-password">
            <label>Confirm password</label>
            <input id="password_confirmation" type="password" autocomplete="new-password">
            <button id="doResetBtn" class="btn" style="margin-top:12px">Confirm</button>
        </form>

        <p class="note">Use the link from your email before it expires.</p>
    </div>

    <script>
        const email = document.getElementById('email').value;
        const messages = document.getElementById('messages');

        function showMessage(text, cls = 'error') {
            messages.innerHTML = `<div class='${cls}'>${text}</div>`;
        }

        document.getElementById('doResetBtn').addEventListener('click', async (e) => {
            e.preventDefault();
            showMessage('');
            const payload = {
                email,
                token: document.getElementById('token').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            };
            try {
                const res = await fetch('/api/v1/auth/reset-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!res.ok) {
                    showMessage(json.message || 'Reset failed');
                    return;
                }
                showMessage(json.message || 'Password reset.', 'success');
                setTimeout(() => {
                    window.location.href = json.redirect_url || '/dashboards';
                }, 1200);
            } catch (e) {
                showMessage('Network error');
            }
        });
    </script>
</body>

</html>
