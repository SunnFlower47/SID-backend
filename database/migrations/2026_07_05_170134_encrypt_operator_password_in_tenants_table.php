<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * stancl/tenancy menyimpan field custom tenant di kolom JSON 'data'.
     * Migration ini mengenkripsi nilai operator_password yang masih plaintext
     * di dalam kolom JSON tersebut.
     *
     * Format Laravel encrypted selalu dimulai dengan 'eyJ' (base64 dari JSON wrapper).
     */
    public function up(): void
    {
        $tenants = DB::table('tenants')->whereNotNull('data')->get(['id', 'data']);

        foreach ($tenants as $tenant) {
            $data = json_decode($tenant->data, true);

            if (
                isset($data['operator_password']) &&
                !empty($data['operator_password']) &&
                !str_starts_with($data['operator_password'], 'eyJ')
            ) {
                $data['operator_password'] = Crypt::encryptString($data['operator_password']);

                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update(['data' => json_encode($data)]);
            }
        }
    }

    /**
     * Reverse: decrypt kembali ke plaintext (hanya untuk rollback/development).
     */
    public function down(): void
    {
        $tenants = DB::table('tenants')->whereNotNull('data')->get(['id', 'data']);

        foreach ($tenants as $tenant) {
            $data = json_decode($tenant->data, true);

            if (
                isset($data['operator_password']) &&
                !empty($data['operator_password']) &&
                str_starts_with($data['operator_password'], 'eyJ')
            ) {
                try {
                    $data['operator_password'] = Crypt::decryptString($data['operator_password']);

                    DB::table('tenants')
                        ->where('id', $tenant->id)
                        ->update(['data' => json_encode($data)]);
                } catch (\Exception $e) {
                    // Skip jika gagal decrypt
                }
            }
        }
    }
};
