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
      @if(session('success'))
          <div id="alertBox"
               class="mb-4 p-4 rounded bg-green-100 text-green-800 border border-green-300">
              {{ session('success') }}
          </div>
      @endif

      @if(session('error'))
          <div id="alertBox"
               class="mb-4 p-4 rounded bg-red-100 text-red-800 border border-red-300">
              {{ session('error') }}
          </div>
      @endif
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
                    <button type="submit" class="action-btn approve submit-btn">
                        <i class="fas fa-sync"></i>
                        <span class="btn-text">Check</span>
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
<!-- Loading Overlay -->
<div id="loadingOverlay"
     class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">

    <div class="bg-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3">
        <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
        <span class="text-lg font-semibold">
            Processing Payment...
        </span>
    </div>

</div>
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Auto hide alert
    const alertBox = document.getElementById("alertBox");

    if (alertBox) {
        setTimeout(() => {
            alertBox.style.transition = "0.5s";
            alertBox.style.opacity = "0";

            setTimeout(() => {
                alertBox.remove();
            }, 500);

        }, 3000);
    }

    // Handle form submit loading
    const forms = document.querySelectorAll("form");

    forms.forEach(form => {

        form.addEventListener("submit", function () {

            // Show loading overlay
            const overlay = document.getElementById("loadingOverlay");

            overlay.classList.remove("hidden");
            overlay.classList.add("flex");

            // Disable buttons
            const buttons = form.querySelectorAll("button");

            buttons.forEach(btn => {

                btn.disabled = true;

                const text = btn.querySelector(".btn-text");

                if (text) {
                    text.innerText = "Checking...";
                }

                btn.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> Processing...';
            });

        });

    });

});
</script>
</body>
</html>
