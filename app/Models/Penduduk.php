<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Traits\HasWilayahLabels;

class Penduduk extends Model
{
    use SoftDeletes, LogsActivity, HasWilayahLabels;

    protected $fillable = [
        'nkk',
        'nik',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'status_perkawinan',
        'kedudukan_keluarga',
        'pendidikan',
        'pekerjaan',
        'nama_ayah',
        'nama_ibu',
        'alamat',
        'rt_id',
        'rw_id',
        'dusun_id',
        'keterangan',
        'kartu_keluarga_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'rt_id' => 'integer',
        'rw_id' => 'integer',
        'dusun_id' => 'integer',
    ];
    
    protected $appends = ['rt_label', 'rw_label', 'dusun_label'];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama', 'nik', 'nkk', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'status_perkawinan', 'kedudukan_keluarga', 'pendidikan', 'pekerjaan', 'alamat', 'rt_id', 'rw_id', 'dusun_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // =========================================================
    // RELATIONS - WILAYAH MASTER
    // =========================================================

    public function rtMaster(): BelongsTo
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    public function rwMaster(): BelongsTo
    {
        return $this->belongsTo(Rw::class, 'rw_id');
    }

    public function dusunMaster(): BelongsTo
    {
        return $this->belongsTo(Dusun::class, 'dusun_id');
    }

    /**
     * Scope for Eager Loading Wilayah Master (High Performance)
     */
    public function scopeWithWilayah($query)
    {
        return $query->with(['rtMaster', 'rwMaster', 'dusunMaster']);
    }

    /**
     * Scope untuk kepala keluarga
     */
    public function scopeKepalaKeluarga($query)
    {
        return $query->where('kedudukan_keluarga', 'Kepala Keluarga');
    }

    /**
     * Scope untuk anggota keluarga
     */
    public function scopeAnggotaKeluarga($query)
    {
        return $query->where('kedudukan_keluarga', '!=', 'Kepala Keluarga');
    }

    /**
     * Scope untuk penduduk berdasarkan NKK
     */
    public function scopeByNKK($query, $nkk)
    {
        return $query->where('nkk', $nkk);
    }

    /**
     * Relasi ke Mutasi (jamak)
     */
    public function mutasis(): HasMany
    {
        return $this->hasMany(Mutasi::class);
    }

    /**
     * Relasi ke Mutasi (tunggal - untuk backward compatibility)
     */
    public function mutasi(): HasMany
    {
        return $this->hasMany(Mutasi::class);
    }

    // Note: created_by dan updated_by tidak ada di database
    // Relasi ini dinonaktifkan sampai kolom ditambahkan ke database

    /**
     * Relasi ke Kartu Keluarga berdasarkan NKK (virtual relationship)
     * Karena tabel kartu_keluargas sudah di-drop, kita buat relasi virtual
     */
    public function kartuKeluarga()
    {
        // Return collection of penduduks with same NKK
        return $this->hasMany(Penduduk::class, 'nkk', 'nkk');
    }

    /**
     * Get kepala keluarga dari KK yang sama
     */
    public function kepalaKeluarga()
    {
        return $this->hasOne(Penduduk::class, 'nkk', 'nkk')
                    ->where('kedudukan_keluarga', 'Kepala Keluarga');
    }

    /**
     * Relasi ke Surat Pengajuan
     */
    public function suratPengajuans(): HasMany
    {
        return $this->hasMany(SuratPengajuan::class);
    }

    /**
     * Scope untuk penduduk aktif
     */
    public function scopeAktif($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope untuk penduduk berdasarkan jenis kelamin
     */
    public function scopeJenisKelamin($query, $jenis)
    {
        return $query->where('jenis_kelamin', $jenis);
    }

    /**
     * Accessor untuk usia
     */
    public function getUsiaAttribute(): int
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->diffInYears(now()) : 0;
    }

    /**
     * Accessor untuk jenis kelamin label
     */
    public function getJenisKelaminLabelAttribute(): string
    {
        return match($this->jenis_kelamin) {
            'LAKI-LAKI' => 'Laki-laki',
            'PEREMPUAN' => 'Perempuan',
            default => $this->jenis_kelamin ?? 'Tidak Diketahui'
        };
    }

    /**
     * Accessor untuk status label (Legacy removed)
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->deleted_at ? 'Mutasi' : 'Aktif';
    }

    /**
     * Accessor untuk alamat lengkap
     */
    public function getAlamatLengkapAttribute(): string
    {
        return "{$this->alamat}, RT {$this->rt_label}/RW {$this->rw_label}, {$this->dusun_label}";
    }

