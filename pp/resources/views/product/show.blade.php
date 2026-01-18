{{-- resources/views/categories.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Categories - E-Commerce Platform</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
 <script src="https://cdn.tailwindcss.com"></script>
    {{-- CSS files - assuming they are in public/css/ --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/categories.css') }}" />
    <style>
        body {
            font-family: sans-serif;
            background-color:rgb(209, 211, 212);
        }

        .category-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 1.5rem 0 1rem;
            color:rgb(64, 130, 221);
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
            box-shadow: 0 2px 10px rgb(209, 211, 212);;
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
            color:rgb(209, 211, 212);;
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
        /* Flash animation for cart icon */
@keyframes flash-cart {
    0% { transform: scale(1); }
    25% { transform: scale(1.2); }
    50% { transform: scale(1); }
    75% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.cart-flash {
    animation: flash-cart 0.6s ease-in-out;
}

    </style>
</head>
<body>
    <header class="header">
        {{-- Logo as a link to home page (using Laravel's url() helper) --}}
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
                <li><a href="{{ url('/#about') }}">About Us</a></li>
                <li><a href="{{ url('/#templates') }}">Templates</a></li>
                <li><a href="{{ url('/#success') }}">Feedback</a></li>
                <li><a href="{{ url('/#contact') }}">Contact</a></li>
                <li><a href="{{ route('orders.history') }}">Orders</a></li>
                {{-- New Icon Buttons for Cart and Account --}}
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
                                <i class="fas fa-sign-out-alt"></i> {{-- Or a user icon if staying logged in --}}
                            </button>
                        </form>
                    @endguest
                </li>
            </ul>
        </nav>
    </header>
</head>
<body>
<br/>
<br/>
<br/>
<br/>
<div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow-md space-y-6"">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- 🔄 Carousel for images --}}
        <div class="relative">
            <div id="carouselImages">
                <img id="mainImage" src="{{ asset('storage/' . $product->image[0]) }}" class="w-full h-[400px] object-cover rounded">
            </div>
            <div class="flex gap-2 mt-4 overflow-x-auto">
                @foreach($product->image as $img)
                    <img src="{{ asset('storage/' . $img) }}" class="h-20 w-20 object-cover cursor-pointer rounded border border-gray-300 thumb-img" onclick="changeMainImage(this)">
                @endforeach
            </div>
        </div>
{{-- ℹ️ Product Info --}}
<div class="product-item"
     data-id="{{ $product->id }}"
     data-name="{{ $product->name }}"
     data-price="{{ $defaultVariant['price'] }}"
     data-image="{{ asset('storage/' . $product->image[0]) }}"
     data-vendor="{{$product->vendor_request_id}}">

    <h2 class="text-3xl text-gray-800 mb-2">{{ $product->name }}</h2>
    <div class="mb-4">
        <label class="block font-mb-1">Size:</label>
        <select id="sizeSelect" onchange="updatePriceAndStock()" class="border p-2 rounded w-full">
            @foreach($sizes as $size)
                <option value="{{ $size }}">{{ $size }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block font-mb-1">Price:</label>
        <p id="priceDisplay" class="text-xl text-green-500 font-bold">${{ number_format($defaultVariant['price'] , 2)}}</p>
    </div>

    <div class="mb-4">
        <label class="block font-mb-1">Quantity (max <span id="maxQty">{{ $defaultVariant['quantity'] }}</span>):</label>
        <input type="number" id="quantityInput" class="border p-2 rounded w-full" min="1" max="{{ $defaultVariant['quantity'] }}" value="1">
    </div>
    <div>
    <label class="block font-mb-2">Detail:</label>
    <p class="text-gray-700 leading-relaxed">
        {{ $product->description }}
    </p>
    <br/>
    </div>
    <button class="add-to-cart">+ Add To Cart</button>
</div>

    </div>

    {{-- 🔁 Related Products --}}
    <div class="mt-14">
        <h3 class="text-2xl font-semibold mb-6 text-gray-700">Related Products</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($relatedProducts as $related)
                <a href="{{ route('product.show', $related->id) }}" class="bg-white shadow rounded-lg overflow-hidden hover:shadow-xl transition">
                    <img src="{{ asset('storage/' . $related->image[0]) }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-800 truncate">{{ $related->name }}</h4>
                        @php
                            $first = collect($related->variants)->first();
                        @endphp
                        <p class="text-green-500 font-bold">${{ number_format($first['price'],2) ?? '0.00' }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
{{-- Footer --}}
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
