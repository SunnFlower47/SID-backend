<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuIndukPendudukService implements BukuAdministrasiInterface
{
    /**
     * Buku Induk Penduduk (B1)
     */
    public function getData(array $filters, bool $isExport)
    {
        $query = $this->getQuery($filters);
        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku Induk Penduduk (B1) Query Builder
     */
    public function getQuery(array $filters)
    {
        $query = \App\Models\Penduduk::withWilayah()->with('kartuKeluarga');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama', 'like', "%{$filters['search']}%")
                  ->orWhere('nik', 'like', "%{$filters['search']}%")
                  ->orWhereHas('kartuKeluarga', fn($sq) => $sq->where('nkk', 'like', "%{$filters['search']}%"));
            });
        }

        if (!empty($filters['rt_id']) && $filters['rt_id'] !== 'all') {
            $query->whereHas('kartuKeluarga', fn($q) => $q->where('rt_id', $filters['rt_id']));
        }
        if (!empty($filters['rw_id']) && $filters['rw_id'] !== 'all') {
            $query->whereHas('kartuKeluarga', fn($q) => $q->where('rw_id', $filters['rw_id']));
        }
        if (!empty($filters['jenis_kelamin']) && $filters['jenis_kelamin'] !== 'all') {
            $query->where('jenis_kelamin', $filters['jenis_kelamin']);
        }
        if (!empty($filters['dusun_id']) && $filters['dusun_id'] !== 'all') {
            $query->whereHas('kartuKeluarga', fn($q) => $q->where('dusun_id', $filters['dusun_id']));
        }
        if (!empty($filters['filter_umur']) && $filters['filter_umur'] !== 'all') {
            $filterUmur = $filters['filter_umur'];
            $today = \Carbon\Carbon::now();

            switch ($filterUmur) {
                case 'bayi':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(2));
                    break;
                case 'balita':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(5))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(2));
                    break;
                case 'anak':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(12))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(5));
                    break;
                case 'remaja':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(18))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(12));
                    break;
                case 'dewasa_muda':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(30))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(18));
                    break;
                case 'dewasa':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(60))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(30));
                    break;
                case 'lansia':
                    $query->where('tanggal_lahir', '<=', $today->copy()->subYears(60));
                    break;
            }
        }

        // Ordered by KK so families are grouped together
        $query->orderBy('kartu_keluarga_id')
              ->orderByRaw("CASE
                  WHEN kedudukan_keluarga = 'Kepala Keluarga' THEN 1
                  WHEN kedudukan_keluarga = 'Istri' THEN 2
                  WHEN kedudukan_keluarga = 'Anak' THEN 3
                  ELSE 9
              END")
              ->orderBy('tanggal_lahir', 'asc');
              
        return $query;
    }
}
