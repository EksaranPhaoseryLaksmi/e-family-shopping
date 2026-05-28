@extends('layouts.vendor')

@section('content')
<br/>
<div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-md space-y-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">🛍️ {{ $vendor->store_name }}</h2>

  @if(session('success'))
      <div id="alertBox"
           class="mb-4 text-green-700 text-sm text-center bg-green-100 border border-green-300 p-3 rounded">

          {{ session('success') }}

      </div>
  @endif

  @if(session('error'))
      <div id="alertBox"
           class="mb-4 text-red-700 text-sm text-center bg-red-100 border border-red-300 p-3 rounded">

          {{ session('error') }}

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

  <button type="button"
  onclick="addVariant()"
  class="text-sm text-blue-600 hover:underline">

  ➕ Add Variant

  </button>
 <button type="submit"
         id="submitBtn"
         class="submit-btn bg-green-600 text-white py-2 rounded hover:bg-green-700">

     <span class="btn-text">
         Submit Product
     </span>

 </button>
</form>
<!-- Loading Overlay -->
<div id="loadingOverlay"
     class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">

    <div class="bg-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3">

        <i class="fas fa-spinner fa-spin text-green-600 text-2xl"></i>

        <span class="text-lg font-semibold">
            Uploading Product...
        </span>

    </div>

</div>
<script>

// =========================
// IMAGE PREVIEW
// =========================
document.getElementById('imageInput')
    .addEventListener('change', function (e) {

    const files = e.target.files;

    const previewContainer =
        document.getElementById('previewContainer');

    previewContainer.innerHTML = '';

    Array.from(files).forEach(file => {

        const reader = new FileReader();

        reader.onload = function (event) {

            const img =
                document.createElement('img');

            img.src = event.target.result;

            img.classList.add(
                'w-24',
                'h-24',
                'object-cover',
                'rounded',
                'shadow'
            );

            previewContainer.appendChild(img);

        };

        reader.readAsDataURL(file);

    });

});

// =========================
// SHOW LOADING
// =========================
function showLoading() {

    const overlay =
        document.getElementById("loadingOverlay");

    if (!overlay) return;

    overlay.classList.remove("hidden");
    overlay.classList.add("flex");
}

// =========================
// HIDE LOADING
// =========================
function hideLoading() {

    const overlay =
        document.getElementById("loadingOverlay");

    if (!overlay) return;

    overlay.classList.add("hidden");
    overlay.classList.remove("flex");
}

// =========================
// ADD VARIANT
// =========================
function addVariant() {

    const container =
        document.getElementById('variant-container');

    const div =
        document.createElement('div');

    div.className =
        'grid grid-cols-3 gap-2 items-center variant-row';

    div.innerHTML = `
        <input name="variant_size[]" placeholder="Size (e.g. S)" class="border p-2 rounded" required>

        <input name="variant_quantity[]" type="number" placeholder="Quantity" class="border p-2 rounded" required>

        <input name="variant_price[]" type="number" step="0.01" placeholder="Price" class="border p-2 rounded" required>

        <button type="button"
                onclick="removeVariant(this)"
                class="text-red-500 text-sm hover:underline">

            ❌

        </button>
    `;

    container.appendChild(div);
}

// =========================
// REMOVE VARIANT
// =========================
function removeVariant(button) {

    const row =
        button.closest('.variant-row');

    row.remove();
}

// =========================
// PAGE READY
// =========================
document.addEventListener("DOMContentLoaded", function () {

    // -------------------------
    // AUTO HIDE ALERT
    // -------------------------
    const alertBox =
        document.getElementById("alertBox");

    if (alertBox) {

        setTimeout(() => {

            alertBox.style.transition = "0.5s";
            alertBox.style.opacity = "0";

            setTimeout(() => {
                alertBox.remove();
            }, 500);

        }, 3000);

    }

    // -------------------------
    // FORM SUBMIT
    // -------------------------
    const form =
        document.querySelector("form");

    form.addEventListener("submit", function () {

        showLoading();

        const submitBtn =
            document.getElementById("submitBtn");

        if (submitBtn) {

            submitBtn.disabled = true;

            submitBtn.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Uploading...';

        }

    });

});

// =========================
// FIX BACK BUTTON STUCK
// =========================
window.addEventListener("pageshow", function () {

    hideLoading();

    const submitBtn =
        document.getElementById("submitBtn");

    if (submitBtn) {

        submitBtn.disabled = false;

        submitBtn.innerHTML =
            '<span class="btn-text">Submit Product</span>';

    }

});

</script>
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
