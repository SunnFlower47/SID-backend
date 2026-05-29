import React, { useState, useEffect } from 'react';
import { useForm, Link, usePage } from '@inertiajs/react';
import { 
    Building2, MapPin, Phone, Clock, 
    Save, X, Image as ImageIcon,
    FileText, CheckCircle, Info
} from 'lucide-react';
import { FormCard, FormField, MapPicker } from '@/Components/Shared';
import Swal from 'sweetalert2';

export default function FasilitasDesaForm({ fasilitas = null, jenisOptions = [], wilayah = {}, isEdit = false }) {
    const { props } = usePage();
    const desaSettings = props.desa_settings || {};

    // Koordinat kantor desa dari profil desa
    const kantorDesaLat = parseFloat(desaSettings.latitude) || -6.5001403;
    const kantorDesaLng = parseFloat(desaSettings.longitude) || 107.5342964;
    const kantorDesa = { lat: kantorDesaLat, lng: kantorDesaLng };

    // GeoJSON batas wilayah — fetch dari API Laravel (server-side, aman)
    const [geojsonData, setGeojsonData] = useState(null);
    useEffect(() => {
        fetch('/api/v1/geojson', {
            headers: { 'Accept': 'application/json' }
        })
            .then(r => r.ok ? r.json() : null)
            .then(res => { if (res?.success && res?.data) setGeojsonData(res.data); })
            .catch(() => {});
    }, []);

    const { data, setData, post, processing, errors } = useForm({
        _method: isEdit ? 'PUT' : 'POST',
        nama: fasilitas?.nama ?? '',
        jenis: fasilitas?.jenis ?? '',
        alamat: fasilitas?.alamat ?? '',
        dusun_id: fasilitas?.dusun_id ?? '',
        rw_id: fasilitas?.rw_id ?? '',
        rt_id: fasilitas?.rt_id ?? '',
        // Default ke koordinat kantor desa untuk fasilitas baru
        latitude: fasilitas?.latitude ?? kantorDesaLat.toString(),
        longitude: fasilitas?.longitude ?? kantorDesaLng.toString(),
        deskripsi: fasilitas?.deskripsi ?? '',
        kontak: fasilitas?.kontak ?? '',
        jam_operasional: fasilitas?.jam_operasional ?? '',
        status_aktif: fasilitas ? !!fasilitas.status_aktif : true,
        foto: null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        const routeName = isEdit ? route('fasilitas-desa.update', fasilitas.id) : route('fasilitas-desa.store');
        
        post(routeName, {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL!',
                    text: `Data fasilitas desa telah ${isEdit ? 'diperbarui' : 'ditambahkan'}.`,
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-3xl' }
                });
            },
        });
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6 text-left">
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Left Side: Basic Info */}
                <div className="lg:col-span-2 space-y-6">
                    <FormCard icon={Building2} title="Informasi Utama">
                        <div className="space-y-4">
                            <FormField.Input
                                label="Nama Fasilitas"
                                required
                                value={data.nama}
                                onChange={e => setData('nama', e.target.value)}
                                error={errors.nama}
                                placeholder="Contoh: Puskesmas Desa Cibatu"
                            />

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <FormField.Select
                                    label="Jenis Fasilitas"
                                    required
                                    value={data.jenis}
                                    onChange={e => setData('jenis', e.target.value)}
                                    error={errors.jenis}
                                    options={jenisOptions}
                                />

                                <div className="space-y-1">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Status Operasional</label>
                                    <div className="flex gap-4">
                                        <button
                                            type="button"
                                            onClick={() => setData('status_aktif', true)}
                                            className={`flex-1 flex items-center justify-center gap-2 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all border-2 ${data.status_aktif ? 'bg-green-50 border-green-500 text-green-700' : 'bg-white border-gray-100 text-gray-400'}`}
                                        >
                                            <CheckCircle className="w-4 h-4" /> AKTIF
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setData('status_aktif', false)}
                                            className={`flex-1 flex items-center justify-center gap-2 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all border-2 ${!data.status_aktif ? 'bg-red-50 border-red-500 text-red-700' : 'bg-white border-gray-100 text-gray-400'}`}
                                        >
                                            <X className="w-4 h-4" /> TUTUP
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <FormField.Textarea
                                label="Alamat Lengkap"
                                required
                                value={data.alamat}
                                onChange={e => setData('alamat', e.target.value)}
                                error={errors.alamat}
                                placeholder="Contoh: Jl. Raya Cibatu No. 123..."
                                rows={3}
                            />
                        </div>
                    </FormCard>

                    {/* Location & Details */}
                    <FormCard icon={Info} title="Detail & Kontak">
                        <div className="space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <FormField.Input
                                    label="Nomor Kontak"
                                    value={data.kontak}
                                    onChange={e => setData('kontak', e.target.value)}
                                    error={errors.kontak}
                                    placeholder="0812..."
                                />

                                <FormField.Input
                                    label="Jam Operasional"
                                    value={data.jam_operasional}
                                    onChange={e => setData('jam_operasional', e.target.value)}
                                    error={errors.jam_operasional}
                                    placeholder="Contoh: 08:00 - 16:00"
                                />
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <FormField.Input
                                    label="Latitude (Garis Lintang)"
                                    type="number"
                                    step="any"
                                    value={data.latitude}
                                    onChange={e => setData('latitude', e.target.value)}
                                    error={errors.latitude}
                                    placeholder="Contoh: -6.912345"
                                />

                                <FormField.Input
                                    label="Longitude (Garis Bujur)"
                                    type="number"
                                    step="any"
                                    value={data.longitude}
                                    onChange={e => setData('longitude', e.target.value)}
                                    error={errors.longitude}
                                    placeholder="Contoh: 107.612345"
                                />
                            </div>

                            <FormField.Textarea
                                label="Deskripsi / Keterangan"
                                value={data.deskripsi}
                                onChange={e => setData('deskripsi', e.target.value)}
                                error={errors.deskripsi}
                                placeholder="Ceritakan tentang fasilitas ini..."
                                rows={4}
                            />
                        </div>
                    </FormCard>

                    {/* Interactive Map */}
                    <FormCard icon={MapPin} title="Pilih Lokasi di Peta">
                        {/* Info koordinat terpilih */}
                        <div className="mb-3 flex items-center gap-2 text-[9px] font-black uppercase tracking-widest text-gray-400">
                            <MapPin className="w-3 h-3 text-green-600" />
                            <span>Koordinat terpilih:</span>
                            <span className="text-green-700 font-black">
                                {parseFloat(data.latitude || 0).toFixed(6)}, {parseFloat(data.longitude || 0).toFixed(6)}
                            </span>
                        </div>
                        <MapPicker
                            value={{ latitude: data.latitude, longitude: data.longitude }}
                            onChange={(coords) => {
                                setData(prev => ({
                                    ...prev,
                                    latitude: coords.latitude,
                                    longitude: coords.longitude
                                }));
                            }}
                            kantorDesa={kantorDesa}
                            geojsonData={geojsonData}
                        />
                    </FormCard>
                </div>

                {/* Right Side: Photo & Territory */}
                <div className="space-y-6">
                    {/* Photo Upload */}
                    <FormCard icon={ImageIcon} title="Foto Fasilitas">
                        <div className="space-y-4">
                            <div className="aspect-video bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden relative group">
                                {data.foto ? (
                                    <img src={URL.createObjectURL(data.foto)} className="w-full h-full object-cover" />
                                ) : (fasilitas?.foto && (
                                    <img src={`/storage/${fasilitas.foto}`} className="w-full h-full object-cover" />
                                ))}
                                
                                <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <label className="px-4 py-2 bg-white rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer hover:bg-gray-100 transition-colors">
                                        Ganti Foto
                                        <input type="file" className="hidden" onChange={e => setData('foto', e.target.files[0])} />
                                    </label>
                                </div>

                                {!data.foto && !fasilitas?.foto && (
                                    <div className="text-center p-6">
                                        <ImageIcon className="w-8 h-8 text-gray-300 mx-auto mb-2" />
                                        <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-tight">Klik untuk unggah foto</p>
                                        <input type="file" className="absolute inset-0 opacity-0 cursor-pointer" onChange={e => setData('foto', e.target.files[0])} />
                                    </div>
                                )}
                            </div>
                            <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest text-center italic leading-tight">Format: JPG, PNG (Maks. 2MB)</p>
                            {errors.foto && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.foto}</p>}
                        </div>
                    </FormCard>

                    {/* Wilayah */}
                    <FormCard icon={MapPin} title="Wilayah Kerja">
                        <FormField.Select
                            label="Dusun"
                            value={data.dusun_id}
                            onChange={e => setData('dusun_id', e.target.value)}
                            error={errors.dusun_id}
                            options={[{ value: '', label: 'Semua Dusun' }, ...(wilayah.dusun?.map(d => ({ value: d.id, label: d.nama.toUpperCase() })) || [])]}
                        />
                    </FormCard>

                    {/* Submit Button */}
                    <button
                        type="submit"
                        disabled={processing}
                        className="w-full flex items-center justify-center gap-3 px-8 py-4 bg-green-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-lg hover:bg-green-700 transition-all disabled:opacity-50"
                    >
                        {processing ? (
                            <>
                                <div className="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                                MEMPROSES...
                            </>
                        ) : (
                            <>
                                <Save className="w-4 h-4" />
                                {isEdit ? 'PERBARUI FASILITAS' : 'SIMPAN FASILITAS'}
                            </>
                        )}
                    </button>
                    <Link
                        href={route('fasilitas-desa.index')}
                        className="w-full flex items-center justify-center gap-3 px-8 py-4 bg-white text-gray-600 rounded-2xl font-black uppercase tracking-widest text-xs border border-gray-200 hover:bg-gray-50 transition-all"
                    >
                        <X className="w-4 h-4" />
                        BATALKAN
                    </Link>
                </div>
            </div>
        </form>
    );
}
