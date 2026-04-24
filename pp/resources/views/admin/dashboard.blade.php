<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Overview</title>
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
      <a href="{{ route('admin.dashboard') }}" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
      <a href="{{ route('admin.products') }}"><i class="fas fa-box"></i> Products</a>
       <a href="{{ route('admin.payments') }}">
                      <i class="fas fa-box"></i> Payments
                  </a>
      <a href="{{ route('admin.orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a>
      <a href="{{ route('admin.customers') }}"><i class="fas fa-user"></i> Users</a>
    </nav>

        <!-- Username & Logout -->
        @auth
        <div class="user-panel" style="display: flex; align-items: center; gap: 1rem;">
          <span style="font-weight: 500; color:rgb(49, 101, 185);">
            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
          </span>
        </div>
        <br/>
        <div class="user-panel" style="display: flex; align-items: center; gap: 1rem;">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-button" style="background-color: #ef4444; color: white; padding: 0.4rem 0.8rem; border: none; border-radius: 0.375rem; cursor: pointer;">
              <i class="fas fa-sign-out-alt"></i> Logout
            </button>
          </form>
        </div>
        @endauth
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="header">
      <script src="https://cdn.tailwindcss.com"></script>
      <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
      <div class="flex justify-between items-center w-full">
        <h1>Dashboard</h1>

      </div>
    </header>

    <!-- Stats cards -->
    <section class="stats-grid">

      <div class="stats-card">
        <h3>Total Vendors</h3>
        <p>{{ $vendor->count() }}</p>
      </div>
      <div class="stats-card">
        <h3>Pending Vendors</h3>
        <p>{{ $pendingCount }}</p>
      </div>
      <div class="stats-card">
        <h3>New Orders</h3>
        <p>{{ $newOrdersCount }}</p>
      </div>
      <div class="stats-card">
        <h3>Customers</h3>
        <p>{{ $customersCount ?? 0 }}</p>
      </div>
    </section>

    <!-- Vendors table -->
    <section class="table-wrapper">
      <h2>Pending Vendor Approvals</h2>
      <table>
        <thead>
          <tr>
            <th>Store Name</th>
            <th>Owner</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Type</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($vendors as $vendor)
            <tr>
              <td>{{ $vendor->store_name }}</td>
              <td>{{ $vendor->owner_name }}</td>
              <td>{{ $vendor->email }}</td>
              <td>{{ $vendor->phone }}</td>
              <td>
                @php
                  $types = ['1' => 'Skin Care', '2' => 'Clothes', '3' => 'Accessory', '4' => 'Education Stuff'];
                @endphp
                {{ $types[$vendor->store_type] ?? 'Unknown' }}
              </td>
              <td>
                <span class="status {{ $vendor->status === 'pending' ? 'status-pending' : 'status-approved' }}">
                  {{ ucfirst($vendor->status) }}
                </span>
              </td>
              <td class="actions">
                @if($vendor->status === 'pending')
                  <form action="{{ route('admin.vendors.approve', $vendor->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="action-btn approve" title="Approve">
                      <i class="fas fa-check"></i> Approve
                    </button>
                  </form>
                  <form action="{{ route('admin.vendors.reject', $vendor->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="action-btn reject" title="Reject">
                      <i class="fas fa-times"></i> Reject
                    </button>
                  </form>
                @else
                  <span class="no-action">No Action</span>
                @endif

                <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="edit-link" title="Edit Vendor">
                  <i class="fas fa-edit"></i> Edit
                </a>

                <form action="{{ route('admin.vendors.delete', $vendor->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="action-btn delete" title="Delete Vendor">
                    <i class="fas fa-trash-alt"></i> Delete
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center; color:#9ca3af;">No vendors found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      {{ $vendors->links() }}
    </section>
  </main>
</div>

</body>
</html>
