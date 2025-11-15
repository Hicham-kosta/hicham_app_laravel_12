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
        Schema::create('wallet_credits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); //receiver
            $table->decimal('amount', 12, 2);
            $table->date('expires_at')->nullable(); // default (+1 year) handled in service
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('added_by')->nullable(); //admin or system
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('user_id', 'expires_at');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('added_by')->references('id')->on('admins')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_credits');
    }
};
