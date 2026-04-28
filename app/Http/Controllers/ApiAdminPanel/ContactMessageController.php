<?php

namespace App\Http\Controllers\ApiAdminPanel;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class ContactMessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ContactMessage::query();
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('subjek', 'like', "%{$search}%"));
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $messages,
            'meta' => [
                'stats' => [
                    'total' => ContactMessage::count(),
                    'unread' => ContactMessage::unread()->count(),
                ]
            ]
        ]);
    }

    public function show(ContactMessage $contactMessage): JsonResponse
    {
        if ($contactMessage->status === 'unread') $contactMessage->markAsRead();
        return response()->json(['status' => 'success', 'data' => $contactMessage]);
    }

    public function markAsReplied(Request $request, ContactMessage $contactMessage): JsonResponse
    {
        $request->validate(['admin_reply' => 'required|string']);
        $contactMessage->markAsReplied($request->admin_reply);
        return response()->json(['status' => 'success', 'message' => 'Pesan ditandai sebagai sudah dijawab']);
    }

    public function archive(ContactMessage $contactMessage): JsonResponse
    {
        $contactMessage->archive();
        return response()->json(['status' => 'success', 'message' => 'Pesan diarsipkan']);
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:mark_read,mark_replied,archive,delete',
            'ids' => 'required|array|min:1',
        ]);

        $query = ContactMessage::whereIn('id', $request->ids);
        
        switch ($request->action) {
            case 'mark_read': $query->update(['status' => 'read', 'read_at' => now()]); break;
            case 'archive': $query->update(['status' => 'archived']); break;
            case 'delete': $query->delete(); break;
        }

        return response()->json(['status' => 'success', 'message' => 'Aksi massal berhasil diterapkan']);
    }

    public function notifications(): JsonResponse
    {
        $totalUnread = ContactMessage::unread()->count() + 
                       \App\Models\SuratPengajuan::where('status', 'pending')->count() +
                       \App\Models\Pengaduan::where('status', 'baru')->count();

        $recent = ContactMessage::unread()->orderBy('created_at', 'desc')->limit(5)->get()->map(fn($m) => [
            'id' => $m->id,
            'type' => 'contact',
            'title' => 'Pesan Baru',
            'message' => "{$m->nama}: {$m->subjek}",
            'time' => $m->created_at->diffForHumans()
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'unread_count' => $totalUnread,
                'recent' => $recent
            ]
        ]);
    }
}
