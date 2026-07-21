<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password_change_code')->nullable()->after('must_change_password');
            $table->timestamp('password_change_code_expires_at')->nullable()->after('password_change_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['password_change_code', 'password_change_code_expires_at']);
        });
    }
};
