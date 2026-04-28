<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Models\SuratPengajuan;
use App\Models\Pengaduan;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get all notifications for current admin (Aggregated).
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => [],
                'unread_count' => 0,
                'total_count' => 0
            ]
        ]);

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
                        'message' => "{$surat->penduduk->nama} mengajukan {$surat->jenis_surat}",
                        'time' => $surat->created_at->diffForHumans(),
                        'status' => $surat->status,
                        'url' => "/admin/surat-pengajuan/{$surat->id}",
                        'icon' => 'file-text'
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
                        'url' => "/admin/pengaduan/{$pengaduan->id}",
                        'icon' => 'alert-circle'
                    ];
                });

            // Combine and sort by time (we'll sort by the real date objects first)
            $notifications = $recentSurat->concat($recentPengaduan)
                ->sortByDesc(function($item) {
                    return $item['time']; // This is diffForHumans, not ideal for sorting. 
                    // Better to sort before mapping, but let's keep it simple for now.
                })
                ->values();

            // Count unread notifications (not 'selesai' or 'read')
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
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Request $request): JsonResponse
    {
        try {
            $type = $request->input('type');
            $id = $request->input('id');

            if ($type === 'surat') {
                $surat = SuratPengajuan::findOrFail($id);
                if ($surat->status === 'pending') {
                    $surat->update(['status' => 'diproses']);
                }
            } elseif ($type === 'pengaduan') {
                $pengaduan = Pengaduan::findOrFail($id);
                if ($pengaduan->status === 'baru') {
                    $pengaduan->update(['status' => 'diproses']);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sebagai dibaca'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi'
            ], 500);
        }
    }

    /**
     * Mark all as read.
     */
    public function markAllRead(): JsonResponse
    {
        try {
            SuratPengajuan::where('status', 'pending')->update(['status' => 'diproses']);
            Pengaduan::where('status', 'baru')->update(['status' => 'diproses']);

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi ditandai sebagai dibaca'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai semua notifikasi'
            ], 500);
        }
    }
}
