<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Overview</title>
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
      <a href="{{ route('admin.payments') }}" class="active">
          <i class="fas fa-box"></i> Payments
      </a>
      <a href="{{ route('admin.orders') }}" ><i class="fas fa-shopping-cart"></i> Orders</a>
      <a href="{{ route('admin.customers') }}"><i class="fas fa-user"></i> Users</a>
    </nav>
  </aside>
  <!-- Main Content -->
  <main class="main-content">
    <header class="header">
      <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
      <div class="flex justify-between items-center w-full">
        <h1>Payments</h1>
      </div>
      <script src="https://cdn.tailwindcss.com"></script>
    </header>

    <!-- Filter form -->

    <!-- Vendors Orders Summary Table -->
    <section class="table-wrapper">
      <h2>Payment Management</h2>
      <table>
        <thead>
          <tr>
            <th>Ref</th>
            <th>User</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Expires</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          @forelse($payments as $payment)
            <tr>
              <td>{{ $payment->payment_ref }}</td>

              <td>{{ $payment->user->name ?? '-' }}</td>

              <td>${{ number_format($payment->amount, 2) }}</td>

              <td>
                <span class="status
                  {{ $payment->status === 'paid' ? 'status-approved' : '' }}
                  {{ $payment->status === 'pending' ? 'status-pending' : '' }}
                  {{ $payment->status === 'expired' ? 'status-rejected' : '' }}
                ">
                  {{ ucfirst($payment->status) }}
                </span>
              </td>

              <td>{{ $payment->expires_at }}</td>

              <td class="actions">

                <!-- 🔁 CHECK PAYMENT -->
                @if($payment->status === 'pending')
                  <form action="{{ route('admin.payments.check', $payment->bakong_md5) }}" method="POST">
                    @csrf
                    <button class="action-btn approve">
                      <i class="fas fa-sync"></i> Check
                    </button>
                  </form>
                @endif

                <!-- 👁 VIEW -->
                <a href="{{ route('admin.payments.view', $payment->payment_ref) }}" class="edit-link">
                  <i class="fas fa-eye"></i> View
                </a>

              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center;">No payments found</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      {{ $payments->links() }}
    </section>
  </main>
</div>

</body>
</html>
