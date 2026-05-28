@extends('layouts.vendor')

@section('content')
<br/>
<form method="POST" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data" class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-md space-y-6">
    @csrf
    @method('PUT')

    <h2 class="text-2xl font-bold text-gray-800">✏️ Edit Product</h2>
@if(session('success'))
<div id="alertBox"
     class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">

    {{ session('success') }}

</div>
@endif

@if(session('error'))
<div id="alertBox"
     class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">

    {{ session('error') }}

</div>
@endif
<label class="block mb-2 text-gray-700">Add New Images</label>
<input type="file"
       id="newImageInput"
       name="new_images[]"
       multiple
       class="w-full mb-4 border p-2 rounded">
<!-- Current images (just preview, not editable) -->
<div id="newImagePreview"
     class="flex gap-2 flex-wrap mb-4">
</div>
<div class="flex gap-2 flex-wrap mb-4">
  @foreach($product->image ?? [] as $index => $img)
    <div class="relative">
      <img src="{{ asset('storage/' . $img) }}" class="h-20 w-20 object-cover rounded border">

      <label class="absolute top-0 right-0 bg-white p-1 rounded-bl cursor-pointer text-xs">
        <input type="checkbox" name="delete_images[]" value="{{ $img }}">
        ❌
      </label>
    </div>
  @endforeach
</div>


    <input type="text" name="name" value="{{ $product->name }}" placeholder="Product Name" class="w-full border p-2 rounded" required>

    <textarea name="description" rows="3" placeholder="Description" class="w-full border p-2 rounded">{{ $product->description }}</textarea>

    {{-- Variants --}}
<h3 class="text-lg font-semibold text-gray-700">Variants</h3>
<div id="variant-container" class="space-y-2">
  @foreach($product->variants ?? [] as $index => $variant)
  <div class="grid grid-cols-3 gap-2 items-center variant-row">
    <input name="variant_size[]" value="{{ $variant['size'] }}" placeholder="Size" class="border p-2 rounded" required>
    <input name="variant_quantity[]" value="{{ $variant['quantity'] }}" type="number" placeholder="Quantity" class="border p-2 rounded" required>
    <input name="variant_price[]" value="{{ $variant['price'] }}" type="number" step="0.01" placeholder="Price" class="border p-2 rounded" required>
    <button type="button" onclick="removeVariant(this)" class="text-red-500 hover:underline text-sm">❌</button>
  </div>
  @endforeach
