<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - E-Commerce Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="{{ asset('photos/favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:20px;
            overflow-x:hidden;

            background-image: url('{{ asset('photos/log.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            font-family: Arial, Helvetica, sans-serif;
        }

        body::before{
            content:'';
            position:absolute;
            inset:0;
            background:rgba(0,0,0,0.70);
            z-index:1;
        }

        .register-container{
            position:relative;
            z-index:2;

            width:100%;
            max-width:520px;

            background:rgba(255,255,255,0.12);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);

            border:1px solid rgba(255,255,255,0.15);
            border-radius:28px;

            padding:40px 32px;

            box-shadow:
                0 10px 40px rgba(0,0,0,0.5),
                0 0 0 1px rgba(255,255,255,0.05);
        }

        .logo-title{
            text-align:center;
            margin-bottom:30px;
        }

        .logo-title h1{
            color:white;
            font-size:2.2rem;
            font-weight:800;
            margin-bottom:10px;
            letter-spacing:1px;
        }

        .logo-title p{
            color:rgba(255,255,255,0.7);
            font-size:14px;
        }

        .success-box{
            background:rgba(34,197,94,0.15);
            border:1px solid rgba(34,197,94,0.4);
            color:#bbf7d0;
            padding:14px;
            border-radius:12px;
            margin-bottom:20px;
            font-size:14px;
        }

        .error-box{
            background:rgba(239,68,68,0.15);
            border:1px solid rgba(239,68,68,0.4);
            color:#fecaca;
            padding:14px;
            border-radius:12px;
            margin-bottom:20px;
            font-size:14px;
        }

        .form-group{
            margin-bottom:18px;
        }

        .form-label{
            display:block;
            color:white;
            font-weight:600;
            margin-bottom:8px;
            font-size:14px;
        }

        .form-input{
            width:100%;
            padding:14px 16px;

            border-radius:14px;
            border:1px solid rgba(255,255,255,0.18);

            background:rgba(255,255,255,0.08);
            color:white;

            font-size:15px;

            transition:0.3s ease;
        }

        .form-input::placeholder{
            color:rgba(255,255,255,0.45);
        }

        .form-input:focus{
            outline:none;
            border-color:#60a5fa;
            background:rgba(255,255,255,0.12);
            box-shadow:0 0 0 4px rgba(59,130,246,0.25);
        }

        .role-wrapper{
            display:flex;
            gap:16px;
            margin-top:10px;
        }

        .role-card{
            flex:1;
            border:1px solid rgba(255,255,255,0.15);
            border-radius:16px;
            padding:16px;
            cursor:pointer;
            transition:0.3s ease;

            background:rgba(255,255,255,0.05);
        }

        .role-card:hover{
            background:rgba(255,255,255,0.10);
            transform:translateY(-2px);
        }

        .role-card.active{
            border-color:#3b82f6;
            background:rgba(59,130,246,0.20);
        }

        .role-card input{
            margin-right:8px;
        }

        .role-title{
            color:white;
            font-weight:700;
            font-size:15px;
        }

        .role-desc{
            color:rgba(255,255,255,0.65);
            font-size:12px;
            margin-top:6px;
        }

        .register-btn{
            width:100%;
            border:none;
            border-radius:16px;

            padding:15px;

            background:linear-gradient(135deg,#2563eb,#1d4ed8);
            color:white;

            font-size:16px;
            font-weight:700;

            cursor:pointer;

            transition:0.3s ease;

            margin-top:10px;
        }

        .register-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 25px rgba(37,99,235,0.4);
        }

        .register-btn:disabled{
            opacity:0.7;
            cursor:not-allowed;
            transform:none;
        }

        .bottom-text{
            text-align:center;
            margin-top:24px;
            color:rgba(255,255,255,0.75);
            font-size:14px;
        }

        .bottom-text a{
            color:#60a5fa;
            text-decoration:none;
            font-weight:700;
        }

        .bottom-text a:hover{
            text-decoration:underline;
        }

        /* Loading Overlay */
        .loading-overlay{
            position:fixed;
            inset:0;
            background:rgba(0,0,0,0.75);

            display:none;
            align-items:center;
            justify-content:center;
            flex-direction:column;

            z-index:99999;
        }

        .loading-overlay.show{
            display:flex;
        }

        .loader{
            width:70px;
            height:70px;
            border:6px solid rgba(255,255,255,0.2);
            border-top-color:#3b82f6;
            border-radius:50%;
            animation:spin 1s linear infinite;
        }

        @keyframes spin{
            to{
                transform:rotate(360deg);
            }
        }

        .loading-text{
            color:white;
            margin-top:20px;
            font-size:18px;
            font-weight:600;
        }

        /* Success Toast */
        .success-toast{
            position:fixed;
            top:20px;
            right:20px;
            background:#16a34a;
            color:white;
            padding:16px 22px;
            border-radius:14px;
            box-shadow:0 10px 30px rgba(0,0,0,0.25);
            z-index:99999;

            transform:translateX(150%);
            transition:0.4s ease;
        }

        .success-toast.show{
            transform:translateX(0);
        }

        @media(max-width:640px){

            .register-container{
                padding:28px 22px;
                border-radius:22px;
            }

            .logo-title h1{
                font-size:1.8rem;
            }

            .role-wrapper{
                flex-direction:column;
            }

            .form-input{
                padding:13px 14px;
            }
        }
    </style>
