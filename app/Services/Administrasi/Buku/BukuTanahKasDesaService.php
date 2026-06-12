<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuTanahKasDesaService implements BukuAdministrasiInterface
{
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\AsetInventaris::query()
            ->with(['barang', 'mutasis'])          // mutasis wajib untuk saldo_kwantitas accessor
            ->whereHas('barang', fn($q) => $q->where('kode_barang', 'like', '2.%'))
            ->orderBy('tanggal_perolehan', 'asc');

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama_barang_override', 'like', "%{$filters['search']}%")
                  ->orWhere('lokasi', 'like', "%{$filters['search']}%")
                  ->orWhereHas('barang', fn($q2) => $q2->where('nama_barang', 'like', "%{$filters['search']}%"));
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuTanahKasDesaService.");
    }

}
