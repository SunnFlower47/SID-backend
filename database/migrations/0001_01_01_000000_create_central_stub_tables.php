<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [SaaS] Buat tabel users DUMMY di db_central.
 *
 * Mengapa perlu ini?
 * Laravel menyimpan remember_me cookie yang berisi user ID.
 * Saat browser membuka halaman pertama kali (bahkan /login),
 * middleware AuthenticateSession bawaan Laravel otomatis mencoba query:
 *   SELECT * FROM users WHERE id = ? LIMIT 1
 * ke koneksi DEFAULT (db_central), sebelum tenancy sempat berjalan.
 *
 * Karena tabel users sebenarnya ada di db_tenant_*, query ini crash dengan:
 *   "Table 'db_central.users' doesn't exist"
 *
 * Solusi: buat tabel users KOSONG di db_central sebagai placeholder.
 * Query tetap berjalan, hasilnya NULL (tidak ada row), dan Laravel
 * dengan benar memperlakukan user sebagai guest — lalu diarahkan ke /login.
 * Tidak ada data user asli yang disimpan di sini.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('email')->nullable()->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // Tabel password_reset_tokens juga bisa dicari oleh remember_me flow
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
    }
};
