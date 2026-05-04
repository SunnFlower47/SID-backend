<?php

namespace App\Http\Controllers\Tenant\Pelayanan;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:pelayanan_informasi']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by name, email, or subject
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subjek', 'like', "%{$search}%");
            });
        }

        // Sort by created_at desc by default
        $query->orderBy('created_at', 'desc');

        $messages = $query->paginate(20);

        // Get statistics
        $stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::unread()->count(),
            'read' => ContactMessage::read()->count(),
            'replied' => ContactMessage::replied()->count(),
            'archived' => ContactMessage::archived()->count(),
        ];

        return view('contact-messages.index', compact('messages', 'stats'));
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactMessage $contactMessage)
    {
        // Mark as read when viewed
        if ($contactMessage->status === 'unread') {
            $contactMessage->markAsRead();
        }

        return view('contact-messages.show', compact('contactMessage'));
    }

    /**
     * Mark message as read
     */
    public function markAsRead(ContactMessage $contactMessage)
    {
        $contactMessage->markAsRead();

        return redirect()->back()->with('success', 'Pesan ditandai sebagai sudah dibaca');
    }

    /**
     * Mark message as replied
     */
    public function markAsReplied(Request $request, ContactMessage $contactMessage)
    {
        $validator = Validator::make($request->all(), [
            'admin_reply' => 'required|string|max:2000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $contactMessage->markAsReplied($request->admin_reply);

        return redirect()->back()->with('success', 'Pesan ditandai sebagai sudah dijawab');
    }

    /**
     * Archive message
     */
    public function archive(ContactMessage $contactMessage)
    {
        $contactMessage->archive();

        return redirect()->back()->with('success', 'Pesan diarsipkan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')->with('success', 'Pesan berhasil dihapus');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:mark_read,mark_replied,archive,delete',
            'message_ids' => 'required|array|min:1',
            'message_ids.*' => 'exists:contact_messages,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $messages = ContactMessage::whereIn('id', $request->message_ids);
        $count = $messages->count();

        switch ($request->action) {
            case 'mark_read':
                $messages->update(['status' => 'read', 'read_at' => now()]);
                $message = "{$count} pesan ditandai sebagai sudah dibaca";
                break;
            case 'mark_replied':
                $messages->update(['status' => 'replied', 'replied_at' => now()]);
                $message = "{$count} pesan ditandai sebagai sudah dijawab";
                break;
            case 'archive':
                $messages->update(['status' => 'archived']);
                $message = "{$count} pesan diarsipkan";
                break;
            case 'delete':
                $messages->delete();
                $message = "{$count} pesan dihapus";
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get notifications for header
     */
    public function notifications()
    {
        try {
            $notifications = collect();
            $totalUnreadCount = 0;

            // 1. Contact Messages (unread) - last 7 days only
            $unreadContactMessages = ContactMessage::unread()
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $contactNotifications = $unreadContactMessages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'type' => 'contact_message',
                    'title' => 'Pesan Kontak Baru',
                    'message' => "{$message->nama}: " . Str::limit($message->subjek, 50),
                    'status' => $message->status,
                    'icon' => 'fas fa-envelope',
                    'time' => $message->created_at->diffForHumans(),
                    'url' => route('contact-messages.show', $message)
                ];
            });

            $notifications = $notifications->merge($contactNotifications);
            $totalUnreadCount += ContactMessage::unread()->where('created_at', '>=', now()->subDays(7))->count();

            // 2. Pengajuan Surat (pending/baru) - last 7 days only
            $pendingSurat = \App\Models\SuratPengajuan::where('status', 'pending')
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $suratNotifications = $pendingSurat->map(function ($surat) {
                return [
                    'id' => $surat->id,
                    'type' => 'surat_pengajuan',
                    'title' => 'Pengajuan Surat Baru',
                    'message' => "{$surat->nama_pemohon}: " . Str::limit($surat->jenis_surat, 50),
                    'status' => $surat->status,
                    'icon' => 'fas fa-file-alt',
                    'time' => $surat->created_at->diffForHumans(),
                    'url' => route('admin.surat-pengajuan.show', $surat)
                ];
            });

            $notifications = $notifications->merge($suratNotifications);
            $totalUnreadCount += \App\Models\SuratPengajuan::where('status', 'pending')->where('created_at', '>=', now()->subDays(7))->count();

            // 3. Pengaduan (pending/baru) - last 7 days only
            $pendingPengaduan = \App\Models\Pengaduan::where('status', 'pending')
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $pengaduanNotifications = $pendingPengaduan->map(function ($pengaduan) {
                return [
                    'id' => $pengaduan->id,
                    'type' => 'pengaduan',
                    'title' => 'Pengaduan Baru',
                    'message' => "{$pengaduan->nama_pelapor}: " . Str::limit($pengaduan->judul, 50),
                    'status' => $pengaduan->status,
                    'icon' => 'fas fa-exclamation-triangle',
                    'time' => $pengaduan->created_at->diffForHumans(),
                    'url' => route('pengaduan.show', $pengaduan)
                ];
            });

            $notifications = $notifications->merge($pengaduanNotifications);
            $totalUnreadCount += \App\Models\Pengaduan::where('status', 'pending')->where('created_at', '>=', now()->subDays(7))->count();

            // Sort all notifications by created_at desc and limit to 10
            $allNotifications = $notifications->sortByDesc(function ($notification) {
                return $notification['time'];
            })->take(10)->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $allNotifications,
                    'unread_count' => $totalUnreadCount
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil notifikasi',
                'data' => [
                    'notifications' => [],
                    'unread_count' => 0
                ]
            ]);
        }
    }
}
