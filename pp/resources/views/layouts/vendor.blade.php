<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $title ?? 'Vendor Dashboard' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="{{ asset('photos/favicon.ico') }}">
  <style>
        /* Custom styles for background and overlay on payment page */
        body {
            /* Fallback dark background */
            background-color: rgb(252, 248, 248);

            /* Background Image Properties */
            background-image: url('{{ asset('photos/pay.jpg') }}'); /* REPLACE 'your_chosen_image.jpg' WITH YOUR ACTUAL FILENAME */
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed; /* Keep image fixed */

            position: relative; /* Needed for the pseudo-element overlay */
            min-height: 100vh; /* Ensure body covers full viewport height */
            z-index: 0; /* Ensure body is behind any overlay */
        }
    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        color: #374151;
        margin: 0 2px;
        border-radius: 0.375rem;
    }
    .pagination .active .page-link {
        background-color: #2563eb;
        color: white;
        border-color: #2563eb;
    }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

  <!-- ✅ HEADER -->
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
      <div class="flex items-center space-x-6">
        <a href="{{ route('vendor.dashboard')}}" class="text-xl font-bold text-blue-600">Home</a>
        <a href="{{ route('vendors.user') }}" class="text-gray-700 hover:text-blue-600">Vendor</a>
        <a href="{{ route('vendors.orders') }}" class="text-gray-700 hover:text-blue-600">Order</a>
      </div>
      
      <div>
        @auth
          <div class="flex items-center space-x-3">
            <span class="text-gray-700">👤 {{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="text-red-600 hover:underline">Logout</button>
            </form>
          </div>
        @else
          <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login</a>
        @endauth
      </div>
    </div>
  </header>

  <!-- ✅ MAIN CONTENT -->
  <main class="flex-1">
    @yield('content')
  </main>

  

</body>
</html>
