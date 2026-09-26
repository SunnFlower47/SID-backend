<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class LandlordAuditLog extends Model
{
    protected $connection = 'mysql'; // selalu ke db_central

    protected $table = 'landlord_audit_logs';

    protected $fillable = [
        'event',
        'actor_email',
        'actor_id',
        'ip_address',
        'user_agent',
        'subject_type',
        'subject_id',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function actor()
    {
        return $this->belongsTo(CentralUser::class, 'actor_id');
    }

    /**
     * Helper statis untuk merekam audit log dari mana saja.
     *
     * @param string      $event       Nama event (login_success, tenant_deleted, dll.)
     * @param string|null $description Deskripsi singkat aksi
     * @param array       $metadata    Data tambahan (opsional)
     */
    public static function record(
        string $event,
        ?string $description = null,
        array $metadata = []
    ): void {
        $request = request();
        $user    = auth('landlord')->user();

        static::create([
            'event'        => $event,
            'actor_email'  => $user?->email ?? ($request->input('email') ?? null),
            'actor_id'     => $user?->id,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'description'  => $description,
            'metadata'     => !empty($metadata) ? $metadata : null,
        ]);
    }

    /**
     * Helper khusus untuk mencatat aksi pada subjek tertentu (tenant, user, dll.).
     */
    public static function recordAction(
        string $event,
        string $subjectType,
        string $subjectId,
        ?string $description = null,
        array $metadata = []
    ): void {
        $request = request();
        $user    = auth('landlord')->user();

        static::create([
            'event'        => $event,
            'actor_email'  => $user?->email,
            'actor_id'     => $user?->id,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'description'  => $description,
            'metadata'     => !empty($metadata) ? $metadata : null,
        ]);
    }
}
