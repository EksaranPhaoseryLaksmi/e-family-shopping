<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendors Overview</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">

<div class="flex-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">Admin Panel</div>
        <nav>
            <a href="{{route('admin.dashboard')}}"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="{{ route('admin.products') }}" class="active"><i class="fas fa-box" class="active"></i> Products</a>
            <a href="{{ route('admin.orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="{{ route('admin.customers') }}"><i class="fas fa-user"></i> Users</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content w-full p-6">
        <h1 class="text-2xl font-bold mb-6">Products</h1>

        <!-- Filter -->
        <form method="GET" class="flex flex-wrap gap-4 mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Name or Email"
                   class="border rounded px-4 py-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
        </form>

        <!-- Totals -->
        <div class="mb-4 font-semibold text-gray-700">
            Total Users: <span class="font-bold">{{ $vendors->total() }}</span> |
            Total Vendor Stores: <span class="font-bold">{{ $totalVendorStores }}</span>
             | <strong>{{ $totalProducts }}</strong> total products on this page
        </div>

        <!-- Table -->
        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
          <table>
  <thead>
    <tr>
      <th>#</th>
      <th>User Name</th>
      <th>Email</th>
      <th>Total Stores</th>
    </tr>
  </thead>
  <tbody>
    @forelse ($vendors as $vendor)
      <tr>
        <td>{{ $loop->iteration + ($vendors->currentPage() - 1) * $vendors->perPage() }}</td>
        <td>{{ $vendor->name }}</td>
        <td>{{ $vendor->email }}</td>
        <td>{{ $vendor->vendor_requests_count }}</td>
      </tr>
      @foreach ($vendor->vendor_requests as $store)
        <tr class="bg-gray-50 text-sm">
          <td></td>
          <td colspan="1"><strong>{{ $store->store_name }}</strong></td>
          <td>
            @php
              $types = [1 => 'Skincare', 2 => 'Clothing', 3 => 'Accessories', 4 => 'Education'];
            @endphp
            {{ $types[$store->store_type] ?? 'Unknown' }}
          </td>
          <td>
            🛍️ {{ $store->products->count() }} products<br>
            📅 {{ $store->created_at->format('d M Y') }}
          </td>
        </tr>
      @endforeach
    @empty
      <tr>
        <td colspan="4">No vendor data found.</td>
      </tr>
    @endforelse
  </tbody>
</table>

<div class="mt-4 text-sm text-gray-600">
  Showing <strong>{{ $vendors->firstItem() }}</strong> to <strong>{{ $vendors->lastItem() }}</strong>
  of <strong>{{ $vendors->total() }}</strong> users
</div>

<div class="mt-2">
  {{ $vendors->appends(request()->query())->links() }}
</div>
    </main>
</div>

</body>
</html>
