import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/Shared';
import { BookOpen, FileBadge, Building2, Users, FileText, ChevronRight, MapPin, Archive, Mails, CreditCard } from 'lucide-react';
import { cn } from '@/lib/utils';

const BookCard = ({ title, desc, icon: Icon, href, color = 'blue' }) => {
    const colors = {
        blue:   'text-blue-600 bg-blue-50 border-blue-100 hover:border-blue-300',
        green:  'text-green-600 bg-green-50 border-green-100 hover:border-green-300',
        purple: 'text-purple-600 bg-purple-50 border-purple-100 hover:border-purple-300',
        amber:  'text-amber-600 bg-amber-50 border-amber-100 hover:border-amber-300',
        teal:   'text-teal-600 bg-teal-50 border-teal-100 hover:border-teal-300',
        indigo: 'text-indigo-600 bg-indigo-50 border-indigo-100 hover:border-indigo-300',
        emerald: 'text-emerald-600 bg-emerald-50 border-emerald-100 hover:border-emerald-300',
    };

    const iconColors = {
        blue:   'bg-blue-600',
        green:  'bg-green-600',
        purple: 'bg-purple-600',
        amber:  'bg-amber-600',
        teal:   'bg-teal-600',
        indigo: 'bg-indigo-600',
        emerald: 'bg-emerald-600',
    };

    return (
        <Link href={href} className={cn(
            "group flex flex-col justify-between p-6 bg-white border border-gray-100 rounded-3xl transition-all shadow-sm hover:shadow-xl",
            "hover:-translate-y-1"
        )}>
            <div>
                <div className="flex items-center justify-between mb-4">
                    <div className={cn("w-12 h-12 rounded-2xl flex items-center justify-center shadow-md", iconColors[color], "text-white")}>
                        <Icon className="w-6 h-6" />
                    </div>
                    <div className={cn("w-8 h-8 rounded-full flex items-center justify-center opacity-0 -translate-x-2 transition-all group-hover:opacity-100 group-hover:translate-x-0", colors[color])}>
                        <ChevronRight className="w-4 h-4" />
                    </div>
                </div>
                <h3 className="text-lg font-black text-gray-900 tracking-tight leading-none mb-2">{title}</h3>
                <p className="text-xs font-semibold text-gray-500 leading-relaxed">{desc}</p>
            </div>
        </Link>
    );
};

