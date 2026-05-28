@extends('layouts.vendor')

@section('content')
<br/>
{{-- Success Message --}}
@if(session('success'))
    <div id="successAlert"
         class="fixed top-5 right-5 z-50 bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-bounce">

        <div class="text-2xl">✅</div>

        <div>
            <p class="font-bold">Success</p>
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    </div>

    <script>
        setTimeout(() => {
            const alert = document.getElementById('successAlert');

            if (alert) {
                alert.style.transition = '0.5s';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';

                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
@endif
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Page Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                    📦 All Orders
                </h1>
                <p class="text-gray-500 mt-1 text-sm">
                    Manage and review customer orders
                </p>
            </div>

            <!-- Stats -->
            <div class="flex flex-wrap gap-3">
                <div class="bg-blue-50 border border-blue-100 px-4 py-3 rounded-xl min-w-[160px]">
                    <p class="text-xs text-blue-600 font-medium uppercase">Total Receipts</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $totalReceipts }}</p>
                </div>

                <div class="bg-green-50 border border-green-100 px-4 py-3 rounded-xl min-w-[180px]">
                    <p class="text-xs text-green-600 font-medium uppercase">Total Amount</p>
                    <p class="text-2xl font-bold text-green-700">
                        ${{ number_format($totalAmount, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <form method="GET" action="{{ route('vendors.orders') }}"
              class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">

            <!-- Vendor -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Vendor
                </label>

                <select name="vendor_id"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Vendors</option>

                    @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}"
                            {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->store_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Status
                </label>

                <select name="status"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                        Pending
                    </option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                        Approved
                    </option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                        Rejected
                    </option>
                </select>
            </div>

            <!-- From Date -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    From Date
                </label>

                <input type="date"
                       name="from_date"
                       value="{{ request('from_date') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- To Date -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    To Date
                </label>

                <input type="date"
                       name="to_date"
                       value="{{ request('to_date') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Per Page -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Per Page
                </label>

                <select name="per_page"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach ([5,10,15,20,30,50] as $limit)
                        <option value="{{ $limit }}"
                            {{ request('per_page', 10) == $limit ? 'selected' : '' }}>
                            {{ $limit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-medium transition">
                    Filter
                </button>

                <a href="{{ route('vendors.orders') }}"
                   class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2.5 rounded-xl font-medium transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Orders -->
    <div class="space-y-6">

        @forelse ($orders as $receiptNo => $group)

            @php
                $first = $group->first();
                $totalQty = $group->sum('quantity');
                $totalPrice = $group->sum('total_price');

                $statusClass = match($first->status) {
                    'approved' => 'bg-green-100 text-green-700 border-green-200',
                    'rejected' => 'bg-red-100 text-red-700 border-red-200',
                    default => 'bg-yellow-100 text-yellow-700 border-yellow-200'
                };
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <!-- Order Header -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-6">

                        <!-- Customer Info -->
                        <div class="space-y-2 text-sm text-gray-700">
                            <h2 class="text-xl font-bold text-gray-800 mb-3">
                                Receipt #{{ $receiptNo }}
                            </h2>

                            <p>
                                <span class="font-semibold">Customer:</span>
                                {{ $first->delivery_name }}
                            </p>

                            <p>
                                <span class="font-semibold">Phone:</span>
                                {{ $first->delivery_phone }}
                            </p>

                            <p>
                                <span class="font-semibold">Address:</span>
                                {{ $first->delivery_address }}
                            </p>

                            <p>
                                <span class="font-semibold">Ordered At:</span>
                                {{ $first->created_at->format('d M Y h:i A') }}
                            </p>

                            @if($first->delivery_map)
                                <a href="{{ $first->delivery_map }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium">
                                    📍 View Location
                                </a>
                            @endif
                        </div>

                        <!-- Summary -->
                        <div class="xl:text-right space-y-2">
                            <div>
                                <p class="text-sm text-gray-500">Vendor</p>
                                <p class="font-semibold text-gray-800">
                                    {{ $first->vendor->store_name ?? 'N/A' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Total Quantity</p>
                                <p class="font-bold text-lg text-gray-800">
                                    {{ $totalQty }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Total Price</p>
                                <p class="font-bold text-2xl text-green-600">
                                    ${{ number_format($totalPrice, 2) }}
                                </p>
                            </div>

                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold border {{ $statusClass }}">
                                {{ ucfirst($first->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Receipt Image -->
                    @if ($first->receipt_image)
                        <div class="mt-5">
                            <p class="text-sm font-semibold text-gray-700 mb-2">
                                Payment Receipt
                            </p>

                            <a href="{{ asset('storage/' . $first->receipt_image) }}"
                               target="_blank">
                                <img src="{{ asset('storage/' . $first->receipt_image) }}"
                                     alt="Receipt"
                                     class="w-48 rounded-xl border shadow-sm hover:scale-105 transition duration-300">
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Products Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-4 text-left font-semibold text-gray-700">#</th>
                                <th class="px-5 py-4 text-left font-semibold text-gray-700">Product</th>
                                <th class="px-5 py-4 text-left font-semibold text-gray-700">Size</th>
                                <th class="px-5 py-4 text-left font-semibold text-gray-700">Quantity</th>
                                <th class="px-5 py-4 text-left font-semibold text-gray-700">Price</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach ($group as $index => $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-4 text-gray-600">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-800">
                                            {{ $item->product_name }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 text-gray-700">
                                        {{ $item->size }}
                                    </td>

                                    <td class="px-5 py-4 text-gray-700">
                                        {{ $item->quantity }}
                                    </td>

                                    <td class="px-5 py-4 font-semibold text-green-600">
                                        ${{ number_format($item->total_price, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Actions -->
                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    @if($first->status === 'paid')

                        <div class="flex flex-wrap gap-3">

                            <form action="{{ route('vendor.orders.approveReceipt', $receiptNo) }}"
                                  method="POST">
                                @csrf

                                <button type="submit"
                                        onclick="return confirm('Approve this receipt?')"
                                        class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-medium transition">
                                    ✅ Approve
                                </button>
                            </form>

                            <form action="{{ route('vendor.orders.rejectReceipt', $receiptNo) }}"
                                  method="POST">
                                @csrf

                                <button type="submit"
                                        onclick="return confirm('Reject this receipt?')"
                                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-medium transition">
                                    ❌ Reject
                                </button>
                            </form>

                        </div>

                    @else

                        <p class="text-sm text-gray-500 italic">
                            This receipt has already been
                            <strong>{{ $first->status }}</strong>.
                        </p>

                    @endif
                </div>
            </div>

        @empty

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="text-6xl mb-4">📦</div>

                <h3 class="text-xl font-bold text-gray-700 mb-2">
                    No Orders Found
                </h3>

                <p class="text-gray-500">
                    There are no matching orders at the moment.
                </p>
            </div>

        @endforelse
    </div>

    <!-- Pagination -->
    @if ($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-8">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ALL FORMS
    document.querySelectorAll('form').forEach(form => {

        form.addEventListener('submit', function () {

            // Prevent double submit
            if (form.dataset.submitted === 'true') {
                return false;
            }

            form.dataset.submitted = 'true';

            // Disable all submit buttons
            form.querySelectorAll('button[type="submit"]').forEach(button => {

                button.disabled = true;

                const originalText = button.innerHTML;

                button.dataset.originalText = originalText;

                button.innerHTML = `
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24">
                            <circle class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4">
                            </circle>

                            <path class="opacity-75"
                                  fill="currentColor"
                                  d="M4 12a8 8 0 018-8v8H4z">
                            </path>
                        </svg>

                        Loading...
                    </span>
                `;

                button.classList.add('opacity-75', 'cursor-not-allowed');
            });

        });

    });

});
</script>
@endsection
