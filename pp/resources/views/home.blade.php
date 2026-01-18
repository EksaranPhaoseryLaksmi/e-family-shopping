<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>E-Commerce Platform</title>
  <link rel="icon" href="{{ asset('photos/favicon.ico') }}" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <script src="{{ asset('js/script.js') }}"></script>
    <style>
    .hide {
        display: none !important;
    }
    .slideshow {
    position: relative;
    width: 100%;
    height: 700px;
    overflow: hidden;
    }

    .slide {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0;
    transition: opacity 1s ease-in-out;
    }

    .slide.active {
    opacity: 1;
    }
    .success {
      text-align: center;
      padding: 60px 20px;
      background: #f9f9f9;
    }

    .success-text h2 {
      font-size: 2.2rem;
      margin-bottom: 40px;
      color: #333;
      font-weight: 700;
    }

    .template-categories {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }

    .category-btn {
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      color: white;
      border: none;
      padding: 15px 30px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .category-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
    }
    </style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slides = document.querySelectorAll('.slide');
        let current = 0;

        if (slides.length === 0) return;

        setInterval(() => {
            slides[current].classList.remove('active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('active');
        }, 3000); // 3 seconds
    });
</script>

</head>
<body>
    <a href="{{ route('login') }}">Login</a>
    {{-- Home page   --}}
  <header class="header">
    {{-- Logo as a link to home page (using Laravel's url() helper) --}}
        <a href="{{ url('/') }}" class="logo-link">
            <div class="logo">
                {{-- Ensure 'e-commerce_logo.jpg' is in public/photos/ --}}
                <img src="{{ asset('photos/e-commerce_logo.jpg') }}" alt="E-Commerce Logo">
            </div>
        </a>
    <nav class="navbar">
      <ul>
        <li><a href="{{ url('/') }}">Home</a></li>
        <li><a href="{{ url('/categories?type=1') }}">Categories</a></li>
       <li><a href="{{ route('orders.history') }}">Orders</a></li>
        <li class="hide"><a href="{{ url('/templates') }}">Templates</a></li>
        <li><a href="{{ url('/feedback') }}">Feedback</a></li>
        <li><a href="{{ url('/contact') }}">Contact</a></li>
        <li><a href="{{ url('/about-us') }}">About Us</a></li>
        {{-- Removed: Cart Icon Button from Home page --}}
        {{-- <li>
            <button id="cart-icon-button" class="icon-button">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count" class="cart-badge">0</span>
            </button>
        </li> --}}

        {{-- Account/Login/Logout Button with Icon (now consistent across pages) --}}
        <li>
            @guest
                {{-- Show user icon for login if not logged in --}}
                <a href="{{ url('/login') }}" class="icon-button" id="login-icon-button">
                    <i class="fas fa-user-circle"></i>
                </a>
            @else
                {{-- Show logout icon/button if logged in --}}
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="icon-button" id="logout-icon-button">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            @endguest
        </li>
      </ul>
    </nav>
  </header>

  {{-- SCRIPT TO HANDLE SCROLLING FOR CLEAN URLS --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const path = window.location.pathname;
        let targetId = '';

        // Map paths to section IDs
        switch (path) {
            case '/about-us':
                targetId = 'about';
                break;
            case '/templates':
                targetId = 'templates';
                break;
            case '/feedback': // This corresponds to the #success section
                targetId = 'success';
                break;
            case '/contact':
                targetId = 'contact';
                break;
            default:
                // Do nothing or scroll to home if needed
                return;
        }

        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            // Use setTimeout to ensure the page has fully rendered before scrolling
            // This is a common practice to ensure accurate scrolling targets
            setTimeout(() => {
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        }
    });
  </script>
  <!-- home -->
 <section id="home" class="home">
     <div class="slideshow">
         <img src="{{ asset('photos/log.jpg') }}" class="slide active">
         <img src="{{ asset('photos/log1.png') }}" class="slide">
     </div>
 </section>


  <!-- about -->
  <section id="about" class="about">
    <h2>About Us</h2>
    <p>
      Our E-Shopping Website provides a strong solution for online shopping, benefiting USI user customers, and delivery staff.
    </p>
    <p>
      By addressing needs and leveraging modern technology, the platform ensures convenience, efficiency, and future growth.
    </p>
  </section>

  <!-- templates -->
  <section class="hide" id="templates" class="templates">
    <h2>Website Templates</h2>
    <div class="template-grid">
      <div class="template-item">
        <img src="{{ asset('photos/pic_1.jpg') }}" alt="Template 1">
        <p>Professional and modern e-commerce templates...</p>
      </div>
      <div class="template-item">
        <img src="{{ asset('photos/pic_2.jpg') }}" alt="Template 2">
        <p>Responsive and customizable themes...</p>
      </div>
    </div>
    <div class="template-cta">
      <h3>Choose the best website template now</h3>
      <button class="choose-button">Get Started</button>
    </div>
  </section>

  <!-- success -->
  <section id="success" class="success">
    <div class="success-text">
      <h2>Templates Shopping Built for Success</h2>
    </div>

    <div class="template-categories">
      <button class="category-btn" onclick="window.location.href='{{ url('/categories?type=1') }}'">
        Skin Care
      </button>

      <button class="category-btn" onclick="window.location.href='{{ url('/categories?type=2') }}'">
        Clothing
      </button>

      <button class="category-btn" onclick="window.location.href='{{ url('/categories?type=3') }}'">
        Accessories
      </button>

      <button class="category-btn" onclick="window.location.href='{{ url('/categories?type=4') }}'">
        Educational Supplies
      </button>
    </div>
  </section>

  <!-- feedback -->
<section id="feedback" class="feedback">
    <h2>The Customer's Feedback</h2>
    <div class="testimonials">
        {{-- Testimonial 1 --}}
        <div class="testimonial">
            <p>"The paper quality is excellent—no ink bleed! It arrived carefully packed in a protective sleeve. This seller always delivers fast. Definitely the best place for serious stationary."</p>
            <div class="profile">
                <img src="{{ asset('photos/profile1.jpg') }}" alt="Profile">
                <div class="profile-info">
                    <span>Prak Sokly</span>
                    <p class="subtitle">High-Quality Daily Journal</p>
                </div>
            </div>
        </div>

        {{-- Testimonial 2 (Duplicate for unique content) --}}
        <div class="testimonial">
            <p>"Really cool design and the cotton is super soft. I only wish the neckband was a little tighter; it feels like it might stretch over time. But overall, a great stylish shirt."</p>
            <div class="profile">
                {{-- Replace with a different image for this testimonial --}}
                <img src="{{ asset('photos/profile2.jpg') }}" alt="Profile 2">
                <div class="profile-info">
                    <span>Rithy Mean</span>
                    <p class="subtitle">Oversized Graphic T-Shirt</p>
                </div>
            </div>
        </div>

        {{-- Testimonial 3 (Duplicate for unique content) --}}
        <div class="testimonial">
            <p>

"Absolutely obsessed with this cream! It gives my skin a lovely glow without being greasy. It absorbed in seconds. The fact that I can find reliable local sellers on this platform is a huge plus."</p>
            <div class="profile">
                {{-- Replace with another different image for this testimonial --}}
                <img src="{{ asset('photos/profile3.jpg') }}" alt="Profile 3">
                <div class="profile-info">
                    <span>Sok Theavy</span>
                    <p class="subtitle">Brightening Vitamin C Cream</p>
                </div>
            </div>
        </div>
    </div>
</section>

  <!-- contact -->
  <section id="contact" class="contact">
    <h2>Contact Us</h2>
    <div class="icon">
      <img src="{{ asset('photos/call.jpg') }}" alt="Call">
      <img src="{{ asset('photos/message.png') }}" alt="Message">
      <img src="{{ asset('photos/bubble-message.jpg') }}" alt="Chat">
    </div>
    <div class="contact-options">
      <div class="contact-box"><h3>Call us</h3><p>+855 11 873 927</p></div>
      <div class="contact-box"><h3>Email us</h3><p>laksmi4554@gmail.com </br> sovannrith.ouk12@gmail.com</p></div>
      <div class="contact-box"><h3>Chat with us</h3><p>9:00am-9:00pm</p></div>
    </div>
  </section>

  <!-- footer -->
  <footer class="footer">
    <ul>
      <li>@ Shopping E-Commerce V1.0.0</li>
    </ul>
    <ul>
      <li>LOCATION</li>
      <li><img src="{{ asset('photos/combodia.jpg') }}" alt="Cambodia"> Cambodia</li>
      <li>Phnom Penh</li>
    </ul>
  </footer>

  <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
