<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:expired-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
   public function handle()
   {
       $sessions = \App\Models\PaymentSession::where('status', 'pending')
           ->where('expires_at', '>', now())
           ->get();
       foreach ($sessions as $session) {
           try {
               $response = \Http::post('http://127.0.0.1:3000/check-payment', [
                   'md5' => $session->bakong_md5
               ]);
               $result = $response->json();
               if (!$result['success']) continue;
               $bakong = $result['data'];
               $isPaid =
                   $bakong['responseCode'] === 0 &&
                   !empty($bakong['data']['acknowledgedDateMs']);

               if ($isPaid) {

                   $cart = json_decode($session->cart, true);

                   foreach ($cart as $item) {
                       \App\Models\Order::create([
                           'vendor_id' => $item['vendor_id'] ?? null,
                           'user_id' => null, // or session user if saved
                           'product_id' => $item['id'] ?? null,
                           'product_name' => $item['name'],
                           'size' => $item['size'] ?? null,
                           'quantity' => $item['quantity'],
                           'total_price' => $item['price'] * $item['quantity'],

                           'delivery_name' => $session->delivery_name,
                           'delivery_address' => $session->delivery_address,
                           'delivery_email' => $session->delivery_email,

                           'status' => 'paid',
                           'receipt_no' => $session->payment_ref,
                       ]);
                   }

                   $session->update(['status' => 'paid']);
               }

           } catch (\Exception $e) {
               \Log::error("Payment check failed: " . $e->getMessage());
           }
       }
   }
}