export default function BukuAdministrasiIndex({ auth }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Koleksi Buku Administrasi">
            <Head title="Buku Administrasi Desa - Admin Panel" />

            <div className="space-y-8 animate-in fade-in duration-700 pb-20">

                {/* ── Header ── */}
                <PageHeader
                    icon={BookOpen}
                    title="Buku Administrasi Desa"
                    subtitle="Berdasarkan format standar Permendagri No. 47 Tahun 2016"
                />

                {/* ── Section: Administrasi Umum ── */}
                <div className="space-y-4">
                    <div className="flex items-center gap-3">
                        <div className="w-8 h-8 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shrink-0 border border-blue-100">
                            <Building2 className="w-4 h-4" />
                        </div>
                        <div>
                            <h2 className="text-sm font-black text-gray-900 tracking-tight uppercase leading-none">Administrasi Umum</h2>
                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Buku Kegiatan & Peraturan</p>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <BookCard 
                            title="Buku Peraturan di Desa"
                            desc="Rekapitulasi seluruh Perdes, Peraturan Kades, dan Keputusan Bersama"
                            icon={FileBadge}
                            href={route('administrasi.buku.show', 'peraturan-desa')}
                            color="blue"
                        />
                        <BookCard 
                            title="Buku Keputusan Kepala Desa"
                            desc="Arsip produk hukum dan surat keputusan internal pemerintahan desa"
                            icon={BookOpen}
                            href={route('administrasi.buku.show', 'keputusan-kades')}
                            color="amber"
                        />
                        <BookCard 
                            title="Buku Inventaris & Kekayaan" 
                            desc="Pencatatan aset, inventaris, dan kekayaan yang dimiliki desa sesuai format Permendagri."
                            icon={Archive} 
                            color="purple"
                            href={route('administrasi.buku.show', 'inventaris-kekayaan')} 
                        />
                        <BookCard 
                            title="Buku Tanah Kas Desa" 
                            desc="Daftar seluruh tanah aset desa — sawah, lapangan, pemakaman, dan tanah bangunan."
                            icon={MapPin} 
                            color="teal"
                            href={route('administrasi.buku.show', 'tanah-kas-desa')} 
                        />
                        <BookCard 
                            title="Buku Aparat Pemerintah Desa" 
                            desc="Data profil, jabatan, dan struktur perangkat pemerintahan desa."
                            icon={Users} 
                            color="green"
                            href={route('administrasi.buku.show', 'aparat-pemerintah')} 
                        />
                        <BookCard 
                            title="Buku Agenda" 
                            desc="Mencatat seluruh data surat masuk dan surat keluar desa secara kronologis."
                            icon={Mails} 
                            color="indigo"
                            href={route('administrasi.buku.show', 'buku-agenda')} 
                        />
                        <BookCard 
                            title="Buku Tanah di Desa" 
                            desc="Mencatat seluruh tanah yang ada di wilayah desa (milik warga, adat, dsb)."
                            icon={MapPin} 
                            color="emerald"
                            href={route('administrasi.buku.show', 'tanah-di-desa')} 
                        />
                    </div>
                </div>

                <div className="h-px w-full bg-gradient-to-r from-transparent via-gray-200 to-transparent my-8" />

                {/* ── Section: Administrasi Penduduk ── */}
                <div className="space-y-4">
                    <div className="flex items-center gap-3">
                        <div className="w-8 h-8 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shrink-0 border border-indigo-100">
                            <Users className="w-4 h-4" />
                        </div>
                        <div>
                            <h2 className="text-sm font-black text-gray-900 tracking-tight uppercase leading-none">Administrasi Penduduk</h2>
                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Buku Data Kependudukan</p>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <BookCard 
                            title="Buku Induk Penduduk" 
                            desc="Laporan data profil seluruh penduduk aktif di wilayah desa."
                            icon={Users} 
                            color="blue"
                            href={route('administrasi.buku.show', 'buku-induk-penduduk')}
                        />
                        <BookCard 
                            title="Buku Mutasi Penduduk" 
                            desc="Daftar warga yang mengalami pindah, datang, lahir, dan wafat."
                            icon={Users} 
                            color="purple"
                            href={route('administrasi.buku.show', 'buku-mutasi-penduduk')}
                        />
                        <BookCard 
                            title="Buku Rekapitulasi Jumlah Penduduk" 
                            desc="Laporan statistik bulanan tentang mutasi penduduk per dusun."
                            icon={FileText} 
                            color="emerald"
                            href={route('administrasi.buku.show', 'buku-rekapitulasi-penduduk')}
                        />
                        <BookCard 
                            title="Buku Penduduk Sementara" 
                            desc="Mencatat warga pendatang sementara (domisili/tamu wajib lapor)."
                            icon={Users} 
                            color="amber"
                            href={route('administrasi.buku.show', 'buku-penduduk-sementara')}
                        />
                        <BookCard 
                            title="Buku KTP dan KK" 
                            desc="Mencatat daftar warga yang memiliki KTP dan Kartu Keluarga."
                            icon={CreditCard} 
                            color="indigo"
                            href={route('administrasi.buku.show', 'buku-ktp-kk')}
                        />
                    </div>
                </div>

                <div className="h-px w-full bg-gradient-to-r from-transparent via-gray-200 to-transparent my-8" />

                {/* ── Section: Administrasi Keuangan ── */}
                <div className="space-y-4">
                    <div className="flex items-center gap-3">
                        <div className="w-8 h-8 bg-green-50 rounded-xl flex items-center justify-center text-green-600 shrink-0 border border-green-100">
                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 className="text-sm font-black text-gray-900 tracking-tight uppercase leading-none">Administrasi Keuangan</h2>
                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Buku Keuangan & Anggaran</p>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <BookCard 
                            title="Buku APB Desa" 
                            desc="Buku Anggaran Pendapatan dan Belanja Desa."
                            icon={FileText} 
                            color="green"
                            href={route('administrasi.buku.show', 'buku-apb-desa')}
                        />
                        <BookCard 
                            title="Buku Rencana Anggaran Biaya" 
                            desc="Buku RAB beserta rincian volume dan harga satuan."
                            icon={FileText} 
                            color="blue"
                            href={route('administrasi.buku.show', 'buku-rab')}
                        />
                    </div>
                </div>

                <div className="h-px w-full bg-gradient-to-r from-transparent via-gray-200 to-transparent my-8" />

                {/* ── Section: Administrasi Pembangunan ── */}
                <div className="space-y-4">
                    <div className="flex items-center gap-3">
                        <div className="w-8 h-8 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 shrink-0 border border-amber-100">
                            <Building2 className="w-4 h-4" />
                        </div>
                        <div>
                            <h2 className="text-sm font-black text-gray-900 tracking-tight uppercase leading-none">Administrasi Pembangunan</h2>
                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Buku Proyek & Pembangunan</p>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <BookCard 
                            title="Buku Rencana Kerja Pembangunan" 
                            desc="Daftar rekapitulasi proyek pembangunan sesuai APBDes yang direncanakan."
                            icon={Building2} 
                            color="amber"
                            href={route('administrasi.buku.show', 'rkp-desa')}
                        />
                        <BookCard 
                            title="Buku Kegiatan Pembangunan" 
                            desc="Pencatatan realisasi kegiatan pelaksanaan proyek pembangunan."
                            icon={Building2} 
                            color="blue"
                            href={route('administrasi.buku.show', 'buku-kegiatan-pembangunan')}
                        />
                        <BookCard 
                            title="Buku Inventaris Hasil Pembangunan" 
                            desc="Daftar aset dan hasil proyek pembangunan desa yang telah selesai."
                            icon={Archive} 
                            color="emerald"
                            href={route('administrasi.buku.show', 'buku-inventaris-pembangunan')}
                        />
                    </div>
                </div>

            </div>
        </AuthenticatedLayout>
    );
}
