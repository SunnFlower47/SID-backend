<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuKaderPemberdayaanService implements BukuAdministrasiInterface
{
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\KaderPemberdayaan::query()->orderBy('nama', 'asc');
        return $isExport ? $query->get() : $query->paginate(50)->withQueryString();
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuKaderPemberdayaanService.");
    }

}