    /**
     * Scope for filtering penduduk
     */
    public function scopeFilter($query, array $filters)
    {
        // Search functionality
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function($subq) use ($search) {
                $subq->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nkk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        });

        // Filter by gender
        $query->when($filters['jenis_kelamin'] ?? null, function ($q, $jenisKelamin) {
            $val = strtoupper(trim($jenisKelamin));
            if (in_array($val, ['L', 'LAKI-LAKI', 'LAKI LAKI', 'PRIA', 'MALE'])) {
                $q->where('jenis_kelamin', 'LAKI-LAKI');
            } elseif (in_array($val, ['P', 'PEREMPUAN', 'WANITA', 'FEMALE'])) {
                $q->where('jenis_kelamin', 'PEREMPUAN');
            } else {
                $q->where('jenis_kelamin', $val);
            }
        });


        // Filter by RT_ID, RW_ID, Dusun_ID (Support both legacy and new keys)
        $query->when($filters['rt_id'] ?? $filters['rt'] ?? null, function ($q, $id) {
            if (is_numeric($id)) $q->where('rt_id', $id);
        });
        $query->when($filters['rw_id'] ?? $filters['rw'] ?? null, function ($q, $id) {
            if (is_numeric($id)) $q->where('rw_id', $id);
        });
        $query->when($filters['dusun_id'] ?? $filters['dusun'] ?? null, function ($q, $id) {
            if (is_numeric($id)) $q->where('dusun_id', $id);
        });

        // Filter by age range
        $query->when($filters['filter_umur'] ?? null, function ($q, $filterUmur) {
            $today = \Carbon\Carbon::now();
            switch ($filterUmur) {
                case 'bayi': $q->where('tanggal_lahir', '>=', $today->copy()->subYears(2)); break;
                case 'balita': $q->whereBetween('tanggal_lahir', [$today->copy()->subYears(5), $today->copy()->subYears(2)]); break;
                case 'anak': $q->whereBetween('tanggal_lahir', [$today->copy()->subYears(12), $today->copy()->subYears(5)]); break;
                case 'remaja': $q->whereBetween('tanggal_lahir', [$today->copy()->subYears(18), $today->copy()->subYears(12)]); break;
                case 'dewasa_muda': $q->whereBetween('tanggal_lahir', [$today->copy()->subYears(30), $today->copy()->subYears(18)]); break;
                case 'dewasa': $q->whereBetween('tanggal_lahir', [$today->copy()->subYears(60), $today->copy()->subYears(30)]); break;
                case 'lansia': $q->where('tanggal_lahir', '<=', $today->copy()->subYears(60)); break;
                case 'umur_20_keatas': $q->where('tanggal_lahir', '<=', $today->copy()->subYears(20)); break;
                case 'umur_20_kebawah': $q->where('tanggal_lahir', '>', $today->copy()->subYears(20)); break;
                case 'umur_40_keatas': $q->where('tanggal_lahir', '<=', $today->copy()->subYears(40)); break;
                case 'umur_40_kebawah': $q->where('tanggal_lahir', '>', $today->copy()->subYears(40)); break;
                case 'umur_50_keatas': $q->where('tanggal_lahir', '<=', $today->copy()->subYears(50)); break;
                case 'umur_60_keatas': $q->where('tanggal_lahir', '<=', $today->copy()->subYears(60)); break;
                case 'umur_60_kebawah': $q->where('tanggal_lahir', '>', $today->copy()->subYears(60)); break;
            }
        });
    }


    /**
     * Scope for ordering by family role
     */
    public function scopeOrderByFamilyRole($query)
    {
        return $query->orderBy('nkk')
            ->orderByRaw("CASE
                WHEN kedudukan_keluarga = 'Kepala Keluarga' THEN 1
                WHEN kedudukan_keluarga = 'Istri' THEN 2
                WHEN kedudukan_keluarga = 'Anak' THEN 3
                WHEN kedudukan_keluarga = 'Menantu' THEN 4
                WHEN kedudukan_keluarga = 'Cucu' THEN 5
                WHEN kedudukan_keluarga = 'Orang Tua' THEN 6
                WHEN kedudukan_keluarga = 'Mertua' THEN 7
                WHEN kedudukan_keluarga = 'Saudara' THEN 8
                ELSE 9
            END")
            ->orderBy('tanggal_lahir', 'asc');
    }
}
