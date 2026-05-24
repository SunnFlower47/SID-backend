<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UmkmResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_usaha' => $this->nama_usaha,
            'nama_pemilik' => $this->nama_pemilik,
            // Zero Trust: Sensor NIK agar tidak bocor ke publik
            'nik_pemilik' => $this->nik_pemilik ? substr($this->nik_pemilik, 0, 4) . '********' . substr($this->nik_pemilik, -4) : null,
            'alamat_usaha' => $this->alamat_usaha,
            'alamat_lengkap' => $this->alamat_lengkap,
            'rt_id' => $this->rt_id,
            'rw_id' => $this->rw_id,
            'dusun_id' => $this->dusun_id,
            'rt_label' => $this->rt_label,
            'rw_label' => $this->rw_label,
            'dusun_label' => $this->dusun_label,
            'no_telepon' => $this->no_telepon,
            'email' => $this->email,
            'jenis_usaha' => $this->jenis_usaha,
            'jenis_usaha_label' => $this->jenis_usaha_label,
            'deskripsi_usaha' => $this->deskripsi_usaha,
            'modal_awal' => $this->modal_awal,
            'omset_bulanan' => $this->omset_bulanan,
            'jumlah_karyawan' => $this->jumlah_karyawan,
            'status_usaha' => $this->status_usaha,
            'status_usaha_label' => $this->status_usaha_label,
            'tanggal_berdiri' => $this->tanggal_berdiri ? $this->tanggal_berdiri->format('Y-m-d') : null,
            'produk_unggulan' => $this->produk_unggulan,
            'foto_usaha' => $this->foto_usaha,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_unggulan' => (bool)$this->is_unggulan,
            'is_verified' => (bool)$this->is_verified,
        ];
    }
}
