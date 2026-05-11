import React from 'react';
import { useForm } from '@inertiajs/react';
import { 
    Store, User, MapPin, Phone, 
    Save, X, Image as ImageIcon,
    FileText, CheckCircle, Info,
    Star, ShieldCheck, Mail, Calendar,
    Users, LayoutGrid
} from 'lucide-react';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';

export default function UmkmForm({ umkm = null, jenisOptions = [], wilayah = {}, isEdit = false }) {
    const { data, setData, post, processing, errors, clearErrors } = useForm({
        _method: isEdit ? 'PUT' : 'POST',
        nama_usaha: umkm?.nama_usaha ?? '',
        nama_pemilik: umkm?.nama_pemilik ?? '',
        nik_pemilik: umkm?.nik_pemilik ?? '',
        alamat_usaha: umkm?.alamat_usaha ?? '',
        dusun_id: umkm?.dusun_id ?? '',
        rw_id: umkm?.rw_id ?? '',
        rt_id: umkm?.rt_id ?? '',
        no_telepon: umkm?.no_telepon ?? '',
        email: umkm?.email ?? '',
        jenis_usaha: umkm?.jenis_usaha ?? '',
        deskripsi_usaha: umkm?.deskripsi_usaha ?? '',
        jumlah_karyawan: umkm?.jumlah_karyawan ?? 0,
        status_usaha: umkm?.status_usaha ?? 'aktif',
        tanggal_berdiri: umkm?.tanggal_berdiri ? new Date(umkm.tanggal_berdiri).toISOString().split('T')[0] : '',
        is_unggulan: umkm ? !!umkm.is_unggulan : false,
        is_verified: umkm ? !!umkm.is_verified : false,
        foto_usaha: [], // Array for new uploads
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        const routeName = isEdit ? route('umkm.update', umkm.id) : route('umkm.store');
        
        post(routeName, {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL!',
                    text: `Data UMKM telah ${isEdit ? 'diperbarui' : 'ditambahkan'}.`,
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-3xl' }
                });
            },
        });
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6 text-left">
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 text-left">
                {/* Left Side: Basic Info & Details */}
                <div className="lg:col-span-2 space-y-6 text-left">
                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 text-left">
                        <div className="flex items-center gap-4 mb-8 text-left">
                            <div className="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-left">
                                <Store className="w-6 h-6 text-green-600" />
                            </div>
                            <div className="text-left">
                                <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter text-left">Informasi Usaha</h3>
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none text-left">Data Dasar & Legalitas Usaha</p>
                            </div>
                        </div>

                        <div className="space-y-6 text-left">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                                <div className="space-y-2 text-left">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Nama Usaha</label>
                                    <div className="relative group text-left">
                                        <Store className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                        <input
                                            type="text"
                                            value={data.nama_usaha}
                                            onChange={e => setData('nama_usaha', e.target.value)}
                                            className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all text-left"
                                            placeholder="Nama UMKM..."
                                        />
                                    </div>
                                    {errors.nama_usaha && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.nama_usaha}</p>}
                                </div>

                                <div className="space-y-2 text-left">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Jenis Usaha</label>
                                    <div className="relative group text-left">
                                        <LayoutGrid className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                        <select
                                            value={data.jenis_usaha}
                                            onChange={e => setData('jenis_usaha', e.target.value)}
                                            className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all appearance-none cursor-pointer text-left"
                                        >
                                            <option value="">Pilih Jenis</option>
                                            {jenisOptions.map(opt => <option key={opt.value} value={opt.value}>{opt.label}</option>)}
                                        </select>
                                    </div>
                                    {errors.jenis_usaha && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.jenis_usaha}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                                <div className="space-y-2 text-left">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Nama Pemilik</label>
                                    <div className="relative group text-left">
                                        <User className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                        <input
                                            type="text"
                                            value={data.nama_pemilik}
                                            onChange={e => setData('nama_pemilik', e.target.value)}
                                            className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all text-left"
                                            placeholder="Nama Pemilik Usaha..."
                                        />
                                    </div>
                                    {errors.nama_pemilik && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.nama_pemilik}</p>}
                                </div>

                                <div className="space-y-2 text-left">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">NIK Pemilik</label>
                                    <div className="relative group text-left">
                                        <ShieldCheck className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                        <input
                                            type="text"
                                            value={data.nik_pemilik}
                                            onChange={e => setData('nik_pemilik', e.target.value)}
                                            className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all text-left"
                                            placeholder="NIK (Opsional)..."
                                            maxLength={16}
                                        />
                                    </div>
                                    {errors.nik_pemilik && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.nik_pemilik}</p>}
                                </div>
                            </div>

                            <div className="space-y-2 text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Alamat Usaha</label>
                                <div className="relative group text-left">
                                    <MapPin className="absolute left-4 top-4 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                    <textarea
                                        value={data.alamat_usaha}
                                        onChange={e => setData('alamat_usaha', e.target.value)}
                                        rows="2"
                                        className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all resize-none text-left"
                                        placeholder="Alamat lengkap lokasi usaha..."
                                    ></textarea>
                                </div>
                                {errors.alamat_usaha && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.alamat_usaha}</p>}
                            </div>
                        </div>
                    </div>

                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 text-left">
                        <div className="flex items-center gap-4 mb-8 text-left">
                            <div className="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-left">
                                <Info className="w-6 h-6 text-blue-600" />
                            </div>
                            <div className="text-left">
                                <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter text-left">Operasional & Kontak</h3>
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none text-left">Detail Karyawan & Kontak Usaha</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
                            <div className="space-y-2 text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Jumlah Karyawan</label>
                                <div className="relative group text-left">
                                    <Users className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                    <input
                                        type="number"
                                        value={data.jumlah_karyawan}
                                        onChange={e => setData('jumlah_karyawan', e.target.value)}
                                        className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all text-left"
                                        min="0"
                                    />
                                </div>
                                {errors.jumlah_karyawan && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.jumlah_karyawan}</p>}
                            </div>

                            <div className="space-y-2 text-left text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Tanggal Berdiri</label>
                                <div className="relative group text-left">
                                    <Calendar className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                    <input
                                        type="date"
                                        value={data.tanggal_berdiri}
                                        onChange={e => setData('tanggal_berdiri', e.target.value)}
                                        className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all text-left"
                                    />
                                </div>
                            </div>

                            <div className="space-y-2 text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Status Usaha</label>
                                <select
                                    value={data.status_usaha}
                                    onChange={e => setData('status_usaha', e.target.value)}
                                    className="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all appearance-none cursor-pointer text-left"
                                >
                                    <option value="aktif">Aktif</option>
                                    <option value="tutup">Tutup</option>
                                    <option value="pindah">Pindah</option>
                                </select>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 text-left">
                            <div className="space-y-2 text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Nomor WhatsApp / Telepon</label>
                                <div className="relative group text-left">
                                    <Phone className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                    <input
                                        type="text"
                                        value={data.no_telepon}
                                        onChange={e => setData('no_telepon', e.target.value)}
                                        className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all text-left"
                                        placeholder="0812..."
                                    />
                                </div>
                            </div>

                            <div className="space-y-2 text-left text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Email Bisnis</label>
                                <div className="relative group text-left text-left">
                                    <Mail className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                    <input
                                        type="email"
                                        value={data.email}
                                        onChange={e => setData('email', e.target.value)}
                                        className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all text-left"
                                        placeholder="email@bisnis.com"
                                    />
                                </div>
                            </div>
                        </div>

                        <div className="mt-6 space-y-2 text-left">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Deskripsi Usaha & Produk</label>
                            <div className="relative group text-left">
                                <FileText className="absolute left-4 top-4 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                <textarea
                                    value={data.deskripsi_usaha}
                                    onChange={e => setData('deskripsi_usaha', e.target.value)}
                                    rows="4"
                                    className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all resize-none text-left"
                                    placeholder="Jelaskan produk atau jasa yang ditawarkan..."
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right Side: Territory & Flags */}
                <div className="space-y-6 text-left">
                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 text-left">
                        <div className="flex items-center gap-4 mb-6 text-left">
                            <div className="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-left">
                                <MapPin className="w-5 h-5 text-red-600" />
                            </div>
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter text-left">Lokasi Wilayah</h3>
                        </div>

                        <div className="space-y-4 text-left">
                            <div className="space-y-1.5 text-left">
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Dusun</label>
                                <select 
                                    value={data.dusun_id} 
                                    onChange={e => setData('dusun_id', e.target.value)} 
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 focus:ring-4 focus:ring-red-500/10 transition-all appearance-none text-left"
                                >
                                    <option value="">Semua Dusun</option>
                                    {wilayah.dusun?.map(d => <option key={d.id} value={d.id}>{d.nama.toUpperCase()}</option>)}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 text-left">
                        <div className="flex items-center gap-4 mb-6 text-left text-left">
                            <div className="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-left text-left text-left">
                                <Star className="w-5 h-5 text-orange-600" />
                            </div>
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter text-left">Atribut Khusus</h3>
                        </div>

                        <div className="space-y-3 text-left">
                            <button
                                type="button"
                                onClick={() => setData('is_unggulan', !data.is_unggulan)}
                                className={`w-full flex items-center justify-between p-4 rounded-2xl border-2 transition-all ${data.is_unggulan ? 'bg-orange-50 border-orange-500 text-orange-700' : 'bg-white border-gray-50 text-gray-400'}`}
                            >
                                <div className="flex items-center gap-3 text-left">
                                    <Star className={cn("w-5 h-5", data.is_unggulan && "fill-current")} />
                                    <span className="text-[10px] font-black uppercase tracking-widest text-left">UMKM Unggulan</span>
                                </div>
                                <div className={cn("w-5 h-5 rounded-full border-2 flex items-center justify-center", data.is_unggulan ? "border-orange-500 bg-orange-500" : "border-gray-200")}>
                                    {data.is_unggulan && <CheckCircle className="w-3 h-3 text-white" />}
                                </div>
                            </button>

                            <button
                                type="button"
                                onClick={() => setData('is_verified', !data.is_verified)}
                                className={`w-full flex items-center justify-between p-4 rounded-2xl border-2 transition-all ${data.is_verified ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-50 text-gray-400'}`}
                            >
                                <div className="flex items-center gap-3 text-left">
                                    <ShieldCheck className="w-5 h-5" />
                                    <span className="text-[10px] font-black uppercase tracking-widest text-left">Terverifikasi</span>
                                </div>
                                <div className={cn("w-5 h-5 rounded-full border-2 flex items-center justify-center text-left", data.is_verified ? "border-blue-500 bg-blue-500" : "border-gray-200")}>
                                    {data.is_verified && <CheckCircle className="w-3 h-3 text-white text-left" />}
                                </div>
                            </button>
                        </div>
                    </div>

                    {/* Photo Upload - simplified for multiple files if needed, but here we just handle the input */}
                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 text-left">
                        <div className="flex items-center gap-4 mb-6 text-left">
                            <div className="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-left">
                                <ImageIcon className="w-5 h-5 text-purple-600" />
                            </div>
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter text-left">Foto Galeri</h3>
                        </div>

                        <div className="space-y-4 text-left">
                            <div className="aspect-square bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden relative group text-left">
                                {data.foto_usaha.length > 0 ? (
                                    <div className="grid grid-cols-2 w-full h-full text-left">
                                        {Array.from(data.foto_usaha).slice(0, 4).map((file, i) => (
                                            <img key={i} src={URL.createObjectURL(file)} className="w-full h-full object-cover border-none" />
                                        ))}
                                    </div>
                                ) : (umkm?.foto_usaha && umkm.foto_usaha.length > 0 && (
                                    <div className="grid grid-cols-2 w-full h-full text-left">
                                        {umkm.foto_usaha.slice(0, 4).map((foto, i) => (
                                            <img key={i} src={`/storage/${foto}`} className="w-full h-full object-cover border-none text-left" />
                                        ))}
                                    </div>
                                ))}
                                
                                <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-left">
                                    <label className="px-4 py-2 bg-white rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer hover:bg-gray-100 transition-colors text-left">
                                        Pilih Foto
                                        <input type="file" multiple className="hidden text-left" onChange={e => setData('foto_usaha', e.target.files)} />
                                    </label>
                                </div>

                                {!data.foto_usaha.length && (!umkm?.foto_usaha || umkm.foto_usaha.length === 0) && (
                                    <div className="text-center p-6 text-left">
                                        <ImageIcon className="w-8 h-8 text-gray-300 mx-auto mb-2 text-left" />
                                        <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-tight text-left">Unggah Foto Usaha</p>
                                        <input type="file" multiple className="absolute inset-0 opacity-0 cursor-pointer text-left" onChange={e => setData('foto_usaha', e.target.files)} />
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className="pt-4 text-left">
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full flex items-center justify-center gap-3 px-8 py-6 bg-green-600 text-white rounded-[2rem] font-black uppercase tracking-widest text-[11px] shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 text-left"
                        >
                            {processing ? "MEMPROSES..." : (isEdit ? "PERBARUI DATA" : "SIMPAN DATA UMKM")}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    );
}
