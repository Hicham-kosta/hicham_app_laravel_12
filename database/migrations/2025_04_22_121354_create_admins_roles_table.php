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
        Schema::create('admins_roles', function (Blueprint $table) {
            $table->id();
            $table->integer('subadmin_id'); // Subadmin ID
            $table->string('module'); // Mopdule name(categories, products etc...)
            $table->tinyInteger('view_access'); // View-only access
            $table->tinyInteger('edit_access'); // View and Edit access
            $table->tinyInteger('full_access'); // Full access(view, edit, delete)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins_roles');
    }
};
