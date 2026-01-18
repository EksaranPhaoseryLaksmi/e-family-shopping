@extends('layouts.vendor')

@section('content')
    <br/>
<div class="max-w-8xl mx-auto bg-white p-6 rounded-xl shadow-md space-y-6">
  <!-- Header and Create Vendor Button -->
  <div class="flex items-center justify-between mb-8">
    <h1 class="text-2xl font-bold text-gray-800">🛍️ Vendors</h1>
    <a href="{{ route('vendor.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
      ➕ Create New Vendor
    </a>
  </div>
    <!-- Filter/Search Form -->
<div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
<form method="GET" action="{{ route('vendors.user') }}" class="mb-4 grid md:grid-cols-4 gap-4">
<div>
<input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..." class="border rounded px-3 py-2 w-full">
</div>
<div>
<select name="type" class="border rounded px-3 py-2 w-full">
  <option value="">Filter by type</option>
  @php $types = [1 => 'Skincare', 2 => 'Clothing', 3 => 'Education', 4 => 'Accessories']; @endphp
  @foreach ($types as $key => $value)
    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
  @endforeach
</select>
</div>
{{-- Per Page Selection --}}
 <div>
  <span class="text-sm text-gray-700 whitespace-nowrap">Item per page:</span>
  <select name="per_page" class="border rounded px-3 py-2">
    @foreach ([5, 10, 15, 20] as $limit)
      <option value="{{ $limit }}" {{ request('per_page', 5) == $limit ? 'selected' : '' }}>
        {{ $limit }}
      </option>
    @endforeach
  </select>
  </div>
  <div>
        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Search
        </button>
  </div>
</form>
</div>
    <!-- Vendor Table -->
    <div class="max-w-8xl mx-auto bg-white p-6 rounded-xl shadow-md space-y-6">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 font-semibold">
          <tr>
            <th class="px-4 py-3">Store Name</th>
            <th class="px-4 py-3">Type</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Phone</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-center">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse ($vendors as $vendor)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">{{ $vendor->store_name }}</td>
              <td class="px-4 py-3">
                      <span>
                            @php
                                $types = [1 => 'Skincare', 2 => 'Clothing', 3 => 'Accessories', 4 => 'Education'];
                                echo $types[$vendor->store_type ?? 0] ?? 'Unknown';
                            @endphp
                        </span>
                    </td>
              <td class="px-4 py-3">{{ $vendor->email }}</td>
              <td class="px-4 py-3">{{ $vendor->phone }}</td>
              <td class="px-4 py-3">
                <span class="px-2 py-1 rounded-full text-xs font-medium text-white 
                  {{ $vendor->status === 'approved' ? 'bg-green-500' : 'bg-yellow-500' }}">
                  {{ ucfirst($vendor->status) }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                @if($vendor->status === 'approved')
                   <a href="{{ route('store.products', $vendor->id) }}"
                    class="inline-block bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm">
                        📦 View Products
                    </a>
                  <a href="{{ route('product.create', $vendor->id) }}" 
                     class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">
                    ➕ Add Product
                  </a>
                @else
                  <span class="text-xs text-gray-400 italic">Pending Approval</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-gray-500">No vendors found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      @if ($vendors instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-6">
        {{ $vendors->links() }}
    </div>
@endif
</div>
@endsection