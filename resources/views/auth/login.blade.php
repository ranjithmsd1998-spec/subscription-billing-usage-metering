<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Subscription Billing</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            background: #f5f7fb;
            color: #172033;
        }
        .card {
            width: 100%;
            max-width: 430px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 34px;
            box-shadow: 0 15px 45px rgba(15, 23, 42, .08);
        }
        .brand { margin-bottom: 28px; }
        .brand h1 { margin: 0; font-size: 25px; color: #111827; }
        .brand p { margin: 7px 0 0; color: #6b7280; font-size: 14px; }
        label { display: block; margin: 0 0 7px; font-size: 13px; font-weight: 600; }
        input {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            margin-bottom: 18px;
        }
        input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, .10); }
        button {
            width: 100%;
            border: 0;
            border-radius: 8px;
            padding: 12px;
            background: #2563eb;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }
        .alert {
            padding: 11px 13px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
        }
        .success { background: #ecfdf5; color: #047857; }
        .error { background: #fef2f2; color: #b91c1c; }
        .field-error { margin: -12px 0 14px; color: #b91c1c; font-size: 12px; }
        .demo { margin-top: 18px; color: #6b7280; font-size: 12px; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <h1>Subscription Billing</h1>
            <p>Sign in to manage usage and billing.</p>
        </div>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="email"
                required
                autofocus
            >

            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror

            <label for="password">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                autocomplete="current-password"
                required
            >

            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror

            <button type="submit">Sign In</button>
        </form>

        <div class="demo">
            Demo admin: <strong>admin@example.com</strong> / <strong>password</strong>
        </div>
    </div>
</body>
</html>
