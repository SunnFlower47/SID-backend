import React from 'react';
import { useForm } from '@inertiajs/react';
import { 
    Building2, MapPin, Phone, Clock, 
    Save, X, Image as ImageIcon,
    FileText, CheckCircle, Info
} from 'lucide-react';
import Swal from 'sweetalert2';

export default function FasilitasDesaForm({ fasilitas = null, jenisOptions = [], wilayah = {}, isEdit = false }) {
    const { data, setData, post, processing, errors, clearErrors } = useForm({
        _method: isEdit ? 'PUT' : 'POST',
        nama: fasilitas?.nama ?? '',
        jenis: fasilitas?.jenis ?? '',
        alamat: fasilitas?.alamat ?? '',
        dusun_id: fasilitas?.dusun_id ?? '',
        rw_id: fasilitas?.rw_id ?? '',
        rt_id: fasilitas?.rt_id ?? '',
        latitude: fasilitas?.latitude ?? '',
        longitude: fasilitas?.longitude ?? '',
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
                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 text-left">
                        <div className="flex items-center gap-4 mb-8">
                            <div className="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center">
                                <Building2 className="w-6 h-6 text-green-600" />
                            </div>
                            <div>
                                <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Informasi Utama</h3>
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Data Dasar Fasilitas Desa</p>
                            </div>
                        </div>

                        <div className="space-y-6">
                            <div className="space-y-2 text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Fasilitas</label>
                                <div className="relative group">
                                    <Building2 className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                    <input
                                        type="text"
                                        value={data.nama}
                                        onChange={e => setData('nama', e.target.value)}
                                        className={`w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 ${errors.nama ? 'focus:ring-red-500/10' : 'focus:ring-green-500/10'} transition-all`}
                                        placeholder="Contoh: Puskesmas Desa Cibatu"
                                    />
                                </div>
                                {errors.nama && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.nama}</p>}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2 text-left">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Fasilitas</label>
                                    <select
                                        value={data.jenis}
                                        onChange={e => setData('jenis', e.target.value)}
                                        className={`w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 ${errors.jenis ? 'focus:ring-red-500/10' : 'focus:ring-green-500/10'} transition-all appearance-none cursor-pointer`}
                                    >
                                        <option value="">Pilih Jenis</option>
                                        {jenisOptions.map(opt => <option key={opt.value} value={opt.value}>{opt.label}</option>)}
                                    </select>
                                    {errors.jenis && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.jenis}</p>}
                                </div>

                                <div className="space-y-2 text-left">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Status Operasional</label>
                                    <div className="flex gap-4">
                                        <button
                                            type="button"
                                            onClick={() => setData('status_aktif', true)}
                                            className={`flex-1 flex items-center justify-center gap-2 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all border-2 ${data.status_aktif ? 'bg-green-50 border-green-500 text-green-700' : 'bg-white border-gray-100 text-gray-400'}`}
                                        >
                                            <CheckCircle className="w-4 h-4" /> AKTIF
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setData('status_aktif', false)}
                                            className={`flex-1 flex items-center justify-center gap-2 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all border-2 ${!data.status_aktif ? 'bg-red-50 border-red-500 text-red-700' : 'bg-white border-gray-100 text-gray-400'}`}
                                        >
                                            <X className="w-4 h-4" /> TUTUP
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div className="space-y-2 text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Lengkap</label>
                                <div className="relative group">
                                    <MapPin className="absolute left-4 top-4 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                    <textarea
                                        value={data.alamat}
                                        onChange={e => setData('alamat', e.target.value)}
                                        rows="3"
                                        className={`w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 ${errors.alamat ? 'focus:ring-red-500/10' : 'focus:ring-green-500/10'} transition-all resize-none`}
                                        placeholder="Contoh: Jl. Raya Cibatu No. 123..."
                                    ></textarea>
                                </div>
                                {errors.alamat && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.alamat}</p>}
                            </div>
                        </div>
                    </div>

                    {/* Location & Details */}
                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 text-left">
                        <div className="flex items-center gap-4 mb-8 text-left">
                            <div className="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-left">
                                <Info className="w-6 h-6 text-blue-600" />
                            </div>
                            <div className="text-left">
                                <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter text-left">Detail & Kontak</h3>
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none text-left">Informasi Tambahan Fasilitas</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2 text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nomor Kontak</label>
                                <div className="relative group">
                                    <Phone className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                    <input
                                        type="text"
                                        value={data.kontak}
                                        onChange={e => setData('kontak', e.target.value)}
                                        className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all"
                                        placeholder="0812..."
                                    />
                                </div>
                            </div>

                            <div className="space-y-2 text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jam Operasional</label>
                                <div className="relative group">
                                    <Clock className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                    <input
                                        type="text"
                                        value={data.jam_operasional}
                                        onChange={e => setData('jam_operasional', e.target.value)}
                                        className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all"
                                        placeholder="Contoh: 08:00 - 16:00"
                                    />
                                </div>
                            </div>
                        </div>

                        <div className="mt-6 space-y-2 text-left">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Deskripsi / Keterangan</label>
                            <div className="relative group text-left">
                                <FileText className="absolute left-4 top-4 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                <textarea
                                    value={data.deskripsi}
                                    onChange={e => setData('deskripsi', e.target.value)}
                                    rows="4"
                                    className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all resize-none text-left"
                                    placeholder="Ceritakan tentang fasilitas ini..."
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right Side: Photo & Territory */}
                <div className="space-y-6">
                    {/* Photo Upload */}
                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 text-left">
                        <div className="flex items-center gap-4 mb-6">
                            <div className="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                                <ImageIcon className="w-5 h-5 text-purple-600" />
                            </div>
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Foto Fasilitas</h3>
                        </div>

                        <div className="space-y-4">
                            <div className="aspect-video bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden relative group">
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
                        </div>
                    </div>

                    {/* Wilayah */}
                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 text-left">
                        <div className="flex items-center gap-4 mb-6">
                            <div className="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                                <MapPin className="w-5 h-5 text-red-600" />
                            </div>
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Wilayah Kerja</h3>
                        </div>

                        <div className="space-y-4">
                            <div className="space-y-1.5 text-left">
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Dusun</label>
                                <select value={data.dusun_id} onChange={e => setData('dusun_id', e.target.value)} className="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 focus:ring-4 focus:ring-red-500/10 transition-all appearance-none">
                                    <option value="">Semua Dusun</option>
                                    {wilayah.dusun?.map(d => <option key={d.id} value={d.id}>{d.nama.toUpperCase()}</option>)}
                                </select>
                            </div>
                            {/* Tambahkan RW/RT jika diperlukan, tapi biasanya fasilitas di Dusun sudah cukup */}
                        </div>
                    </div>

                    {/* Submit Button */}
                    <button
                        type="submit"
                        disabled={processing}
                        className="w-full flex items-center justify-center gap-3 px-8 py-6 bg-green-600 text-white rounded-[2rem] font-black uppercase tracking-widest text-[11px] shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50"
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
                        className="w-full flex items-center justify-center gap-3 px-8 py-5 bg-white text-gray-400 rounded-[2rem] font-black uppercase tracking-widest text-[11px] border-2 border-gray-100 hover:bg-gray-50 transition-all"
                    >
                        <X className="w-4 h-4" />
                        BATALKAN
                    </Link>
                </div>
            </div>
        </form>
    );
}
