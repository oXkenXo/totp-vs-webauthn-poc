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
            $table->text('totp_secret')->nullable()->after('password');
            $table->boolean('totp_enabled')->default(false)->after('totp_secret');
            $table->string('nim', 20)->nullable()->unique()->after('name');
            $table->string('prodi')->nullable()->after('nim');
            $table->string('kelas', 20)->nullable()->after('prodi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['totp_secret', 'totp_enabled', 'nim', 'prodi', 'kelas']);
        });
    }
};
