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
                        'color' => $this->getStatusColor($surat->status),
                        'timestamp' => $surat->created_at->timestamp
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
                        'color' => $this->getPengaduanStatusColor($pengaduan->status),
                        'timestamp' => $pengaduan->created_at->timestamp
                    ];
                });

            // Get recent announcements from central (active, not expired & targeted)
            $recentAnnouncements = \App\Models\Central\BroadcastAnnouncement::active()
                ->forTenant(tenant('id'))
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->latest()
                ->get()
                ->map(function ($ann) {
                    return [
                        'id' => $ann->id,
                        'type' => 'announcement',
                        'title' => 'Pengumuman ' . ($ann->sender_name ?? 'Diskominfo'),
                        'message' => $ann->title . ": " . \Illuminate\Support\Str::limit($ann->message, 80),
                        'time' => $ann->created_at->diffForHumans(),
                        'status' => 'info',
                        'url' => route('dashboard'),
                        'icon' => 'fas fa-bullhorn',
                        'color' => $ann->type === 'danger' ? 'text-red-600' : ($ann->type === 'warning' ? 'text-yellow-600' : 'text-blue-600'),
                        'timestamp' => $ann->created_at->timestamp
                    ];
                });

            // Combine and sort by timestamp
            $notifications = $recentSurat->concat($recentPengaduan)->concat($recentAnnouncements)
                ->sortByDesc('timestamp')
                ->values()
                ->map(function($item) {
                    unset($item['timestamp']);
                    return $item;
                });

            // Count unread notifications
            $unreadCount = $notifications->where('status', '!=', 'selesai')->count();

            // If it's an Inertia page visit (like clicking "Lihat Semua"), return the Inertia page
            if (!$request->wantsJson() || $request->header('X-Inertia')) {
                return \Inertia\Inertia::render('Tenant/Notification/Index', [
                    'notifications' => $notifications,
                    'unreadCount' => $unreadCount,
                    'totalCount' => $notifications->count()
                ]);
            }

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

            $announcementCount = \App\Models\Central\BroadcastAnnouncement::active()
                ->forTenant(tenant('id'))
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->count();

            $totalCount = $suratCount + $pengaduanCount + $announcementCount;

            return response()->json([
                'success' => true,
                'data' => [
                    'surat_count' => $suratCount,
                    'pengaduan_count' => $pengaduanCount,
                    'announcement_count' => $announcementCount,
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
