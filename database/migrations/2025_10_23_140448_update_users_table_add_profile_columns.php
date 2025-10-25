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
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type')->default('Customer')->comment('Customer|Vendor');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('1 = Active, 0 = Inactive');
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode', 20)->nullable();
            $table->string('country')->default('United Kingdom');
            $table->string('phone', 20)->nullable();
            $table->string('company')->nullable();
            $table->boolean('is_admin')->default(false)->comment('flag for admin users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'user_type',
                'status',
                'address_line1',
                'address_line2',
                'city',
                'county',
                'postcode',
                'country',
                'phone',
                'company',
                'is_admin',
            ]);
        });
    }
};
