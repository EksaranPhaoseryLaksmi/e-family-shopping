@extends('layouts.vendor')

@section('content')
<br/>
<div class="max-w-8xl mx-auto bg-white p-6 rounded-xl shadow-md space-y-6">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">📊 Vendor Dashboard</h1>

    <!-- Overview Cards -->
    <div class="grid md:grid-cols-3 gap-6">
        <!-- Vendor Stats -->
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-lg font-semibold mb-2">Vendors</h2>
            <p class="text-green-600">✅ Approved: <strong>{{ $totalApprovedVendors }}</strong></p>
            <p class="text-yellow-600">⏳ Pending: <strong>{{ $totalPendingVendors }}</strong></p>
        </div>

        <!-- Order Status (based on receipt_no) -->
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-lg font-semibold mb-2">Orders</h2>
            <p class="text-green-600">✅ Approved: <strong>{{ $statusCounts['approved'] ?? 0 }}</strong></p>
            <p class="text-yellow-600">⏳ Pending: <strong>{{ $statusCounts['pending'] ?? 0 }}</strong></p>
            <p class="text-red-600">❌ Rejected: <strong>{{ $statusCounts['rejected'] ?? 0 }}</strong></p>
        </div>

        <!-- Total Sales (approved only) -->
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-lg font-semibold mb-2">Total Approved Sales</h2>
            <p class="text-lg text-blue-700 font-bold">💰 ${{ number_format($totalApprovedAmount, 2) }}</p>
        </div>
    </div>

    <!-- Per-Vendor Table -->
    <div class="bg-white rounded shadow p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">📦 Products and Orders by Vendor</h2>
        <table class="w-full text-sm text-left border border-gray-200">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 border">Vendor</th>
                    <th class="px-4 py-2 border text-center">Total Products</th>
                    <th class="px-4 py-2 border text-center">Total Orders (by Receipt)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vendors as $vendor)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2 border">{{ $vendor->store_name }}</td>
                        <td class="px-4 py-2 border text-center">
                            {{ $productsPerVendor[$vendor->id]->total_products ?? 0 }}
                        </td>
                        <td class="px-4 py-2 border text-center">
                            {{ $ordersPerVendor[$vendor->id] ?? 0 }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
