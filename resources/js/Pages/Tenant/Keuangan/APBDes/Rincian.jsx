import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard } from '@/Components/Shared';
import { ArrowLeft, Save, Trash2, Edit2, Plus, List } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Rincian({ auth, apbdes }) {
    const { data, setData, post, put, processing, errors, reset, clearErrors } = useForm({
        apbdes_id: apbdes.id,
        uraian: '',
        volume: '',
        satuan: '',
        harga_satuan: '',
        keterangan: '',
    });

    const [isEditing, setIsEditing] = useState(false);
    const [editId, setEditId] = useState(null);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEditing) {
            put(route('anggaran.update-rincian', editId), {
                preserveScroll: true,
                onSuccess: () => {
                    reset();
                    setIsEditing(false);
                    setEditId(null);
                }
            });
        } else {
            post(route('anggaran.store-rincian'), {
                preserveScroll: true,
                onSuccess: () => reset('uraian', 'volume', 'satuan', 'harga_satuan', 'keterangan')
            });
        }
    };

    const handleEdit = (item) => {
        setIsEditing(true);
        setEditId(item.id);
        setData({
            apbdes_id: apbdes.id,
            uraian: item.uraian,
            volume: item.volume,
            satuan: item.satuan,
            harga_satuan: item.harga_satuan,
            keterangan: item.keterangan || '',
        });
        clearErrors();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const handleCancelEdit = () => {
        setIsEditing(false);
        setEditId(null);
        reset();
        clearErrors();
    };

    const handleDelete = (id, uraian) => {
        Swal.fire({
            title: 'Hapus Rincian?',
            text: `Rincian "${uraian}" akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px]',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('anggaran.delete-rincian', id), { preserveScroll: true });
            }
        });
    };

    const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    
    // Calculate total rincian
    const totalRincian = apbdes.rincians.reduce((acc, curr) => acc + Number(curr.jumlah), 0);

    return (
        <AuthenticatedLayout user={auth.user} title="Rincian RAB">
            <Head title={`Rincian RAB - ${apbdes.nama_rekening}`} />

            <div className="space-y-6 pb-20">
                <PageHeader
                    title="Rincian Anggaran (RAB)"
                    subtitle={`Rekening: ${apbdes.kode_rekening} - ${apbdes.nama_rekening}`}
                    icon={List}
                    actions={[
                        {
                            label: 'KEMBALI',
                            icon: ArrowLeft,
                            href: route('transparansi-desa.apbdes', { tahun: apbdes.tahun }),
                            variant: 'white'
                        }
                    ]}
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Form Section */}
                    <div className="lg:col-span-1">
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                            <h3 className="text-sm font-black text-gray-900 uppercase tracking-widest mb-4">
                                {isEditing ? 'Edit Rincian' : 'Tambah Rincian Baru'}
                            </h3>
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div>
                                    <label className="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Uraian / Nama Barang</label>
                                    <input 
                                        type="text" 
                                        value={data.uraian} 
                                        onChange={e => setData('uraian', e.target.value)}
                                        className="w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500" 
                                        placeholder="Contoh: Semen Portland"
                                    />
                                    {errors.uraian && <p className="text-[10px] text-red-500 mt-1">{errors.uraian}</p>}
                                </div>
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Volume</label>
                                        <input 
                                            type="number" step="0.01"
                                            value={data.volume} 
                                            onChange={e => setData('volume', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500" 
                                            placeholder="Contoh: 10"
                                        />
                                        {errors.volume && <p className="text-[10px] text-red-500 mt-1">{errors.volume}</p>}
                                    </div>
                                    <div>
                                        <label className="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Satuan</label>
                                        <input 
                                            type="text" 
                                            value={data.satuan} 
                                            onChange={e => setData('satuan', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500" 
                                            placeholder="Contoh: Sak"
                                        />
                                        {errors.satuan && <p className="text-[10px] text-red-500 mt-1">{errors.satuan}</p>}
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Harga Satuan (Rp)</label>
                                    <input 
                                        type="number" 
                                        value={data.harga_satuan} 
                                        onChange={e => setData('harga_satuan', e.target.value)}
                                        className="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-mono focus:ring-green-500 focus:border-green-500" 
                                        placeholder="Contoh: 50000"
                                    />
                                    {errors.harga_satuan && <p className="text-[10px] text-red-500 mt-1">{errors.harga_satuan}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Keterangan</label>
                                    <textarea 
                                        value={data.keterangan} 
                                        onChange={e => setData('keterangan', e.target.value)}
                                        className="w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500" 
                                        rows="2"
                                    ></textarea>
                                </div>

                                {/* Total Preview */}
                                {(data.volume && data.harga_satuan) ? (
                                    <div className="bg-green-50 p-4 rounded-xl border border-green-100">
                                        <p className="text-[10px] font-black text-green-600 uppercase tracking-widest mb-1">Jumlah</p>
                                        <p className="text-lg font-black text-green-700">{formatRupiah(data.volume * data.harga_satuan)}</p>
                                    </div>
                                ) : null}

                                <div className="flex gap-2 pt-2">
                                    <button 
                                        type="submit" 
                                        disabled={processing}
                                        className="flex-1 bg-green-600 text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-green-700 transition-colors flex justify-center items-center gap-2"
                                    >
                                        {isEditing ? <Save className="w-4 h-4" /> : <Plus className="w-4 h-4" />}
                                        {isEditing ? 'SIMPAN PERUBAHAN' : 'TAMBAH RINCIAN'}
                                    </button>
                                    {isEditing && (
                                        <button 
                                            type="button" 
                                            onClick={handleCancelEdit}
                                            className="px-4 bg-gray-100 text-gray-600 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-200 transition-colors"
                                        >
                                            BATAL
                                        </button>
                                    )}
                                </div>
                            </form>
                        </div>
                    </div>

                    {/* Table Section */}
                    <div className="lg:col-span-2 space-y-4">
                        <div className="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
                            <div>
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Anggaran Pagu</p>
                                <p className="text-lg font-black text-gray-900">{formatRupiah(apbdes.anggaran)}</p>
                            </div>
                            <div className="text-right">
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Rincian (RAB)</p>
                                <p className={`text-lg font-black ${totalRincian > apbdes.anggaran ? 'text-red-600' : 'text-blue-600'}`}>
                                    {formatRupiah(totalRincian)}
                                </p>
                            </div>
                        </div>

                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="bg-gray-50 border-b border-gray-100">
                                            <th className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">NO</th>
                                            <th className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">URAIAN</th>
                                            <th className="px-4 py-3 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">VOLUME</th>
                                            <th className="px-4 py-3 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">HARGA SATUAN</th>
                                            <th className="px-4 py-3 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">JUMLAH</th>
                                            <th className="px-4 py-3 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100">
                                        {apbdes.rincians?.length > 0 ? (
                                            apbdes.rincians.map((rincian, index) => (
                                                <tr key={rincian.id} className="hover:bg-gray-50/50">
                                                    <td className="px-4 py-3 text-gray-500 font-mono text-xs">{index + 1}</td>
                                                    <td className="px-4 py-3">
                                                        <p className="font-bold text-gray-900">{rincian.uraian}</p>
                                                        {rincian.keterangan && <p className="text-[10px] text-gray-400">{rincian.keterangan}</p>}
                                                    </td>
                                                    <td className="px-4 py-3 text-center text-gray-600">{rincian.volume} {rincian.satuan}</td>
                                                    <td className="px-4 py-3 text-right font-mono text-xs">{formatRupiah(rincian.harga_satuan)}</td>
                                                    <td className="px-4 py-3 text-right font-mono text-xs font-bold text-green-700">{formatRupiah(rincian.jumlah)}</td>
                                                    <td className="px-4 py-3 text-center">
                                                        <div className="flex justify-center gap-1">
                                                            <button onClick={() => handleEdit(rincian)} className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                                                <Edit2 className="w-3.5 h-3.5" />
                                                            </button>
                                                            <button onClick={() => handleDelete(rincian.id, rincian.uraian)} className="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                                                <Trash2 className="w-3.5 h-3.5" />
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan="6" className="px-4 py-8 text-center text-gray-400 text-xs italic">
                                                    Belum ada rincian RAB untuk rekening ini
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
