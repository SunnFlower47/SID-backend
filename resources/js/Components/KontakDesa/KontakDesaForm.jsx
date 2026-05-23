import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { Save, Phone, Mail, Globe, Share2, MessageSquare, Clock, FileText, Image as ImageIcon, ArrowRight } from 'lucide-react';
import { FormCard, FormField } from '@/Components/Shared';

export default function KontakDesaForm({ kontak = null, jenisOptions, wilayah, isEdit = false }) {
    const { data, setData, post, put, processing, errors } = useForm({
        _method: isEdit ? 'PUT' : 'POST',
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
            post(route('kontak-desa.update', kontak.id));
        } else {
            post(route('kontak-desa.store'));
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6 text-left animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Left Column: Main Info */}
                <div className="lg:col-span-2 space-y-6">
                    <FormCard 
                        title="Informasi Utama" 
                        icon={FileText} 
                        iconColor="text-green-600" 
                        iconBg="bg-green-50"
                    >
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <FormField.Input
                                label="Nama Kontak / Instansi"
                                value={data.nama}
                                onChange={e => setData('nama', e.target.value)}
                                error={errors.nama}
                                placeholder="Contoh: Kantor Desa Cibatu"
                            />

                            <FormField.Select
                                label="Jenis Kontak"
                                value={data.jenis}
                                onChange={e => setData('jenis', e.target.value)}
                                error={errors.jenis}
                                options={[
                                    { value: '', label: 'Pilih Jenis' },
                                    ...jenisOptions.map(opt => ({ value: opt.value, label: opt.label }))
                                ]}
                            />

                            <FormField.Input
                                label="Jabatan / Keterangan"
                                value={data.jabatan}
                                onChange={e => setData('jabatan', e.target.value)}
                                error={errors.jabatan}
                                placeholder="Contoh: Pusat Pelayanan Terpadu"
                            />

                            <FormField.Input
                                label="Urutan Tampil"
                                type="number"
                                value={data.urutan}
                                onChange={e => setData('urutan', e.target.value)}
                                error={errors.urutan}
                            />
                        </div>

                        <div className="mt-6 space-y-6 text-left">
                            <FormField.Textarea
                                label="Alamat Lengkap"
                                value={data.alamat}
                                onChange={e => setData('alamat', e.target.value)}
                                error={errors.alamat}
                                rows={3}
                                placeholder="Alamat lengkap instansi atau perangkat..."
                            />

                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                <FormField.Select
                                    label="Dusun"
                                    value={data.dusun_id}
                                    onChange={e => setData('dusun_id', e.target.value)}
                                    error={errors.dusun_id}
                                    options={[
                                        { value: '', label: 'PILIH DUSUN' },
                                        ...wilayah.dusun.map(d => ({ value: d.id, label: d.nama.toUpperCase() }))
                                    ]}
                                />
                                <FormField.Select
                                    label="RW"
                                    value={data.rw_id}
                                    onChange={e => setData('rw_id', e.target.value)}
                                    error={errors.rw_id}
                                    options={[
                                        { value: '', label: 'RW' },
                                        ...wilayah.rw.map(r => ({ value: r.id, label: r.kode }))
                                    ]}
                                />
                                <FormField.Select
                                    label="RT"
                                    value={data.rt_id}
                                    onChange={e => setData('rt_id', e.target.value)}
                                    error={errors.rt_id}
                                    options={[
                                        { value: '', label: 'RT' },
                                        ...wilayah.rt.filter(r => !data.rw_id || r.rw_id == data.rw_id).map(r => ({ value: r.id, label: r.kode }))
                                    ]}
                                />
                            </div>
                        </div>
                    </FormCard>

                    <FormCard 
                        title="Informasi Kontak & Sosial" 
                        icon={Phone} 
                        iconColor="text-blue-600" 
                        iconBg="bg-blue-50"
                    >
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <FormField.Input
                                label="No. Telepon Kantor"
                                icon={Phone}
                                value={data.no_telepon}
                                onChange={e => setData('no_telepon', e.target.value)}
                                error={errors.no_telepon}
                                placeholder="0264-xxxxxx"
                            />

                            <FormField.Input
                                label="No. HP / WhatsApp"
                                icon={MessageSquare}
                                value={data.no_hp}
                                onChange={e => setData('no_hp', e.target.value)}
                                error={errors.no_hp}
                                placeholder="08xxxxxxxx"
                            />

                            <FormField.Input
                                label="Email"
                                type="email"
                                icon={Mail}
                                value={data.email}
                                onChange={e => setData('email', e.target.value)}
                                error={errors.email}
                                placeholder="email@desa.id"
                            />

                            <FormField.Input
                                label="Website"
                                type="url"
                                icon={Globe}
                                value={data.website}
                                onChange={e => setData('website', e.target.value)}
                                error={errors.website}
                                placeholder="https://..."
                            />
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">
                            <FormField.Input
                                label="Facebook"
                                type="url"
                                icon={Share2}
                                value={data.facebook}
                                onChange={e => setData('facebook', e.target.value)}
                                error={errors.facebook}
                            />
                            <FormField.Input
                                label="Instagram"
                                type="url"
                                icon={Share2}
                                value={data.instagram}
                                onChange={e => setData('instagram', e.target.value)}
                                error={errors.instagram}
                            />
                            <FormField.Input
                                label="Youtube"
                                type="url"
                                icon={Share2}
                                value={data.youtube}
                                onChange={e => setData('youtube', e.target.value)}
                                error={errors.youtube}
                            />
                        </div>
                    </FormCard>
                </div>

                {/* Right Column: Photo & Settings */}
                <div className="space-y-6">
                    <FormCard 
                        title="Foto & Media" 
                        icon={ImageIcon} 
                        iconColor="text-orange-600" 
                        iconBg="bg-orange-50"
                    >
                        <div className="space-y-4 text-left">
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

                        <div className="space-y-6 mt-6 text-left">
                            <FormField.Input
                                label="Jam Operasional"
                                icon={Clock}
                                value={data.jam_operasional}
                                onChange={e => setData('jam_operasional', e.target.value)}
                                error={errors.jam_operasional}
                                placeholder="Contoh: Sen-Jum, 08:00 - 16:00"
                            />

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
                    </FormCard>

                    <div className="pt-4">
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full flex items-center justify-center gap-3 px-8 py-6 bg-green-600 text-white rounded-[2rem] font-black uppercase tracking-widest text-[11px] shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50"
                        >
                            {processing ? "MENYIMPAN..." : (isEdit ? "PERBARUI KONTAK" : "SIMPAN KONTAK")}
                            <ArrowRight className="w-4 h-4 ml-2" />
                        </button>
                    </div>
                </div>
            </div>
        </form>
    );
}
