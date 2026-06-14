import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    FileSignature, Plus, Search, 
    CheckCircle2, XCircle, Clock, Eye, Edit,
    Download, User, Calendar,
    FileText, Settings2,
    X, Check, AlertCircle
} from 'lucide-react';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

// Shared Components
import { PageHeader, TableCard, Badge, EmptyState, DataTable } from '@/Components/Shared';

dayjs.locale('id');

export default function Index({ auth, pengajuans, statusList, suratTypes, filters }) {
    const [searchQuery, setSearchQuery] = useState(filters.search || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || '');
    const [typeFilter, setTypeFilter] = useState(filters.jenis_surat || '');

    // State for Status Modal
    const [showStatusModal, setShowStatusModal] = useState(false);
    const [selectedPengajuan, setSelectedPengajuan] = useState(null);
    const [statusUpdateData, setStatusUpdateData] = useState({
        status: '',
        keterangan_tambahan: '',
        file_balasan_admin: null
    });

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('admin.surat-pengajuan.index'), {
            search: searchQuery,
            status: statusFilter,
            jenis_surat: typeFilter
        }, { preserveState: true });
    };

    const openStatusModal = (pengajuan) => {
        setSelectedPengajuan(pengajuan);
        setStatusUpdateData({
            status: pengajuan.status,
            keterangan_tambahan: pengajuan.keterangan_tambahan || '',
            file_balasan_admin: null
        });
        setShowStatusModal(true);
    };

    const handleUpdateStatusSubmit = (e) => {
        e.preventDefault();
        router.post(route('admin.surat-pengajuan.update-status', selectedPengajuan.id), {
            ...statusUpdateData,
            _method: 'patch'
        }, {
            forceFormData: true,
            onSuccess: () => {
                setShowStatusModal(false);
                Swal.fire({
                    title: 'BERHASIL!',
                    text: 'Status pengajuan telah diperbarui.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-3xl border-none shadow-2xl',
                        title: 'font-black tracking-tighter uppercase italic text-green-600',
                    }
                });
            }
        });
    };

    const getStatusStyle = (status) => {
        switch (status) {
            case 'selesai':
                return { color: 'green', icon: CheckCircle2 };
            case 'diproses':
                return { color: 'blue', icon: Clock };
            case 'ditolak':
                return { color: 'red', icon: XCircle };
            default:
                return { color: 'yellow', icon: Clock };
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Layanan Surat">
            <Head title="Layanan Surat" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader 
                    title="Layanan Surat"
                    subtitle="Administrasi & Pengajuan Surat Desa"
                    icon={FileSignature}
                    actions={[
                        {
                            label: 'BUAT SURAT',
                            icon: Plus,
                            href: route('admin.surat-pengajuan.create'),
                            variant: 'white'
                        }
                    ]}
                />

                {/* Filters */}
                <form onSubmit={handleSearch} className="bg-white p-4 rounded-3xl border border-gray-100 shadow-sm flex flex-col lg:flex-row gap-4 items-end">
                    <div className="flex-1 w-full space-y-2 text-left">
                        <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Pencarian</label>
                        <div className="relative">
                            <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input 
                                type="text"
                                value={searchQuery}
                                onChange={e => setSearchQuery(e.target.value)}
                                className="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                placeholder="Cari nomor surat atau nama penduduk..."
                            />
                        </div>
                    </div>
                    <div className="w-full lg:w-48 space-y-2 text-left">
                        <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Status</label>
                        <select 
                            value={statusFilter}
                            onChange={e => setStatusFilter(e.target.value)}
                            className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all"
                        >
                            <option value="">Semua Status</option>
                            {Object.entries(statusList).map(([val, label]) => (
                                <option key={val} value={val}>{label}</option>
                            ))}
                        </select>
                    </div>
                    <div className="w-full lg:w-48 space-y-2 text-left">
                        <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Surat</label>
                        <select 
                            value={typeFilter}
                            onChange={e => setTypeFilter(e.target.value)}
                            className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all"
                        >
                            <option value="">Semua Jenis</option>
                            {suratTypes.map(type => (
                                <option key={type.id} value={type.id}>{type.nama}</option>
                            ))}
                        </select>
                    </div>
                    <button type="submit" className="flex items-center justify-center gap-2 w-full lg:w-auto px-8 py-3 bg-green-600 text-white rounded-2xl text-[10px] font-black hover:bg-green-700 active:scale-95 transition-all uppercase tracking-widest shadow-md shadow-green-200">
                        <Search className="w-3.5 h-3.5" /> FILTER
                    </button>
                </form>

                {/* Table View */}
                <TableCard 
                    title="Daftar Pengajuan Surat"
                    icon={FileSignature}
                    total={pengajuans?.total || 0}
                    pagination={pengajuans}
                    noPadding
                >
                    <DataTable 
                        columns={[
                            {
                                header: 'Nomor & Tanggal',
                                accessor: 'nomor_surat',
                                render: (p) => (
                                    <div className="flex flex-col">
                                        <span className="text-sm font-black text-slate-800 tracking-tighter uppercase italic">{p.nomor_surat || p.nomor_pengajuan || 'DRAFT'}</span>
                                        <div className="flex items-center text-[10px] font-bold text-gray-500 uppercase mt-1">
                                            <Calendar className="w-3 h-3 mr-1" />
                                            {dayjs(p.tanggal_surat).format('DD MMMM YYYY')}
                                        </div>
                                    </div>
                                )
                            },
                            {
                                header: 'Penduduk',
                                accessor: 'penduduk',
                                render: (p) => p.penduduk ? (
                                    <div className="flex items-center gap-3">
                                        <div className="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-700">
                                            <User className="w-5 h-5" />
                                        </div>
                                        <div className="flex flex-col text-left">
                                            <span className="text-sm font-black text-slate-800 uppercase italic tracking-tight">{p.penduduk.nama}</span>
                                            <span className="text-[10px] font-bold text-gray-500 tracking-widest uppercase">{p.penduduk.nik}</span>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="flex items-center gap-3 text-left">
                                        <div className="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-700 italic font-black text-xs">
                                            PM
                                        </div>
                                        <div className="flex flex-col text-left">
                                            <span className="text-sm font-black text-slate-800 uppercase italic tracking-tight">{p.data_tambahan?.nama || 'INPUT MANUAL'}</span>
                                            <span className="text-[10px] font-bold text-gray-500 tracking-widest uppercase">{p.data_tambahan?.nik || 'NON-PENDUDUK'}</span>
                                        </div>
                                    </div>
                                )
                            },
                            {
                                header: 'Jenis Surat',
                                accessor: 'jenis_surat',
                                render: (p) => (
                                    <div className="flex items-center gap-2">
                                        <div className="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                                            <FileText className="w-4 h-4" />
                                        </div>
                                        <span className="text-[10px] font-black text-slate-600 uppercase tracking-widest">
                                            {suratTypes.find(t => t.id === p.jenis_surat)?.nama || p.jenis_surat}
                                        </span>
                                    </div>
                                )
                            },
                            {
                                header: 'Status',
                                accessor: 'status',
                                render: (p) => {
                                    const style = getStatusStyle(p.status);
                                    return (
                                        <Badge color={style.color} icon={style.icon}>
                                            {statusList[p.status] || p.status}
                                        </Badge>
                                    );
                                }
                            },
                            {
                                header: 'Aksi',
                                accessor: 'aksi',
                                headerClassName: 'text-right',
                                className: 'text-right',
                                render: (p) => (
                                    <div className="flex justify-end gap-2">
                                        <button 
                                            onClick={() => openStatusModal(p)}
                                            className="w-8 h-8 flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-600 hover:text-white transition-all shadow-sm"
                                            title="Update Status"
                                        >
                                            <Settings2 className="w-4 h-4" />
                                        </button>
                                        
                                        <Link 
                                            href={route('admin.surat-pengajuan.show', p.id)}
                                            className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                                            title="Lihat Detail"
                                        >
                                            <Eye className="w-4 h-4" />
                                        </Link>

                                        <Link 
                                            href={route('admin.surat-pengajuan.edit', p.id)}
                                            className="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-800 hover:text-white transition-all shadow-sm"
                                            title="Edit Data"
                                        >
                                            <Edit className="w-4 h-4" />
                                        </Link>

                                        <a 
                                            href={route('admin.surat-pengajuan.pdf', p.id)}
                                            target="_blank"
                                            className="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-all shadow-sm"
                                            title="Cetak PDF"
                                        >
                                            <Download className="w-4 h-4" />
                                        </a>
                                    </div>
                                )
                            }
                        ]}
                        data={pengajuans.data}
                        emptyState={
                            <EmptyState 
                                title="Tidak Ada Pengajuan"
                                message="Gunakan tombol Buat Surat untuk memulai"
                                action={{
                                    label: "BUAT SURAT",
                                    href: route('admin.surat-pengajuan.create')
                                }}
                            />
                        }
                    />
                </TableCard>
            </div>

            {/* STATUS UPDATE MODAL (REACT PORT OF BLADE MODAL) */}
            {showStatusModal && (
                <div className="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div className="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        {/* Background overlay */}
                        <div 
                            className="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity animate-in fade-in duration-300" 
                            aria-hidden="true"
                            onClick={() => setShowStatusModal(false)}
                        ></div>

                        {/* Modal panel */}
                        <div className="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-in zoom-in duration-300">
                            <div className="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-4 relative">
                                <button 
                                    onClick={() => setShowStatusModal(false)}
                                    className="absolute top-6 right-6 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all"
                                >
                                    <X className="w-5 h-5" />
                                </button>

                                <div className="sm:flex sm:items-start">
                                    <div className="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-yellow-100 text-yellow-600 sm:mx-0 sm:h-12 sm:w-12">
                                        <Settings2 className="h-6 w-6" />
                                    </div>
                                    <div className="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 className="text-2xl font-black text-gray-900 uppercase italic tracking-tighter" id="modal-title">
                                            Update Status Surat
                                        </h3>
                                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                            {selectedPengajuan?.nomor_surat || 'DRAFT'} - {selectedPengajuan?.penduduk?.nama || selectedPengajuan?.data_tambahan?.nama}
                                        </p>
                                    </div>
                                </div>

                                <form onSubmit={handleUpdateStatusSubmit} className="mt-8 space-y-6">
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center">
                                            <AlertCircle className="w-3 h-3 mr-1" />
                                            Pilih Status Baru
                                        </label>
                                        <div className="grid grid-cols-2 gap-3 mt-2">
                                            {Object.entries(statusList).map(([key, label]) => (
                                                <button
                                                    key={key}
                                                    type="button"
                                                    onClick={() => setStatusUpdateData({...statusUpdateData, status: key})}
                                                    className={cn(
                                                        "px-4 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-between group",
                                                        statusUpdateData.status === key 
                                                            ? "bg-gray-900 text-white ring-4 ring-gray-100 shadow-xl" 
                                                            : "bg-gray-50 text-gray-400 hover:bg-gray-100"
                                                    )}
                                                >
                                                    {label}
                                                    {statusUpdateData.status === key && <Check className="w-3 h-3 text-green-400 animate-in zoom-in" />}
                                                </button>
                                            ))}
                                        </div>
                                    </div>

                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center">
                                            <FileText className="w-3 h-3 mr-1" />
                                            Keterangan / Alasan
                                        </label>
                                        <textarea
                                            value={statusUpdateData.keterangan_tambahan}
                                            onChange={(e) => setStatusUpdateData({...statusUpdateData, keterangan_tambahan: e.target.value})}
                                            className="w-full px-5 py-4 bg-gray-50 border-none rounded-[1.5rem] text-sm font-bold focus:ring-4 focus:ring-green-100 transition-all min-h-[120px] shadow-inner"
                                            placeholder="Tambahkan catatan untuk admin atau alasan penolakan untuk warga..."
                                        ></textarea>
                                    </div>

                                    {statusUpdateData.status === 'selesai' && (
                                        <div className="space-y-2 animate-in fade-in duration-300">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center">
                                                <FileText className="w-3 h-3 mr-1" />
                                                Lampiran File Surat Balasan (Opsional)
                                            </label>
                                            <input
                                                type="file"
                                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                onChange={(e) => setStatusUpdateData({...statusUpdateData, file_balasan_admin: e.target.files[0]})}
                                                className="w-full px-5 py-3 bg-gray-50 border-none rounded-[1.5rem] text-sm font-bold focus:ring-4 focus:ring-green-100 transition-all shadow-inner file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-green-100 file:text-green-700 hover:file:bg-green-200"
                                            />
                                            {selectedPengajuan?.email_pengaju ? (
                                                <p className="text-[9px] text-gray-400 font-bold ml-2 mt-1">File ini akan otomatis dilampirkan ke email balasan untuk warga.</p>
                                            ) : (
                                                <p className="text-[9px] text-amber-500 font-bold ml-2 mt-1">Warga tidak memberikan email. File ini dapat diunduh warga melalui halaman Lacak Status.</p>
                                            )}
                                        </div>
                                    )}

                                    <div className="pt-4 pb-4 flex flex-col sm:flex-row-reverse gap-3">
                                        <button
                                            type="submit"
                                            className="w-full sm:flex-1 inline-flex justify-center items-center px-6 py-4 bg-green-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-100 transition-all shadow-lg shadow-green-100"
                                        >
                                            <CheckCircle2 className="w-4 h-4 mr-2" />
                                            Update Status
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setShowStatusModal(false)}
                                            className="w-full sm:w-auto inline-flex justify-center items-center px-6 py-4 bg-gray-100 text-gray-500 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-gray-200 focus:outline-none transition-all"
                                        >
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
