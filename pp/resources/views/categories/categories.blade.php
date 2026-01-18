<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Categories - E-Commerce Platform</title>
    <link rel="icon" href="{{ asset('photos/favicon.ico') }}" type="image/x-icon">

    {{-- Tailwind CSS CDN (optional, for styling) --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<header class="p-4 bg-white shadow mb-6">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <a href="{{ url('/') }}" class="text-xl font-bold text-gray-900">E-Commerce Logo</a>
        <nav>
            <a href="{{ url('/') }}" class="mr-4 hover:underline">Home</a>
            <a href="{{ route('categories.index') }}" class="font-semibold underline">Categories</a>
            <a href="{{ url('/payment') }}" class="ml-4 hover:underline">Shopping Cart</a>
            <a href="{{ url('/login') }}" class="ml-4 hover:underline">Log In</a>
        </nav>
    </div>
</header>

<div class="max-w-7xl mx-auto px-4">

    @foreach ($products as $category => $items)
    <section id="{{ strtolower($category) }}" class="mb-12">
        <h2 class="text-3xl font-bold mb-6">{{ $category }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($items as $product)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                <div class="h-48 bg-gray-100 mb-4 overflow-hidden rounded">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
                <h3 class="font-semibold text-lg mb-1">{{ $product->name }}</h3>
                <p class="text-gray-700 mb-1">Price: ${{ number_format($product->price, 2) }}</p>
                <p class="text-gray-700 mb-2">Quantity: {{ $product->quantity }}</p>
                <p class="text-gray-700 mb-2">Size: {{ $product->size ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500 mb-2">
                    Store: <strong>{{ $product->store->name ?? 'N/A' }}</strong><br>
                    Type: <strong>{{ $product->store->store_type ?? 'N/A' }}</strong>
                </p>
                <button class="mt-auto bg-black text-white py-2 rounded hover:bg-gray-800">+ Add To Cart</button>
            </div>
            @endforeach
        </div>
    </section>
    @endforeach

    @if($products->isEmpty())
        <p class="text-center text-gray-500">No products available.</p>
    @endif

</div>

<footer class="mt-12 p-6 bg-white text-center text-gray-500 text-sm">
    &copy; {{ date('Y') }} Your E-Commerce Platform
</footer>

</body>
</html>
