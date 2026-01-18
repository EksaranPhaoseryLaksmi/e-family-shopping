<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToVendorsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('vendors', 'status')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->string('status')->default('pending'); // pending, approved, rejected
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('vendors', 'status')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
}
