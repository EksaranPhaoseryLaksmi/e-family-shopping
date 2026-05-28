{{-- resources/views/product/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ $product->name }} - E-Commerce Platform</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/categories.css') }}" />

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

    <style>

        body {
            font-family: sans-serif;
            background-color: rgb(209, 211, 212);
        }

        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,0.85);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            backdrop-filter: blur(2px);
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 6px solid #dbeafe;
            border-top: 6px solid #2563eb;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            margin-top: 15px;
            font-weight: 600;
            color: #1f2937;
        }

        .success-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #16a34a;
            color: white;
            padding: 14px 20px;
            border-radius: 10px;
            z-index: 999999;
            display: none;
            font-weight: 600;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .product-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .thumb-img {
            transition: all 0.3s ease;
        }

        .thumb-img:hover {
            transform: scale(1.05);
            border-color: #2563eb;
        }

        .product-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
        }

        .product-price {
            color: #16a34a;
            font-size: 1.8rem;
            font-weight: bold;
        }

        .add-to-cart {
            width: 100%;
            background: #2563eb;
            color: white;
            padding: 14px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .add-to-cart:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .related-card {
            transition: all 0.25s ease;
        }

        .related-card:hover {
            transform: translateY(-5px);
        }

        .cart-flash {
            animation: flash-cart 0.6s ease-in-out;
        }

        @keyframes flash-cart {
            0% { transform: scale(1); }
            25% { transform: scale(1.2); }
            50% { transform: scale(1); }
            75% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .badge-stock {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .fade-in {
            animation: fadeIn 0.35s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

    </style>
</head>

<body>

{{-- Loading --}}
<div id="loadingOverlay" class="loading-overlay">
    <div class="spinner"></div>
    <div class="loading-text">Loading...</div>
</div>

{{-- Success Toast --}}
<div id="successToast" class="success-toast">
    Added to cart successfully
</div>

<header class="header">

    <a href="{{ url('/') }}" class="logo-link loading-link">
        <div class="logo">
            <img src="{{ asset('photos/e-commerce_logo.jpg') }}"
                 alt="E-commerce Logo">
        </div>
    </a>

    <nav class="navbar">
        <ul>

            <li><a href="{{ url('/') }}" class="loading-link">Home</a></li>

            <li>
                <a href="{{ url('/categories?type=1') }}"
                   class="loading-link">
                    Categories
                </a>
            </li>

            <li><a href="{{ url('/#about') }}">About Us</a></li>

            <li><a href="{{ url('/#templates') }}">Templates</a></li>

            <li><a href="{{ url('/#success') }}">Feedback</a></li>

            <li><a href="{{ url('/#contact') }}">Contact</a></li>

            <li>
                <a href="{{ route('orders.history') }}"
                   class="loading-link">
                    Orders
                </a>
            </li>

            {{-- Cart --}}
            <li>
                <button id="cart-icon-button" class="icon-button">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count" class="cart-badge">0</span>
                </button>
            </li>

            {{-- Login --}}
            <li>
                @guest

                    <a href="{{ url('/login') }}"
                       class="icon-button loading-link">

                        <i class="fas fa-user-circle"></i>
                    </a>

                @else

                    <form method="POST"
                          action="{{ route('logout') }}"
                          class="loading-form">

                        @csrf

                        <button type="submit"
                                class="icon-button">

                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>

                @endguest
            </li>

        </ul>
    </nav>
</header>

<br><br><br><br>

<div class="max-w-6xl mx-auto p-4">

    <div class="product-wrapper p-6 fade-in">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            {{-- Images --}}
            <div>

                <img id="mainImage"
                     src="{{ asset('storage/' . $product->image[0]) }}"
                     class="w-full h-[450px] object-cover rounded-xl shadow">

                <div class="flex gap-3 mt-5 overflow-x-auto pb-2">

                    @foreach($product->image as $img)

                        <img src="{{ asset('storage/' . $img) }}"
                             class="h-24 w-24 object-cover rounded-lg border-2 border-gray-200 cursor-pointer thumb-img"
                             onclick="changeMainImage(this)">

                    @endforeach

                </div>

            </div>

            {{-- Product Info --}}
            <div class="product-item"
                 data-id="{{ $product->id }}"
                 data-name="{{ $product->name }}"
                 data-price="{{ $defaultVariant['price'] }}"
                 data-image="{{ asset('storage/' . $product->image[0]) }}"
                 data-vendor="{{ $product->vendor_request_id }}">

                <h1 class="product-title mb-3">
                    {{ $product->name }}
                </h1>

                <p class="text-gray-500 mb-5">
                    Vendor:
                    <strong>
                        {{ $product->store->store_name ?? 'Unknown Store' }}
                    </strong>
                </p>

                {{-- Size --}}
                <div class="mb-5">

                    <label class="block font-semibold mb-2">
                        Select Size
                    </label>

                    <select id="sizeSelect"
                            onchange="updatePriceAndStock()"
                            class="border border-gray-300 p-3 rounded-lg w-full">

                        @foreach($sizes as $size)

                            <option value="{{ $size }}">
                                {{ $size }}
                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- Price --}}
                <div class="mb-5">

                    <label class="block font-semibold mb-2">
                        Price
                    </label>

                    <p id="priceDisplay" class="product-price">
                        ${{ number_format($defaultVariant['price'], 2) }}
                    </p>

                </div>

                {{-- Stock --}}
                <div class="mb-5">

                    <span class="badge-stock">
                        In Stock:
                        <span id="maxQty">
                            {{ $defaultVariant['quantity'] }}
                        </span>
                    </span>

                </div>

                {{-- Quantity --}}
                <div class="mb-6">

                    <label class="block font-semibold mb-2">
                        Quantity
                    </label>

                    <input type="number"
                           id="quantityInput"
                           class="border border-gray-300 p-3 rounded-lg w-full"
                           min="1"
                           max="{{ $defaultVariant['quantity'] }}"
                           value="1">

                </div>

                {{-- Description --}}
                <div class="mb-8">

                    <label class="block font-semibold mb-2">
                        Product Detail
                    </label>

                    <p class="text-gray-700 leading-relaxed">
                        {{ $product->description }}
                    </p>

                </div>

                {{-- Buttons --}}
                <div class="flex flex-col gap-4">

                    <button class="add-to-cart"
                            onclick="addToCart()">

                        <i class="fas fa-cart-plus mr-2"></i>
                        Add To Cart
                    </button>

                    <button onclick="shareProduct()"
                            class="border border-blue-600 text-blue-600 py-3 rounded-lg hover:bg-blue-50 transition">

                        <i class="fas fa-share-alt mr-2"></i>
                        Share Product
                    </button>

                </div>

            </div>

        </div>

        {{-- Related --}}
        <div class="mt-16">

            <h3 class="text-2xl font-bold text-gray-700 mb-8">
                Related Products
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

                @foreach($relatedProducts as $related)

                    <a href="{{ route('product.show', $related->id) }}"
                       class="bg-white shadow rounded-xl overflow-hidden hover:shadow-2xl related-card loading-link">

                        <img src="{{ asset('storage/' . $related->image[0]) }}"
                             class="w-full h-52 object-cover">

                        <div class="p-4">

                            <h4 class="font-semibold text-gray-800 truncate">
                                {{ $related->name }}
                            </h4>

                            @php
                                $first = collect($related->variants)->first();
                            @endphp

                            <p class="text-green-600 font-bold mt-2">
                                ${{ number_format($first['price'], 2) ?? '0.00' }}
                            </p>

                        </div>

                    </a>

                @endforeach

            </div>

        </div>

    </div>

</div>

{{-- Footer --}}
<footer class="footer mt-16">

    <ul>
        <li>@ Shopping E-Commerce V1.0.0</li>
    </ul>

    <ul>
        <li>LOCATION</li>
        <li>
            <img src="{{ asset('photos/combodia.jpg') }}" alt="Cambodia">
            Cambodia
        </li>
        <li>Phnom Penh</li>
    </ul>

</footer>

{{-- Cart Modal --}}
<div id="cartModal" class="modal">

    <div class="modal-content">

        <span class="close-button">&times;</span>

        <h2>Your Shopping Bag</h2>

        <div id="modal-cart-items">

            <p class="text-center text-gray-500 empty-cart-message">
                Your shopping bag is empty.
            </p>

        </div>

        <div class="modal-cart-summary">
            <p>
                Total:
                $
                <span id="modal-cart-total">0.00</span>
            </p>
        </div>

        <div class="modal-buttons">

            <button id="continue-shopping-button">
                Continue Shopping
            </button>

            <a href="{{ url('/payment') }}"
               id="secure-checkout-button"
               class="button-link loading-link">

                Secure Checkout
            </a>

        </div>

    </div>

</div>

<script src="{{ asset('js/script.js') }}"></script>

<script>

    const variants = @json($variants);

    // =========================
    // Loading
    // =========================

    function showLoading() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    // =========================
    // Success Toast
    // =========================

    function showSuccess(message) {

        const toast = document.getElementById('successToast');

        toast.innerText = message;
        toast.style.display = 'block';

        setTimeout(() => {
            toast.style.display = 'none';
        }, 2500);
    }

    // =========================
    // Change Image
    // =========================

    function changeMainImage(el) {

        document.getElementById('mainImage').src = el.src;

    }

    // =========================
    // Update Price
    // =========================

    function updatePriceAndStock() {

        const selectedSize =
            document.getElementById('sizeSelect').value;

        const match =
            variants.find(v => v.size === selectedSize);

        if (match) {

            document.getElementById('priceDisplay')
                .textContent =
                '$' + parseFloat(match.price).toFixed(2);

            document.getElementById('quantityInput')
                .max = match.quantity;

            document.getElementById('maxQty')
                .textContent = match.quantity;

            if (
                document.getElementById('quantityInput').value >
                match.quantity
            ) {

                document.getElementById('quantityInput').value =
                    match.quantity;
            }
        }
    }

    // =========================
    // Add To Cart
    // =========================

    function addToCart() {

        const quantity =
            parseInt(document.getElementById('quantityInput').value);

        if (quantity <= 0) {

            alert('Invalid quantity');
            return;
        }

        const cartBtn =
            document.getElementById('cart-icon-button');

        cartBtn.classList.add('cart-flash');

        setTimeout(() => {
            cartBtn.classList.remove('cart-flash');
        }, 600);

        showSuccess('Product added to cart successfully');

        let count =
            parseInt(document.getElementById('cart-count').innerText);

        document.getElementById('cart-count').innerText =
            count + quantity;
    }

    // =========================
    // Share Product
    // =========================

    function shareProduct() {

        const url = window.location.href;

        if (navigator.share) {

            navigator.share({
                title: document.title,
                url: url
            });

        } else {

            navigator.clipboard.writeText(url);

            showSuccess('Product link copied');
        }
    }

    // =========================
    // Auto Loading
    // =========================

    document.querySelectorAll('.loading-link').forEach(link => {

        link.addEventListener('click', function () {

            showLoading();

        });

    });

    document.querySelectorAll('.loading-form').forEach(form => {

        form.addEventListener('submit', function () {

            showLoading();

        });

    });

    window.addEventListener('pageshow', function () {

        hideLoading();

    });

</script>

</body>
</html>
