import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/Shared';
import { 
    Newspaper, ArrowLeft, Calendar, 
    User, Eye, Tag, Clock, CheckCircle,
    Share2, Megaphone, CalendarDays,
    Star, LayoutGrid, Quote
} from 'lucide-react';
import { cn } from '@/lib/utils';

export default function Show({ auth, berita }) {
    const getCategoryBadge = (category) => {
        switch(category) {
            case 'pengumuman':
                return "bg-red-50 text-red-600 border-red-100";
            case 'agenda':
                return "bg-blue-50 text-blue-600 border-blue-100";
            default:
                return "bg-green-50 text-green-600 border-green-100";
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

    const CategoryIcon = getCategoryIcon(berita.kategori);

    return (
        <AuthenticatedLayout user={auth.user} title={`Detail: ${berita.judul}`}>
            <Head title={`Detail: ${berita.judul} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader
                    title={berita.judul}
                    subtitle="Detail Publikasi & Statistik Informasi"
                    icon={CategoryIcon}
                    titleSize="sm"
                    backHref={route('berita.index')}
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 text-left">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6 text-left">
                        <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-left text-left">
                            {berita.gambar && (
                                <div className="aspect-video w-full overflow-hidden border-b border-gray-50 text-left text-left">
                                    <img src={berita.image_url || `/storage/${berita.gambar}`} className="w-full h-full object-cover text-left text-left" alt={berita.judul} />
                                </div>
                            )}

                            <div className="p-8 sm:p-12 text-left text-left">
                                <div className="flex flex-wrap items-center justify-between gap-4 mb-10 text-left text-left">
                                    <div className="flex flex-wrap items-center gap-3 text-left">
                                        <div className={cn(
                                            "px-5 py-2 rounded-full border text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2 text-left",
                                            getCategoryBadge(berita.kategori)
                                        )}>
                                            <CategoryIcon className="w-4 h-4" />
                                            {berita.kategori}
                                        </div>
                                        {berita.featured && (
                                            <div className="px-5 py-2 bg-orange-50 text-orange-600 border border-orange-100 rounded-full text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2 text-left text-left text-left">
                                                <Star className="w-4 h-4 fill-current text-left text-left" /> FEATURED
                                            </div>
                                        )}
                                        <div className={cn(
                                            "px-5 py-2 rounded-full border text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2 text-left",
                                            berita.status === 'published' ? "bg-green-50 text-green-600 border-green-100" : "bg-yellow-50 text-yellow-600 border-yellow-100"
                                        )}>
                                            {berita.status === 'published' ? <CheckCircle className="w-4 h-4" /> : <Clock className="w-4 h-4" />}
                                            {berita.status.toUpperCase()}
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-6 text-gray-400 text-left">
                                        <div className="flex items-center gap-2 text-left">
                                            <Eye className="w-4 h-4" />
                                            <span className="text-xs font-black italic">1.2K VIEWS</span>
                                        </div>
                                    </div>
                                </div>

                                <h2 className="text-3xl sm:text-5xl font-black text-gray-900 leading-tight mb-8 uppercase italic tracking-tighter text-left">
                                    {berita.judul}
                                </h2>

                                <div className="flex items-center gap-6 mb-12 pb-8 border-b border-gray-50 text-left text-left text-left">
                                    <div className="flex items-center gap-3 text-left text-left">
                                        <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-left">
                                            <User className="w-6 h-6 text-gray-400 text-left text-left" />
                                        </div>
                                        <div className="text-left text-left text-left">
                                            <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1 text-left text-left">Penulis</p>
                                            <p className="text-xs font-black text-gray-900 uppercase italic text-left text-left text-left">{berita.author?.name || 'ADMIN DESA'}</p>
                                        </div>
                                    </div>
                                    <div className="h-10 w-px bg-gray-100 text-left text-left"></div>
                                    <div className="text-left text-left text-left text-left">
                                        <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1 text-left text-left text-left text-left">Diterbitkan Pada</p>
                                        <div className="flex items-center gap-2 text-left text-left">
                                            <Calendar className="w-4 h-4 text-green-500 text-left text-left text-left" />
                                            <p className="text-xs font-black text-gray-900 uppercase italic text-left text-left text-left text-left">
                                                {new Date(berita.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div className="prose prose-lg max-w-none text-left">
                                     {berita.excerpt && (
                                         <div className="bg-gray-50 rounded-[2rem] p-8 mb-10 border-l-8 border-green-500 relative text-left">
                                             <Quote className="absolute top-4 right-6 w-12 h-12 text-gray-100 -z-0" />
                                             <p 
                                                 className="text-gray-600 font-bold italic leading-relaxed relative z-10 text-lg text-left"
                                                 dangerouslySetInnerHTML={{ __html: berita.excerpt }}
                                             />
                                         </div>
                                     )}
                                     <div 
                                         className="text-gray-700 font-medium leading-[2] text-lg text-left prose max-w-none"
                                         dangerouslySetInnerHTML={{ __html: berita.konten }}
                                     />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Sidebar Info */}
                    <div className="space-y-6 text-left">
                        <div className="bg-white rounded-[3rem] shadow-sm border border-gray-100 p-8 sm:p-10 text-left">
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter mb-8 flex items-center gap-4 text-left">
                                <LayoutGrid className="w-5 h-5 text-green-600" />
                                Metadata Konten
                            </h3>
                            
                            <div className="space-y-6 text-left">
                                <div className="p-5 bg-gray-50 rounded-3xl border border-gray-100 text-left">
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 text-left">Permalink / Slug</p>
                                    <p className="text-[10px] font-bold text-blue-600 break-all text-left">/{berita.slug}</p>
                                </div>
                                <div className="p-5 bg-gray-50 rounded-3xl border border-gray-100 text-left">
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 text-left">Terakhir Diperbarui</p>
                                    <p className="text-xs font-black text-gray-900 uppercase italic text-left">
                                        {new Date(berita.updated_at).toLocaleDateString('id-ID', { hour: '2-digit', minute: '2-digit', day: 'numeric', month: 'short' })}
                                    </p>
                                </div>
                                <div className="p-5 bg-gradient-to-br from-green-600 to-green-700 rounded-3xl shadow-lg shadow-green-100 text-white text-left">
                                    <p className="text-[9px] font-black text-green-100 uppercase tracking-widest mb-3 text-left">Aksi Konten</p>
                                    <div className="flex flex-col gap-2 text-left">
                                        <Link href={route('berita.edit', berita.slug)} className="w-full py-3 bg-white/20 hover:bg-white text-white hover:text-green-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-center text-left">
                                            EDIT KONTEN
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bg-gray-900 rounded-[3rem] shadow-xl p-8 sm:p-10 text-white text-left">
                            <div className="flex items-center gap-4 mb-8 text-left">
                                <div className="w-12 h-12 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/10 text-left">
                                    <Share2 className="w-6 h-6 text-yellow-400" />
                                </div>
                                <h3 className="text-sm font-black uppercase italic tracking-tighter text-left">Bagikan Informasi</h3>
                            </div>
                            <p className="text-[10px] font-bold text-gray-400 leading-relaxed mb-8 uppercase tracking-widest text-left">
                                Pastikan informasi ini sampai ke seluruh masyarakat melalui kanal resmi desa.
                            </p>
                            <div className="grid grid-cols-2 gap-3 text-left">
                                <button className="py-3.5 bg-green-600 hover:bg-green-700 rounded-2xl text-[9px] font-black uppercase tracking-widest transition-all text-left text-center">WHATSAPP</button>
                                <button className="py-3.5 bg-blue-600 hover:bg-blue-700 rounded-2xl text-[9px] font-black uppercase tracking-widest transition-all text-left text-center">FACEBOOK</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
