import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    Settings, Plus, Edit2, Trash2, ArrowLeft, 
    Save, X, CheckCircle, XCircle, GripVertical,
    Users, Phone, Info
} from 'lucide-react';
import Swal from 'sweetalert2';

export default function Index({ auth, jabatans }) {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editId, setEditId] = useState(null);

    const { data, setData, post, put, delete: destroy, processing, errors, reset, clearErrors } = useForm({
        nama: '',
        is_struktur: true,
        is_kontak: true,
        urutan: 0,
    });

    const openCreateModal = () => {
        reset();
        clearErrors();
        setEditId(null);
        setIsModalOpen(true);
    };

    const openEditModal = (jabatan) => {
        setData({
            nama: jabatan.nama,
            is_struktur: !!jabatan.is_struktur,
            is_kontak: !!jabatan.is_kontak,
            urutan: jabatan.urutan,
        });
        setEditId(jabatan.id);
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        reset();
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editId) {
            put(route('master-jabatan.update', editId), {
                onSuccess: () => {
                    closeModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'BERHASIL!',
                        text: 'Data jabatan telah diperbarui.',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-3xl' }
                    });
                }
            });
        } else {
            post(route('master-jabatan.store'), {
                onSuccess: () => {
                    closeModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'BERHASIL!',
                        text: 'Jabatan baru telah ditambahkan.',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-3xl' }
                    });
                }
            });
        }
    };

    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'HAPUS JABATAN?',
            html: `Apakah Anda yakin ingin menghapus <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Data yang sudah terhubung mungkin akan terdampak</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATAL',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                destroy(route('master-jabatan.destroy', id), {
                    onSuccess: () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'TERHAPUS!',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-3xl' }
                        });
                    }
                });
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Master Jabatan">
            <Head title="Master Jabatan - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4 text-left">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Settings className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Master Jabatan</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Pengaturan Jabatan & Kategori Kontak</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3">
                            <button 
                                onClick={openCreateModal}
                                className="flex items-center px-6 py-3 bg-white text-gray-900 hover:bg-gray-100 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest"
                            >
                                <Plus className="w-3.5 h-3.5 mr-2" />
                                TAMBAH JABATAN
                            </button>
                            <Link 
                                href={route('struktur-desa.index')}
                                className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all hover:scale-105 uppercase tracking-widest backdrop-blur-md border border-white/10"
                            >
                                <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                                KEMBALI
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Table */}
                <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-left">
                    <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                        <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter">
                            <Info className="w-6 h-6 text-gray-600" />
                            Daftar Jabatan & Kategori
                        </h3>
                        <span className="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-[10px] font-black uppercase tracking-widest italic">
                            Total: {jabatans.length}
                        </span>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm text-gray-600">
                            <thead>
                                <tr className="bg-gray-50/50 border-b border-gray-100">
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Urutan</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Jabatan</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Struktur Desa</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Kontak Desa</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {jabatans.map((jabatan, index) => (
                                    <tr key={jabatan.id} className="hover:bg-gray-50/50 transition-colors group">
                                        <td className="px-6 py-4">
                                            <span className="text-xs font-black text-gray-400 tabular-nums">#{jabatan.urutan}</span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="font-black text-gray-900 uppercase italic tracking-tight">{jabatan.nama}</div>
                                            <div className="text-[10px] text-gray-400 font-bold">Slug: {jabatan.slug}</div>
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            {jabatan.is_struktur ? (
                                                <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-100 text-[9px] font-black uppercase tracking-widest italic">
                                                    <Users className="w-2.5 h-2.5" /> AKTIF
                                                </span>
                                            ) : (
                                                <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-50 text-gray-400 border border-gray-100 text-[9px] font-black uppercase tracking-widest italic">
                                                    <XCircle className="w-2.5 h-2.5" /> TIDAK
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            {jabatan.is_kontak ? (
                                                <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100 text-[9px] font-black uppercase tracking-widest italic">
                                                    <Phone className="w-2.5 h-2.5" /> AKTIF
                                                </span>
                                            ) : (
                                                <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-50 text-gray-400 border border-gray-100 text-[9px] font-black uppercase tracking-widest italic">
                                                    <XCircle className="w-2.5 h-2.5" /> TIDAK
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <div className="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button 
                                                    onClick={() => openEditModal(jabatan)}
                                                    className="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-all active:scale-95"
                                                >
                                                    <Edit2 className="w-4 h-4" />
                                                </button>
                                                <button 
                                                    onClick={() => handleDelete(jabatan.id, jabatan.nama)}
                                                    className="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-all active:scale-95"
                                                >
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {/* Modal */}
            {isModalOpen && (
                <div className="fixed inset-0 z-[100] overflow-y-auto">
                    <div className="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div className="fixed inset-0 transition-opacity" aria-hidden="true" onClick={closeModal}>
                            <div className="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                        </div>

                        <span className="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <div className="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-in zoom-in-95 duration-300">
                            <form onSubmit={handleSubmit}>
                                <div className="bg-white px-8 pt-8 pb-6">
                                    <div className="flex items-center justify-between mb-8">
                                        <div className="flex items-center gap-4">
                                            <div className="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center">
                                                <Plus className="w-6 h-6 text-gray-900" />
                                            </div>
                                            <div>
                                                <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter">
                                                    {editId ? 'Perbarui Jabatan' : 'Tambah Jabatan'}
                                                </h3>
                                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Manajemen Master Data</p>
                                            </div>
                                        </div>
                                        <button type="button" onClick={closeModal} className="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                                            <X className="w-5 h-5 text-gray-400" />
                                        </button>
                                    </div>

                                    <div className="space-y-6">
                                        <div className="space-y-2 text-left">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Jabatan / Kategori</label>
                                            <input
                                                type="text"
                                                value={data.nama}
                                                onChange={e => setData('nama', e.target.value)}
                                                className={`w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 ${errors.nama ? 'focus:ring-red-500/10' : 'focus:ring-gray-900/5'} transition-all`}
                                                placeholder="Contoh: Kepala Desa"
                                                required
                                            />
                                            {errors.nama && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.nama}</p>}
                                        </div>

                                        <div className="space-y-2 text-left">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Urutan Tampil</label>
                                            <input
                                                type="number"
                                                value={data.urutan}
                                                onChange={e => setData('urutan', e.target.value)}
                                                className="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-gray-900/5 transition-all"
                                            />
                                        </div>

                                        <div className="grid grid-cols-2 gap-4">
                                            <button
                                                type="button"
                                                onClick={() => setData('is_struktur', !data.is_struktur)}
                                                className={`p-4 rounded-2xl border-2 transition-all text-left ${data.is_struktur ? 'border-green-500 bg-green-50/50' : 'border-gray-100 bg-white opacity-50'}`}
                                            >
                                                <div className="flex items-center justify-between mb-2">
                                                    <Users className={`w-5 h-5 ${data.is_struktur ? 'text-green-600' : 'text-gray-400'}`} />
                                                    {data.is_struktur && <CheckCircle className="w-4 h-4 text-green-600" />}
                                                </div>
                                                <p className={`text-[10px] font-black uppercase tracking-widest ${data.is_struktur ? 'text-green-700' : 'text-gray-400'}`}>Struktur Desa</p>
                                            </button>

                                            <button
                                                type="button"
                                                onClick={() => setData('is_kontak', !data.is_kontak)}
                                                className={`p-4 rounded-2xl border-2 transition-all text-left ${data.is_kontak ? 'border-blue-500 bg-blue-50/50' : 'border-gray-100 bg-white opacity-50'}`}
                                            >
                                                <div className="flex items-center justify-between mb-2">
                                                    <Phone className={`w-5 h-5 ${data.is_kontak ? 'text-blue-600' : 'text-gray-400'}`} />
                                                    {data.is_kontak && <CheckCircle className="w-4 h-4 text-blue-600" />}
                                                </div>
                                                <p className={`text-[10px] font-black uppercase tracking-widest ${data.is_kontak ? 'text-blue-700' : 'text-gray-400'}`}>Kontak Desa</p>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div className="bg-gray-50 px-8 py-6 rounded-b-[2.5rem]">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full flex items-center justify-center gap-3 px-6 py-5 bg-gray-900 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-xl shadow-gray-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50"
                                    >
                                        {processing ? (
                                            <>
                                                <div className="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                                                MEMPROSES...
                                            </>
                                        ) : (
                                            <>
                                                <Save className="w-4 h-4" />
                                                {editId ? 'PERBARUI DATA' : 'SIMPAN DATA'}
                                            </>
                                        )}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
