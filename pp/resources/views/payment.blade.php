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
                <button type="button" class="bank-button khqr" id="khqr-btn">
                    <img src="{{ asset('photos/ABA.jpg') }}">
                </button>
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
               </select>

               <div class="name-inputs">
                   <input type="text" id="delivery-first-name" placeholder="First Name">
                   <input type="text" id="delivery-last-name" placeholder="Last Name">
               </div>

               <input type="text" id="delivery-address" placeholder="Address">
               <input type="text" id="delivery-apartment" placeholder="Apartment, suite, etc. (optional)">
               <input type="text" id="delivery-city" placeholder="City">

               <!-- ✅ NEW: Phone number -->
               <input type="text" id="delivery-phone" placeholder="Phone Number (e.g. +855 12 345 678)">

               <!-- ✅ NEW: Google Map link -->
               <input type="text" id="delivery-map" placeholder="Google Maps link (optional)">
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

    <!-- KHQR (Bakong) Payment Modal -->
    <div id="khqrModal" class="payment-modal">
        <div class="modal-content aba-modal">
            <div class="modal-header">
                <button class="modal-back-button"><i class="fas fa-arrow-left"></i> back</button>
                <div class="bank-logo-title">
                    e-Family Payment
                </div>
            </div>

            <div class="modal-body">
                <img src="{{ asset('photos/bakong.png') }}" class="bakong-logo">
                <p class="account-name">SENG BUN</p>
                <p class="account-amount">$ <span id="khqr-amount">0.00</span></p>

                <div class="qr-code-placeholder">
                    <img id="khqr-image" class="qr-code-image" style="display:none;">

                </div>
                <p id="khqr-status">⏳ Generating QR...</p>
                <p id="countdown" style="font-size:14px; margin-top:10px; font-weight:bold;"></p>
                <p class="qr-instruction">Scan with ABA / ACLEDA / Wing</p>

             <!--   <button class="scan-complete-button">I Paid</button> -->
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

</body>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const khqrBtn = document.getElementById("khqr-btn");
    const khqrModal = document.getElementById("khqrModal");
    const qrImg = document.getElementById("khqr-image");
    const status = document.getElementById("khqr-status");
    const amountText = document.getElementById("khqr-amount");
    const countdown = document.getElementById("countdown");
    const transactionCompleteModal = document.getElementById("transactionCompleteModal");
    const total = document.getElementById("cart-total-payment");
    const phone = document.getElementById('delivery-phone')?.value.trim() || '';
    const mapLink = document.getElementById('delivery-map')?.value.trim() || '';
    let timerInterval = null;     // For the countdown UI
    let pollingInterval = null;   // For the checkPayment API calls
    let isLoading = false;
    let currentRequest = null;

    // ==========================================
    // STOP ALL PROCESSES (Clear intervals)
    // ==========================================
    function stopPaymentProcesses() {
        if (timerInterval) clearInterval(timerInterval);
        if (pollingInterval) clearInterval(pollingInterval);
        timerInterval = null;
        pollingInterval = null;
    }

    // ==========================================
    // COUNTDOWN LOGIC
    // ==========================================
    function startCountdown(expTime) {
        clearInterval(timerInterval);
        qrImg.style.opacity = "1";

        timerInterval = setInterval(() => {
            const now = Date.now();
            const diff = expTime - now;

            if (diff <= 0) {
                stopPaymentProcesses(); // 🛑 STOP checking payment when expired
                countdown.innerHTML = "❌ QR EXPIRED";
                status.innerText = "Please generate new QR";
                qrImg.style.opacity = "0.3";
                isLoading = false;
                return;
            }

            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            countdown.innerHTML = `⏳ Expires in ${minutes}:${seconds.toString().padStart(2, "0")}`;
        }, 1000);
    }

    // ==========================================
    // CHECK PAYMENT STATUS (POLLING)
    // ==========================================
    function startCheckPayment() {
        // Clear any existing polling first
        if (pollingInterval) clearInterval(pollingInterval);

        pollingInterval = setInterval(() => {
            if (!window.paymentRef) return;

            fetch(`/khqr/check?payment_ref=${window.paymentRef}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === "paid") {
                    stopPaymentProcesses(); // 🛑 STOP checking once paid
                    status.innerText = "✅ Payment Success";
                    localStorage.removeItem("shoppingCart");
                    openModal(transactionCompleteModal);
                }
            })
            .catch(err => console.error("Polling error:", err));
        }, 3000);
    }

    function openModal(modalElement) {
        if (!modalElement) return;
        document.querySelectorAll(".payment-modal, .modal").forEach(modal => {
            modal.classList.remove("active");
            modal.style.display = "none";
        });
        modalElement.classList.add("active");
        modalElement.style.display = "flex";
    }

    // ==========================================
    // GENERATE QR BUTTON CLICK
    // ==========================================
    khqrBtn.addEventListener("click", function () {
        if (isLoading) return;
        isLoading = true;

        const amount = parseFloat(total?.innerText || 0);
        amountText.innerText = amount.toFixed(2);

        khqrModal.style.display = "flex";
        status.innerText = "⏳ Generating QR...";
        qrImg.style.display = "none";
        countdown.innerHTML = "";

        if (currentRequest) currentRequest.abort();
        const controller = new AbortController();
        currentRequest = controller;

        const shoppingCart = JSON.parse(localStorage.getItem("shoppingCart")) || [];

        fetch("/khqr/generate", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                cart: shoppingCart,
                amount: amount,
                delivery_name: document.getElementById('delivery-first-name').value + ' ' + document.getElementById('delivery-last-name').value,
                delivery_address: document.getElementById('delivery-address').value + ', ' + document.getElementById('delivery-city').value,
                delivery_email: document.getElementById('contact-email').value,
                delivery_phone: document.getElementById('delivery-phone')?.value.trim() || '',
                delivery_map: document.getElementById('delivery-map')?.value.trim() || '',
            })
        })
        .then(res => res.json())
        .then(res => {
            window.paymentRef = res.payment_ref;
            qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(res.qrString)}`;
            qrImg.style.display = "block";
            status.innerText = "Scan to pay 💳";

            startCountdown(res.expiration);
            startCheckPayment();
            isLoading = false;
        })
        .catch(err => {
            if (err.name === "AbortError") return;
            status.innerText = "❌ Error generating QR";
            isLoading = false;
        });
    });

    // ==========================================
    // CLOSE MODAL / BACK BUTTON
    // ==========================================
    document.querySelectorAll(".modal-back-button").forEach(btn => {
        btn.addEventListener("click", function () {
            khqrModal.style.display = "none";

            stopPaymentProcesses(); // 🛑 STOP checking payment when user closes modal

            qrImg.style.opacity = "1";
            qrImg.style.display = "none";
            countdown.innerHTML = "";
            status.innerText = "";
            isLoading = false;
        });
    });
});
</script>
<script src="{{ asset('js/payment.js') }}"></script>
</html>
