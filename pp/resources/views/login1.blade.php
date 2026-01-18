<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>Account</title>
    <link rel="icon" href="{{ asset('photos/favicon.ico') }}" type="image/x-icon">
</head>
<body>
    <header class="header">
        {{-- Logo as a link to home page (using Laravel's url() helper) --}}
        <a href="{{ url('/') }}" class="logo-link">
            <div class="logo">
                {{-- Ensure 'e-commerce_logo.jpg' is in public/photos/ --}}
                <img src="{{ asset('photos/e-commerce_logo.jpg') }}" alt="E-commerce Logo">
            </div>
        </a>
    </header>

    <div class="container">
        <div class="login-box">

            <!-- LOGIN FORM -->
            <div id="login-form">
                <h1>LOGIN</h1>
                <p class="subtitle">Please enter your Email and Password:</p>
                <form id="login-form-element">
                    <input type="email" id="login-email" placeholder="Email" required /><br />
                    <div class="password-container">
                        <input type="password" id="login-password" placeholder="Password" required />
                        <span class="forgot-password" id="show-forgot-password">Forget password?</span>
                    </div>
                    <button type="submit" id="login-btn">LOGIN</button>
                </form>
                <p class="signup">Don't have an account?
                    <a href="#" id="show-register" class="hover-scale">Register.</a>
                </p>
            </div>

            <!-- REGISTER FORM -->
            <div id="register-form" style="display: none;">
                <h1>REGISTER</h1>
                <p class="subtitle">Please fill in the details below:</p>
                <form id="register-form-element">
                    <input type="text" id="firstName" placeholder="First Name" required /><br />
                    <div class="error-message" id="firstName-error"></div>

                    <input type="text" id="lastName" placeholder="Last Name" required /><br />
                    <div class="error-message" id="lastName-error"></div>

                    <input type="email" id="register-email" placeholder="Email" required /><br />
                    <input type="password" id="register-password" placeholder="Password" required /><br />
                    <button type="submit" id="register-btn">Create Account</button>
                </form>
                <p class="signup">Already have an account?
                    <a href="#" id="show-login" class="hover-scale">Back to Login</a>
                </p>
            </div>

            <!-- FORGOT PASSWORD FORM -->
            <div id="forgot-password-form" style="display: none;">
                <h1>RESET PASSWORD</h1>
                <p class="subtitle">Enter your email to receive a password reset link:</p>
                <form id="forgot-password-form-element">
                    <input type="email" id="reset-email" placeholder="Email" required /><br />
                    <button type="submit" id="reset-password-btn">Send Reset Link</button>
                </form>
                <p class="signup">
                    <a href="#" id="back-to-login-from-reset" class="hover-scale">Back to Login</a>
                </p>
            </div>

        </div>
    </div>

    <script type="module" src="{{ asset('js/login.js') }}"></script>
</body>
</html>
