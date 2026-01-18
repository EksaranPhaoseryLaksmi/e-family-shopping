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
            font-size: 1.5rem;
            font-weight: bold;
            margin: 1.5rem 0 1rem;
            color: #1f2937;
            text-align: center;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
            padding: 0 1rem 2rem;
            max-width: 1200px;
            margin: auto;
        }
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-4px);
        }
        .product-image {
            position: relative;
            height: 180px;
            overflow: hidden;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .product-image img:hover {
            transform: scale(1.05);
        }
        .product-info {
            padding: 1rem;
            text-align: center;
        }
        .product-info h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #111827;
        }
        .product-info p {
            color: #16a34a;
            font-weight: bold;
            font-size: 1rem;
        }
        .detail-btn {
            margin-top: 0.75rem;
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .detail-btn:hover {
            background-color: #1e3a8a;
        }
        .custom-pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    gap: 10px;
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

    </style>
</head>
<body>
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
                        <a href="{{ url('/login') }}" class="icon-button" id="login-icon-button">
                            <i class="fas fa-user-circle"></i>
                        </a>
                    @else
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

    <nav class="category-navbar">
        <ul>
            <li><a href="{{ url('/categories?type=1') }}">Skincare</a></li>
            <li><a href="{{ url('/categories?type=2') }}">Clothing</a></li>
            <li><a href="{{ url('/categories?type=3') }}">Accessories</a></li>
            <li><a href="{{ url('/categories?type=4') }}">Education</a></li>
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
        <br/><br/>
        <br/><br/>

        <h2 class="category-title">{{ $label }}</h2>
         <div>
<form method="GET" action="{{ url('/categories') }}" class="flex justify-center mb-6">
    <input type="hidden" name="type" value="{{ request('type') }}">
    <input
        type="text"
        name="search"
        placeholder="Search products or stores..."
        value="{{ request('search') }}"
        class="border border-gray-300 rounded px-3 py-2 w-full max-w-md focus:outline-none focus:ring focus:border-blue-400"
    />
    <button type="submit" class="ml-2 bg-blue-600 text-white px-4 rounded hover:bg-blue-700 transition">
        Search
    </button>
</form>
</div>
        <div class="grid-container">
            @if($products->count())
                @foreach($products as $product)
                    @php
                        $images = is_array($product->image) ? $product->image : [$product->image];
                        $firstImage = $images[0] ?? null;
                        $variants = $product->variants ?? [];
                        $price = count($variants)
                            ? min(array_column($variants, 'price'))
                            : ($product->price ?? 0);
                    @endphp
                    <div class="product-card">
                        <div class="product-image" onclick="window.location='{{ route('product.show', $product->id) }}'">
                            <img src="{{ $firstImage ? asset('storage/' . $firstImage) : asset('photos/no-image.png') }}" alt="{{ $product->name }}">
                        </div>
                        <div class="product-info">
                            <h3>{{ $product->name }}</h3>
                            <p>${{ number_format($price, 2) }}</p>
                            <div class="flex justify-center gap-4 mt-4">
                                <label class="text-sm text-gray-600 mt-1">
                                    {{ $product->store->store_name ?? 'Unknown Store' }}
                                </label>
                                <button class="wishlist-btn text-gray-500 hover:text-red-500 text-xl" onclick="toggleWishlist(this)">
                                    <i class="far fa-heart"></i>
                                </button>
                                <button class="share-btn text-gray-500 hover:text-blue-500 text-xl"
                                        onclick="shareProduct('{{ route('product.show', $product->id) }}')">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                            </div>
                            <a href="{{ route('product.show', $product->id) }}" class="detail-btn">See Details</a>
                        </div>
                    </div>
                @endforeach
            @else
                <p style="text-align: center; color: #6b7280;">No {{ strtolower($label) }} products available.</p>
            @endif
        </div>

    {{-- Pagination --}}
    <div class="flex flex-col md:flex-row md:items-center justify-center gap-6 border-t border-gray-300 pt-6 flex-wrap text-sm text-gray-700 text-center">
     {{-- Pagination Links --}}
    <div>
        {{ $products->appends(request()->query())->links('vendor.pagination.custom') }}
    </div>
    </div>

     <div class="flex flex-col md:flex-row md:items-center justify-center gap-6 border-t border-gray-300 pt-6 flex-wrap text-sm text-gray-700 text-center">
    <div>
    {{-- Per Page Filter --}}
    <form method="GET" action="{{ url('/categories') }}" class="flex items-center space-x-2">
        <input type="hidden" name="type" value="{{ request('type') }}">
        <label for="perPage">Item Per Page</label>
        <select name="perPage" id="perPage" onchange="this.form.submit()"
            class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring focus:border-blue-400">
            @foreach ([10, 20, 30, 50] as $size)
                <option value="{{ $size }}" {{ request('perPage', 6) == $size ? 'selected' : '' }}>
                    {{ $size }}
                </option>
            @endforeach
        </select>
    </form>
    </div>
    </div>
      <div class="flex flex-col md:flex-row md:items-center justify-center gap-6 border-t border-gray-300 pt-6 flex-wrap text-sm text-gray-700 text-center">
    {{-- Pagination Summary --}}
    <div>
        <strong>{{ $products->firstItem() }}</strong> to <strong>{{ $products->lastItem() }}</strong>
        of <strong>{{ $products->total() }}</strong> results
        (Page {{ $products->currentPage() }} of {{ $products->lastPage() }})
    </div>
    </div>
    </section>
    @else
        <p style="text-align:center; margin-top: 2rem; color:#555;">Please select a category from above.</p>
    @endif

    {{-- Cart Pop-up Modal Structure --}}
        <div id="cartModal" class="modal">
            <div class="modal-content">
                <span class="close-button">&times;</span>
                <h2>Your Shopping Bag</h2>
                <div id="modal-cart-items">
                    {{-- Cart items will be rendered here by JavaScript --}}
                    <p class="text-center text-gray-500 empty-cart-message">Your shopping bag is empty.</p>
                </div>
                <div class="modal-cart-summary">
                    <p>Total: $<span id="modal-cart-total">0.00</span></p>
                </div>
                <div class="modal-buttons">
                    <button id="continue-shopping-button">Continue Shopping</button>
                    {{-- CHANGED: Now a direct link --}}
                    <a href="{{ url('/payment') }}" id="secure-checkout-button" class="button-link">Secure Checkout</a>
                </div>
            </div>
        </div>


        {{-- JavaScript file - assuming it is in public/js/ --}}
        <script src="{{ asset('js/script.js') }}"></script>

</body>
</html>
<script>
    const variants = @json($variants);

    function changeMainImage(el) {
        document.getElementById('mainImage').src = el.src;
    }

    function updatePriceAndStock() {
        const selectedSize = document.getElementById('sizeSelect').value;
        const match = variants.find(v => v.size === selectedSize);

        if (match) {
            document.getElementById('priceDisplay').textContent = '$' + parseFloat(match.price).toFixed(2);
            document.getElementById('quantityInput').max = match.quantity;
            document.getElementById('maxQty').textContent = match.quantity;
            if (document.getElementById('quantityInput').value > match.quantity) {
                document.getElementById('quantityInput').value = match.quantity;
            }
        }
    }
</script>
