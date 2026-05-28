{{-- resources/views/categories.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Categories - E-Commerce Platform</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/categories.css') }}" />

    <style>
        body {
            font-family: sans-serif;
            background-color: rgb(214, 218, 221);
        }

        .category-title {
            font-size: 1.7rem;
            font-weight: bold;
            margin: 1.5rem 0 1rem;
            color: #1f2937;
            text-align: center;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 1.5rem;
            padding: 0 1rem 2rem;
            max-width: 1300px;
            margin: auto;
        }

        .product-card {
            background: white;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            transition: all 0.25s ease;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .product-image {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: #f3f4f6;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.35s;
        }

        .product-image img:hover {
            transform: scale(1.08);
        }

        .product-info {
            padding: 1rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .product-info h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #111827;
            min-height: 50px;
        }

        .product-price {
            color: #16a34a;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .store-name {
            color: #6b7280;
            font-size: 0.85rem;
            margin-top: 0.3rem;
        }

        .detail-btn {
            margin-top: auto;
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 0.55rem 1rem;
            font-size: 0.9rem;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .detail-btn:hover {
            background-color: #1e40af;
        }

        .custom-pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 10px;
            flex-wrap: wrap;
        }

        .custom-pagination a,
        .custom-pagination span {
            padding: 8px 12px;
            background-color: white;
            color: #1f2937;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
        }

        .custom-pagination .active {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
        }

        .custom-pagination .disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,0.8);
            z-index: 9999;
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

        .loading-text {
            margin-top: 16px;
            color: #1f2937;
            font-weight: 600;
            font-size: 1rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .success-toast {
            position: fixed;
            top: 25px;
            right: 25px;
            background: #16a34a;
            color: white;
            padding: 14px 20px;
            border-radius: 10px;
            z-index: 99999;
            display: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            font-weight: 600;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .wishlist-active {
            color: red !important;
        }

        @media(max-width:768px) {
            .grid-container {
                grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            }

            .product-image {
                height: 180px;
            }
        }
    </style>
</head>

<body>

{{-- Loading --}}
<div id="loadingOverlay" class="loading-overlay">
    <div class="spinner"></div>
    <div class="loading-text">Loading products...</div>
</div>

{{-- Success Toast --}}
<div id="successToast" class="success-toast">
    Action completed successfully
</div>

<header class="header">
    <a href="{{ url('/') }}" class="logo-link">
        <div class="logo">
            <img src="{{ asset('photos/e-commerce_logo.jpg') }}" alt="E-commerce Logo">
        </div>
    </a>

    <nav class="navbar">
        <ul>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li><a href="{{ url('/categories?type=1') }}">Categories</a></li>
            <li><a href="{{ route('orders.history') }}">Orders</a></li>
            <li><a href="{{ url('/#success') }}">Feedback</a></li>
            <li><a href="{{ url('/#contact') }}">Contact</a></li>
            <li><a href="{{ url('/#about') }}">About Us</a></li>

            <li>
                <button id="cart-icon-button" class="icon-button">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count" class="cart-badge">0</span>
                </button>
            </li>

            <li>
                @guest
                    <a href="{{ url('/login') }}" class="icon-button">
                        <i class="fas fa-user-circle"></i>
                    </a>
                @else
                    <form method="POST" action="{{ route('logout') }}" class="loading-form">
                        @csrf
                        <button type="submit" class="icon-button">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                @endguest
            </li>
        </ul>
    </nav>
</header>

{{-- Category Navbar --}}
<nav class="category-navbar">
    <ul>
        <li><a href="{{ url('/categories?type=1') }}" class="loading-link">Skincare</a></li>
        <li><a href="{{ url('/categories?type=2') }}" class="loading-link">Clothing</a></li>
        <li><a href="{{ url('/categories?type=3') }}" class="loading-link">Accessories</a></li>
        <li><a href="{{ url('/categories?type=4') }}" class="loading-link">Education</a></li>
    </ul>
</nav>

@php
$currentType = request('type');

$storeTypeNames = [
    1 => 'Skincare',
    2 => 'Clothing',
    3 => 'Accessories',
    4 => 'Education',
];
@endphp

@if ($currentType && isset($storeTypeNames[$currentType]))

@php
$label = $storeTypeNames[$currentType];
@endphp

<section id="{{ strtolower($label) }}">
    <br><br><br><br>

    <h2 class="category-title">{{ $label }}</h2>

    {{-- Search --}}
    <div class="px-4">
        <form method="GET"
              action="{{ url('/categories') }}"
              class="flex flex-col md:flex-row justify-center gap-3 mb-8 loading-form">

            <input type="hidden" name="type" value="{{ request('type') }}">

            <input
                type="text"
                name="search"
                placeholder="Search products or stores..."
                value="{{ request('search') }}"
                class="border border-gray-300 rounded px-4 py-2 w-full max-w-lg focus:outline-none focus:ring focus:border-blue-400"
            />

            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                Search
            </button>
        </form>
    </div>

    {{-- Products --}}
    <div class="grid-container">

        @forelse($products as $product)

            @php
                $images = is_array($product->image)
                    ? $product->image
                    : [$product->image];

                $firstImage = $images[0] ?? null;

                $variants = $product->variants ?? [];

                $price = count($variants)
                    ? min(array_column($variants, 'price'))
                    : ($product->price ?? 0);
            @endphp

            <div class="product-card">

                <div class="product-image"
                     onclick="goToPage('{{ route('product.show', $product->id) }}')">

                    <img
                        src="{{ $firstImage ? asset('storage/' . $firstImage) : asset('photos/no-image.png') }}"
                        alt="{{ $product->name }}"
                    >
                </div>

                <div class="product-info">

                    <h3>{{ $product->name }}</h3>

                    <div class="product-price">
                        ${{ number_format($price, 2) }}
                    </div>

                    <div class="store-name">
                        {{ $product->store->store_name ?? 'Unknown Store' }}
                    </div>

                    <div class="flex justify-center gap-5 mt-4 mb-4">

                        {{-- Wishlist --}}
                        <button
                            class="wishlist-btn text-gray-500 hover:text-red-500 text-xl"
                            onclick="toggleWishlist(this)"
                            type="button">

                            <i class="far fa-heart"></i>
                        </button>

                        {{-- Share --}}
                        <button
                            class="share-btn text-gray-500 hover:text-blue-500 text-xl"
                            onclick="shareProduct('{{ route('product.show', $product->id) }}')"
                            type="button">

                            <i class="fas fa-share-alt"></i>
                        </button>
                    </div>

                    <a href="{{ route('product.show', $product->id) }}"
                       class="detail-btn loading-link">
                        See Details
                    </a>

                </div>
            </div>

        @empty

            <div class="col-span-full text-center text-gray-500 text-lg py-10">
                No {{ strtolower($label) }} products available.
            </div>

        @endforelse

    </div>

    {{-- Pagination --}}
    <div class="mt-8 flex justify-center">
        {{ $products->appends(request()->query())->links('vendor.pagination.custom') }}
    </div>

    {{-- Per Page --}}
    <div class="mt-8 flex justify-center">
        <form method="GET"
              action="{{ url('/categories') }}"
              class="flex items-center gap-3 loading-form">

            <input type="hidden" name="type" value="{{ request('type') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">

            <label for="perPage">Item Per Page</label>

            <select
                name="perPage"
                id="perPage"
                onchange="this.form.submit()"
                class="border border-gray-300 rounded px-3 py-2">

                @foreach ([10, 20, 30, 50] as $size)
                    <option value="{{ $size }}"
                        {{ request('perPage', 10) == $size ? 'selected' : '' }}>
                        {{ $size }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Summary --}}
    <div class="mt-6 text-center text-sm text-gray-700">
        <strong>{{ $products->firstItem() ?? 0 }}</strong>
        to
        <strong>{{ $products->lastItem() ?? 0 }}</strong>

        of

        <strong>{{ $products->total() }}</strong>
        results

        (Page {{ $products->currentPage() }}
        of
        {{ $products->lastPage() }})
    </div>

</section>

@else

<div class="text-center mt-20 text-gray-600 text-lg">
    Please select a category from above.
</div>

@endif

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

    // =========================
    // Loading Overlay
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

    function showSuccess(message = 'Success') {

        const toast = document.getElementById('successToast');

        toast.innerText = message;
        toast.style.display = 'block';

        setTimeout(() => {
            toast.style.display = 'none';
        }, 2500);
    }

    // =========================
    // Auto loading for forms
    // =========================

    document.querySelectorAll('.loading-form').forEach(form => {

        form.addEventListener('submit', function () {
            showLoading();
        });

    });

    // =========================
    // Auto loading for links
    // =========================

    document.querySelectorAll('.loading-link').forEach(link => {

        link.addEventListener('click', function () {
            showLoading();
        });

    });

    // =========================
    // Back/Forward Fix
    // =========================

    window.addEventListener('pageshow', function () {
        hideLoading();
    });

    // =========================
    // Go To Page
    // =========================

    function goToPage(url) {
        showLoading();
        window.location.href = url;
    }

    // =========================
    // Wishlist
    // =========================

    function toggleWishlist(button) {

        const icon = button.querySelector('i');

        if (icon.classList.contains('far')) {

            icon.classList.remove('far');
            icon.classList.add('fas');

            button.classList.add('wishlist-active');

            showSuccess('Added to wishlist ❤️');

        } else {

            icon.classList.remove('fas');
            icon.classList.add('far');

            button.classList.remove('wishlist-active');

            showSuccess('Removed from wishlist');

        }
    }

    // =========================
    // Share Product
    // =========================

    function shareProduct(url) {

        if (navigator.share) {

            navigator.share({
                title: 'Product',
                url: url
            });

        } else {

            navigator.clipboard.writeText(url);

            showSuccess('Product link copied');

        }
    }

</script>

</body>
</html>
