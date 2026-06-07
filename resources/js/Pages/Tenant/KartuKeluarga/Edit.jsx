import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Home, Save, MapPin, Crown, Info } from 'lucide-react';
import Swal from 'sweetalert2';
import { PageHeader, FormCard } from '@/Components/Shared';

export default function Edit({ auth, kk, nkk, masterRwOptions }) {
    const { data, setData, put, processing, errors } = useForm({
        nama_kepala_keluarga: kk.nama_kepala_keluarga || '',
        alamat: kk.alamat || '',
        rt_id: kk.rt_id || '',
        rw_id: kk.rw_id || '',
        tempat_dikeluarkan: kk.tempat_dikeluarkan || '',
        tanggal_dikeluarkan: kk.tanggal_dikeluarkan || '',
    });

    const [availableRts, setAvailableRts] = useState([]);

    useEffect(() => {
        if (data.rw_id) {
            const selectedRw = masterRwOptions.find(rw => String(rw.id) === String(data.rw_id));
            setAvailableRts(selectedRw ? selectedRw.rts : []);
        } else {
            setAvailableRts([]);
        }
    }, [data.rw_id, masterRwOptions]);

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('kk.update', nkk), {
            onSuccess: () => {
                // Flash success is handled globally in AuthenticatedLayout
            },
            onError: (errs) => {
                if (Object.keys(errs).length > 0) {
                    Swal.fire({
                        title: 'Terjadi Kesalahan!',
                        text: 'Silakan periksa kembali inputan Anda.',
                        icon: 'error'
                    });
                }
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title={`Edit KK: ${kk.nkk}`}>
            <Head title="Edit Kartu Keluarga" />

            <div className="max-w-7xl mx-auto space-y-6 animate-in fade-in duration-500 pb-12">
                {/* Header */}
                <PageHeader
                    title="Edit Kartu Keluarga"
                    subtitle={`NKK: ${kk.nkk}`}
                    icon={Home}
                    backHref={route('kk.index')}
                    titleSize="lg"
                />
                <div className="w-full">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Main Form Card */}
                        <FormCard title="Informasi Utama KK" icon={Crown}>
                            <div className="space-y-8">
                                {/* Alert Info */}
                            <div className="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex gap-4">
                                <Info className="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
                                <div className="text-xs text-blue-800 leading-relaxed">
                                    <p className="font-black uppercase tracking-widest mb-1 italic">Catatan Penting:</p>
                                    <p>Mengubah Nama Kepala Keluarga di sini juga akan otomatis memperbarui data nama di profil Penduduk yang bersangkutan.</p>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {/* Nama Kepala Keluarga */}
                                <div className="md:col-span-2">
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Nama Kepala Keluarga (Sesuai KK) *</label>
                                    <div className="relative">
                                        <Crown className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-300" />
                                        <input 
                                            type="text"
                                            value={data.nama_kepala_keluarga}
                                            onChange={e => setData('nama_kepala_keluarga', e.target.value.toUpperCase())}
                                            className={`w-full pl-12 pr-4 py-3.5 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-black text-sm uppercase italic tracking-tight ${errors.nama_kepala_keluarga ? 'border-red-500' : 'border-gray-100'}`}
                                            placeholder="MASUKKAN NAMA KEPALA KELUARGA"
                                            required
                                        />
                                    </div>
                                    {errors.nama_kepala_keluarga && <p className="mt-2 text-[10px] font-bold text-red-600 uppercase italic px-1">{errors.nama_kepala_keluarga}</p>}
                                </div>

                                {/* Alamat */}
                                <div className="md:col-span-2">
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Alamat Domisili (KP / Nama Jalan / No Rumah) *</label>
                                    <div className="relative">
                                        <MapPin className="absolute left-4 top-4 w-5 h-5 text-gray-300" />
                                        <textarea 
                                            value={data.alamat}
                                            onChange={e => setData('alamat', e.target.value.toUpperCase())}
                                            rows="3"
                                            className={`w-full pl-12 pr-4 py-3.5 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-bold text-sm uppercase ${errors.alamat ? 'border-red-500' : 'border-gray-100'}`}
                                            placeholder="CONTOH: KP. JATI RT 01 RW 02"
                                            required
                                        />
                                    </div>
                                    {errors.alamat && <p className="mt-2 text-[10px] font-bold text-red-600 uppercase italic px-1">{errors.alamat}</p>}
                                </div>

                                {/* RW Select */}
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Rukun Warga (RW) *</label>
                                    <select 
                                        value={data.rw_id}
                                        onChange={e => setData('rw_id', e.target.value)}
                                        className={`w-full px-4 py-3.5 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-black text-sm uppercase italic tracking-tight ${errors.rw_id ? 'border-red-500' : 'border-gray-100'}`}
                                        required
                                    >
                                        <option value="">PILIH RW</option>
                                        {masterRwOptions.map(rw => (
                                            <option key={rw.id} value={rw.id}>RW {rw.kode} - {rw.nama}</option>
                                        ))}
                                    </select>
                                    {errors.rw_id && <p className="mt-2 text-[10px] font-bold text-red-600 uppercase italic px-1">{errors.rw_id}</p>}
                                </div>

                                {/* RT Select */}
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Rukun Tetangga (RT) *</label>
                                    <select 
                                        value={data.rt_id}
                                        onChange={e => setData('rt_id', e.target.value)}
                                        disabled={!data.rw_id}
                                        className={`w-full px-4 py-3.5 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-black text-sm uppercase italic tracking-tight disabled:opacity-50 ${errors.rt_id ? 'border-red-500' : 'border-gray-100'}`}
                                        required
                                    >
                                        <option value="">PILIH RT</option>
                                        {availableRts.map(rt => (
                                            <option key={rt.id} value={rt.id}>RT {rt.kode} {rt.dusun ? `- DUSUN ${rt.dusun.toUpperCase()}` : ''}</option>
                                        ))}
                                    </select>
                                    {errors.rt_id && <p className="mt-2 text-[10px] font-bold text-red-600 uppercase italic px-1">{errors.rt_id}</p>}
                                </div>

                                {/* Tempat Dikeluarkan */}
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Tempat Dikeluarkan KK</label>
                                    <div className="relative">
                                        <input 
                                            type="text"
                                            value={data.tempat_dikeluarkan}
                                            onChange={e => setData('tempat_dikeluarkan', e.target.value.toUpperCase())}
                                            className={`w-full px-4 py-3.5 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-black text-sm uppercase italic tracking-tight ${errors.tempat_dikeluarkan ? 'border-red-500' : 'border-gray-100'}`}
                                            placeholder="CONTOH: GARUT"
                                        />
                                    </div>
                                    {errors.tempat_dikeluarkan && <p className="mt-2 text-[10px] font-bold text-red-600 uppercase italic px-1">{errors.tempat_dikeluarkan}</p>}
                                </div>

                                {/* Tanggal Dikeluarkan */}
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Tanggal Dikeluarkan KK</label>
                                    <div className="relative">
                                        <input 
                                            type="date"
                                            value={data.tanggal_dikeluarkan}
                                            onChange={e => setData('tanggal_dikeluarkan', e.target.value)}
                                            className={`w-full px-4 py-3.5 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-black text-sm uppercase italic tracking-tight ${errors.tanggal_dikeluarkan ? 'border-red-500' : 'border-gray-100'}`}
                                        />
                                    </div>
                                    {errors.tanggal_dikeluarkan && <p className="mt-2 text-[10px] font-bold text-red-600 uppercase italic px-1">{errors.tanggal_dikeluarkan}</p>}
                                </div>
                            </div>
                        </div>
                    </FormCard>

                    {/* Action Buttons */}
                    <div className="flex flex-col sm:flex-row items-center gap-4 pt-4 pb-12">
                        <Link 
                            href={route('kk.show', nkk)}
                            className="w-full sm:w-auto px-10 py-3.5 bg-white text-gray-600 hover:bg-gray-50 font-black rounded-xl transition-all border border-gray-100 text-[10px] uppercase tracking-[0.2em] flex items-center justify-center shadow-sm"
                        >
                            BATALKAN PERUBAHAN
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full sm:flex-1 px-10 py-3.5 bg-gradient-to-r from-green-600 to-green-800 text-white font-black rounded-xl shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-95 transition-all text-[10px] uppercase tracking-[0.2em] flex items-center justify-center disabled:opacity-50"
                        >
                            {processing ? (
                                <><div className="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-3"></div> MENYIMPAN...</>
                            ) : (
                                <><Save className="w-4 h-4 mr-2" /> SIMPAN PERUBAHAN DATA KK</>
                            )}
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
