import React from 'react';
import { useForm, Link } from '@inertiajs/react';
import { 
    Store, User, MapPin, Phone, 
    Save, X, Image as ImageIcon,
    FileText, CheckCircle, Info,
    Star, ShieldCheck, Mail, Calendar,
    Users, LayoutGrid
} from 'lucide-react';
import { FormCard, FormField } from '@/Components/Shared';
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
                    <FormCard icon={Store} title="Informasi Usaha" description="Data Dasar & Legalitas Usaha">
                        <div className="space-y-6 text-left">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                                <FormField.Input
                                    label="Nama Usaha"
                                    required
                                    value={data.nama_usaha}
                                    onChange={e => setData('nama_usaha', e.target.value)}
                                    error={errors.nama_usaha}
                                    placeholder="Nama UMKM..."
                                />
                                <FormField.Select
                                    label="Jenis Usaha"
                                    required
                                    value={data.jenis_usaha}
                                    onChange={e => setData('jenis_usaha', e.target.value)}
                                    error={errors.jenis_usaha}
                                    options={jenisOptions}
                                />
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                                <FormField.Input
                                    label="Nama Pemilik"
                                    required
                                    value={data.nama_pemilik}
                                    onChange={e => setData('nama_pemilik', e.target.value)}
                                    error={errors.nama_pemilik}
                                    placeholder="Nama Pemilik Usaha..."
                                />
                                <FormField.Input
                                    label="NIK Pemilik"
                                    value={data.nik_pemilik}
                                    onChange={e => setData('nik_pemilik', e.target.value)}
                                    error={errors.nik_pemilik}
                                    placeholder="NIK (Opsional)..."
                                    maxLength={16}
                                />
                            </div>

                            <FormField.Textarea
                                label="Alamat Usaha"
                                required
                                value={data.alamat_usaha}
                                onChange={e => setData('alamat_usaha', e.target.value)}
                                error={errors.alamat_usaha}
                                placeholder="Alamat lengkap lokasi usaha..."
                                rows={2}
                            />
                        </div>
                    </FormCard>

                    <FormCard icon={Info} title="Operasional & Kontak" description="Detail Karyawan & Kontak Usaha">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
                            <FormField.Input
                                label="Jumlah Karyawan"
                                type="number"
                                min="0"
                                value={data.jumlah_karyawan}
                                onChange={e => setData('jumlah_karyawan', e.target.value)}
                                error={errors.jumlah_karyawan}
                            />
                            <FormField.Input
                                label="Tanggal Berdiri"
                                type="date"
                                value={data.tanggal_berdiri}
                                onChange={e => setData('tanggal_berdiri', e.target.value)}
                                error={errors.tanggal_berdiri}
                            />
                            <FormField.Select
                                label="Status Usaha"
                                value={data.status_usaha}
                                onChange={e => setData('status_usaha', e.target.value)}
                                error={errors.status_usaha}
                                options={[
                                    { value: 'aktif', label: 'Aktif' },
                                    { value: 'tutup', label: 'Tutup' },
                                    { value: 'pindah', label: 'Pindah' }
                                ]}
                            />
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 text-left">
                            <FormField.Input
                                label="Nomor WhatsApp / Telepon"
                                value={data.no_telepon}
                                onChange={e => setData('no_telepon', e.target.value)}
                                error={errors.no_telepon}
                                placeholder="0812..."
                            />
                            <FormField.Input
                                label="Email Bisnis"
                                type="email"
                                value={data.email}
                                onChange={e => setData('email', e.target.value)}
                                error={errors.email}
                                placeholder="email@bisnis.com"
                            />
                        </div>

                        <div className="mt-6">
                            <FormField.Textarea
                                label="Deskripsi Usaha & Produk"
                                value={data.deskripsi_usaha}
                                onChange={e => setData('deskripsi_usaha', e.target.value)}
                                error={errors.deskripsi_usaha}
                                placeholder="Jelaskan produk atau jasa yang ditawarkan..."
                                rows={4}
                            />
                        </div>
                    </FormCard>
                </div>

                {/* Right Side: Territory & Flags */}
                <div className="space-y-6 text-left">
                    <FormCard icon={MapPin} title="Lokasi Wilayah">
                        <FormField.Select
                            label="Dusun"
                            value={data.dusun_id}
                            onChange={e => setData('dusun_id', e.target.value)}
                            error={errors.dusun_id}
                            options={[{ value: '', label: 'Semua Dusun' }, ...(wilayah.dusun?.map(d => ({ value: d.id, label: d.nama.toUpperCase() })) || [])]}
                        />
                    </FormCard>

                    <FormCard icon={Star} title="Atribut Khusus">
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
                    </FormCard>

                    <FormCard icon={ImageIcon} title="Foto Galeri">
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
                    </FormCard>

                    <div className="space-y-3">
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
                                    {isEdit ? 'PERBARUI UMKM' : 'SIMPAN UMKM'}
                                </>
                            )}
                        </button>
                        <Link
                            href={route('umkm.index')}
                            className="w-full flex items-center justify-center gap-3 px-8 py-4 bg-white text-gray-600 rounded-2xl font-black uppercase tracking-widest text-xs border border-gray-200 hover:bg-gray-50 transition-all"
                        >
                            <X className="w-4 h-4" />
                            BATALKAN
                        </Link>
                    </div>
                </div>
            </div>
        </form>
    );
}
