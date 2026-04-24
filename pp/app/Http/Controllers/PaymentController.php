<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentSession;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payment');
    }

    public function generateKHQR(Request $request)
    {
        $cart = $request->cart;
        $amount = $request->amount;

        $paymentRef = md5(uniqid());

        $expiresAt = now()->addMinutes(5);
try {
        // Call Node KHQR server
        $response = Http::post("http://127.0.0.1:3000/generate-khqr", [
            "bakongAccountID" => "bun_seng@abaa",
            "merchantName" => "SENG BUN",
            "merchantCity" => "PHNOM PENH",
            "amount" => $amount * 4000
        ]);

        $result = $response->json();
        // Save session
        $session = PaymentSession::create([
                       'payment_ref' => $paymentRef,
                       'cart' => json_encode($request->cart),
                       'amount' => $request->amount,
                       'status' => 'pending',
                       'expires_at' => now()->addMinutes(5),
                       'delivery_name' => $request->delivery_name,
                       'delivery_address' => $request->delivery_address,
                       'delivery_email' => $request->delivery_email,
                       'delivery_phone' => $request->delivery_phone,
                       'delivery_map' => $request->delivery_map,
                       'bakong_md5' => $result["md5"],
                       'user_id' => auth()->id()
                   ]);
        return response()->json([
            "success" => true,
            "qrString" => $result["qrString"],
            "expiration" => $result["expiration"],
            "payment_ref" => $paymentRef,
        ]);
         } catch (\Exception $e) {
                   return response()->json([
                       "success" => false,
                       "error" => $e->getMessage()
                   ], 500);
               }
   }

   public function generateQr(Request $request)
   {
       try {
           $response = Http::post("http://127.0.0.1:3000/generate-khqr", [
               "bakongAccountID" => "bun_seng@abaa",
               "merchantName"    => "SENG BUN",
               "merchantCity"    => "PHNOM PENH",
               "amount"          => (int) $request->amount ?? 10000
           ]);

           if (!$response->successful()) {
               return response()->json([
                   "success" => false,
                   "error" => "Node API failed",
                   "detail" => $response->body()
               ]);
           }

           return response()->json($response->json());

       } catch (\Exception $e) {
           return response()->json([
               "success" => false,
               "error" => $e->getMessage()
           ], 500);
       }
   }

   public function checkPaymentFake(Request $request)
   {
       $ref = $request->payment_ref;

       $session = PaymentSession::where('payment_ref', $ref)->first();

       if (!$session) {
           return response()->json(["status" => "not_found"]);
       }

       $paid = true; // simulate

       if ($paid && $session->status !== 'paid') {

           $cart = json_decode($session->cart, true);

           foreach ($cart as $item) {
               \App\Models\Order::create([
                   'vendor_id' => $item['vendor_id'] ?? null,
                   'user_id' => auth()->id(),
                   'product_id' => $item['id'] ?? null,
                   'product_name' => $item['name'],
                   'size' => $item['size'] ?? null,
                   'quantity' => $item['quantity'],
                   'total_price' => $item['price'] * $item['quantity'],
                   'delivery_name' => $session->delivery_name,
                   'delivery_address' => $session->delivery_address,
                   'delivery_email' => "mail@mail.com",
                   'status' => 'paid',
                   'receipt_no' => $ref,
               ]);
           }

           $session->update(['status' => 'paid']);

           return response()->json(["status" => "paid"]);
       }

       return response()->json(["status" => "pending"]);
   }

   public function checkPayment(Request $request)
   {
       $ref = $request->payment_ref;

       $session = PaymentSession::where('payment_ref', $ref)->first();

       if (!$session) {
           return response()->json(["status" => "not_found"]);
       }

       // 🔥 Call Node API (Bakong)
       $response = Http::post('http://127.0.0.1:3000/check-payment', [
           'md5' => $session->bakong_md5
       ]);

       $result = $response->json();

       if (!$result['success']) {
           return response()->json([
               "status" => "error",
               "error" => $result['error']
           ]);
       }

       $bakong = $result['data'];

       // =========================
       // ✅ CHECK PAYMENT SUCCESS
       // =========================
       $isPaid =
           $bakong['responseCode'] === 0 &&
           !empty($bakong['data']['acknowledgedDateMs']);

       if ($isPaid && $session->status !== 'paid') {

           $cart = json_decode($session->cart, true);

           foreach ($cart as $item) {
               \App\Models\Order::create([
                   'vendor_id' => $item['vendor_id'] ?? null,
                   'user_id' => auth()->id(),
                   'product_id' => $item['id'] ?? null,
                   'product_name' => $item['name'],
                   'size' => $item['size'] ?? null,
                   'quantity' => $item['quantity'],
                   'total_price' => $item['price'] * $item['quantity'],

                   // ✅ REAL DELIVERY DATA
                   'delivery_name' => $session->delivery_name,
                   'delivery_address' => $session->delivery_address,
                   'delivery_email' => Auth::user()->email,
                   'delivery_phone' => $session->delivery_phone,
                   'delivery_map' => $session->delivery_map,
                   'status' => 'paid',
                   'receipt_no' => $ref,
               ]);
           }

           // ✅ mark session paid
           $session->update(['status' => 'paid']);

           return response()->json(["status" => "paid"]);
       }

       return response()->json(["status" => "pending"]);
   }

   public function showPage()
   {
         $amount = 1; // or from DB/order
         return view('payment_qr', compact('amount'));
   }
}
