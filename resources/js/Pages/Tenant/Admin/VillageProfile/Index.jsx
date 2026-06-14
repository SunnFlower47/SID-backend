import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/Shared';
import { 
    HelpCircle, 
    Image, 
    Flag, 
    MapPin, 
    Share2, 
    Check, 
    Home, 
    Mail, 
    Phone, 
    Globe, 
    Map,
    Loader2
} from 'lucide-react';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';

export default function Index({ auth, profile }) {
    const [activeTab, setActiveTab] = useState('umum');

    // Form for General & Profile Info
    const { data, setData, post, processing, errors } = useForm({
        nama_desa: profile.general.nama_desa || '',
        kecamatan: profile.general.kecamatan || '',
        kabupaten: profile.general.kabupaten || '',
        provinsi: profile.general.provinsi || '',
        kode_pos: profile.general.kode_pos || '',
        alamat_lengkap: profile.general.alamat_lengkap || '',
        telepon: profile.general.telepon || '',
        email: profile.general.email || '',
        website: profile.general.website || '',
        latitude: profile.general.latitude || '',
        longitude: profile.general.longitude || '',
        luas_total: profile.geography.luas_total || '',
        visi: profile.additional.visi || '',
        misi: profile.additional.misi || '',
        sejarah_desa: profile.additional.sejarah_desa || '',
        tahun_berdiri: profile.additional.tahun_berdiri || '',
        kepala_desa_pertama: profile.additional.kepala_desa_pertama || '',
        karakteristik_desa: profile.additional.karakteristik_desa || '',
        link_facebook: profile.additional.facebook || '',
        link_instagram: profile.additional.instagram || '',
        link_youtube: profile.additional.youtube || '',
        link_whatsapp: profile.additional.whatsapp || '',
    });

    const [previews, setPreviews] = useState({
        logo_desa: null,
        logo_kabupaten: null,
        logo_provinsi: null
    });

    // Form for Logos
    const { data: logoData, setData: setLogoData, post: postLogos, processing: logoProcessing } = useForm({
        logo_desa: null,
        logo_kabupaten: null,
        logo_provinsi: null,
        _method: 'POST'
    });

    const handleLogoChange = (key, file) => {
        if (!file) return;
        
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            Swal.fire({
                title: 'FILE TERLALU BESAR!',
                text: 'Ukuran file logo maksimal adalah 2 MB. Silakan kompres gambar Anda terlebih dahulu.',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OKE',
                customClass: {
                    popup: 'rounded-[2.5rem] border-none shadow-2xl',
                    title: 'font-black tracking-tighter uppercase italic text-red-600',
                    confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px]'
                }
            });
            return;
        }
        
        setLogoData(key, file);

        // Generate Instant Preview
        const reader = new FileReader();
        reader.onloadend = () => {
            setPreviews(prev => ({
                ...prev,
                [key]: reader.result
            }));
        };
        reader.readAsDataURL(file);
    };

    const submitGeneral = (e) => {
        e.preventDefault();
        post(route('profil-desa.update'), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'BERHASIL!',
                    text: 'Profil desa telah diperbarui secara terpusat.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-3xl border-none shadow-2xl',
                        title: 'font-black tracking-tighter uppercase italic text-green-600',
                    }
                });
            }
        });
    };

    const submitLogos = (e) => {
        e.preventDefault();
        postLogos(route('profil-desa.update-logos'), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'LOGO DIPERBARUI!',
                    text: 'Branding desa Anda kini telah diperbarui.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
            }
        });
    };

    const tabs = [
        { id: 'umum', label: 'INFORMASI UMUM', icon: <HelpCircle className="w-4 h-4" /> },
        { id: 'branding', label: 'LOGO & BRANDING', icon: <Image className="w-4 h-4" /> },
        { id: 'visi', label: 'VISI, MISI & PROFIL', icon: <Flag className="w-4 h-4" /> },
        { id: 'geografi', label: 'WILAYAH', icon: <MapPin className="w-4 h-4" /> },
        { id: 'sosmed', label: 'MEDIA SOSIAL', icon: <Share2 className="w-4 h-4" /> },
    ];

    return (
        <AuthenticatedLayout user={auth.user} title="Profil Desa">
            <Head title="Profil Desa Terpusat" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader
                    title="Profil Desa"
                    subtitle="Pusat Informasi & Identitas Desa Cibatu"
                    icon={Home}
                />

                {/* Tab Navigation */}
                <div className="flex flex-wrap gap-2 sm:gap-3 p-2 bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm overflow-x-auto no-scrollbar">
                    {tabs.map((tab) => (
                        <button
                            key={tab.id}
                            onClick={() => setActiveTab(tab.id)}
                            className={cn(
                                "flex items-center px-4 py-3 sm:px-6 rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest whitespace-nowrap",
                                activeTab === tab.id
                                    ? "bg-green-600 text-white shadow-lg shadow-green-200"
                                    : "bg-gray-50 text-gray-500 hover:bg-gray-100"
                            )}
                        >
                            <span className="mr-2">{tab.icon}</span>
                            {tab.label}
                        </button>
                    ))}
                </div>

                <div className="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <form onSubmit={activeTab === 'branding' ? submitLogos : submitGeneral} className="p-6 sm:p-8">
                        
                        {activeTab === 'umum' && (
                            <div className="space-y-6 animate-in slide-in-from-left duration-300">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Desa</label>
                                        <input 
                                            type="text" 
                                            value={data.nama_desa}
                                            onChange={e => setData('nama_desa', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kecamatan</label>
                                        <input 
                                            type="text" 
                                            value={data.kecamatan}
                                            onChange={e => setData('kecamatan', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kabupaten</label>
                                        <input 
                                            type="text" 
                                            value={data.kabupaten}
                                            onChange={e => setData('kabupaten', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kode Pos</label>
                                        <input 
                                            type="text" 
                                            value={data.kode_pos}
                                            onChange={e => setData('kode_pos', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        />
                                    </div>
                                    <div className="md:col-span-2 space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Lengkap</label>
                                        <textarea 
                                            rows="3"
                                            value={data.alamat_lengkap}
                                            onChange={e => setData('alamat_lengkap', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        ></textarea>
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-1"><Phone className="w-3 h-3"/> Telepon</label>
                                        <input 
                                            type="text" 
                                            value={data.telepon}
                                            onChange={e => setData('telepon', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-1"><Mail className="w-3 h-3"/> Email</label>
                                        <input 
                                            type="email" 
                                            value={data.email}
                                            onChange={e => setData('email', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-1"><Globe className="w-3 h-3"/> Base URL / Website Desa</label>
                                        <input 
                                            type="url" 
                                            value={data.website}
                                            onChange={e => setData('website', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                            placeholder="https://desa-cibatu.id"
                                        />
                                    </div>
                                </div>
                            </div>
                        )}

                        {activeTab === 'branding' && (
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-8 animate-in slide-in-from-right duration-300">
                                {[
                                    { key: 'logo_desa', label: 'Logo Desa', current: profile.branding.desa },
                                    { key: 'logo_kabupaten', label: 'Logo Kabupaten', current: profile.branding.kabupaten },
                                    { key: 'logo_provinsi', label: 'Logo Provinsi', current: profile.branding.provinsi },
                                ].map((logo) => {
                                    const displayImage = previews[logo.key] || logo.current;
                                    return (
                                        <div key={logo.key} className="flex flex-col items-center p-6 bg-gray-50 rounded-3xl border border-dashed border-gray-300">
                                            <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4">{logo.label}</label>
                                            <div className="relative group">
                                                <div className="w-32 h-32 rounded-3xl bg-white shadow-inner flex items-center justify-center p-4 overflow-hidden border border-gray-100">
                                                    {displayImage ? (
                                                        <img src={displayImage} alt={logo.label} className="w-full h-full object-contain" />
                                                    ) : (
                                                        <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-300 font-black">?</div>
                                                    )}
                                                </div>
                                                <label className="absolute inset-0 flex items-center justify-center bg-emerald-600/80 text-white rounded-3xl opacity-0 group-hover:opacity-100 cursor-pointer transition-all duration-300">
                                                    <span className="text-[10px] font-black uppercase tracking-widest">GANTI</span>
                                                    <input 
                                                        type="file" 
                                                        className="hidden" 
                                                        onChange={e => handleLogoChange(logo.key, e.target.files[0])}
                                                    />
                                                </label>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}

                        {activeTab === 'visi' && (
                            <div className="space-y-8 animate-in zoom-in-95 duration-300">
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-emerald-600 uppercase tracking-widest ml-1 flex items-center gap-2"><Flag className="w-4 h-4"/> Visi Desa</label>
                                    <textarea 
                                        rows="4"
                                        value={data.visi}
                                        onChange={e => setData('visi', e.target.value)}
                                        className="w-full bg-emerald-50/30 border-emerald-100 rounded-3xl px-6 py-4 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-lg italic"
                                        placeholder="Masukkan visi desa..."
                                    ></textarea>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-emerald-600 uppercase tracking-widest ml-1">Misi Desa</label>
                                    <textarea 
                                        rows="6"
                                        value={data.misi}
                                        onChange={e => setData('misi', e.target.value)}
                                        className="w-full bg-gray-50 border-gray-200 rounded-3xl px-6 py-4 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        placeholder="Masukkan misi desa (pisahkan dengan baris baru untuk setiap poin)..."
                                    ></textarea>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-emerald-600 uppercase tracking-widest ml-1">Sejarah Desa</label>
                                    <textarea 
                                        rows="6"
                                        value={data.sejarah_desa}
                                        onChange={e => setData('sejarah_desa', e.target.value)}
                                        className="w-full bg-gray-50 border-gray-200 rounded-3xl px-6 py-4 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        placeholder="Masukkan sejarah singkat berdirinya desa..."
                                    ></textarea>
                                </div>
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gray-100">
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tahun Berdiri</label>
                                        <input 
                                            type="text" 
                                            value={data.tahun_berdiri}
                                            onChange={e => setData('tahun_berdiri', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                            placeholder="Contoh: 1860"
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kepala Desa Pertama</label>
                                        <input 
                                            type="text" 
                                            value={data.kepala_desa_pertama}
                                            onChange={e => setData('kepala_desa_pertama', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                            placeholder="Contoh: Ki Arpan"
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Karakteristik Desa</label>
                                        <input 
                                            type="text" 
                                            value={data.karakteristik_desa}
                                            onChange={e => setData('karakteristik_desa', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                            placeholder="Contoh: Industri / Pertanian"
                                        />
                                    </div>
                                </div>
                            </div>
                        )}

                        {activeTab === 'geografi' && (
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8 animate-in slide-in-from-bottom duration-300">
                                <div className="space-y-6">
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-1"><Map className="w-3 h-3"/> Luas Total (Ha)</label>
                                        <input 
                                            type="number" 
                                            value={data.luas_total}
                                            onChange={e => setData('luas_total', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        />
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 italic">Latitude (Kantor Desa)</label>
                                            <input 
                                                type="text" 
                                                value={data.latitude}
                                                onChange={e => setData('latitude', e.target.value)}
                                                className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                            />
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 italic">Longitude (Kantor Desa)</label>
                                            <input 
                                                type="text" 
                                                value={data.longitude}
                                                onChange={e => setData('longitude', e.target.value)}
                                                className="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        {activeTab === 'sosmed' && (
                            <div className="space-y-6 animate-in slide-in-from-right duration-300">
                                {[
                                    { key: 'link_facebook', label: 'Facebook', icon: <Share2 className="w-5 h-5 text-blue-600" />, color: 'bg-blue-50 border-blue-100', placeholder: 'https://facebook.com/desacibatu' },
                                    { key: 'link_instagram', label: 'Instagram', icon: <Share2 className="w-5 h-5 text-pink-600" />, color: 'bg-pink-50 border-pink-100', placeholder: 'https://instagram.com/desacibatu' },
                                    { key: 'link_youtube', label: 'YouTube', icon: <Share2 className="w-5 h-5 text-red-600" />, color: 'bg-red-50 border-red-100', placeholder: 'https://youtube.com/@desacibatu' },
                                    { key: 'link_whatsapp', label: 'WhatsApp', icon: <Share2 className="w-5 h-5 text-green-600" />, color: 'bg-green-50 border-green-100', placeholder: 'https://wa.me/628xxxxxxxxxx' },
                                ].map((item) => (
                                    <div key={item.key} className={cn("flex items-center gap-4 p-4 rounded-3xl border", item.color)}>
                                        <div className="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm shrink-0">
                                            {item.icon}
                                        </div>
                                        <div className="flex-1">
                                            <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">{item.label} URL</label>
                                            <input 
                                                type="url" 
                                                value={data[item.key]}
                                                placeholder={item.placeholder}
                                                onChange={e => setData(item.key, e.target.value)}
                                                className="w-full bg-transparent border-none p-0 font-bold text-gray-900 focus:ring-0 text-sm placeholder:text-gray-300"
                                            />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}

                        <div className="mt-12 flex items-center justify-end border-t border-gray-100 pt-8">
                            <button
                                type="submit"
                                disabled={processing || logoProcessing}
                                className="flex items-center px-10 py-4 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-2xl text-xs font-black shadow-xl shadow-green-200 transition-all hover:scale-105 uppercase tracking-widest"
                            >
                                {processing || logoProcessing ? (
                                    <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                                ) : (
                                    <Check className="w-4 h-4 mr-2" />
                                )}
                                SIMPAN PERUBAHAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
