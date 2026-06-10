import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, EmptyState, Pagination } from '@/Components/Shared';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { FileBadge, Search, Download, Printer, Filter, Calendar, RefreshCw } from 'lucide-react';
import Lottie from 'lottie-react';
import loadingAnimation from '@/assets/lottie/loading-circle-animation.json';
import successAnimation from '@/assets/lottie/success-animation.json';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';

const LottieComponent = Lottie?.default || Lottie;

export default function BukuLayout({ 
    auth, 
    jenis_buku, 
    judul, 
    data, 
    filters, 
    
    // Props untuk tabel standar (paginated)
    tableHead, 
    renderRow, 
    
    // Konfigurasi tabel
    isCustomTable = false,
    children, // Untuk custom table seperti Inventaris
    
    // Konfigurasi Filter
    hasStandardFilter = true,
    customFilter = null,
    extraFilterFields = null, // Tambahan field filter khusus
    isInventarisFilter = false, // Spesifik hanya tahun (Legacy)
    hasTahunFilter = false,     // Menampilkan input Tahun
    hideDateFilter = false,     // Menyembunyikan Tanggal
    onCustomFilterSubmit = null, // Hook untuk memodifikasi parameter filter sebelum submit
}) {
    const currentYear = new Date().getFullYear();
    const [search, setSearch] = useState(filters?.search || '');
    const [startDate, setStartDate] = useState(filters?.start_date || '');
    const [endDate, setEndDate] = useState(filters?.end_date || '');
    const [tahun, setTahun] = useState(filters?.tahun || String(currentYear));
    
    const hasActiveFilters = filters?.search || filters?.start_date || filters?.end_date || (isInventarisFilter && filters?.tahun && filters?.tahun !== String(currentYear)) || (hasTahunFilter && filters?.tahun && filters?.tahun !== String(currentYear));
    const [showFilters, setShowFilters] = useState(hasActiveFilters ? true : false);

    const [isExporting, setIsExporting] = useState(false);
    const [showSuccess, setShowSuccess] = useState(false);
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        const removeStart = router.on('start', () => setIsLoading(true));
        const removeFinish = router.on('finish', () => setIsLoading(false));
        return () => {
            removeStart();
            removeFinish();
        };
    }, []);

    const handleExport = async (e) => {
        if (e) e.preventDefault();
        setIsExporting(true);

        try {
            let params = { ...filters };
            if (isInventarisFilter) {
                params.tahun = tahun;
            } else if (!customFilter) {
                params.search = search;
                if (!hideDateFilter) {
                    params.start_date = startDate;
                    params.end_date = endDate;
                } else {
                    delete params.start_date;
                    delete params.end_date;
                }
                if (hasTahunFilter) {
                    params.tahun = tahun;
                }
            }
            
            if (onCustomFilterSubmit) {
                params = onCustomFilterSubmit(params, 'export');
            }
            
            const response = await axios.get(route('administrasi.buku.export.excel', jenis_buku), {
                params,
                responseType: 'blob'
            });

            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `Buku_${jenis_buku.replace(/-/g, '_')}_${new Date().toLocaleDateString('id-ID')}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();

            setShowSuccess(true);
            setTimeout(() => setShowSuccess(false), 3000);
        } catch (error) {
            console.error('Export error:', error);
            Swal.fire('Gagal!', 'Terjadi kesalahan saat mengekspor data.', 'error');
        } finally {
            setIsExporting(false);
        }
    };

    const handleFilter = (e) => {
        e.preventDefault();
        let params = { ...filters }; // preserve external filters like apbdes_id
        if (isInventarisFilter) {
            params.tahun = tahun;
        } else {
            params.search = search;
            if (!hideDateFilter) {
                params.start_date = startDate;
                params.end_date = endDate;
            } else {
                delete params.start_date;
                delete params.end_date;
            }
            if (hasTahunFilter) {
                params.tahun = tahun;
            }
        }
        
        if (onCustomFilterSubmit) {
            params = onCustomFilterSubmit(params, 'filter');
        }
        
        router.get(route('administrasi.buku.show', jenis_buku), params, { preserveState: true });
    };

    const handleReset = () => {
        setSearch(''); setStartDate(''); setEndDate('');
        setTahun(String(currentYear));
        let params = { ...filters };
        delete params.search;
        delete params.start_date;
        delete params.end_date;
        delete params.tahun;
        
        if (onCustomFilterSubmit) {
            params = onCustomFilterSubmit(params, 'reset');
        }
        
        router.get(route('administrasi.buku.show', jenis_buku), params);
    };

    const queryParams = new URLSearchParams();
    if (filters) {
        Object.keys(filters).forEach(key => {
            if (filters[key]) queryParams.append(key, filters[key]);
        });
    }

    if (isInventarisFilter) {
        if (tahun) queryParams.set('tahun', tahun);
    } else if (!customFilter) {
        if (search) queryParams.set('search', search);
        if (!hideDateFilter) {
            if (startDate) queryParams.set('start_date', startDate);
            if (endDate) queryParams.set('end_date', endDate);
        }
        if (hasTahunFilter && tahun) queryParams.set('tahun', tahun);
    }
    const pdfUrl = `${route('administrasi.buku.export.pdf', jenis_buku)}?${queryParams.toString()}`;

    const handlePrintPdf = (e) => {
        e.preventDefault();
        
        // Pengecekan keamanan limitasi PDF
        const totalData = data?.total || (Array.isArray(data) ? data.length : 0);
        
        if (totalData > 500) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Terlalu Besar',
                text: `Data saat ini mencapai ${totalData} baris. Mencetak dokumen PDF dengan ukuran sebesar ini dapat menyebabkan server berhenti bekerja (Crash). Silakan gunakan filter pencarian atau unduh menggunakan Excel!`,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        window.open(pdfUrl, '_blank');
    };

    const actions = [
        { label: 'Cetak PDF', icon: Printer, onClick: handlePrintPdf, variant: 'white' },
        { label: 'Unduh Excel', icon: Download, onClick: handleExport, loading: isExporting, variant: 'ghost' },
    ];

    return (
        <AuthenticatedLayout user={auth.user} title={judul}>
            <Head title={`${judul} - Admin Panel`} />

            {/* Custom Loading Overlay */}
            {isExporting && (
                <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm animate-in fade-in duration-300">
                    <div className="bg-white rounded-3xl p-8 shadow-2xl flex flex-col items-center gap-4 max-w-xs w-full mx-4 animate-in zoom-in-95 duration-300">
                        <div className="w-24 h-24">
                            <LottieComponent animationData={loadingAnimation} loop={true} />
                        </div>
                        <div className="text-center">
                            <h3 className="text-lg font-black text-gray-900">Mengekspor Data</h3>
                            <p className="text-sm text-gray-500 mt-1">Mohon tunggu, file Excel sedang disiapkan...</p>
                        </div>
                    </div>
                </div>
            )}

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader
                    icon={FileBadge}
                    title={judul}
                    subtitle="Format baku sesuai ketentuan Permendagri 47/2016"
                    actions={actions}
                    backHref={route('administrasi.buku.index')}
                />

                {/* Filters */}
                {customFilter ? customFilter : hasStandardFilter ? (
                    <div className="mb-6 space-y-4">
                        <div className="flex justify-between items-center bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm transition-all">
                            <div className="flex items-center gap-2 sm:gap-4">
                                <div className="w-8 h-8 sm:w-12 sm:h-12 bg-green-50 rounded-xl flex items-center justify-center">
                                    <Search className="w-4 h-4 sm:w-6 sm:h-6 text-green-600" />
                                </div>
                                <div>
                                    <h3 className="text-[10px] sm:text-sm font-black text-gray-950 uppercase italic tracking-tighter leading-none mb-1 text-left">Konfigurasi Data</h3>
                                    <p className="hidden sm:block text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">Pencarian & Filter Buku</p>
                                </div>
                            </div>
                            <button
                                onClick={() => setShowFilters(!showFilters)}
                                className={cn(
                                    "flex items-center px-4 py-2 sm:px-6 sm:py-3 rounded-xl text-[9px] sm:text-xs font-black transition-all border shadow-sm active:scale-95",
                                    showFilters
                                        ? "bg-yellow-400 text-yellow-900 border-yellow-500 shadow-yellow-400/20"
                                        : "bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100"
                                )}
                            >
                                <Filter className="w-3 h-3 sm:w-4 sm:h-4 mr-2" />
                                {showFilters ? 'TUTUP PANEL' : 'BUKA FILTER'}
                            </button>
                        </div>

                        {showFilters && (
                            <form onSubmit={handleFilter} className="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex flex-col lg:flex-row gap-4 items-end animate-in slide-in-from-top-2 duration-300">
                                <div className="flex-1 w-full grid grid-cols-1 sm:grid-cols-2 lg:flex gap-4">
                                    {isInventarisFilter ? (
                                        <div className="flex-1 space-y-2 text-left">
                                            <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Ketik Tahun</label>
                                            <div className="relative">
                                                <Calendar className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                                <input
                                                    type="number"
                                                    placeholder="Masukkan Tahun..."
                                                    className="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                                    value={tahun}
                                                    onChange={(e) => setTahun(e.target.value)}
                                                    min="1900"
                                                    max="2099"
                                                />
                                            </div>
                                        </div>
                                    ) : (
                                        <>
                                            <div className="flex-1 space-y-2 text-left">
                                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Pencarian</label>
                                                <div className="relative">
                                                    <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                                    <input type="text" placeholder="Cari data..."
                                                        className="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                                        value={search} onChange={(e) => setSearch(e.target.value)} />
                                                </div>
                                            </div>
                                            {hasTahunFilter && (
                                                <div className="w-full lg:w-32 space-y-2 text-left">
                                                    <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Tahun</label>
                                                    <div className="relative">
                                                        <Calendar className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                                        <input type="number" placeholder="Tahun"
                                                            className="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                                            value={tahun} onChange={(e) => setTahun(e.target.value)}
                                                            min="1900" max="2099" />
                                                    </div>
                                                </div>
                                            )}
                                            {!hideDateFilter && (
                                                <>
                                                    <div className="w-full lg:w-48 space-y-2 text-left">
                                                        <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Dari Tanggal</label>
                                                        <div className="relative">
                                                            <Calendar className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                                            <input type="date"
                                                                className="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                                                value={startDate} onChange={(e) => setStartDate(e.target.value)} />
                                                        </div>
                                                    </div>
                                                    <div className="w-full lg:w-48 space-y-2 text-left">
                                                        <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Sampai Tanggal</label>
                                                        <div className="relative">
                                                            <Calendar className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                                            <input type="date"
                                                                className="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                                                value={endDate} onChange={(e) => setEndDate(e.target.value)} />
                                                        </div>
                                                    </div>
                                                </>
                                            )}
                                            {extraFilterFields}
                                        </>
                                    )}
                                </div>
                                <div className="flex gap-2 w-full lg:w-auto">
                                    <button type="submit" className="flex items-center justify-center gap-2 flex-1 lg:flex-none px-6 py-3 bg-green-600 text-white rounded-2xl text-[10px] font-black hover:bg-green-700 active:scale-95 transition-all uppercase tracking-widest shadow-md shadow-green-200">
                                        <Filter className="w-3.5 h-3.5" /> TERAPKAN
                                    </button>
                                    {(search || startDate || endDate || (isInventarisFilter && tahun !== String(currentYear)) || (hasTahunFilter && tahun !== String(currentYear))) && (
                                        <button type="button" onClick={handleReset} className="flex-1 lg:flex-none flex items-center justify-center gap-2 px-6 py-3 bg-gray-50 text-gray-500 rounded-2xl text-[10px] font-black hover:bg-gray-100 hover:text-gray-700 transition-all border border-gray-200 uppercase tracking-widest">
                                            <RefreshCw className="w-3.5 h-3.5" /> RESET
                                        </button>
                                    )}
                                </div>
                            </form>
                        )}
                    </div>
                ) : null}

                {/* Table Rendering */}
                {isCustomTable ? (
                    children
                ) : isLoading ? (
                    <SkeletonTable rows={10} columns={8} />
                ) : (
                    <TableCard
                        icon={FileBadge}
                        title="Pratinjau Data"
                        total={data?.total || 0}
                        totalLabel="Data"
                    >
                        {data?.data?.length === 0 ? (
                            <EmptyState
                                title="Data Kosong"
                                message={`Tidak ada data ${judul} yang ditemukan.`}
                                icon={Filter}
                            />
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="w-full text-xs min-w-[1000px]">
                                    <thead>{tableHead}</thead>
                                    <tbody>
                                        {data?.data?.map((item, index) => 
                                            renderRow(item, index, (data.from || 0) + index)
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        )}
                        {/* Pagination */}
                        {data?.links && data.links.length > 3 && (
                            <div className="p-4 border-t border-gray-100 flex justify-center">
                                <Pagination 
                                    links={data.links} 
                                    from={data.from} 
                                    to={data.to} 
                                    total={data.total} 
                                />
                            </div>
                        )}
                    </TableCard>
                )}
            </div>

            {/* Success Animation Overlay */}
            {showSuccess && (
                <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/20 backdrop-blur-sm animate-in fade-in duration-300">
                    <div className="bg-white p-8 rounded-3xl shadow-2xl flex flex-col items-center animate-in zoom-in duration-300">
                        <div className="w-48 h-48">
                            <LottieComponent animationData={successAnimation} loop={false} />
                        </div>
                        <h3 className="text-2xl font-black text-gray-900 mt-4 uppercase italic tracking-tighter">Export Berhasil!</h3>
                        <p className="text-sm text-gray-500 font-bold uppercase tracking-widest mt-1">Data Anda sudah siap.</p>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
