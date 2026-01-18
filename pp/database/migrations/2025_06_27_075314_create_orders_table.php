<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
   {
       Schema::create('orders', function (Blueprint $table) {
           $table->id();
           $table->unsignedBigInteger('vendor_id')->nullable();
           $table->unsignedBigInteger('user_id')->nullable();
           $table->string('delivery_name');
           $table->string('delivery_address');
           $table->string('delivery_email');
           $table->string('product_name');      // <-- Add this line!
           $table->integer('quantity');
           $table->decimal('total_price', 8, 2);
           $table->string('status')->default('pending');
           $table->timestamps();
       });
   }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
