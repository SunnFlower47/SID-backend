import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Shared/Pagination';
import { AlertTriangle, Clock, CheckCircle, ArrowLeft, Search, FileText, Wrench, History, ChevronDown, Filter } from 'lucide-react';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';
import { cn } from '@/lib/utils';

const LottieComponent = Lottie?.default || Lottie;

export default function Index({ auth, kkList, stats, tab, search, status }) {
    const [searchValue, setSearchValue] = React.useState(search || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('kk.bermasalah.index'), {
            tab: tab,
            search: searchValue,
            status: status
        }, { preserveState: true });
    };

    const handleFilterChange = (newStatus) => {
        router.get(route('kk.bermasalah.index'), {
            tab: tab,
            search: searchValue,
            status: newStatus
        }, { preserveState: true });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="KK Bermasalah">
            <Head title="Audit KK Bermasalah" />

            <div className="space-y-6 animate-in fade-in duration-500">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 text-white relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <AlertTriangle className="w-8 h-8 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black tracking-tight uppercase italic leading-none">KK Bermasalah</h1>
                                <p className="text-red-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">Audit Kehilangan Kepala Keluarga</p>
                            </div>
                        </div>
                        <Link 
                            href={route('kk.index')}
                            className="inline-flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                        >
                            <ArrowLeft className="w-4 h-4 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div className="bg-white rounded-2xl p-5 border border-red-100 shadow-sm flex items-center gap-4">
                        <div className="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center shrink-0">
                            <AlertTriangle className="w-6 h-6" />
                        </div>
                        <div>
                            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Belum Ditangani</p>
                            <h3 className="text-2xl font-black text-red-700 leading-none">{stats.bermasalah}</h3>
                        </div>
                    </div>
                    <div className="bg-white rounded-2xl p-5 border border-orange-100 shadow-sm flex items-center gap-4">
                        <div className="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center shrink-0">
                            <Clock className="w-6 h-6" />
                        </div>
                        <div>
                            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Sementara Aktif</p>
                            <h3 className="text-2xl font-black text-orange-600 leading-none">{stats.bermasalah_sementara}</h3>
                        </div>
                    </div>
                    <div className="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm flex items-center gap-4">
                        <div className="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                            <CheckCircle className="w-6 h-6" />
                        </div>
                        <div>
                            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Sudah Selesai</p>
                            <h3 className="text-2xl font-black text-emerald-700 leading-none">{stats.resolved}</h3>
                        </div>
                    </div>
                </div>

                {/* Main Content Area */}
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    {/* Tabs */}
                    <div className="flex border-b border-gray-100 p-2">
                        <Link 
                            href={route('kk.bermasalah.index', { tab: 'pending' })}
                            className={cn(
                                "flex-1 py-4 text-center rounded-2xl transition-all flex items-center justify-center gap-2",
                                tab === 'pending' 
                                    ? "bg-red-50 text-red-700 shadow-inner font-black italic uppercase text-xs" 
                                    : "text-gray-400 hover:text-gray-600 font-bold uppercase text-[10px] tracking-widest"
                            )}
                        >
                            <AlertTriangle className={cn("w-4 h-4", tab === 'pending' ? "text-red-600" : "text-gray-300")} />
                            Perlu Ditangani
                            {stats.pending_total > 0 && (
                                <span className="ml-1 bg-red-600 text-white text-[10px] rounded-full px-2 py-0.5 animate-pulse">{stats.pending_total}</span>
                            )}
                        </Link>
                        <Link 
                            href={route('kk.bermasalah.index', { tab: 'resolved' })}
                            className={cn(
                                "flex-1 py-4 text-center rounded-2xl transition-all flex items-center justify-center gap-2",
                                tab === 'resolved' 
                                    ? "bg-emerald-50 text-emerald-700 shadow-inner font-black italic uppercase text-xs" 
                                    : "text-gray-400 hover:text-gray-600 font-bold uppercase text-[10px] tracking-widest"
                            )}
                        >
                            <History className={cn("w-4 h-4", tab === 'resolved' ? "text-emerald-600" : "text-gray-300")} />
                            Riwayat Audit
                        </Link>
                    </div>

                    {/* Search Bar */}
                    <div className="p-4 sm:p-6 bg-gray-50/50 border-b border-gray-100">
                        <form onSubmit={handleSearch} className="flex flex-col sm:flex-row gap-3">
                            <div className="relative flex-1">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Search className="text-gray-400 w-4 h-4" />
                                </div>
                                <input 
                                    type="text" 
                                    name="search"
                                    value={searchValue}
                                    onChange={(e) => setSearchValue(e.target.value)}
                                    placeholder="Cari NKK atau nama kepala keluarga..."
                                    className="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 text-sm font-bold bg-white shadow-sm transition-all"
                                />
                            </div>

                            {tab === 'pending' && (
                                <div className="relative min-w-[180px]">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <Filter className="text-gray-400 w-4 h-4" />
                                    </div>
                                    <select 
                                        name="status"
                                        value={status || ''}
                                        onChange={(e) => handleFilterChange(e.target.value)}
                                        className="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-500 text-sm font-bold bg-white cursor-pointer shadow-sm transition-all"
                                    >
                                        <option value="">Semua Status</option>
                                        <option value="bermasalah">Kritis</option>
                                        <option value="bermasalah_sementara">Sementara</option>
                                    </select>
                                </div>
                            )}

                            <button type="submit" className="px-8 py-3 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg shadow-black/10 active:scale-95">
                                CARI DATA
                            </button>
                        </form>
                    </div>

                    {/* Table */}
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm">
                            <thead className="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                {tab === 'resolved' ? (
                                    <tr>
                                        <th className="px-6 py-4">NKK Lama</th>
                                        <th className="px-6 py-4">Nama KK Lama</th>
                                        <th className="px-6 py-4">NKK Baru</th>
                                        <th className="px-6 py-4 text-right">Aksi Audit</th>
                                    </tr>
                                ) : (
                                    <tr>
                                        <th className="px-6 py-4">NKK</th>
                                        <th className="px-6 py-4">Calon Kepala KK</th>
                                        <th className="px-6 py-4">Status</th>
                                        <th className="px-6 py-4 text-right">Aksi Selesaikan</th>
                                    </tr>
                                )}
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {kkList.data.length > 0 ? kkList.data.map(kk => (
                                    <tr key={kk.id} className={cn("hover:bg-gray-50 transition-all", tab === 'pending' ? 'hover:bg-red-50/50' : 'hover:bg-emerald-50/50')}>
                                        <td className="px-6 py-4">
                                            <div className="font-mono text-xs font-bold text-gray-900">{kk.nkk}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <p className="font-black text-gray-900 uppercase text-xs italic">{kk.nama_kepala_keluarga || 'Tidak Ada'}</p>
                                        </td>
                                        {tab === 'resolved' ? (
                                            <>
                                                <td className="px-6 py-4">
                                                    <span className="font-mono text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded inline-block">
                                                        {(() => {
                                                            if (kk.mutasiPenyebab?.detail_tambahan?.nkk_baru) {
                                                                return kk.mutasiPenyebab.detail_tambahan.nkk_baru;
                                                            }
                                                            if (kk.catatan_bermasalah) {
                                                                try {
                                                                    const parsed = JSON.parse(kk.catatan_bermasalah);
                                                                    if (parsed.nkk_baru) return parsed.nkk_baru;
                                                                } catch (e) {
                                                                    // Ignore parsing error
                                                                }
                                                            }
                                                            return '-';
                                                        })()}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-right">
                                                    {kk.mutasi_penyebab_id ? (
                                                        <Link href={route('mutasi.data.show', kk.mutasi_penyebab_id)} className="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-[10px] font-black uppercase tracking-widest">
                                                            <FileText className="w-3 h-3 mr-2" /> DETAIL MUTASI
                                                        </Link>
                                                    ) : (
                                                        <span className="text-[10px] font-bold text-gray-400 italic uppercase">Tidak Ada Data Mutasi</span>
                                                    )}
                                                </td>
                                            </>
                                        ) : (
                                            <>
                                                <td className="px-6 py-4">
                                                    {kk.status_kk === 'bermasalah' ? (
                                                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black bg-red-100 text-red-800 uppercase tracking-tighter italic">
                                                            <AlertTriangle className="w-3 h-3 mr-1" /> KRITIS
                                                        </span>
                                                    ) : (
                                                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black bg-orange-100 text-orange-800 uppercase tracking-tighter italic">
                                                            <Clock className="w-3 h-3 mr-1" /> SEMENTARA
                                                        </span>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 text-right">
                                                    <Link href={route('kk.bermasalah', kk.nkk)} className="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-200 transition-all hover:scale-105 active:scale-95">
                                                        <Wrench className="w-3.5 h-3.5 mr-2" /> SELESAIKAN
                                                    </Link>
                                                </td>
                                            </>
                                        )}
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="4" className="px-6 py-20 text-center">
                                            <div className="w-48 h-48 mx-auto mb-4 opacity-80">
                                                <LottieComponent animationData={noDataAnimation} loop={true} />
                                            </div>
                                            <h4 className="text-sm font-black text-gray-900 uppercase italic">Data Sudah Bersih</h4>
                                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Tidak ada KK yang bermasalah saat ini.</p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <div className="p-4 border-t border-gray-100 bg-gray-50/50">
                        <Pagination links={kkList.links} total={kkList.total} from={kkList.from} to={kkList.to} />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
