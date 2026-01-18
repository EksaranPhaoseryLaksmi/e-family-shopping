@extends('layouts.vendor')

@section('content')
<br/>
<div class="max-w-8xl mx-auto bg-white p-6 rounded-xl shadow-md space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-800">All Orders</h1>

        <!-- Filter Form -->
   <form method="GET" action="{{ route('vendors.orders') }}" class="flex flex-wrap gap-3 items-end mb-6">

    {{-- Vendor Filter --}}
    <div>
        <label class="block text-sm text-gray-700">Vendor</label>
        <select name="vendor_id" class="border rounded px-3 py-2">
            <option value="">All Vendors</option>
            @foreach ($vendors as $vendor)
                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                    {{ $vendor->store_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Status Filter --}}
    <div>
        <label class="block text-sm text-gray-700">Status</label>
        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </div>

    {{-- Date Range Filters --}}
    <div>
        <label class="block text-sm text-gray-700">From</label>
        <input type="date" name="from_date" value="{{ request('from_date') }}" class="border rounded px-3 py-2" />
    </div>

    <div>
        <label class="block text-sm text-gray-700">To</label>
        <input type="date" name="to_date" value="{{ request('to_date') }}" class="border rounded px-3 py-2" />
    </div>

    {{-- Per Page --}}
    <div>
        <label class="block text-sm text-gray-700">Items Per Page</label>
        <select name="per_page" class="border rounded px-3 py-2">
            @foreach ([5, 10, 15, 20] as $limit)
                <option value="{{ $limit }}" {{ request('per_page', 5) == $limit ? 'selected' : '' }}>
                    {{ $limit }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Submit + Reset Buttons --}}
    <div class="flex gap-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mt-6">Filter</button>

        <a href="{{ route('vendors.orders') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mt-6">
            Reset
        </a>
    </div>
</form>

    </div>
<!-- Summary Stats -->
<div class="flex flex-wrap gap-2 mt-4">
    <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded shadow text-sm">
        📦 <strong>Total Receipts:</strong> {{ $totalReceipts }}
    </div>
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded shadow text-sm">
        💰 <strong>Total Order Amount:</strong> ${{ number_format($totalAmount, 2) }}
    </div>
</div>

    @forelse ($orders as $receiptNo => $group)
        @php
            $first = $group->first();
            $totalQty = $group->sum('quantity');
            $totalPrice = $group->sum('total_price');
        @endphp

        <div class="border border-gray-200 rounded-lg p-4 shadow-sm space-y-3">
            <!-- Summary -->
            <div class="flex justify-between flex-wrap">
                <div class="space-y-1">
                    <p><strong>Receipt No:</strong> {{ $receiptNo }}</p>
                    <p><strong>Name:</strong> {{ $first->delivery_name }}</p>
                    <p><strong>Address:</strong> {{ $first->delivery_address }}</p>
                    <!--<p><strong>Email:</strong> {{ $first->delivery_email }}</p>-->
                    <p><strong>Ordered At:</strong> {{ $first->created_at->format('d M Y H:i') }}</p>
                    @if ($first->receipt_image)
                    <p>
                        <strong>Customer's Receipt:</strong><br/>
                        <a href="{{ asset('storage/' . $first->receipt_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $first->receipt_image) }}"
                                alt="Receipt Image"
                                class="w-40 h-auto rounded border border-gray-300 shadow hover:scale-105 transition duration-300" />
                        </a>
                    </p>
                    @endif
                </div>
                <div class="text-right space-y-1">
                    <p><strong>Vendor:</strong> {{ $first->vendor->store_name ?? 'N/A' }}</p>
                    <p><strong>Total Quantity:</strong> {{ $totalQty }}</p>
                    <p><strong>Total Price:</strong> ${{ number_format($totalPrice, 2) }}</p>
                    <p>
                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                            {{ $first->status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                               ($first->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($first->status) }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Product Table -->
            <div class="overflow-x-auto mt-2">
                <table class="w-full text-sm text-left border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 border">#</th>
                            <th class="px-3 py-2 border">Product</th>
                            <th class="px-3 py-2 border">Size</th>
                            <th class="px-3 py-2 border">Quantity</th>
                            <th class="px-3 py-2 border">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($group as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 border">{{ $item->product_name }}</td>
                            <td class="px-3 py-2 border">{{ $item->size }}</td>
                            <td class="px-3 py-2 border">{{ $item->quantity }}</td>
                            <td class="px-3 py-2 border">${{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            @if($first->status === 'pending')
                <div class="mt-4 flex gap-3">
                    <form action="{{ route('vendor.orders.approveReceipt', $receiptNo) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            <i class="fas fa-check mr-1"></i> Approve
                        </button>
                    </form>
                    <form action="{{ route('vendor.orders.rejectReceipt', $receiptNo) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            <i class="fas fa-times mr-1"></i> Reject
                        </button>
                    </form>
                </div>
            @else
                <p class="text-sm text-gray-500 mt-2 italic">This receipt has already been {{ $first->status }}.</p>
            @endif
        </div>
    @empty
        <p class="text-gray-500 text-center">No orders found.</p>
    @endforelse
</div>
@endsection
