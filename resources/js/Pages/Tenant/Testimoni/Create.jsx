import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { MessageSquare, ArrowLeft, Save, User, Mail, Phone, MapPin, Star, Info } from 'lucide-react';

export default function Create({ auth, masterRwOptions }) {
    const { data, setData, post, processing, errors } = useForm({
        nama: '', email: '', telepon: '', rw_id: '', rt_id: '', dusun_id: '',
        testimoni: '', rating: 5, kategori: 'umum', is_anonymous: false,
    });

    const [availableRts, setAvailableRts] = useState([]);

    useEffect(() => {
        if (data.rw_id) {
            const selectedRw = masterRwOptions.find(rw => rw.id === parseInt(data.rw_id));
            setAvailableRts(selectedRw?.rts || []);
        } else {
            setAvailableRts([]);
        }
    }, [data.rw_id]);

    const handleRtChange = (rtId) => {
        const selectedRt = availableRts.find(rt => rt.id === parseInt(rtId));
        setData(prev => ({ ...prev, rt_id: rtId, dusun_id: selectedRt ? selectedRt.dusun_id : '' }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('testimoni.store'));
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Testimoni">
            <Head title="Tambah Testimoni Warga" />

            <div className="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-10 text-left">
                <div className="bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-900 rounded-[3rem] shadow-2xl p-8 sm:p-10 relative overflow-hidden text-left">
                    <div className="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                        <div className="flex items-center gap-6">
                            <div className="w-16 h-16 sm:w-20 sm:h-20 bg-white/10 backdrop-blur-xl rounded-[2rem] flex items-center justify-center border border-white/20 shadow-2xl shrink-0">
                                <MessageSquare className="w-8 h-8 sm:w-10 sm:h-10 text-white" />
                            </div>
                            <div>
                                <h1 className="text-2xl sm:text-4xl font-black text-white tracking-tighter uppercase italic leading-none">Tambah Testimoni</h1>
                                <p className="text-indigo-100 font-bold text-xs sm:text-sm uppercase tracking-[0.2em] mt-2 opacity-80 italic">Input data testimoni baru</p>
                            </div>
                        </div>
                        <Link href={route('testimoni.index')} className="flex items-center px-8 py-4 bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/20 text-white rounded-[2rem] text-[10px] font-black uppercase tracking-widest transition-all">
                            <ArrowLeft className="w-4 h-4 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div className="lg:col-span-2 space-y-8">
                        <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10">
                            <div className="space-y-6">
                                <div>
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 block">Isi Testimoni</label>
                                    <textarea
                                        value={data.testimoni}
                                        onChange={e => setData('testimoni', e.target.value)}
                                        className="w-full px-6 py-5 bg-gray-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-3xl text-sm font-medium text-gray-700 shadow-inner min-h-[200px]"
                                        placeholder="Ketik testimoni di sini..."
                                    ></textarea>
                                    {errors.testimoni && <p className="text-rose-500 text-[10px] font-black mt-2 uppercase">{errors.testimoni}</p>}
                                </div>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 block">Rating Kepuasan</label>
                                        <div className="flex items-center gap-2 p-4 bg-gray-50 rounded-2xl shadow-inner">
                                            {[1, 2, 3, 4, 5].map(s => (
                                                <button key={s} type="button" onClick={() => setData('rating', s)} className="focus:outline-none transform hover:scale-110 transition-transform">
                                                    <Star className={`w-8 h-8 ${data.rating >= s ? 'fill-orange-400 text-orange-400' : 'text-gray-200'}`} />
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                    <div>
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 block">Kategori</label>
                                        <select value={data.kategori} onChange={e => setData('kategori', e.target.value)} className="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold shadow-inner">
                                            <option value="umum">Umum</option>
                                            <option value="pelayanan">Pelayanan</option>
                                            <option value="pembangunan">Pembangunan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="flex justify-end">
                            <button type="submit" disabled={processing} className="px-12 py-5 bg-gray-900 text-white rounded-[2rem] text-[10px] font-black uppercase tracking-[0.2em] shadow-2xl hover:scale-105 active:scale-95 transition-all flex items-center gap-3">
                                <Save className="w-5 h-5" /> SIMPAN DATA TESTIMONI
                            </button>
                        </div>
                    </div>

                    <div className="space-y-8">
                        <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                            <div className="px-8 py-5 border-b border-gray-50 bg-gray-50/50 uppercase font-black text-[10px] text-gray-400 italic tracking-widest flex items-center gap-2">
                                <User className="w-4 h-4" /> Informasi Pengirim
                            </div>
                            <div className="p-8 space-y-5">
                                <input type="text" placeholder="Nama Lengkap" value={data.nama} onChange={e => setData('nama', e.target.value)} className="w-full px-6 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold shadow-inner" />
                                <input type="email" placeholder="Email (Opsional)" value={data.email} onChange={e => setData('email', e.target.value)} className="w-full px-6 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold shadow-inner" />
                                <input type="text" placeholder="Telepon (Opsional)" value={data.telepon} onChange={e => setData('telepon', e.target.value)} className="w-full px-6 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold shadow-inner" />
                                <div className="flex items-center gap-2 px-2">
                                    <input type="checkbox" id="anon" checked={data.is_anonymous} onChange={e => setData('is_anonymous', e.target.checked)} className="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label htmlFor="anon" className="text-[10px] font-black text-gray-400 uppercase tracking-widest cursor-pointer">Anonim</label>
                                </div>
                            </div>
                        </div>
                        <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                            <div className="px-8 py-5 border-b border-gray-50 bg-gray-50/50 uppercase font-black text-[10px] text-gray-400 italic tracking-widest flex items-center gap-2">
                                <MapPin className="w-4 h-4" /> Lokasi / Wilayah
                            </div>
                            <div className="p-8 space-y-4">
                                <select value={data.rw_id} onChange={e => setData('rw_id', e.target.value)} className="w-full px-6 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold shadow-inner uppercase tracking-widest">
                                    <option value="">Pilih RW</option>
                                    {masterRwOptions.map(rw => <option key={rw.id} value={rw.id}>RW {rw.kode} - {rw.nama}</option>)}
                                </select>
                                <select value={data.rt_id} onChange={e => handleRtChange(e.target.value)} disabled={!data.rw_id} className="w-full px-6 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold shadow-inner uppercase tracking-widest disabled:opacity-50">
                                    <option value="">Pilih RT</option>
                                    {availableRts.map(rt => <option key={rt.id} value={rt.id}>RT {rt.kode}</option>)}
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
