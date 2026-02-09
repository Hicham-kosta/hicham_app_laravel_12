<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifySessionsTableForMultiAuth extends Migration
{
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['user_id']);
            
            // Change the column to nullable and remove the constraint
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Add a polymorphic user type column
            $table->string('user_type')->nullable()->after('user_id');
            
            // Add a composite index for user_id and user_type
            $table->index(['user_id', 'user_type']);
        });
    }

    public function down()
    {
        Schema::table('sessions', function (Blueprint $table) {
            // Remove the index
            $table->dropIndex(['user_id', 'user_type']);
            
            // Drop the user_type column
            $table->dropColumn('user_type');
            
            // Re-add the foreign key constraint (if you want to revert)
            // Note: This will fail if there are admin IDs in the user_id column
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
}