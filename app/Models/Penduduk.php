<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Penduduk extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'kartu_keluarga_id',
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
        'rt',
        'rw',
        'dusun',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama', 'nik', 'nkk', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'status_perkawinan', 'kedudukan_keluarga', 'pendidikan', 'pekerjaan', 'alamat', 'rt', 'rw', 'dusun'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
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
     * Scope untuk penduduk berdasarkan dusun
     */
    public function scopeDusun($query, $dusun)
    {
        return $query->where('dusun', $dusun);
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
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Accessor untuk status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'aktif' => 'Aktif',
            'meninggal' => 'Meninggal',
            'pindah' => 'Pindah',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Accessor untuk RT formatted
     */
    public function getRtFormattedAttribute(): string
    {
        return str_pad($this->rt, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Accessor untuk RW formatted
     */
    public function getRwFormattedAttribute(): string
    {
        return str_pad($this->rw, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Accessor untuk alamat lengkap
     */
    public function getAlamatLengkapAttribute(): string
    {
        return "{$this->alamat}, RT {$this->rt_formatted}/RW {$this->rw_formatted}, {$this->dusun}";
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
            if (in_array($jenisKelamin, ['L', 'Laki-laki', 'LAKI-LAKI'])) {
                $q->where('jenis_kelamin', 'LAKI-LAKI');
            } elseif (in_array($jenisKelamin, ['P', 'Perempuan', 'PEREMPUAN'])) {
                $q->where('jenis_kelamin', 'PEREMPUAN');
            } else {
                $q->where('jenis_kelamin', $jenisKelamin);
            }
        });

        // Filter by RT, RW, Dusun
        $query->when($filters['rt'] ?? null, fn($q, $rt) => $q->where('rt', $rt));
        $query->when($filters['rw'] ?? null, fn($q, $rw) => $q->where('rw', $rw));
        $query->when($filters['dusun'] ?? null, fn($q, $dusun) => $q->where('dusun', $dusun));

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
