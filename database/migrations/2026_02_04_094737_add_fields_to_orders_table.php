<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
          $table->decimal('total_commission', 10, 2)->default(0);
          $table->decimal('vendor_payable', 10, 2)->default(0);
          $table->decimal('admin_earnings', 10, 2)->default(0);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_commission', 'vendor_payable', 'admin_earnings']);
        });
    }
    
};
