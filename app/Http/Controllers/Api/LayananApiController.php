<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class LayananApiController extends Controller
{
    use ApiResponse;

    /**
     * Get services list
     */
    public function getServices()
    {
        $services = [
            [
                'id' => 'surat-keterangan',
                'name' => 'Surat Keterangan',
                'description' => 'Berbagai jenis surat keterangan',
                'icon' => 'fas fa-file-alt',
                'color' => 'blue'
            ],
            [
                'id' => 'bantuan-sosial',
                'name' => 'Bantuan Sosial',
                'description' => 'Informasi bantuan sosial pemerintah',
                'icon' => 'fas fa-hand-holding-heart',
                'color' => 'green'
            ],
            [
                'id' => 'pengaduan',
                'name' => 'Pengaduan',
                'description' => 'Layanan pengaduan dan keluhan',
                'icon' => 'fas fa-comments',
                'color' => 'red'
            ],
            [
                'id' => 'konsultasi',
                'name' => 'Konsultasi',
                'description' => 'Konsultasi administrasi desa',
                'icon' => 'fas fa-user-tie',
                'color' => 'purple'
            ]
        ];

        return $this->successResponse($services);
    }
}
