<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel audit log terpusat khusus untuk aksi-aksi kritis di Landlord Panel.
     * Berbeda dari tenant_activity_logs, tabel ini tidak terikat ke tenant tertentu
     * dan mencatat semua aksi keamanan: login, logout, gagal login, hapus desa, dll.
     */
    public function up(): void
    {
        Schema::create('landlord_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event');                    // login_success, login_failed, logout, tenant_deleted, dll.
            $table->string('actor_email')->nullable();  // email yang mencoba login (termasuk yang gagal)
            $table->unsignedBigInteger('actor_id')->nullable(); // ID central_user jika berhasil auth
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('subject_type')->nullable(); // misal: 'Tenant', 'CentralUser'
            $table->string('subject_id')->nullable();   // ID dari subjek yang diubah
            $table->text('description')->nullable();    // deskripsi aksi
            $table->json('metadata')->nullable();       // data tambahan (request params, sebelum/sesudah)
            $table->timestamps();

            $table->index(['event', 'created_at']);
            $table->index('actor_id');
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landlord_audit_logs');
    }
};
