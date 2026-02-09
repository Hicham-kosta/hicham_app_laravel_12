<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorIdToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Add vendor_id column
            $table->foreignId('vendor_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('admins')
                  ->onDelete('set null');
                  
            // Add index for better performance
            $table->index('vendor_id');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn('vendor_id');
        });
    }
}