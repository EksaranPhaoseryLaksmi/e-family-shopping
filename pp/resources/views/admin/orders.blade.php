<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Overview</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">

<!-- Sidebar -->
<div class="flex-container">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-header">Admin Panel</div>

    <nav>
      <a href="{{ route('admin.dashboard') }}" ><i class="fas fa-chart-line"></i> Dashboard</a>
      <a href="{{ route('admin.products') }}"><i class="fas fa-box"></i> Products</a>
      <a href="{{ route('admin.orders') }}" class="active"><i class="fas fa-shopping-cart"></i> Orders</a>
      <a href="{{ route('admin.customers') }}"><i class="fas fa-user"></i> Users</a>
    </nav>
  </aside>
  <!-- Main Content -->
  <main class="main-content">
    <header class="header">
      <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
      <div class="flex justify-between items-center w-full">
        <h1>Orders</h1>
      </div>
      <script src="https://cdn.tailwindcss.com"></script>
    </header>

    <!-- Filter form -->
    <form method="GET" class="mb-4">
      <input type="text" name="search" placeholder="User name or email"
             value="{{ request('search') }}" class="border rounded px-3 py-2" />
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <!-- Stats cards -->
    <section class="stats-grid mb-6">
      <div class="stats-card"><h3>Pending Orders</h3><p>{{ $totalPending }}</p></div>
      <div class="stats-card"><h3>Approved Orders</h3><p>{{ $totalApproved }}</p></div>
      <div class="stats-card"><h3>Rejected Orders</h3><p>{{ $totalRejected }}</p></div>
      <div class="stats-card"><h3>Total Orders</h3><p>{{ $totalOrders }}</p></div>
    </section>

    <!-- Vendors Orders Summary Table -->
    <section class="table-wrapper">
      <table class="min-w-full border border-gray-200 text-left text-sm">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="px-4 py-2 border">#</th>
            <th class="px-4 py-2 border">Vendor Name</th>
            <th class="px-4 py-2 border">Vendor Email</th>
            <th class="px-4 py-2 border">Store Name</th>
            <th class="px-4 py-2 border">Store Type</th>
            <th class="px-4 py-2 border">Created At</th>
            <th class="px-4 py-2 border">Pending Orders</th>
            <th class="px-4 py-2 border">Approved Orders</th>
            <th class="px-4 py-2 border">Rejected Orders</th>
            <th class="px-4 py-2 border">Total Orders</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($stores as $store)
          <tr class="border-t hover:bg-gray-50">
            <td class="px-4 py-2 border">{{ $loop->iteration + ($stores->currentPage() - 1) * $stores->perPage() }}</td>
            <td class="px-4 py-2 border">{{ $store->user->name ?? 'N/A' }}</td>
            <td class="px-4 py-2 border">{{ $store->user->email ?? 'N/A' }}</td>
            <td class="px-4 py-2 border">{{ $store->store_name }}</td>
            <td class="px-4 py-2 border">
              @php
                $types = [1 => 'Skincare', 2 => 'Clothing', 3 => 'Accessories', 4 => 'Education'];
                echo $types[$store->store_type] ?? 'Unknown';
              @endphp
            </td>
            <td class="px-4 py-2 border">{{ $store->created_at->format('d M Y') }}</td>
            <td class="px-4 py-2 border">{{ $store->pending_orders_count }}</td>
            <td class="px-4 py-2 border">{{ $store->approved_orders_count }}</td>
            <td class="px-4 py-2 border">{{ $store->rejected_orders_count }}</td>
            <td class="px-4 py-2 border">{{ $store->total_orders_count }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="px-4 py-4 text-center text-gray-500">No stores found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="mt-4">
        {{ $stores->appends(request()->query())->links() }}
      </div>
    </section>
  </main>
</div>

</body>
</html>
