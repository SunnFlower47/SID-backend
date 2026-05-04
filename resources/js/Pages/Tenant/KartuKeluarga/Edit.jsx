import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Home, ArrowLeft, Save, MapPin, Crown, Info, CheckCircle, AlertTriangle } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Edit({ auth, kk, nkk, masterRwOptions }) {
    const { data, setData, put, processing, errors } = useForm({
        nama_kepala_keluarga: kk.nama_kepala_keluarga || '',
        alamat: kk.alamat || '',
        rt_id: kk.rt_id || '',
        rw_id: kk.rw_id || '',
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
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 text-white relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Home className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black tracking-tight uppercase italic leading-none text-left">Edit Kartu Keluarga</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic text-left">NKK: {kk.nkk}</p>
                            </div>
                        </div>
                        <Link 
                            href={route('kk.index')}
                            className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all w-fit"
                        >
                            <ArrowLeft className="w-4 h-4 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>
                <div className="w-full">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Main Form Card */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                                <h3 className="text-sm font-black text-gray-900 flex items-center uppercase italic tracking-tighter">
                                    <Crown className="w-5 h-5 text-yellow-500 mr-2" />
                                    Informasi Utama KK
                                </h3>
                            </div>
                            
                            <div className="p-8 space-y-8">
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
                            </div>
                        </div>
                    </div>

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
