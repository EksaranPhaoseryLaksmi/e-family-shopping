<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Create Your Store</title>
    {{-- Linking to external CSS using Laravel's asset helper --}}
    <link rel="stylesheet" href="{{ asset('css/vendor-style.css') }}" />

    {{-- Firebase v8 CDN --}}
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-firestore.js"></script>
</head>
<body>
    @if(session('success'))
        <div id="alertBox"
             class="fixed top-4 right-4 z-50 bg-green-100 text-green-800 border border-green-300 px-4 py-3 rounded shadow-lg">

            {{ session('success') }}

        </div>
    @endif

    @if(session('error'))
        <div id="alertBox"
             class="fixed top-4 right-4 z-50 bg-red-100 text-red-800 border border-red-300 px-4 py-3 rounded shadow-lg">

            {{ session('error') }}

        </div>
    @endif
        <!-- Step 1 -->
        <div id="step1" class="container">
            <h2>CREATE YOUR STORE</h2>

            <p>Where are your customers located?</p>
            <div class="multi-option" data-name="location">
                <button data-key="islocal">Local</button>
                <button data-key="isinternational">International</button>
                <button data-key="isboth">Both</button>
            </div>

            <p>Do you have product photos ready?</p>
            <div class="single-option" data-name="photos">
                <button data-value="1">Yes</button>
                <button data-value="0">No</button>
            </div>

            <p>How do you want to deliver your products?</p>
            <div class="single-option" data-name="delivery">
                <button data-value="1"> Handle it your own</button>
                <button data-value="0">Let website do it for you</button>
            </div>

           <button id="next1" class="submit-btn">

               <span class="btn-text">
                   Next →
               </span>

           </button>
        </div>

        <!-- Step 2 -->
        <div id="step2" class="container hidden">
            <h2>CREATE YOUR STORE</h2>

            <p>How do you want to get paid?</p>
            <div class="multi-option" data-name="payment">
                <button data-type="isbank">Bank Transfer</button>
                <button data-type="iscard">Credit Card</button>
                <button data-type="iscash">Cash on Delivery</button>
            </div>

            <p>Do you need help with product description, pricing, or design?</p>
            <div class="single-option" data-name="help">
                <button data-value="1">Yes</button>
                <button data-value="0">No</button>
            </div>

            <input type="text" id="storeName" placeholder="Enter your store’s name here..." />

            <p>What type of store do you want to create?</p>
            <div class="single-option" data-name="storeType">
                <button data-value="1">Skin Care</button>
                <button data-value="2">Clothes</button>
                <button data-value="3">Accessory</button>
                <button data-value="4">Education Stuff</button>
            </div>

            <button id="back">← Back</button>
            <button id="createStore" class="submit-btn">

                <span class="btn-text">
                    Create Store
                </span>

            </button>
        </div>

    <!-- Product Page -->
    <div id="productPage" class="container hidden">
        <h2 id="displayStoreName">Your Store</h2>
        <p id="displayStoreType"></p>
        <h2 id="displaymyproduct"> My Product </h2>
        <!-- Product Row -->
        <div id="productList" class="product-list"></div>

        <button id="addMore" class="submit-btn">

            <span class="btn-text">
                Add More
            </span>

        </button>
        <br/>
        <br/>
    </div>
<!-- Loading Overlay -->
<div id="loadingOverlay"
     class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center">

    <div class="bg-white px-6 py-4 rounded-xl shadow-xl flex items-center gap-3">

        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>

        <span class="text-lg font-semibold text-gray-700">
            Processing...
        </span>

    </div>

</div>
<script>

// =========================
// SHOW LOADING
// =========================
function showLoading() {

    const overlay =
        document.getElementById("loadingOverlay");

    if (!overlay) return;

    overlay.classList.remove("hidden");
    overlay.classList.add("flex");
}

// =========================
// HIDE LOADING
// =========================
function hideLoading() {

    const overlay =
        document.getElementById("loadingOverlay");

    if (!overlay) return;

    overlay.classList.add("hidden");
    overlay.classList.remove("flex");
}

// =========================
// PAGE READY
// =========================
document.addEventListener("DOMContentLoaded", function () {

    // -------------------------
    // AUTO HIDE ALERT
    // -------------------------
    const alertBox =
        document.getElementById("alertBox");

    if (alertBox) {

        setTimeout(() => {

            alertBox.style.transition = "0.5s";
            alertBox.style.opacity = "0";

            setTimeout(() => {
                alertBox.remove();
            }, 500);

        }, 3000);
    }

    // -------------------------
    // BUTTON LOADING
    // -------------------------
    const buttons =
        document.querySelectorAll(".submit-btn");

    buttons.forEach(btn => {

        btn.addEventListener("click", function () {

            showLoading();

            btn.disabled = true;

            const btnText =
                btn.querySelector(".btn-text");

            if (btnText) {

                btnText.innerHTML =
                    'Loading...';

            }

        });

    });

});

// =========================
// FIX BACK BUTTON STUCK
// =========================
window.addEventListener("pageshow", function () {

    hideLoading();

    document.querySelectorAll(".submit-btn")
        .forEach(btn => {

            btn.disabled = false;

        });

});

</script>
    {{-- Linking to external JavaScript using Laravel's asset helper --}}
    <script src="{{ asset('js/vendor-script.js') }}"></script>
</body>
</html>
