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
        <h1>Payment Detail</h1>
      </div>
      <script src="https://cdn.tailwindcss.com"></script>
    </header>

    <!-- Filter form -->

    <!-- Vendors Orders Summary Table -->
    <section class="table-wrapper">


        <p><strong>Ref:</strong> {{ $payment->payment_ref }}</p>
        <p><strong>Status:</strong> {{ $payment->status }}</p>
        <p><strong>Amount:</strong> ${{ $payment->amount }}</p>
        <p><strong>MD5:</strong> {{ $payment->bakong_md5 }}</p>

        <p><strong>Cart:</strong></p>
        <pre>{{ json_encode(json_decode($payment->cart), JSON_PRETTY_PRINT) }}</pre>
    </section>
  </main>
</div>

</body>
</html>

