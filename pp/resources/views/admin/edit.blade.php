<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Vendor Overview</title>
      <script src="https://cdn.tailwindcss.com"></script>
      <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
      <!-- FontAwesome -->
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">

<div class="flex-container">
 <!-- Sidebar -->
   <aside class="sidebar">
     <div class="sidebar-header">Admin Panel</div>

     <nav>
       <a href="{{ route('admin.dashboard') }}" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
       <a href="{{ route('admin.products') }}"><i class="fas fa-box"></i> Products</a>
       <a href="{{ route('admin.orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a>
       <a href="{{ route('admin.customers') }}"><i class="fas fa-user"></i> Users</a>
     </nav>
   </aside>
 <main class="main-content">
    <header class="header">
          <style>
              * {
                  box-sizing: border-box;
                  font-family: Arial, sans-serif;
              }

              body {
                  margin: 0;
                  background: #f3f4f6;
              }

              /* Main content */
              .content {
                  flex: 1;
                  padding: 30px;
              }

              .card {
                  background: white;
                  padding: 25px;
                  border-radius: 8px;
                  max-width: 900px;
              }

              label {
                  font-weight: 600;
                  display: block;
                  margin-bottom: 4px;
              }

              input, select {
                  width: 100%;
                  padding: 8px;
                  border: 1px solid #d1d5db;
                  border-radius: 6px;
              }

              button {
                  background: #2563eb;
                  color: white;
                  padding: 10px 20px;
                  border: none;
                  border-radius: 6px;
                  cursor: pointer;
              }

              button:hover {
                  background: #1d4ed8;
              }
          </style>
      <div class="flex justify-between items-center w-full">
        <h1>Edit Vendor</h1>

      </div>
    </header>
    <div class="card">

<form action="{{ route('admin.vendors.update', $vendor->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Store Name</label>
    <input type="text" name="store_name" value="{{ $vendor->store_name }}" required>

    <label>Owner Name</label>
    <input type="text" name="owner_name" value="{{ $vendor->owner_name }}" required>

    <label>Email</label>
    <input type="email" name="email" value="{{ $vendor->email }}" required>

    <label>Phone</label>
    <input type="text" name="phone" value="{{ $vendor->phone }}" required>

    <label>Address</label>
    <input type="text" name="address" value="{{ $vendor->address }}" required>

    <label>Location</label>
    <input type="text" name="location" value="{{ $vendor->location }}" required>

    <label>Photos</label>
    <select name="photos">
        <option value="1" {{ $vendor->photos ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ !$vendor->photos ? 'selected' : '' }}>No</option>
    </select>

    <label>Delivery</label>
    <select name="delivery">
        <option value="1" {{ $vendor->delivery ? 'selected' : '' }}>Own</option>
        <option value="0" {{ !$vendor->delivery ? 'selected' : '' }}>Website</option>
    </select>

    <label>Payment</label>
    <input type="text" name="payment" value="{{ $vendor->payment }}" required>

    <label>Help</label>
    <select name="help">
        <option value="1" {{ $vendor->help ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ !$vendor->help ? 'selected' : '' }}>No</option>
    </select>

    <label>Store Type</label>
    <select name="store_type">
        <option value="1" {{ $vendor->store_type == 1 ? 'selected' : '' }}>Skin Care</option>
        <option value="2" {{ $vendor->store_type == 2 ? 'selected' : '' }}>Clothes</option>
        <option value="3" {{ $vendor->store_type == 3 ? 'selected' : '' }}>Accessory</option>
        <option value="4" {{ $vendor->store_type == 4 ? 'selected' : '' }}>Education Stuff</option>
    </select>
    <div>
    <label>Status</label>
    <select name="status">
        <option value="pending" {{ $vendor->status === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="approved" {{ $vendor->status === 'approved' ? 'selected' : '' }}>Approved</option>
        <option value="rejected" {{ $vendor->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
    </select>
    </div>
</br>
    <div>
    <button type="submit">Update Vendor</button>
    </div>
</form>
</div>
  </main>
</div>

</body>
</html>