</div>
<button type="button" onclick="addVariant()" class="text-sm text-blue-600 hover:underline mt-2">➕ Add Variant</button>

    <div class="flex gap-4 mt-4">
      <button type="submit"
              class="submit-btn bg-blue-600 text-white py-2 px-6 rounded hover:bg-blue-700">

          <span class="btn-text">
              Update
          </span>

      </button>
      <a href="{{ route('store.products', $product->vendor_request_id) }}"
         onclick="showLoading()"
         class="text-gray-600 hover:underline self-center">

          Cancel

      </a>
    </div>
  </form>
  <script>
  function addVariant() {
    const container = document.getElementById('variant-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-3 gap-2 items-center variant-row';
    div.innerHTML = `
      <input name="variant_size[]" placeholder="Size" class="border p-2 rounded" required>
      <input name="variant_quantity[]" type="number" placeholder="Quantity" class="border p-2 rounded" required>
      <input name="variant_price[]" type="number" step="0.01" placeholder="Price" class="border p-2 rounded" required>
      <button type="button" onclick="removeVariant(this)" class="text-red-500 hover:underline text-sm">❌</button>
    `;
    container.appendChild(div);
  }

  function removeVariant(button) {
    const row = button.closest('.variant-row');
    row.remove();
  }

  // Optional: Preview new images (if you add an ID to input)
  const newImageInput = document.querySelector('input[name="new_images[]"]');
  if (newImageInput) {
    newImageInput.addEventListener('change', function (e) {
      const previewContainer = document.getElementById('newImagePreview');
      if (previewContainer) {
        previewContainer.innerHTML = '';
        Array.from(e.target.files).forEach(file => {
          const reader = new FileReader();
          reader.onload = event => {
            const img = document.createElement('img');
            img.src = event.target.result;
            img.classList.add('h-20', 'w-20', 'object-cover', 'rounded', 'border');
            previewContainer.appendChild(img);
          };
          reader.readAsDataURL(file);
        });
      }
    });
  }
</script>
<!-- Loading Overlay -->
<div id="loadingOverlay"
     class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">

    <div class="bg-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3">

        <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>

        <span class="text-lg font-semibold">
            Updating Product...
        </span>

    </div>

</div>
<script>

// ========================
// LOADING FUNCTIONS
// ========================
function showLoading() {

    const overlay =
        document.getElementById('loadingOverlay');

    if (!overlay) return;

    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
}

function hideLoading() {

    const overlay =
        document.getElementById('loadingOverlay');

    if (!overlay) return;

    overlay.classList.add('hidden');
    overlay.classList.remove('flex');
}

// ========================
// VARIANTS
// ========================
function addVariant() {

    const container =
        document.getElementById('variant-container');

    const div =
        document.createElement('div');

    div.className =
        'grid grid-cols-3 gap-2 items-center variant-row';

    div.innerHTML = `

      <input name="variant_size[]"
             placeholder="Size"
             class="border p-2 rounded"
             required>

      <input name="variant_quantity[]"
             type="number"
             placeholder="Quantity"
             class="border p-2 rounded"
             required>

      <input name="variant_price[]"
             type="number"
             step="0.01"
             placeholder="Price"
             class="border p-2 rounded"
             required>

      <button type="button"
              onclick="removeVariant(this)"
              class="text-red-500 hover:underline text-sm">

              ❌

      </button>

    `;

    container.appendChild(div);
}

function removeVariant(button) {

    const row =
        button.closest('.variant-row');

    row.remove();
}

// ========================
// PAGE READY
// ========================
document.addEventListener('DOMContentLoaded', function () {

    // --------------------
    // IMAGE PREVIEW
    // --------------------
    const newImageInput =
        document.getElementById('newImageInput');

    if (newImageInput) {

        newImageInput.addEventListener('change', function (e) {

            const previewContainer =
                document.getElementById('newImagePreview');

            previewContainer.innerHTML = '';

            Array.from(e.target.files).forEach(file => {

                const reader =
                    new FileReader();

                reader.onload = function (event) {

                    const img =
                        document.createElement('img');

                    img.src =
                        event.target.result;

                    img.classList.add(
                        'h-20',
                        'w-20',
                        'object-cover',
                        'rounded',
                        'border'
                    );

                    previewContainer.appendChild(img);
                };

                reader.readAsDataURL(file);
            });

        });

    }

    // --------------------
    // FORM SUBMIT
    // --------------------
    const form =
        document.querySelector('form');

    if (form) {

        form.addEventListener('submit', function () {

            showLoading();

            const btn =
                document.querySelector('.submit-btn');

            if (btn) {

                btn.disabled = true;

                btn.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> Updating...';
            }

        });

    }

    // --------------------
    // AUTO HIDE ALERT
    // --------------------
    const alertBox =
        document.getElementById('alertBox');

    if (alertBox) {

        setTimeout(() => {

            alertBox.style.transition = '0.5s';
            alertBox.style.opacity = '0';

            setTimeout(() => {

                alertBox.remove();

            }, 500);

        }, 3000);

    }

});

// ========================
// FIX BACK BUTTON STUCK
// ========================
window.addEventListener('pageshow', function () {

    hideLoading();

    const btn =
        document.querySelector('.submit-btn');

    if (btn) {

        btn.disabled = false;

        btn.innerHTML =
            '<span class="btn-text">Update</span>';
    }

});

</script>
@endsection
