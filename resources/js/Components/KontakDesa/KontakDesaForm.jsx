import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { Save, X, Phone, Mail, Globe, Share2, MessageSquare, MapPin, Clock, FileText, Image as ImageIcon, CheckCircle } from 'lucide-react';

export default function KontakDesaForm({ kontak = null, jenisOptions, wilayah, isEdit = false }) {
    const { data, setData, post, put, processing, errors } = useForm({
        nama: kontak?.nama || '',
        jenis: kontak?.jenis || '',
        jabatan: kontak?.jabatan || '',
        alamat: kontak?.alamat || '',
        rt_id: kontak?.rt_id || '',
        rw_id: kontak?.rw_id || '',
        dusun_id: kontak?.dusun_id || '',
        no_telepon: kontak?.no_telepon || '',
        no_hp: kontak?.no_hp || '',
        email: kontak?.email || '',
        website: kontak?.website || '',
        facebook: kontak?.facebook || '',
        instagram: kontak?.instagram || '',
        youtube: kontak?.youtube || '',
        whatsapp: kontak?.whatsapp || '',
        jam_operasional: kontak?.jam_operasional || '',
        deskripsi: kontak?.deskripsi || '',
        status_aktif: kontak?.status_aktif ?? true,
        urutan: kontak?.urutan || 0,
        foto: null,
    });

    const [preview, setPreview] = useState(kontak?.foto ? `/storage/${kontak.foto}` : null);

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        setData('foto', file);
        if (file) {
            const reader = new FileReader();
            reader.onloadend = () => setPreview(reader.result);
            reader.readAsDataURL(file);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            // Use POST with _method PUT for file uploads in Laravel
            const formData = new FormData();
            Object.keys(data).forEach(key => {
                if (data[key] !== null) {
                    formData.append(key, data[key]);
                }
            });
            formData.append('_method', 'PUT');
            post(route('kontak-desa.update', kontak.id));
        } else {
            post(route('kontak-desa.store'));
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-8 text-left animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Left Column: Main Info */}
                <div className="lg:col-span-2 space-y-6">
                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
                        <div className="flex items-center gap-3 mb-2">
                            <FileText className="w-5 h-5 text-green-600" />
                            <h3 className="text-sm font-black text-gray-900 uppercase tracking-widest italic">Informasi Utama</h3>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Kontak / Instansi</label>
                                <input
                                    type="text"
                                    value={data.nama}
                                    onChange={e => setData('nama', e.target.value)}
                                    className={`w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 ${errors.nama ? 'focus:ring-red-500' : 'focus:ring-green-500'} transition-all`}
                                    placeholder="Contoh: Kantor Desa Cibatu"
                                />
                                {errors.nama && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.nama}</p>}
                            </div>

                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Kontak</label>
                                <select
                                    value={data.jenis}
                                    onChange={e => setData('jenis', e.target.value)}
                                    className={`w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 ${errors.jenis ? 'focus:ring-red-500' : 'focus:ring-green-500'} transition-all`}
                                >
                                    <option value="">Pilih Jenis</option>
                                    {jenisOptions.map(opt => (
                                        <option key={opt.value} value={opt.value}>{opt.label}</option>
                                    ))}
                                </select>
                                {errors.jenis && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.jenis}</p>}
                            </div>

                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jabatan / Keterangan</label>
                                <input
                                    type="text"
                                    value={data.jabatan}
                                    onChange={e => setData('jabatan', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                    placeholder="Contoh: Pusat Pelayanan Terpadu"
                                />
                            </div>

                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Urutan Tampil</label>
                                <input
                                    type="number"
                                    value={data.urutan}
                                    onChange={e => setData('urutan', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                />
                            </div>
                        </div>

                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Lengkap</label>
                            <textarea
                                value={data.alamat}
                                onChange={e => setData('alamat', e.target.value)}
                                rows="3"
                                className={`w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 ${errors.alamat ? 'focus:ring-red-500' : 'focus:ring-green-500'} transition-all`}
                                placeholder="Alamat lengkap instansi atau perangkat..."
                            ></textarea>
                            {errors.alamat && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.alamat}</p>}
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Dusun</label>
                                <select
                                    value={data.dusun_id}
                                    onChange={e => setData('dusun_id', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                >
                                    <option value="">PILIH DUSUN</option>
                                    {wilayah.dusun.map(d => <option key={d.id} value={d.id}>{d.nama.toUpperCase()}</option>)}
                                </select>
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">RW</label>
                                <select
                                    value={data.rw_id}
                                    onChange={e => setData('rw_id', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                >
                                    <option value="">RW</option>
                                    {wilayah.rw.map(r => <option key={r.id} value={r.id}>{r.kode}</option>)}
                                </select>
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">RT</label>
                                <select
                                    value={data.rt_id}
                                    onChange={e => setData('rt_id', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                >
                                    <option value="">RT</option>
                                    {wilayah.rt.filter(r => !data.rw_id || r.rw_id == data.rw_id).map(r => (
                                        <option key={r.id} value={r.id}>{r.kode}</option>
                                    ))}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
                        <div className="flex items-center gap-3 mb-2">
                            <Phone className="w-5 h-5 text-blue-600" />
                            <h3 className="text-sm font-black text-gray-900 uppercase tracking-widest italic">Informasi Kontak & Sosial</h3>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                                    <Phone className="w-3 h-3" /> No. Telepon Kantor
                                </label>
                                <input
                                    type="text"
                                    value={data.no_telepon}
                                    onChange={e => setData('no_telepon', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                    placeholder="0264-xxxxxx"
                                />
                            </div>

                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                                    <MessageSquare className="w-3 h-3" /> No. HP / WhatsApp
                                </label>
                                <input
                                    type="text"
                                    value={data.no_hp}
                                    onChange={e => setData('no_hp', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                    placeholder="08xxxxxxxx"
                                />
                            </div>

                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                                    <Mail className="w-3 h-3" /> Email
                                </label>
                                <input
                                    type="email"
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                    placeholder="email@desa.id"
                                />
                            </div>

                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                                    <Globe className="w-3 h-3" /> Website
                                </label>
                                <input
                                    type="url"
                                    value={data.website}
                                    onChange={e => setData('website', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                    placeholder="https://..."
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                                    <Share2 className="w-3 h-3 text-blue-600" /> Facebook
                                </label>
                                <input
                                    type="url"
                                    value={data.facebook}
                                    onChange={e => setData('facebook', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                />
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                                    <Share2 className="w-3 h-3 text-pink-600" /> Instagram
                                </label>
                                <input
                                    type="url"
                                    value={data.instagram}
                                    onChange={e => setData('instagram', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                />
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                                    <Share2 className="w-3 h-3 text-red-600" /> Youtube
                                </label>
                                <input
                                    type="url"
                                    value={data.youtube}
                                    onChange={e => setData('youtube', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right Column: Photo & Settings */}
                <div className="space-y-6">
                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
                        <div className="flex items-center gap-3 mb-2">
                            <ImageIcon className="w-5 h-5 text-orange-600" />
                            <h3 className="text-sm font-black text-gray-900 uppercase tracking-widest italic">Foto & Media</h3>
                        </div>

                        <div className="space-y-4">
                            <div className="aspect-square w-full bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 overflow-hidden relative group">
                                {preview ? (
                                    <>
                                        <img src={preview} className="w-full h-full object-cover" alt="Preview" />
                                        <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <label className="cursor-pointer bg-white text-gray-900 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest">Ganti Foto</label>
                                        </div>
                                    </>
                                ) : (
                                    <label className="absolute inset-0 cursor-pointer flex flex-col items-center justify-center space-y-2 text-gray-400 hover:text-green-600 transition-colors">
                                        <ImageIcon className="w-10 h-10" />
                                        <span className="text-[10px] font-black uppercase tracking-widest">Unggah Foto</span>
                                    </label>
                                )}
                                <input type="file" onChange={handleFileChange} className="hidden" accept="image/*" />
                            </div>
                            <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest text-center italic">Format: JPG, PNG, GIF (Maks. 2MB)</p>
                        </div>

                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                                <Clock className="w-3 h-3" /> Jam Operasional
                            </label>
                            <input
                                type="text"
                                value={data.jam_operasional}
                                onChange={e => setData('jam_operasional', e.target.value)}
                                className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all"
                                placeholder="Contoh: Sen-Jum, 08:00 - 16:00"
                            />
                        </div>

                        <div className="pt-4 border-t border-gray-50">
                            <label className="flex items-center gap-3 cursor-pointer group">
                                <div className="relative">
                                    <input
                                        type="checkbox"
                                        checked={data.status_aktif}
                                        onChange={e => setData('status_aktif', e.target.checked)}
                                        className="sr-only"
                                    />
                                    <div className={`w-12 h-6 rounded-full transition-colors ${data.status_aktif ? 'bg-green-600' : 'bg-gray-200'}`}></div>
                                    <div className={`absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform ${data.status_aktif ? 'translate-x-6' : ''}`}></div>
                                </div>
                                <span className="text-[10px] font-black text-gray-900 uppercase tracking-widest italic group-hover:text-green-600 transition-colors">
                                    {data.status_aktif ? 'STATUS: AKTIF' : 'STATUS: NONAKTIF'}
                                </span>
                            </label>
                        </div>
                    </div>

                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full flex items-center justify-center gap-3 px-6 py-4 bg-gradient-to-r from-green-600 to-green-800 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-lg shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:scale-100"
                        >
                            {processing ? (
                                <>
                                    <div className="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                                    MENYIMPAN...
                                </>
                            ) : (
                                <>
                                    <Save className="w-4 h-4" />
                                    {isEdit ? 'PERBARUI KONTAK' : 'SIMPAN KONTAK'}
                                </>
                            )}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    );
}
