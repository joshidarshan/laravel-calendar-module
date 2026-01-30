<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            height: 100vh;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #111827;
            padding: 30px;
            width: 360px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.5);
            color: #fff;
        }

        .login-card h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .login-card input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #374151;
            background: #020617;
            color: #fff;
            outline: none;
        }

        .login-card input:focus {
            border-color: #3b82f6;
        }

        .login-card button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #3b82f6;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-card button:hover {
            background: #2563eb;
        }

        .login-card .link {
            margin-top: 15px;
            text-align: center;
        }

        .login-card .link a {
            color: #60a5fa;
            text-decoration: none;
            font-size: 14px;
        }

        .login-card .link a:hover {
            text-decoration: underline;
        }

        .success-msg {
            background: #064e3b;
            color: #6ee7b7;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="login-card">
    <h2>Login</h2>

    @if(session('success'))
        <div class="success-msg">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>

    <div class="link">
        <a href="{{ route('register') }}">Create new account</a>
    </div>
</div>

</body>
</html>