</head>

<body>

    {{-- Loading Overlay --}}
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loader"></div>
        <div class="loading-text">Creating your account...</div>
    </div>

    {{-- Success Toast --}}
    <div id="successToast" class="success-toast">
        ✅ Registration successful!
    </div>

    <div class="register-container">

        <div class="logo-title">
            <h1>Create Account</h1>
            <p>Join the E-family Platform today</p>
        </div>

        @if(session('success'))
            <div class="success-box">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error-box">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="registerForm" method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Enter your full name"
                    required
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Enter your email"
                    required
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Enter password"
                    required
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirm password"
                    required
                    class="form-input"
                >
            </div>

            <div class="form-group">
                <label class="form-label">Register As</label>

                <div class="role-wrapper">

                    <label class="role-card active" id="customerCard">
                        <input type="radio" name="role" value="user" checked>
                        <span class="role-title">Customer</span>

                        <div class="role-desc">
                            Buy and order products
                        </div>
                    </label>

                    <label class="role-card" id="vendorCard">
                        <input type="radio" name="role" value="vendor">
                        <span class="role-title">Vendor</span>

                        <div class="role-desc">
                            Sell and manage products
                        </div>
                    </label>

                </div>
            </div>

            <button type="submit" class="register-btn" id="registerBtn">
                Create Account
            </button>

        </form>

        <div class="bottom-text">
            Already have an account?
            <a href="{{ route('login') }}">
                Login here
            </a>
        </div>

    </div>

<script>

    // Role Toggle
    const customerCard = document.getElementById('customerCard');
    const vendorCard = document.getElementById('vendorCard');

    customerCard.addEventListener('click', () => {
        customerCard.classList.add('active');
        vendorCard.classList.remove('active');
    });

    vendorCard.addEventListener('click', () => {
        vendorCard.classList.add('active');
        customerCard.classList.remove('active');
    });

    // Loading Submit
    const registerForm = document.getElementById('registerForm');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const registerBtn = document.getElementById('registerBtn');

    registerForm.addEventListener('submit', function () {

        loadingOverlay.classList.add('show');

        registerBtn.disabled = true;
        registerBtn.innerHTML = 'Please wait...';
    });

    // Success Toast
    @if(session('success'))
        const successToast = document.getElementById('successToast');

        setTimeout(() => {
            successToast.classList.add('show');
        }, 300);

        setTimeout(() => {
            successToast.classList.remove('show');
        }, 3500);
    @endif

</script>

</body>
</html>
