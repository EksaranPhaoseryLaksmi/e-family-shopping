<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'E-Commerce Platform')</title>
  <link rel="icon" href="{{ asset('photos/favicon.ico') }}" type="image/x-icon">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  @stack('styles')
</head>
<body>
  {{-- Navbar/Header --}}
  <header class="header">
    <a href="{{ url('/') }}" class="logo-link">
      <div class="logo">
        <img src="{{ asset('photos/e-commerce_logo.jpg') }}" alt="E-Commerce Logo">
      </div>
    </a>
    <nav class="navbar">
      <ul>
        <li><a href="{{ url('/') }}">Home</a></li>
        <li><a href="{{ url('/categories') }}">Categories</a></li>
        <li><a href="{{ url('/about-us') }}">About Us</a></li>
        <li><a href="{{ url('/templates') }}">Templates</a></li>
        <li><a href="{{ url('/feedback') }}">Feedback</a></li>
        <li><a href="{{ url('/contact') }}">Contact</a></li>
{{-- New Icon Buttons for Cart and Account --}}
        <li>
            <button id="cart-icon-button" class="icon-button">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count" class="cart-badge">0</span>
            </button>
        </li>
      </ul>
    </nav>
  </header>
  <br/>
  {{-- Main Page Content --}}
  <main>
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="footer">
    <ul>
      <li>ABOUT</li>
      <li>Add-ons</li>
      <li>Feature</li>
      <li>Pricing</li>
      <li>Success Story</li>
      <li>Customer</li>
    </ul>
    <ul>
      <li>PLATFORM</li>
      <li>Adobe Commerce</li>
      <li>Shopify</li>
      <li>Wix</li>
    </ul>
    <ul>
      <li>RESOURCE</li>
      <li>Blog</li>
      <li>Knowledge Base</li>
      <li>Privacy Policy</li>
    </ul>
    <ul>
      <li>LOCATION</li>
      <li><img src="{{ asset('photos/combodia.jpg') }}" alt="Cambodia"> Cambodia</li>
      <li>Phnom Penh</li>
    </ul>
  </footer>

  {{-- Global Scripts --}}
  <script src="{{ asset('js/script.js') }}"></script>
  @stack('scripts')

</body>
</html>
