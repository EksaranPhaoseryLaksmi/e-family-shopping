<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Overview</title>
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
      <a href="{{ route('admin.payments') }}">
                <i class="fas fa-box"></i> Payments
            </a>
      <a href="{{ route('admin.orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a>
      <a href="{{ route('admin.customers') }}" class="active"><i class="fas fa-user"></i> Users</a>
    </nav>
  </aside>

<!-- Main Content -->
<main class="main-content">
  <header class="header">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <h1>Users</h1>
  </header>

  <!-- Stats cards -->
    <section class="stats-grid">
      <div class="stats-card">
        <h3>Vendors</h3>
        <p>{{ $vendors->count() }}</p>
      </div>
      <!-- Revenue card commented as per your request -->
      <!--<div class="stats-card">
        <h3>Revenue</h3>
        <p>$254,000</p>
      </div>-->
      <div class="stats-card">
        <h3>Customers</h3>
        <p>{{ $customersCount ?? 0 }}</p>
      </div>
    </section>
<form method="GET" action="{{ route('admin.customers') }}" class="mb-4">
    <label for="role">Filter by Role:</label>
    <select name="role" id="role" onchange="this.form.submit()">
        <option value="user" {{ $selectedRole === 'user' ? 'selected' : '' }}>Customer</option>
        <option value="vendor" {{ $selectedRole === 'vendor' ? 'selected' : '' }}>Vendor</option>
    </select>
</form>
    <!-- Vendors table -->
    <section class="table-wrapper">
  <div>
    <table>
      <thead>
          <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Joined At</th>
              <th>Status</th> <!-- new -->
              <th>Action</th> <!-- new -->
          </tr>
      </thead>
      <tbody>
      @forelse($customers as $customer)
      <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $customer->name }}</td>
          <td>{{ $customer->email }}</td>
          <td>{{ $customer->created_at->format('d M Y H:i') }}</td>

          <!-- Status -->
          <td>
              @if($customer->status)
                  <span class="text-green-600 font-bold">Active</span>
              @else
                  <span class="text-red-600 font-bold">Disabled</span>
              @endif
          </td>

          <!-- Action Button -->
          <td class="space-y-2">

              <!-- Enable / Disable -->
              <form method="POST" action="{{ route('admin.users.toggle', $customer->id) }}">
                  @csrf
                  <button type="submit"
                      class="px-3 py-1 rounded text-white w-full
                      {{ $customer->status ? 'bg-red-500' : 'bg-green-500' }}">
                      {{ $customer->status ? 'Disable' : 'Enable' }}
                  </button>
              </form>

              <!-- Resend Verification (ONLY if not verified) -->
              @if(is_null($customer->email_verified_at))
                  <form method="POST" action="{{ route('admin.users.resendVerification', $customer->id) }}">
                      @csrf
                      <button type="submit"
                          class="px-3 py-1 rounded bg-blue-500 text-white w-full">
                          Resend Verify
                      </button>
                  </form>
              @endif

              <!-- Reset Password -->
              <form method="POST" action="{{ route('admin.users.resetPassword', $customer->id) }}">
                  @csrf
                  <button type="submit"
                      class="px-3 py-1 rounded bg-yellow-500 text-black w-full">
                      Reset Password
                  </button>
              </form>

          </td>

      </tr>
      @empty
      <tr>
          <td colspan="6" style="text-align:center;">No customers found.</td>
      </tr>
      @endforelse
      </tbody>
    </table>
  </div>

    <div class="pagination-links">
    {{ $customers->appends(['role' => $selectedRole])->links() }}
</div>

    </section>
  </main>
</div>

</body>
</html>
