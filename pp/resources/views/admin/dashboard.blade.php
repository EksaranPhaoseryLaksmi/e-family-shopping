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
                    <button type="submit"
                            class="action-btn approve submit-btn"
                            title="Approve">

                        <i class="fas fa-check"></i>
                        <span class="btn-text">Approve</span>

                    </button>
                  </form>
                  <form action="{{ route('admin.vendors.reject', $vendor->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    <button type="submit"
                            class="action-btn reject submit-btn"
                            title="Reject">

                        <i class="fas fa-times"></i>
                        <span class="btn-text">Reject</span>

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
                  <button type="submit"
                          class="action-btn delete submit-btn"
                          title="Delete Vendor">

                      <i class="fas fa-trash-alt"></i>
                      <span class="btn-text">Delete</span>

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
<!-- Loading Overlay -->
<div id="loadingOverlay"
     class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">

    <div class="bg-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3">

        <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>

        <span class="text-lg font-semibold">
            Processing Request...
        </span>

    </div>

</div>
<script>
document.addEventListener("DOMContentLoaded", function () {

    // =========================
    // AUTO HIDE ALERT
    // =========================
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

    // =========================
    // FORM LOADING
    // =========================
    const forms = document.querySelectorAll("form");

    forms.forEach(form => {

        form.addEventListener("submit", function () {

            const overlay =
                document.getElementById("loadingOverlay");

            overlay.classList.remove("hidden");
            overlay.classList.add("flex");

            // disable buttons
            const buttons =
                form.querySelectorAll("button");

            buttons.forEach(btn => {

                btn.disabled = true;

                const btnText =
                    btn.querySelector(".btn-text");

                if (btnText) {
                    btnText.innerText = "Processing...";
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
