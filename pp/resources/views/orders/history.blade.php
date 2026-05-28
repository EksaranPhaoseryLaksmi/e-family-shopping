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
                <img src="{{ asset('photos/e-commerce_logo.jpg') }}" alt="E-commerce Logo">
            </div>
        </a>
        <nav class="navbar">
            <ul>
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
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;" class="logout-form">
                            @csrf
                            <button type="submit" class="submit-btn icon-button" id="logout-icon-button">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    @endguest
                </li>
            </ul>
        </nav>
    </header>

<br/>
<br/>
<br/>
<br/>
<div class="max-w-8xl mx-auto bg-white p-6 rounded-xl shadow-md space-y-6">

    <h1 class="text-2xl font-bold mb-6">Orders History</h1>

    @if(session('success'))
    <div id="alertBoxSuccess" class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div id="alertBoxError" class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
        {{ session('error') }}
    </div>
    @endif

    <form method="GET" action="{{ route('orders.history') }}" class="mb-6 flex flex-wrap gap-4 items-end filter-form">
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
            <button type="submit" class="submit-btn px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                <span class="btn-text">Filter</span>
            </button>
            <a href="{{ route('orders.history') }}" onclick="showLoading()" class="reset-btn px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-800 hover:bg-gray-200 transition">
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
    <div class="hidden md:block">
        @foreach($orders as $receiptNo => $group)
            @php
                $first = $group->first();
                $groupTotal = $group->sum('total_price');
            @endphp
            <div class="mb-10 border border-gray-300 rounded-lg p-4 shadow-sm bg-white">
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

                <div class="text-sm text-gray-700 mb-3">
                    <p>
                        <strong>Delivery:</strong> {{ $first->delivery_name }},
                        {{ $first->delivery_address }}, {{ $first->delivery_phone }},
                        @if($first->delivery_map)
                            <a href="{{ $first->delivery_map }}" target="_blank" class="text-blue-600 hover:underline">
                                📍 View Map
                            </a>
                        @else
                            <span class="text-gray-400">No map provided</span>
                        @endif
                    </p>
                    <p><strong>Order Date:</strong> {{ $first->created_at->format('Y-m-d') }}</p>
                </div>

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

                <table class="w-full table-auto border border-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 text-left">Product</th>
                            <th class="border px-3 py-2 text-center">Quantity</th>
                            <th class="border px-3 py-2 text-right">Total Price</th>
                            <th class="border px-3 py-2 text-left">Vendor</th>
                            <th class="border px-3 py-2 text-center">Status</th>
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
                            <td class="border px-3 py-2 text-center">{{ $order->quantity }}</td>
                            <td class="border px-3 py-2 text-right">$ {{ number_format($order->total_price, 2) }}</td>
                            <td class="border px-3 py-2">{{ $order->vendor->store_name ?? 'N/A' }}</td>
                            <td class="border px-3 py-2 text-center">
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="text-right mt-2 font-semibold text-gray-800">
                    Total: ${{ number_format($groupTotal, 2) }}
                </div>
            </div>
        @endforeach

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-8 border-t pt-4 text-sm text-gray-700">
            {{-- Per Page Filter --}}
            <form method="GET" action="{{ route('orders.history') }}" class="flex items-center space-x-2 per-page-form">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                <input type="hidden" name="to_date" value="{{ request('to_date') }}">

                <label for="perPage">Items Per Page:</label>
                <select name="perPage" id="perPage" onchange="showLoading(); this.form.submit()"
                    class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring focus:border-blue-400">
                    @foreach ([5, 10, 20, 30, 50] as $size)
                        <option value="{{ $size }}" {{ request('perPage', 5) == $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
            </form>

            {{-- Pagination Links --}}
            <div class="pagination-container">
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

<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3">
        <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
        <span class="text-lg font-semibold">Loading Orders...</span>
    </div>
</div>

<script>
// ==========================================
// LOADING STATE MANAGERS
// ==========================================
function showLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (!overlay) return;
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (!overlay) return;
    overlay.classList.add('hidden');
    overlay.classList.remove('flex');

    document.querySelectorAll('.submit-btn').forEach(btn => {
        btn.disabled = false;
        const textSpan = btn.querySelector('.btn-text');
        if (textSpan) {
            btn.innerHTML = `<span class="btn-text">${textSpan.innerText}</span>`;
        } else if (btn.id === 'logout-icon-button') {
            btn.innerHTML = '<i class="fas fa-sign-out-alt"></i>';
        }
    });
}

// ==========================================
// CORE INTERACTION LISTENERS
// ==========================================
document.addEventListener('DOMContentLoaded', function () {

    // Auto Dismiss Alert Boxes Smoothly
    document.querySelectorAll('#alertBoxSuccess, #alertBoxError').forEach(alertBox => {
        setTimeout(() => {
            alertBox.style.transition = 'opacity 0.5s ease';
            alertBox.style.opacity = '0';
            setTimeout(() => alertBox.remove(), 500);
        }, 3000);
    });

    // Only handle form loading displays for filters/internal updates
    document.querySelectorAll('form.filter-form, form.per-page-form').forEach(form => {
        form.addEventListener('submit', function () {
            showLoading();
            form.querySelectorAll('.submit-btn').forEach(btn => {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            });
        });
    });

    // Intercept inside native pagination item components only
    document.querySelectorAll('.pagination-container a').forEach(pagLink => {
        pagLink.addEventListener('click', function() {
            showLoading();
        });
    });
});

// ==========================================
// ANTI-STICKING LIFECYCLE INTERCEPTS
// ==========================================
window.addEventListener('pageshow', function (event) {
    hideLoading();
});

window.addEventListener('pagehide', function () {
    hideLoading();
});

document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        hideLoading();
    }
});
</script>
</body>
</html>
