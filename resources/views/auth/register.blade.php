<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>

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

        .auth-card {
            background: #111827;
            padding: 30px;
            width: 380px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.5);
            color: #fff;
        }

        .auth-card h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 14px;
            position: relative;
        }

        .auth-card input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #374151;
            background: #020617;
            color: #fff;
            outline: none;
        }

        .auth-card input:focus {
            border-color: #3b82f6;
        }

        .error-input {
            border-color: #ef4444 !important;
        }

        .error-text {
            color: #f87171;
            font-size: 13px;
            margin-top: 4px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 13px;
            color: #9ca3af;
        }

        .auth-card button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #3b82f6;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 5px;
        }

        .auth-card button:hover {
            background: #2563eb;
        }

        .link {
            margin-top: 15px;
            text-align: center;
        }

        .link a {
            color: #60a5fa;
            text-decoration: none;
            font-size: 14px;
        }

        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="auth-card">
    <h2>Create Account</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <input type="text" name="name" placeholder="Full Name"
                   value="{{ old('name') }}"
                   class="@error('name') error-input @enderror">
            @error('name')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <input type="email" name="email" placeholder="Email"
                   value="{{ old('email') }}"
                   class="@error('email') error-input @enderror">
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <input type="password" id="password" name="password" placeholder="Password"
                   class="@error('password') error-input @enderror">
            <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
            @error('password')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <input type="password" id="password_confirmation" name="password_confirmation"
                   placeholder="Confirm Password">
            <span class="toggle-password"
                  onclick="togglePassword('password_confirmation')">👁️</span>
        </div>

        <button type="submit">Register</button>
    </form>

    <div class="link">
        <a href="{{ route('login') }}">Already have an account? Login</a>
    </div>
</div>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>

</body>
</html>
