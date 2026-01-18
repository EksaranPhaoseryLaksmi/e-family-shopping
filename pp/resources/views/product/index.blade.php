@extends('layouts.vendor')

@section('content')
<br/>
<div class="max-w-8xl mx-auto bg-white p-6 rounded-xl shadow-md space-y-6">
<h2 class="text-2xl font-bold text-gray-800">📋 Products</h2>
    <!-- Header and Search Bar -->
    <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
<form action="{{ route('store.products', $store->id)}}" method="GET" class="flex flex-col md:flex-row gap-3 items-center mb-6">
    <div>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Search by name..."
        class="px-4 py-2 border rounded-md w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-blue-500" />
    </div>
    <div>
        <label for="per_page" class="text-sm text-gray-700">Items per page:</label>
        <select name="per_page" id="per_page" onchange="this.form.submit()"
            class="border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-2 py-1">
            @foreach ([5, 10, 20, 40, 50] as $count)
                <option value="{{ $count }}" {{ $perPage == $count ? 'selected' : '' }}>{{ $count }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Search
        </button>
    </div>
</form>
</div>
    @if(session('success'))
        <div class="mb-4 text-green-700 bg-green-100 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif
    <!-- Products Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border border-gray-200">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 border">Image</th>
                    <th class="px-4 py-2 border">Product</th>
                    <th class="px-4 py-2 border">Variants</th>
                    <th class="px-4 py-2 border">Store</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2 border">
                            @php $images = $product->image ?? []; @endphp
                            @if(count($images))
                                <img src="{{ asset('storage/' . $images[0]) }}" class="w-16 h-16 object-cover rounded">
                            @else
                                <span class="text-gray-400">No Image</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border">
                            <div class="font-semibold">{{ $product->name }}</div>
                            <div class="text-xs text-gray-600">{{ Str::limit($product->description, 60) }}</div>
                        </td>
                        <td class="px-4 py-2 border text-xs space-y-1">
                            @foreach ($product->variants ?? [] as $variant)
                                <div>
                                    📏 <strong>{{ $variant['size'] }}</strong>,
                                    💰 ${{ number_format($variant['price'], 2) }},
                                    📦 {{ $variant['quantity'] }}
                                </div>
                            @endforeach
                        </td>
                        <td class="px-4 py-2 border text-sm">
                            {{ $product->store->store_name ?? 'N/A' }}<br>
                            <span class="text-xs text-gray-500">
                                @php
                                    $types = [1 => 'Skincare', 2 => 'Clothing', 3 => 'Accessories', 4 => 'Education'];
                                    echo $types[$product->store->store_type ?? 0] ?? 'Unknown';
                                @endphp
                            </span>
                        </td>
                        <td class="px-4 py-2 border text-sm">
                            <a href="{{ route('product.edit', $product->id) }}" class="text-yellow-600 hover:underline block">✏️ Edit</a>
                            <details class="inline-block">
                                <summary class="text-red-600 hover:underline cursor-pointer mt-1">🗑️ Delete</summary>
                                <div class="mt-2 bg-white border rounded-lg shadow-md p-4 w-64">
                                    <p class="text-gray-800 mb-4">Are you sure you want to delete this product?</p>
                                    <div class="flex justify-end space-x-4">
                                        <button onclick="this.closest('details').removeAttribute('open')" type="button"
                                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">No</button>
                                        <form action="{{ route('product.destroy', $product->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Yes</button>
                                        </form>
                                    </div>
                                </div>
                            </details>     
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- Only call links if $products is paginated --}}
@if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endif

    </div>
</div>
@endsection
