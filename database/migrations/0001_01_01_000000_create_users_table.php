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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
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
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('sessions');
    }
};
