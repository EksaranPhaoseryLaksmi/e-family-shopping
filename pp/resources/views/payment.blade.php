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
            background-image: url('{{ asset('photos/pay.jpg') }}');
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

        /* Loading UI States & Dynamic Visuals */
        .bank-button.loading-state {
            pointer-events: none;
            opacity: 0.6;
            position: relative;
        }
        .qr-code-placeholder {
            min-height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fdfdfd;
            border-radius: 8px;
            border: 2px dashed #ddd;
            margin: 15px auto;
            position: relative;
        }
        .spinner-loader {
            border: 4px solid rgba(0,0,0,0.1);
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border-left-color: #0975A8;
            animation: spin 1s linear infinite;
            display: none;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .text-error {
            color: #d9534f !important;
            font-weight: bold;
        }
        .text-success {
            color: #2baf2b !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header class="header">
        {{-- Logo as a link to home page --}}
        <a href="{{ url('/') }}" class="logo-link">
            <div class="logo">
                <img src="{{ asset('photos/e-commerce_logo.jpg') }}" alt="E-commerce Logo">
            </div>
        </a>
        <nav class="navbar">
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/categories?type=1') }}">Categories</a></li>
                <li><a href="{{ url('/#about') }}">About Us</a></li>
                <li><a href="{{ url('/#templates') }}">Templates</a></li>
                <li><a href="{{ url('/#success') }}">Feedback</a></li>
                <li><a href="{{ url('/#contact') }}">Contact</a></li>

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
                <button type="button" class="bank-button khqr" id="khqr-btn">
                    <img src="{{ asset('photos/ABA.jpg') }}" alt="ABA Pay">
                    <span id="btn-spinner" style="display:none; margin-left:10px;"><i class="fas fa-spinner fa-spin"></i></span>
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

                <input type="text" id="delivery-phone" placeholder="Phone Number (e.g. +855 12 345 678)">

                <input type="text" id="delivery-map" placeholder="Google Maps link (optional)">
            </div>
            <div class="hide">
                <div class="payment-section">
                    <h3>Payment</h3>
                    <p>All transactions are secure and encrypted.</p>
                    <div class="credit-card-section">
                        <h4>Credit Card</h4>
                        <div class="card-icons">
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

    {{-- Cart Pop-up Modal --}}
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

    <div id="khqrModal" class="payment-modal">
        <div class="modal-content aba-modal">
            <div class="modal-header">
                <button class="modal-back-button"><i class="fas fa-arrow-left"></i> back</button>
                <div class="bank-logo-title">
                    e-Family Payment
                </div>
            </div>

            <div class="modal-body">
                <img src="{{ asset('photos/bakong.png') }}" class="bakong-logo" alt="Bakong">
                <p class="account-name">SENG BUN</p>
                <p class="account-amount">$ <span id="khqr-amount">0.00</span></p>

                <div class="qr-code-placeholder" id="qr-container">
                    <div class="spinner-loader" id="qr-spinner"></div>
                    <img id="khqr-image" class="qr-code-image" style="display:none;" alt="KHQR Code">
                </div>
                <p id="khqr-status">⏳ Generating QR...</p>
                <p id="countdown" style="font-size:14px; margin-top:10px; font-weight:bold;"></p>
                <p class="qr-instruction">Scan with ABA / ACLEDA / Wing</p>
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
                <button class="close-transaction-modal-button" id="success-continue-btn">Continue Shopping</button>
            </div>
        </div>
    </div>

</body>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const khqrBtn = document.getElementById("khqr-btn");
    const btnSpinner = document.getElementById("btn-spinner");
    const khqrModal = document.getElementById("khqrModal");
    const qrImg = document.getElementById("khqr-image");
    const qrSpinner = document.getElementById("qr-spinner");
    const status = document.getElementById("khqr-status");
    const amountText = document.getElementById("khqr-amount");
    const countdown = document.getElementById("countdown");
    const transactionCompleteModal = document.getElementById("transactionCompleteModal");
    const successContinueBtn = document.getElementById("success-continue-btn");
    const total = document.getElementById("cart-total-payment");

    let timerInterval = null;
    let pollingInterval = null;
    let isLoading = false;
    let currentRequest = null;

    // ==========================================
    // STOP ALL PROCESSES (Clear intervals & UI resetting)
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
                stopPaymentProcesses();
                countdown.innerHTML = "❌ QR EXPIRED";
                status.className = "text-error";
                status.innerText = "Please generate a new QR code.";
                qrImg.style.opacity = "0.2";
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
        if (pollingInterval) clearInterval(pollingInterval);

        pollingInterval = setInterval(() => {
            if (!window.paymentRef) return;

            fetch(`/khqr/check?payment_ref=${window.paymentRef}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === "paid") {
                    stopPaymentProcesses();
                    status.className = "text-success";
                    status.innerText = "✅ Payment Success!";

                    // Clear cart data
                    localStorage.removeItem("shoppingCart");

                    // Clear specific visuals and trigger Success Modal
                    setTimeout(() => {
                        khqrModal.style.display = "none";
                        openModal(transactionCompleteModal);
                    }, 1000);
                }
            })
            .catch(err => console.error("Polling status checkpoint failed:", err));
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

        // Toggle Loading Button UI State
        khqrBtn.classList.add("loading-state");
        if(btnSpinner) btnSpinner.style.display = "inline-block";

        const amount = parseFloat(total?.innerText || 0);
        amountText.innerText = amount.toFixed(2);

        // Prep Modal UI display for processing
        khqrModal.style.display = "flex";
        status.className = "";
        status.innerText = "⏳ Requesting dynamic QR string...";
        qrImg.style.display = "none";
        qrSpinner.style.display = "block"; // Turn on inner modal loader
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
                delivery_name: (document.getElementById('delivery-first-name')?.value || '') + ' ' + (document.getElementById('delivery-last-name')?.value || ''),
                delivery_address: (document.getElementById('delivery-address')?.value || '') + ', ' + (document.getElementById('delivery-city')?.value || ''),
                delivery_email: document.getElementById('contact-email')?.value || '',
                delivery_phone: document.getElementById('delivery-phone')?.value.trim() || '',
                delivery_map: document.getElementById('delivery-map')?.value.trim() || '',
            }),
            signal: controller.signal
        })
        .then(res => {
            if (!res.ok) throw new Error("Server response error occurred.");
            return res.json();
        })
        .then(res => {
            if(!res.qrString) {
                throw new Error("Invalid checkout payload details.");
            }

            window.paymentRef = res.payment_ref;

            // Generate standard image URL structure
            qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(res.qrString)}`;

            // Ensure visual spinner removes only when imagery fetches completely
            qrImg.onload = function() {
                qrSpinner.style.display = "none";
                qrImg.style.display = "block";
                status.innerText = "Scan to pay 💳";

                // Clear active button loader structures
                khqrBtn.classList.remove("loading-state");
                if(btnSpinner) btnSpinner.style.display = "none";
                isLoading = false;
            };

            startCountdown(res.expiration);
            startCheckPayment();
        })
        .catch(err => {
            if (err.name === "AbortError") return;
            console.error(err);

            // Clean up loaders on exception instances
            qrSpinner.style.display = "none";
            khqrBtn.classList.remove("loading-state");
            if(btnSpinner) btnSpinner.style.display = "none";

            status.className = "text-error";
            status.innerText = "❌ Error generating QR. Please try again.";
            isLoading = false;
        });
    });

    // ==========================================
    // CLOSE MODAL / BACK BUTTONS
    // ==========================================
    document.querySelectorAll(".modal-back-button").forEach(btn => {
        btn.addEventListener("click", function () {
            khqrModal.style.display = "none";
            stopPaymentProcesses();

            qrImg.style.opacity = "1";
            qrImg.style.display = "none";
            qrSpinner.style.display = "none";
            countdown.innerHTML = "";
            status.innerText = "";
            status.className = "";

            khqrBtn.classList.remove("loading-state");
            if(btnSpinner) btnSpinner.style.display = "none";
            isLoading = false;
        });
    });

    // Redirect when transaction success continues
    if(successContinueBtn) {
        successContinueBtn.addEventListener("click", function() {
            window.location.href = "{{ url('/') }}";
        });
    }
});
</script>
<script src="{{ asset('js/payment.js') }}"></script>
</html>
