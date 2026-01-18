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

            <button id="next1">Next →</button>
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
            <button id="createStore">Create Store</button>
        </div>

    <!-- Product Page -->
    <div id="productPage" class="container hidden">
        <h2 id="displayStoreName">Your Store</h2>
        <p id="displayStoreType"></p>
        <h2 id="displaymyproduct"> My Product </h2>
        <!-- Product Row -->
        <div id="productList" class="product-list"></div>

        <button id="addMore">Add More</button>
        <br/>
        <br/>
    </div>

    {{-- Linking to external JavaScript using Laravel's asset helper --}}
    <script src="{{ asset('js/vendor-script.js') }}"></script>
</body>
</html>
