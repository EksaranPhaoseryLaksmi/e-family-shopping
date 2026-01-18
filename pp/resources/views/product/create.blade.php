@extends('layouts.vendor')

@section('content')
<br/>
<div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-md space-y-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">🛍️ {{ $vendor->store_name }}</h2>

    @if(session('success'))
        <div class="mb-4 text-green-600 text-sm text-center bg-green-100 p-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data" class="grid gap-4">
  @csrf
  <input type="hidden" name="vendor_request_id" value="{{ $vendor->id }}">

  <!-- ✅ Name & Description -->
  <input type="text" name="name" placeholder="Product Name" class="border p-2 rounded" required>
  <textarea name="description" rows="3" placeholder="Product Description" class="border p-2 rounded"></textarea>

  <!-- ✅ Image Upload -->
<label class="block">
    <span class="text-gray-600">Upload Images</span>
    <input type="file" name="image[]" id="imageInput" multiple accept="image/*" class="block w-full mt-1">
</label>
<div id="previewContainer" class="flex flex-wrap gap-2 mt-2"></div>


  <!-- ✅ Variants Section -->
<div id="variant-container" class="space-y-2">
  <div class="grid grid-cols-3 gap-2 items-center variant-row">
    <input name="variant_size[]" placeholder="Size (e.g. S)" class="border p-2 rounded" required>
    <input name="variant_quantity[]" type="number" placeholder="Quantity" class="border p-2 rounded" required>
    <input name="variant_price[]" type="number" step="0.01" placeholder="Price" class="border p-2 rounded" required>
    <button type="button" onclick="removeVariant(this)" class="text-red-500 text-sm hover:underline">❌</button>
  </div>
</div>

  <button type="button" onclick="addVariant()" class="text-sm text-blue-600 hover:underline">➕ Add Variant</button>

  <button type="submit" class="bg-green-600 text-white py-2 rounded hover:bg-green-700">Submit Product</button>
</form>

<script>
document.getElementById('imageInput').addEventListener('change', function (e) {
    const files = e.target.files;
    const previewContainer = document.getElementById('previewContainer');
    previewContainer.innerHTML = '';

    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function (event) {
            const img = document.createElement('img');
            img.src = event.target.result;
            img.classList.add('w-24', 'h-24', 'object-cover', 'rounded', 'shadow');
            previewContainer.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});

function addVariant() {
    const container = document.getElementById('variant-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-3 gap-2 items-center variant-row';
    div.innerHTML = `
        <input name="variant_size[]" placeholder="Size (e.g. S)" class="border p-2 rounded" required>
        <input name="variant_quantity[]" type="number" placeholder="Quantity" class="border p-2 rounded" required>
        <input name="variant_price[]" type="number" step="0.01" placeholder="Price" class="border p-2 rounded" required>
        <button type="button" onclick="removeVariant(this)" class="text-red-500 text-sm hover:underline">❌</button>
    `;
    container.appendChild(div);
}

function removeVariant(button) {
    const row = button.closest('.variant-row');
    row.remove();
}
</script>

</div>


@endsection
