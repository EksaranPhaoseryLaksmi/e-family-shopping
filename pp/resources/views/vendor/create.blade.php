@extends('layouts.vendor')

@section('content')
<br/>
<div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow-md space-y-6">
  <form method="POST" action="{{ route('vendor.store') }}" id="storeForm" novalidate class="grid gap-4">
    @csrf
    <h1 class="text-2xl font-bold text-center text-blue-700">Create Your Store</h1>

    <!-- Step 1 -->
    <div id="step1" class="space-y-4">
      <div>
        <p class="font-semibold">Where are your customers located?</p>
        <label class="block"><input type="radio" name="location" value="local" required class="mr-2"> Local</label>
        <label class="block"><input type="radio" name="location" value="international" class="mr-2"> International</label>
        <label class="block"><input type="radio" name="location" value="both" class="mr-2"> Both</label>
      </div>
<br/>
      <div>
        <p class="font-semibold">Do you have product photos ready?</p>
        <label class="block"><input type="radio" name="photos" value="1" required class="mr-2"> Yes</label>
        <label class="block"><input type="radio" name="photos" value="0" class="mr-2"> No</label>
      </div>
<br/>
      <div>
        <p class="font-semibold">How do you want to deliver your products?</p>
        <label class="block"><input type="radio" name="delivery" value="1" required class="mr-2"> Handle it your own</label>
        <label class="block"><input type="radio" name="delivery" value="0" class="mr-2"> Let website do it for you</label>
      </div>
<br/>
      <button type="button" id="next1" class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Next →</button>
    </div>

    <!-- Step 2 -->
    <div id="step2" class="space-y-4 hidden">
      <div>
        <p class="font-semibold">How do you want to get paid?</p>
        <label class="block"><input type="radio" name="payment" value="bank" required class="mr-2"> Bank Transfer</label>
        <label class="block"><input type="radio" name="payment" value="card" class="mr-2"> Credit Card</label>
        <label class="block"><input type="radio" name="payment" value="cash" class="mr-2"> Cash on Delivery</label>
      </div>

      <div>
        <p class="font-semibold">Do you need help with product description, pricing, or design?</p>
        <label class="block"><input type="radio" name="help" value="1" required class="mr-2"> Yes</label>
        <label class="block"><input type="radio" name="help" value="0" class="mr-2"> No</label>
      </div>

      <div>
        <p class="font-semibold">Enter your store’s name:</p>
        <input type="text" name="store_name" required class="w-full px-3 py-2 border border-gray-300 rounded" />
      </div>

      <div>
        <p class="font-semibold">Owner Name:</p>
        <input type="text" name="owner_name" required class="w-full px-3 py-2 border border-gray-300 rounded" />
      </div>

      <div>
        <p class="font-semibold">Email:</p>
        <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded" />
      </div>

      <div>
        <p class="font-semibold">Phone:</p>
        <input type="text" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded" />
      </div>

      <div>
        <p class="font-semibold">Address:</p>
        <textarea name="address" required class="w-full px-3 py-2 border border-gray-300 rounded"></textarea>
      </div>

      <div>
        <p class="font-semibold">What type of store do you want to create?</p>
        <select name="store_type" required class="w-full px-3 py-2 border border-gray-300 rounded">
          <option value="">-- Select --</option>
          <option value="1">Skin Care</option>
          <option value="2">Clothes</option>
          <option value="3">Accessory</option>
          <option value="4">Education Stuff</option>
        </select>
      </div>

      <div class="flex justify-between">
        <button type="button" id="back" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">← Back</button>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Create Store</button>
      </div>
    </div>
  </form>
</div>
  <script>
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');

    document.getElementById('next1').onclick = () => {
      step1.classList.add('hidden');
      step2.classList.remove('hidden');
      window.scrollTo({top: 0, behavior: 'smooth'});
    };

    document.getElementById('back').onclick = () => {
      step2.classList.add('hidden');
      step1.classList.remove('hidden');
      window.scrollTo({top: 0, behavior: 'smooth'});
    };
  </script>

@endsection
