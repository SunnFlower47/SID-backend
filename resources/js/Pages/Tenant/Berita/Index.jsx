import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import BeritaStats from '@/Components/Berita/BeritaStats';
import BeritaFilters from '@/Components/Berita/BeritaFilters';
import { Pagination, PageHeader, EmptyState, TableCard, Badge } from '@/Components/Shared';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { 
    Newspaper, Plus, Edit2, Trash2, Eye, 
    Calendar, User, Star, Megaphone, 
    CalendarDays, MoreVertical, Search
} from 'lucide-react';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';

export default function Index({ auth, berita, stats, filters }) {
    const handleDelete = (slug, judul) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Apakah Anda yakin ingin menghapus konten <b class="text-red-600">${judul}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATALKAN',
            customClass: {
                popup: 'rounded-[2.5rem] border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('berita.destroy', slug), {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'TERHAPUS!',
                            text: 'Berita telah berhasil dihapus.',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-3xl' }
                        });
                    }
                });
            }
        });
    };

    const getCategoryBadge = (category) => {
        switch(category) {
            case 'pengumuman':
                return "red";
            case 'agenda':
                return "blue";
            default:
                return "emerald";
        }
    };

    const getCategoryIcon = (category) => {
        switch(category) {
            case 'pengumuman':
                return Megaphone;
            case 'agenda':
                return CalendarDays;
            default:
                return Newspaper;
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Berita & Pengumuman">
            <Head title="Berita & Pengumuman - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader
                    icon={Newspaper}
                    title="Berita & Pengumuman"
                    subtitle="Pusat Informasi & Kegiatan Masyarakat Desa"
                    actions={[
                        { label: 'Tambah Berita', icon: Plus, href: route('berita.create') }
                    ]}
                />

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <BeritaStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <BeritaFilters filters={filters} />

                {/* Data Grid / Table */}
                <Deferred data="berita" fallback={<SkeletonTable columns={5} rows={6} />}>
                    <TableCard
                        icon={Newspaper}
                        title="Arsip Informasi Desa"
                        total={berita?.total || 0}
                        totalLabel="Konten"
                        pagination={berita}
                        noPadding
                    >
                        {berita?.data?.length > 0 ? (
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-0 divide-y md:divide-y-0 md:divide-x divide-gray-50 text-left">
                                {berita.data.map((item) => {
                                    const CategoryIcon = getCategoryIcon(item.kategori);
                                    return (
                                        <div key={item.id} className="p-8 hover:bg-green-50/20 transition-all group flex flex-col h-full text-left">
                                            <div className="flex items-start justify-between mb-6 text-left">
                                                <Badge color={getCategoryBadge(item.kategori)} className="flex items-center gap-1.5">
                                                    <CategoryIcon className="w-3 h-3" />
                                                    {item.kategori}
                                                </Badge>
                                                <div className={cn(
                                                    "w-2.5 h-2.5 rounded-full shadow-sm animate-pulse",
                                                    item.status === 'published' ? "bg-green-500 shadow-green-200" : "bg-yellow-400 shadow-yellow-100"
                                                )} title={item.status === 'published' ? 'Published' : 'Draft'}></div>
                                            </div>

                                            <div className="flex-1 text-left">
                                                <div className="aspect-video w-full rounded-2xl bg-gray-100 mb-5 overflow-hidden relative border border-gray-100 text-left">
                                                    {item.gambar ? (
                                                        <img src={`/storage/${item.gambar}`} className="w-full h-full object-cover group-hover:scale-110 transition-all duration-700 text-left" alt={item.judul} />
                                                    ) : (
                                                        <div className="w-full h-full flex items-center justify-center bg-gray-50 text-gray-200">
                                                            <Newspaper className="w-12 h-12" />
                                                        </div>
                                                    )}
                                                    {item.featured && (
                                                        <div className="absolute top-3 left-3 bg-white/90 backdrop-blur-md p-2 rounded-xl text-orange-500 shadow-xl border border-orange-100">
                                                            <Star className="w-4 h-4 fill-current" />
                                                        </div>
                                                    )}
                                                </div>

                                                <h4 className="text-lg font-black text-gray-900 leading-tight mb-3 line-clamp-2 uppercase italic tracking-tighter group-hover:text-green-700 transition-colors text-left">{item.judul}</h4>
                                                <p className="text-gray-500 text-xs font-bold leading-relaxed line-clamp-2 mb-6 text-left">
                                                    {item.excerpt || "Tidak ada ringkasan tersedia untuk konten ini."}
                                                </p>
                                            </div>

                                            <div className="space-y-4 pt-6 border-t border-gray-50 text-left">
                                                <div className="flex items-center justify-between text-[9px] font-black text-gray-400 uppercase tracking-widest text-left">
                                                    <div className="flex items-center gap-2 text-left text-left">
                                                        <Calendar className="w-3.5 h-3.5 text-gray-300" />
                                                        {new Date(item.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                                                    </div>
                                                    <div className="flex items-center gap-2 text-left">
                                                        <User className="w-3.5 h-3.5 text-gray-300 text-left" />
                                                        {item.author?.name || 'ADMIN'}
                                                    </div>
                                                </div>

                                                <div className="flex gap-2 text-left">
                                                    <Link href={route('berita.show', item.slug)} className="flex-1 py-3 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded-xl text-[10px] font-black text-center transition-all uppercase tracking-widest border border-blue-100 text-left">
                                                        DETAIL
                                                    </Link>
                                                    <Link href={route('berita.edit', item.slug)} className="px-4 py-3 bg-gray-50 hover:bg-gray-800 hover:text-white text-gray-700 rounded-xl transition-all border border-gray-100 text-left">
                                                        <Edit2 className="w-4 h-4" />
                                                    </Link>
                                                    <button onClick={() => handleDelete(item.slug, item.judul)} className="px-4 py-3 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 rounded-xl transition-all border border-red-100 text-left">
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        ) : (
                            <EmptyState
                                icon={Newspaper}
                                title="Belum Ada Informasi"
                                message="Pusat berita masih kosong. Mulai publikasikan informasi penting untuk masyarakat desa."
                                action={{
                                    label: "TERBITKAN BERITA PERTAMA",
                                    href: route('berita.create'),
                                    icon: Plus
                                }}
                            />
                        )}
                    </TableCard>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}
