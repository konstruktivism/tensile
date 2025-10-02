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
        Schema::table('organisations', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
            $table->text('address')->nullable()->after('email');
            $table->string('phone')->nullable()->after('address');
            $table->string('website')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn(['email', 'address', 'phone', 'website']);
        });
    }
};
