<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('description');
            $table->string('image_public_id')->nullable()->after('image_url');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_url')->nullable()->after('title');
            $table->string('avatar_public_id')->nullable()->after('avatar_url');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['image_url', 'image_public_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_url', 'avatar_public_id']);
        });
    }
};
