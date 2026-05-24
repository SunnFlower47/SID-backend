<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use App\Traits\ApiResponse;

class PengumumanApiController extends Controller
{
    use ApiResponse;

    /**
     * Get announcements
     */
    public function index()
    {
        $announcements = Berita::published()
            ->where('kategori', 'pengumuman')
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get();

        return $this->successResponse($announcements);
    }

    /**
     * Get single announcement
     */
    public function show($id)
    {
        $announcement = Berita::published()
            ->where('id', $id)
            ->where('kategori', 'pengumuman')
            ->first();

        if (!$announcement) {
            return $this->errorResponse('Pengumuman tidak ditemukan', null, 404);
        }

        return $this->successResponse($announcement);
    }
}
