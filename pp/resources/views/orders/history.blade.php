<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order - E-Commerce Platform</title>
 <script src="https://cdn.tailwindcss.com"></script>
    {{-- CSS files - assuming they are in public/css/ --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
<style>
 .hide {
            display: none !important;
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
                <li><a href="{{ route('orders.history') }}">Orders</a></li>
                <li><a href="{{ url('/#templates') }}">Templates</a></li>
                <li><a href="{{ url('/#success') }}">Feedback</a></li>
                <li><a href="{{ url('/#contact') }}">Contact</a></li>
                <li><a href="{{ url('/#about') }}">About Us</a></li>
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
<div class="max-w-8xl mx-auto bg-white p-6 rounded-xl shadow-md space-y-6">

    <h1 class="text-2xl font-bold mb-6">Orders History</h1>
<form method="GET" action="{{ route('orders.history') }}" class="mb-6 flex flex-wrap gap-4 items-end">
    {{-- Search by Store or Product Name --}}
    <div>
        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
        <input type="text" name="search" id="search" value="{{ request('search') }}"
            placeholder="Store or Product Name"
            class="mt-1 block w-56 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>

    {{-- Status Dropdown --}}
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" id="status"
            class="mt-1 block w-44 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <option value="">All</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </div>

    {{-- Date Filters --}}
    <div>
        <label for="from_date" class="block text-sm font-medium text-gray-700">From</label>
        <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}"
            class="mt-1 block w-44 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>

    <div>
        <label for="to_date" class="block text-sm font-medium text-gray-700">To</label>
        <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}"
            class="mt-1 block w-44 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
    </div>

    {{-- Buttons --}}
    <div class="flex items-center gap-2">
        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Filter</button>
        <a href="{{ route('orders.history') }}"
            class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-800 hover:bg-gray-200 transition">
            Reset
        </a>
    </div>
</form>

<div class="mb-4 text-sm font-medium flex flex-wrap gap-4">
    <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
        Total Receipts: <strong class="ml-1">{{ $totalReceipts }}</strong>
    </span>
    <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full">
        Total Amount: <strong class="ml-1">${{ number_format($totalAmount, 2) }}</strong>
    </span>
</div>


@if($orders->isEmpty())
    <p class="text-gray-600">You haven't placed any orders yet.</p>
@else
  <!-- Desktop Layout -->
<div class="hidden md:block">
    @foreach($orders as $receiptNo => $group)
        @php
            $first = $group->first();
            $groupTotal = $group->sum('total_price');
        @endphp
        <div class="mb-10 border border-gray-300 rounded-lg p-4 shadow-sm bg-white">
            <!-- Header -->
            <div class="hide">
                <h2 class="text-lg font-semibold text-gray-800">Receipt #: {{ $receiptNo }}</h2>
                @php
                $status = strtolower($first->status);
                $statusLabel = $status === 'rejected' ? 'Refund' : ucfirst($status);
                $statusColor = match($status) {
                    'approved' => 'bg-blue-500',
                    'rejected' => 'bg-red-500',
                    default => 'bg-yellow-500'
                };
                @endphp
                <span class="text-sm text-white {{ $statusColor }} px-3 py-1 rounded">
                    Status: {{ $statusLabel }}
                </span>
            </div>

            <!-- Delivery & Date -->
            <div class="text-sm text-gray-700 mb-3">
                <p>
                    <strong>Delivery:</strong> {{ $first->delivery_name }},
                    {{ $first->delivery_address }}
                </p>
                <p><strong>Order Date:</strong> {{ $first->created_at->format('Y-m-d') }}</p>
            </div>
            <!-- Receipt Image -->
            @if ($first->receipt_image)
                <div class="mb-3">
                    <p class="text-sm text-gray-700 font-medium">Receipt Image:</p>
                    <a href="{{ asset('storage/' . $first->receipt_image) }}" target="_blank">
                        <img src="{{ asset('storage/' . $first->receipt_image) }}"
                            alt="Receipt"
                            class="w-40 h-auto mt-2 rounded border border-gray-300 shadow hover:scale-105 transition duration-300" />
                    </a>
                </div>
            @endif
            <!-- Product Table -->
            <table class="w-full table-auto border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2">Product</th>
                        <th class="border px-3 py-2">Quantity</th>
                        <th class="border px-3 py-2">Total Price</th>
                        <th class="border px-3 py-2">Vendor</th>
                        <th class="border px-3 py-2">Status</th> {{-- NEW --}}
                    </tr>
                </thead>
                <tbody>
                @foreach($group as $order)
                    @php
                        $orderStatus = strtolower($order->status);
                        $statusText = ucfirst($orderStatus);
                        $statusClass = match($orderStatus) {
                            'approved' => 'bg-green-100 text-green-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            default => 'bg-yellow-100 text-yellow-700'
                        };
                    @endphp

                    <tr class="hover:bg-gray-50">
                        <td class="border px-3 py-2">{{ $order->product_name }}</td>
                        <td class="border px-3 py-2">{{ $order->quantity }}</td>
                        <td class="border px-3 py-2">$ {{ number_format($order->total_price, 2) }}</td>
                        <td class="border px-3 py-2">{{ $order->vendor->store_name ?? 'N/A' }}</td>
                        <td class="border px-3 py-2">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>

            <!-- Total Price Footer -->
            <div class="text-right mt-2 font-semibold text-gray-800">
                Total: ${{ number_format($groupTotal, 2) }}
            </div>
        </div>
    @endforeach
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-8 border-t pt-4 text-sm text-gray-700">

    {{-- Per Page Filter --}}
    <form method="GET" action="{{ route('orders.history') }}" class="flex items-center space-x-2">
        {{-- Keep current filters --}}
        <input type="hidden" name="shop" value="{{ request('shop') }}">
        <input type="hidden" name="product" value="{{ request('product') }}">
        <input type="hidden" name="from_date" value="{{ request('from_date') }}">
        <input type="hidden" name="to_date" value="{{ request('to_date') }}">

        <label for="perPage">Items Per Page:</label>
        <select name="perPage" id="perPage" onchange="this.form.submit()"
            class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring focus:border-blue-400">
            @foreach ([5, 10, 20, 30,50] as $size)
                <option value="{{ $size }}" {{ request('perPage', 5) == $size ? 'selected' : '' }}>
                    {{ $size }}
                </option>
            @endforeach
        </select>
    </form>
    {{-- Pagination Links --}}
    <div>
        {{ $orders->links() }}
    </div>

</div>

</div>

@endif

</div>
<br/>
<br/>
<br/>
<br/>
<br/>
</body>
</html>
