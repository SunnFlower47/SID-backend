<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuratPengajuan;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get notifications for admin dashboard
     */
    public function index(Request $request)
    {
        try {
            // Get recent surat pengajuan (last 7 days)
            $recentSurat = SuratPengajuan::where('created_at', '>=', Carbon::now()->subDays(7))
                ->with(['penduduk'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($surat) {
                    return [
                        'id' => $surat->id,
                        'type' => 'surat',
                        'title' => 'Pengajuan Surat Baru',
                        'message' => ($surat->penduduk?->nama ?? 'Penduduk Tidak Ditemukan') . " mengajukan " . ($surat->surat_type_name),
                        'time' => $surat->created_at->diffForHumans(),
                        'status' => $surat->status,
                        'url' => route('admin.surat-pengajuan.show', $surat->id),
                        'icon' => 'fas fa-file-alt',
                        'color' => $this->getStatusColor($surat->status)
                    ];
                });

            // Get recent pengaduan (last 7 days)
            $recentPengaduan = Pengaduan::where('created_at', '>=', Carbon::now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($pengaduan) {
                    return [
                        'id' => $pengaduan->id,
                        'type' => 'pengaduan',
                        'title' => 'Pengaduan Warga',
                        'message' => "{$pengaduan->nama_pelapor} melaporkan: {$pengaduan->judul}",
                        'time' => $pengaduan->created_at->diffForHumans(),
                        'status' => $pengaduan->status,
                        'url' => route('pengaduan.show', $pengaduan->id),
                        'icon' => 'fas fa-exclamation-triangle',
                        'color' => $this->getPengaduanStatusColor($pengaduan->status)
                    ];
                });

            // Combine and sort by time
            $notifications = $recentSurat->concat($recentPengaduan)
                ->sortByDesc('time')
                ->values();

            // Count unread notifications
            $unreadCount = $notifications->where('status', '!=', 'selesai')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $notifications,
                    'unread_count' => $unreadCount,
                    'total_count' => $notifications->count()
                ]
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request)
    {
        try {
            $type = $request->input('type');
            $id = $request->input('id');

            if ($type === 'surat') {
                $surat = SuratPengajuan::findOrFail($id);
                $surat->update(['status' => 'diproses']);
            } elseif ($type === 'pengaduan') {
                $pengaduan = Pengaduan::findOrFail($id);
                $pengaduan->update(['status' => 'diproses']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sebagai dibaca'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification counts
     */
    public function counts()
    {
        try {
            $suratCount = SuratPengajuan::where('created_at', '>=', Carbon::now()->subDays(7))
                ->where('status', '!=', 'selesai')
                ->count();

            $pengaduanCount = Pengaduan::where('created_at', '>=', Carbon::now()->subDays(7))
                ->where('status', '!=', 'selesai')
                ->count();

            $totalCount = $suratCount + $pengaduanCount;

            return response()->json([
                'success' => true,
                'data' => [
                    'surat_count' => $suratCount,
                    'pengaduan_count' => $pengaduanCount,
                    'total_count' => $totalCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat jumlah notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status color for surat
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'pending' => 'text-yellow-600',
            'diproses' => 'text-blue-600',
            'selesai' => 'text-green-600',
            'ditolak' => 'text-red-600',
            default => 'text-gray-600'
        };
    }

    /**
     * Get status color for pengaduan
     */
    private function getPengaduanStatusColor($status)
    {
        return match($status) {
            'baru' => 'text-yellow-600',
            'diproses' => 'text-blue-600',
            'selesai' => 'text-green-600',
            'ditolak' => 'text-red-600',
            default => 'text-gray-600'
        };
    }
}
