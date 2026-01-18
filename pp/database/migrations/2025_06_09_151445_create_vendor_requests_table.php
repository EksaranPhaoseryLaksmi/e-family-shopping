<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('vendor_requests', function (Blueprint $table) {
            $table->id();
            $table->string('store_name');
            $table->string('owner_name');
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->string('location')->nullable();
            $table->boolean('photos')->nullable();
            $table->boolean('delivery')->nullable();
            $table->string('payment')->nullable();
            $table->boolean('help')->nullable();
            $table->integer('store_type')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('user_id')->nullable(); // link to users table
            $table->timestamps();

            // Foreign key if you want (optional)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_requests');
    }
}
