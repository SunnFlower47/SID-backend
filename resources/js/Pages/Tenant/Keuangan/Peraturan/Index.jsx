import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { FileText, Plus, Upload, CheckCircle2, XCircle, Clock, AlertTriangle, ChevronRight, Download } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';
import { PageHeader } from '@/Components/Shared';

const STATUS_CONFIG = {
    draft: { label: 'Draft', color: 'text-gray-600', bg: 'bg-gray-100', icon: Clock },
    diajukan_bpd: { label: 'Diajukan ke BPD', color: 'text-blue-600', bg: 'bg-blue-50', icon: ChevronRight },
    dibahas: { label: 'Sedang Dibahas', color: 'text-yellow-600', bg: 'bg-yellow-50', icon: AlertTriangle },
    disetujui: { label: 'Disetujui / Sah', color: 'text-green-600', bg: 'bg-green-50', icon: CheckCircle2 },
    ditolak: { label: 'Ditolak / Revisi', color: 'text-red-600', bg: 'bg-red-50', icon: XCircle },
};

export default function Index({ auth, peraturans, filters }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showStatusModal, setShowStatusModal] = useState(null); // hold the item being updated
    const [showUploadModal, setShowUploadModal] = useState(null);

    // Form: Create Pengajuan
    const createForm = useForm({
        jenis_peraturan: 'APBDes',
        tahun_anggaran: new Date().getFullYear(),
        judul: `Peraturan Desa tentang APBDes Tahun ${new Date().getFullYear()}`,
    });

    const handleCreateSubmit = (e) => {
        e.preventDefault();
        createForm.post(route('peraturan-desa.store'), {
            onSuccess: () => setShowCreateModal(false),
        });
    };

    // Form: Update Status
    const statusForm = useForm({
        status: '',
        keterangan_bpd: '',
        nomor_peraturan: '',
        tanggal_ditetapkan: '',
    });

    const openStatusModal = (item) => {
        statusForm.setData({
            status: item.status,
            keterangan_bpd: item.keterangan_bpd || '',
            nomor_peraturan: item.nomor_peraturan || '',
            tanggal_ditetapkan: item.tanggal_ditetapkan || '',
        });
        setShowStatusModal(item);
    };

    const handleStatusSubmit = (e) => {
        e.preventDefault();
        statusForm.put(route('peraturan-desa.update-status', showStatusModal.id), {
            onSuccess: () => setShowStatusModal(null),
        });
    };

    // Form: Upload Dokumen
    const uploadForm = useForm({
        file_dokumen: null,
    });

    const handleUploadSubmit = (e) => {
        e.preventDefault();
        uploadForm.post(route('peraturan-desa.upload-dokumen', showUploadModal.id), {
            onSuccess: () => {
                setShowUploadModal(null);
                uploadForm.reset();
            },
        });
    };

    const handleDelete = (item) => {
        if (item.status === 'disetujui') {
            Swal.fire({ icon: 'error', title: 'Terkunci', text: 'Peraturan yang sudah disetujui tidak bisa dihapus.' });
            return;
        }

        Swal.fire({
            title: 'Hapus Pengajuan?',
            text: "Data yang dihapus tidak dapat dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('peraturan-desa.destroy', item.id));
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Persetujuan BPD">
            <Head title="Persetujuan BPD" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader
                    title="Persetujuan BPD"
                    subtitle="Manajemen Pengesahan Peraturan Desa"
                    icon={FileText}
                    gradient="from-teal-600 via-teal-700 to-teal-800"
                    titleSize="lg"
                    actions={[
                        {
                            label: 'DASHBOARD',
                            href: route('transparansi-desa.index'),
                            variant: 'ghost',
                        },
                        {
                            label: 'BUAT PENGAJUAN',
                            icon: Plus,
                            onClick: () => setShowCreateModal(true),
                            variant: 'white',
                        },
                    ]}
                />

                {/* Info Banner */}
                <div className="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex items-start gap-3">
                    <AlertTriangle className="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
                    <div>
                        <p className="text-xs font-black text-blue-800 uppercase tracking-tighter italic">Penting: Penguncian (Lock) APBDes</p>
                        <p className="text-[10px] font-bold text-blue-600 uppercase tracking-wider mt-0.5">
                            Jika sebuah Peraturan Desa tentang APBDes telah disetujui (Sah), maka data Anggaran APBDes pada tahun tersebut akan <b>Terkunci Otomatis</b>. Rekening APBDes tidak akan bisa ditambah, diedit, atau dihapus kecuali status Perdes dikembalikan ke Draft/Revisi.
                        </p>
                    </div>
                </div>

                {/* Data Grid */}
                <div className="grid grid-cols-1 gap-4">
                    {peraturans.data.map((item) => {
                        const cfg = STATUS_CONFIG[item.status];
                        const Icon = cfg.icon;

                        return (
                            <div key={item.id} className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 relative group hover:border-teal-200 transition-all">
                                <div className="flex flex-col md:flex-row md:items-start justify-between gap-6">
                                    {/* Info Kiri */}
                                    <div className="flex-1">
                                        <div className="flex items-center gap-3 mb-2">
                                            <span className={cn('px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest', cfg.bg, cfg.color, 'flex items-center gap-1.5')}>
                                                <Icon className="w-3 h-3" /> {cfg.label}
                                            </span>
                                            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest border border-gray-200 px-2 py-0.5 rounded-md">
                                                Tahun {item.tahun_anggaran}
                                            </span>
                                            <span className="text-[10px] font-black text-teal-600 uppercase tracking-widest bg-teal-50 px-2 py-0.5 rounded-md">
                                                {item.jenis_peraturan}
                                            </span>
                                        </div>
                                        <h3 className="text-lg font-black text-gray-900 uppercase italic tracking-tighter">{item.judul}</h3>
                                        
                                        {item.status === 'disetujui' && (
                                            <div className="mt-3 flex items-center gap-4 text-xs font-bold text-gray-500">
                                                <p><b>Nomor Perdes:</b> {item.nomor_peraturan}</p>
                                                <p><b>Tanggal Sah:</b> {item.tanggal_ditetapkan}</p>
                                            </div>
                                        )}

                                        {item.keterangan_bpd && (
                                            <div className="mt-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Catatan / Keterangan BPD:</p>
                                                <p className="text-xs text-gray-700 italic">"{item.keterangan_bpd}"</p>
                                            </div>
                                        )}

                                        {item.file_dokumen_url && (
                                            <a href={item.file_dokumen_url} target="_blank" rel="noreferrer" className="mt-4 inline-flex items-center px-4 py-2 bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                                <Download className="w-3.5 h-3.5 mr-2" /> UNDUH PDF PERDES FINAL
                                            </a>
                                        )}
                                    </div>

                                    {/* Aksi Kanan */}
                                    <div className="flex flex-col gap-2 min-w-[180px]">
                                        <button onClick={() => openStatusModal(item)} className="px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all w-full">
                                            UPDATE STATUS
                                        </button>
                                        
                                        {item.status === 'disetujui' && (
                                            <button onClick={() => setShowUploadModal(item)} className="px-4 py-2.5 bg-blue-50 text-blue-600 border border-blue-100 hover:bg-blue-100 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all w-full flex items-center justify-center">
                                                <Upload className="w-3 h-3 mr-1.5" /> UPLOAD PDF
                                            </button>
                                        )}

                                        {item.status !== 'disetujui' && (
                                            <button onClick={() => handleDelete(item)} className="px-4 py-2.5 text-red-500 hover:bg-red-50 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all w-full">
                                                HAPUS
                                            </button>
                                        )}
                                    </div>
                                </div>
                            </div>
                        );
                    })}

                    {peraturans.data.length === 0 && (
                        <div className="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                            <p className="text-xs font-black text-gray-400 uppercase tracking-widest">Belum ada pengajuan Peraturan Desa</p>
                        </div>
                    )}
                </div>
            </div>

            {/* MODAL: CREATE */}
            {showCreateModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl animate-in zoom-in-95 duration-200">
                        <div className="flex items-center justify-between mb-6">
                            <h2 className="text-lg font-black text-gray-900 uppercase italic tracking-tighter">Buat Pengajuan Baru</h2>
                            <button onClick={() => setShowCreateModal(false)} className="text-gray-400 hover:text-gray-900"><XCircle className="w-5 h-5" /></button>
                        </div>
                        <form onSubmit={handleCreateSubmit} className="space-y-4">
                            <div>
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Peraturan</label>
                                <select value={createForm.data.jenis_peraturan} onChange={e => createForm.setData('jenis_peraturan', e.target.value)} className="w-full mt-1 border-gray-200 rounded-xl text-sm font-bold focus:ring-teal-500">
                                    <option value="APBDes">APBDes</option>
                                    <option value="Perubahan APBDes">Perubahan APBDes</option>
                                    <option value="Lpj APBDes">Laporan Pertanggungjawaban APBDes</option>
                                </select>
                            </div>
                            <div>
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tahun Anggaran</label>
                                <input type="number" value={createForm.data.tahun_anggaran} onChange={e => createForm.setData('tahun_anggaran', e.target.value)} className="w-full mt-1 border-gray-200 rounded-xl text-sm font-bold focus:ring-teal-500" />
                            </div>
                            <div>
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Judul Dokumen</label>
                                <input type="text" value={createForm.data.judul} onChange={e => createForm.setData('judul', e.target.value)} className="w-full mt-1 border-gray-200 rounded-xl text-sm font-bold focus:ring-teal-500" />
                            </div>
                            <button type="submit" disabled={createForm.processing} className="w-full py-4 mt-6 bg-teal-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-teal-700 disabled:opacity-50">
                                {createForm.processing ? 'Menyimpan...' : 'Buat Pengajuan'}
                            </button>
                        </form>
                    </div>
                </div>
            )}

            {/* MODAL: UPDATE STATUS */}
            {showStatusModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white rounded-3xl w-full max-w-lg p-6 shadow-2xl animate-in zoom-in-95 duration-200">
                        <div className="flex items-center justify-between mb-6">
                            <h2 className="text-lg font-black text-gray-900 uppercase italic tracking-tighter">Update Status Pengesahan</h2>
                            <button onClick={() => setShowStatusModal(null)} className="text-gray-400 hover:text-gray-900"><XCircle className="w-5 h-5" /></button>
                        </div>
                        <form onSubmit={handleStatusSubmit} className="space-y-4">
                            <div>
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Status Saat Ini</label>
                                <select value={statusForm.data.status} onChange={e => statusForm.setData('status', e.target.value)} className="w-full mt-1 border-gray-200 rounded-xl text-sm font-bold focus:ring-teal-500">
                                    <option value="draft">Draft (Persiapan)</option>
                                    <option value="diajukan_bpd">Diajukan ke BPD</option>
                                    <option value="dibahas">Sedang Dibahas BPD</option>
                                    <option value="ditolak">Ditolak / Butuh Revisi</option>
                                    <option value="disetujui">Disetujui / Sah</option>
                                </select>
                            </div>

                            {statusForm.data.status === 'disetujui' && (
                                <div className="p-4 bg-green-50 border border-green-100 rounded-xl space-y-4 mb-4">
                                    <div className="flex items-center gap-2 mb-2">
                                        <AlertTriangle className="w-4 h-4 text-green-600" />
                                        <p className="text-[10px] font-black text-green-700 uppercase tracking-widest">Informasi Pengesahan</p>
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <label className="text-[10px] font-black text-green-700 uppercase tracking-widest ml-1">Nomor Perdes</label>
                                            <input type="text" value={statusForm.data.nomor_peraturan} onChange={e => statusForm.setData('nomor_peraturan', e.target.value)} required className="w-full mt-1 border-green-200 bg-white rounded-xl text-sm font-bold focus:ring-green-500" />
                                        </div>
                                        <div>
                                            <label className="text-[10px] font-black text-green-700 uppercase tracking-widest ml-1">Tanggal Ditetapkan</label>
                                            <input type="date" value={statusForm.data.tanggal_ditetapkan} onChange={e => statusForm.setData('tanggal_ditetapkan', e.target.value)} required className="w-full mt-1 border-green-200 bg-white rounded-xl text-sm font-bold focus:ring-green-500" />
                                        </div>
                                    </div>
                                </div>
                            )}

                            <div>
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Keterangan / Catatan BPD (Opsional)</label>
                                <textarea rows="3" value={statusForm.data.keterangan_bpd} onChange={e => statusForm.setData('keterangan_bpd', e.target.value)} className="w-full mt-1 border-gray-200 rounded-xl text-sm font-bold focus:ring-teal-500 resize-none"></textarea>
                            </div>

                            <button type="submit" disabled={statusForm.processing} className="w-full py-4 mt-6 bg-gray-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-black disabled:opacity-50">
                                {statusForm.processing ? 'Menyimpan...' : 'Simpan Status'}
                            </button>
                        </form>
                    </div>
                </div>
            )}

            {/* MODAL: UPLOAD */}
            {showUploadModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl animate-in zoom-in-95 duration-200">
                        <div className="flex items-center justify-between mb-6">
                            <h2 className="text-lg font-black text-gray-900 uppercase italic tracking-tighter">Upload Dokumen Final</h2>
                            <button onClick={() => setShowUploadModal(null)} className="text-gray-400 hover:text-gray-900"><XCircle className="w-5 h-5" /></button>
                        </div>
                        <form onSubmit={handleUploadSubmit} className="space-y-4">
                            <div className="p-4 border-2 border-dashed border-gray-200 rounded-2xl text-center hover:bg-gray-50 transition-colors relative cursor-pointer">
                                <input type="file" accept=".pdf" onChange={e => uploadForm.setData('file_dokumen', e.target.files[0])} className="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required />
                                <Upload className="w-8 h-8 text-gray-400 mx-auto mb-2" />
                                <p className="text-xs font-bold text-gray-600">Klik atau drop file PDF Perdes di sini</p>
                                {uploadForm.data.file_dokumen && (
                                    <p className="mt-2 text-[10px] font-black text-teal-600 uppercase tracking-widest bg-teal-50 inline-block px-3 py-1 rounded-full">
                                        File Terpilih: {uploadForm.data.file_dokumen.name}
                                    </p>
                                )}
                            </div>
                            <button type="submit" disabled={uploadForm.processing || !uploadForm.data.file_dokumen} className="w-full py-4 mt-6 bg-teal-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-teal-700 disabled:opacity-50">
                                {uploadForm.processing ? 'Mengunggah...' : 'Upload Dokumen'}
                            </button>
                        </form>
                    </div>
                </div>
            )}

        </AuthenticatedLayout>
    );
}
