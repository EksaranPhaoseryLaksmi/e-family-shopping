<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="icon" href="{{ asset('photos/favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Custom styles for background and overlay */
        body {
            /* Fallback dark background */
            background-color: #1a1a1a;
            color: white; /* Ensure text is visible */
            
            /* Background Image Properties - UPDATED TO USE LARAVEL ASSET */
            background-image: url('{{ asset('photos/log.jpg') }}'); /* REPLACE 'your_chosen_image.jpg' WITH YOUR ACTUAL FILENAME */
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed; /* Keep image fixed */

            position: relative; /* Needed for the pseudo-element overlay */
            min-height: 100vh; /* Ensure body covers full viewport height */
            
            /* Flexbox for centering is already in Tailwind classes on body */
            z-index: 0; /* Ensure body is behind any overlay */
        }

        /* Dark overlay for readability */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6); /* 60% opaque black overlay */
            z-index: 1; /* Place overlay above background image */
        }

        /* Style for the form container */
        .auth-form-container {
            /* Semi-transparent background for the form itself */
            background-color: rgba(255, 255, 255, 0.1); 
            border-radius: 1.5rem; /* Increased border-radius for more rounded look, Tailwind: rounded-3xl equivalent */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7); /* Stronger, darker shadow */
            position: relative; /* Position above the overlay */
            z-index: 2; /* Place form above the overlay */
            backdrop-filter: blur(12px); /* Stronger frosted glass effect */
            -webkit-backdrop-filter: blur(12px); /* For Safari */
            border: 1px solid rgba(255, 255, 255, 0.2); /* Subtle light border */
            padding: 2.5rem; /* Increased padding */
            max-width: 28rem; /* Increased max-width, Tailwind: max-w-xl equivalent */
            width: 95%; /* Responsive width */
        }

        .auth-form-container h2 {
            font-size: 2.25rem; /* Increased font size for heading, Tailwind: text-4xl equivalent */
            margin-bottom: 2rem; /* Increased margin-bottom */
            color: white; /* Ensure heading color is white */
        }

        .auth-form-container label {
            color: rgba(255, 255, 255, 0.8); /* Lighter label color */
            font-weight: 500; /* Medium font weight */
            margin-bottom: 0.5rem; /* Standard margin-bottom */
            display: block; /* Ensure it takes full width */
        }

        .auth-form-container input {
            background-color: rgba(255, 255, 255, 0.08); /* More subtle input background */
            border: 1px solid rgba(255, 255, 255, 0.3); /* Lighter border */
            border-radius: 0.625rem; /* Increased border-radius, ~rounded-lg equivalent */
            color: white; /* Input text color */
            padding: 0.75rem 1rem; /* Increased padding */
            font-size: 1.125rem; /* Increased font size */
            transition: all 0.3s ease;
        }

        .auth-form-container input::placeholder {
            color: rgba(255, 255, 255, 0.5); /* Lighter placeholder text */
        }

        .auth-form-container input:focus {
            outline: none;
            border-color: #fdd835; /* Highlight border on focus */
            box-shadow: 0 0 10px rgba(253, 216, 53, 0.6); /* Yellow glow on focus */
        }

        .auth-form-container button {
            background-color: #fdd835;
            color: black;
            padding: 0.875rem 1.5rem; /* Increased padding */
            border-radius: 0.75rem; /* More rounded */
            font-weight: bold;
            font-size: 1.25rem; /* Increased font size */
            margin-top: 1.5rem; /* Increased margin-top */
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .auth-form-container button:hover {
            background-color: #e0c030;
            transform: translateY(-3px); /* More lift on hover */
        }

        .auth-form-container p.mt-4 {
            font-size: 1.1rem; /* Slightly larger text */
            color: rgba(255, 255, 255, 0.7); /* Lighter text color */
            margin-top: 1.5rem; /* Adjusted margin */
        }

        .auth-form-container p.mt-4 a {
            color: #fdd835; /* Yellow links */
            font-weight: 600; /* Stronger link text */
        }

        .auth-form-container p.mt-4 a:hover {
            color: white;
        }

        /* Error messages */
        .auth-form-container .bg-red-100 {
            background-color: rgba(255, 99, 71, 0.2); /* Muted red with transparency */
            color: #ff6347; /* Tomato red text */
            border: 1px solid #ff6347; /* Matching border */
            font-size: 1rem; /* Slightly larger error text */
            padding: 1rem; /* More padding */
            border-radius: 0.75rem; /* More rounded */
            margin-bottom: 1.5rem; /* Adjusted margin */
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 768px) {
            .auth-form-container {
                padding: 2rem; /* Adjusted padding */
                border-radius: 1rem; /* Adjusted border-radius */
            }
            .auth-form-container h2 {
                font-size: 2rem; /* Adjusted font size */
                margin-bottom: 1.5rem;
            }
            .auth-form-container input {
                padding: 0.625rem 0.875rem; /* Adjusted padding */
                font-size: 1rem; /* Adjusted font size */
            }
            .auth-form-container button {
                padding: 0.75rem 1.25rem;
                font-size: 1.125rem;
                margin-top: 1rem;
            }
            .auth-form-container p.mt-4 {
                font-size: 0.95rem;
                margin-top: 1rem;
            }
            .auth-form-container .bg-red-100 {
                padding: 0.8rem;
                font-size: 0.9rem;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">

    <div class="auth-form-container">
        <h2>Login</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Email</label>
                <input type="email" name="email" required class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Password</label>
                <input type="password" name="password" required class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
            </div>

            <button type="submit" class="w-full">Login</button>
        </form>

        <p class="mt-4 text-center">
            Don't have an account?
            <a href="{{ route('register') }}">Register here</a>
        </p>
    </div>

</body>
</html>
