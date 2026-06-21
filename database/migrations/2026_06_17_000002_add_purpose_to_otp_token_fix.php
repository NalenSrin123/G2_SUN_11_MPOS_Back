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
        if (Schema::hasTable('otp_token')) {
            Schema::table('otp_token', function (Blueprint $table) {
                if (!Schema::hasColumn('otp_token', 'purpose')) {
                    $table->string('purpose', 32)->default('login');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('otp_token')) {
            Schema::table('otp_token', function (Blueprint $table) {
                if (Schema::hasColumn('otp_token', 'purpose')) {
                    $table->dropColumn('purpose');
                }
            });
        }
    }
};
