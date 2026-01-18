{{-- resources/views/payment.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment - E-Commerce Platform</title>
    <link rel="icon" href="{{ asset('photos/favicon.ico') }}" type="image/x-icon">

    {{-- CSS files - assuming they are in public/css/ --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/payment.css') }}">

    <style>
        /* Custom styles for background and overlay on payment page */
        body {
            /* Fallback dark background */
            background-color: rgb(252, 248, 248);
            color: white; /* Ensure text is visible */

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
     .hide {
            display: none !important;
        }
    .pay-now-button {
        display: none;
    }

    </style>
</head>
<body>
    <header class="header">
        {{-- Logo as a link to home page --}}
        <a href="{{ url('/') }}" class="logo-link">
            <div class="logo">
                {{-- Ensure 'e-commerce_logo.jpg' is in public/photos/ --}}
                <img src="{{ asset('photos/e-commerce_logo.jpg') }}" alt="E-commerce Logo">
            </div>
        </a>
        <nav class="navbar">
            <ul>
                {{-- Navigation links using Laravel's url() helper --}}
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/categories?type=1') }}">Categories</a></li>
                <li><a href="{{ url('/#about') }}">About Us</a></li> {{-- Assuming #about is on the home page --}}
                <li><a href="{{ url('/#templates') }}">Templates</a></li> {{-- Assuming #templates is on the home page --}}
                <li><a href="{{ url('/#success') }}">Feedback</a></li> {{-- Assuming #success is on the home page --}}
                <li><a href="{{ url('/#contact') }}">Contact</a></li> {{-- Assuming #contact is on the home page --}}

                {{-- Icon Buttons for Cart and Account (consistent with categories page) --}}
                <li>
                    <button id="cart-icon-button-payment" class="icon-button">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cart-count" class="cart-badge">0</span>
                    </button>
                </li>
                <li>
                    @guest
                        <a href="{{ url('/login') }}" class="icon-button" id="login-icon-button-payment">
                            <i class="fas fa-user-circle"></i>
                        </a>
                    @else
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="icon-button" id="logout-icon-button-payment">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    @endguest
                </li>
            </ul>
        </nav>
    </header>

    <main class="payment-main-container">
        <section class="checkout-form">
            <h2>Express Checkout</h2>
            <div class="express-buttons">
                {{-- Bank button images - ensure they are in public/photos/ --}}
                <button class="bank-button acleda" data-bank="acleda"><img src="{{ asset('photos/ACLEDA.jpg') }}" alt="ACLEDA Bank"></button>
                <button class="bank-button aba" data-bank="aba"><img src="{{ asset('photos/ABA.jpg') }}" alt="ABA Bank"></button>
            </div>
            <p class="or-separator">OR</p>

            <div class="contact-section" style="display: none;">
            <h3>Contact</h3>
            <input type="email" id="contact-email" placeholder="Email">
            <label class="checkbox-container">
                <input type="checkbox" checked>
                Email me with new and offers
            </label>
            </div>
            <div class="delivery-section">
                <h3>Delivery</h3>
                <p>It'll be right in front of your house.</p>
                <select class="country-region" id="delivery-country">
                    <option value="">Choose your country</option>
                    <option value="cambodia">Cambodia</option>
                    {{-- Add more countries as needed --}}
                </select>
                <div class="name-inputs">
                    <input type="text" id="delivery-first-name" placeholder="First Name">
                    <input type="text" id="delivery-last-name" placeholder="Last Name">
                </div>
                <input type="text" id="delivery-address" placeholder="Address">
                <input type="text" id="delivery-apartment" placeholder="Apartment, suite, etc. (optional)">
                <input type="text" id="delivery-city" placeholder="City">
            </div>
            <div class="hide">
            <div class="payment-section">
                <h3>Payment</h3>
                <p>All transactions are secure and encrypted.</p>
                <div class="credit-card-section">
                    <h4>Credit Card</h4>
                    <div class="card-icons">
                        {{-- Added common card icons as Font Awesome --}}
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fab fa-cc-amex"></i>
                        <i class="fab fa-cc-discover"></i>
                    </div>
                    <input type="text" id="card-number" placeholder="Card number (e.g., 0000-0000-0000-0000)" maxlength="19">
                    <div class="card-details">
                        <input type="text" id="card-expiration" placeholder="MM/YY" maxlength="5">
                        <input type="text" id="card-security" placeholder="CVV (e.g., 000)" maxlength="4">
                        <span class="tooltip-icon">?</span>
                    </div>
                    <input type="text" id="card-name" placeholder="Name on card">
                </div>
            </div>
            </div>
            <button class="pay-now-button">Pay Now</button>
            <p class="privacy-info">Your info will be saved to a Shop account. By continuing, you agree to Shop's Terms of Service and acknowledge the Privacy Policy.</p>
        </section>

        <section class="order-summary">
            <div class="order-summary-header">
                <h3>Cart summary</h3>
                {{-- Removed: <button id="edit-cart-button" class="edit-button">Edit</button> --}}
            </div>
            <div class="product-list-container" id="product-list-container-payment">
                <p class="text-center text-gray-500 empty-cart-message">Your cart is empty.</p>
            </div>
            <div class="order-total-summary">
                <div class="subtotal-row">
                    <span>Subtotal:</span>
                    <span>$<span id="cart-subtotal">0.00</span></span>
                </div>
                <div class="total-row">
                    <span>Total:</span>
                    <span>$<span id="cart-total-payment">0.00</span></span>
                </div>
            </div>
        </section>
    </main>


    {{-- Cart Pop-up Modal (same structure as categories, but ensure only one is active at a time if navigating) --}}
    <div id="cartModal" class="hide">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Your Shopping Bag</h2>
            <div id="modal-cart-items-payment">
                <p class="text-center text-gray-500 empty-cart-message">Your shopping bag is empty.</p>
            </div>
            <div class="modal-cart-summary">
                <p>Total: $<span id="modal-cart-total-payment">0.00</span></p>
            </div>
            <div class="modal-buttons">
                <button id="continue-shopping-button-modal">Continue Shopping</button>
                <a href="{{ url('/payment') }}" id="secure-checkout-button-modal" class="button-link">Secure Checkout</a>
            </div>
        </div>
    </div>


     {{-- ABA Payment Modal --}}
    <div id="abaModal" class="payment-modal">
        <div class="modal-content aba-modal">
            <div class="modal-header">
                <button class="modal-back-button"><i class="fas fa-arrow-left"></i> back</button>
                <div class="bank-logo-title">ABA</div>
            </div>
            <div class="modal-body">
                <p class="account-name">OUK SOVANNRITH</p>
                <p class="account-amount">$ <span id="aba-modal-amount">0.00</span></p>
                <div class="qr-code-placeholder">
                    {{-- QR image - ensure it is in public/photos/ --}}
                    <img src="{{ asset('photos/ABA_QR.png') }}" alt="ABA QR Code" class="qr-code-image">
                </div>
                <p class="qr-instruction">Scan to Pay</p>
            <div class="receipt-upload-section">
                <input type="file" id="receipt-upload-ABA" class="receipt-upload-input" accept="image/*">
            </div>
            <div>
               <label for="receipt-upload-ABA" class="upload-label">Your ABA Receipt</label>
            </div>
                <button class="scan-complete-button" data-bank="aba">Scan Complete</button>
            </div>
        </div>
    </div>
    {{-- ACLEDA Payment Modal --}}
    <div id="acledaModal" class="payment-modal">
        <div class="modal-content acleda-modal">
            <div class="modal-header">
                <button class="modal-back-button"><i class="fas fa-arrow-left"></i> back</button>
                <div class="bank-logo-title">ACLEDA</div>
            </div>
            <div class="modal-body">
                <p class="account-name">EKSARAN PHAOSERYLAKSMI</p>
                <p class="account-amount">$ <span id="acleda-modal-amount">0.00</span></p>
                <div class="qr-code-placeholder">
                    {{-- QR image - ensure it is in public/photos/ --}}
                    <img src="{{ asset('photos/ACLEDA_QR.png') }}" alt="ACLEDA QR Code" class="qr-code-image">
                </div>
                <p class="qr-instruction">Scan to Pay</p>
                <button class="scan-complete-button" data-bank="acleda">Scan Complete</button>
            </div>
        </div>
    </div>

    {{-- Transaction Complete Message Modal --}}
    <div id="transactionCompleteModal" class="payment-modal">
        <div class="modal-content transaction-complete-modal">
            <div class="modal-body">
                <i class="fas fa-check-circle success-icon"></i>
                <h3 class="success-message-title">Transaction Complete!</h3>
                <p class="success-message-text">Your order has been successfully placed. Thank you for shopping with us!</p>
                <button class="close-transaction-modal-button">Continue Shopping</button>
            </div>
        </div>
    </div>

    {{-- JavaScript files - assuming they are in public/js/ --}}
    {{-- <script src="{{ asset('js/script.js') }}"></script> --}} <!-- Removed as per previous instructions -->
    <script src="{{ asset('js/payment.js') }}"></script>
</body>
</html>
