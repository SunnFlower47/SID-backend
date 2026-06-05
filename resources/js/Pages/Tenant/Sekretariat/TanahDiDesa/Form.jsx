import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { MapPin, Save, ArrowLeft, Layers, ShieldCheck, Map, User, Ruler } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Form({ auth, tanahDiDesa }) {
    const isEdit = !!tanahDiDesa;

    const { data, setData, post, put, processing, errors } = useForm({
        nop: tanahDiDesa?.nop || '',
        nama_pemilik: tanahDiDesa?.nama_pemilik || '',
        tempat_lahir_berdiri: tanahDiDesa?.tempat_lahir_berdiri || '',
        tanggal_lahir_berdiri: tanahDiDesa?.tanggal_lahir_berdiri || '',
        status_kepemilikan: tanahDiDesa?.status_kepemilikan || '',
        tanggal_perolehan: tanahDiDesa?.tanggal_perolehan || '',
        no_sertifikat: tanahDiDesa?.no_sertifikat || '',
        tanggal_penerbitan_sertifikat: tanahDiDesa?.tanggal_penerbitan_sertifikat || '',
        no_buku_c: tanahDiDesa?.no_buku_c || '',
        no_persil: tanahDiDesa?.no_persil || '',
        no_kelas: tanahDiDesa?.no_kelas || '',
        
        luas_sawah: tanahDiDesa?.luas_sawah || '0',
        luas_tegalan: tanahDiDesa?.luas_tegalan || '0',
        luas_kebun: tanahDiDesa?.luas_kebun || '0',
        luas_perumahan: tanahDiDesa?.luas_perumahan || '0',
        luas_industri: tanahDiDesa?.luas_industri || '0',
        luas_fasilitas_umum: tanahDiDesa?.luas_fasilitas_umum || '0',
        luas_lain_lain: tanahDiDesa?.luas_lain_lain || '0',
        
        lokasi_tanah: tanahDiDesa?.lokasi_tanah || '',
        batas_utara: tanahDiDesa?.batas_utara || '',
        batas_timur: tanahDiDesa?.batas_timur || '',
        batas_selatan: tanahDiDesa?.batas_selatan || '',
        batas_barat: tanahDiDesa?.batas_barat || '',
        
        keterangan: tanahDiDesa?.keterangan || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        
        const action = isEdit ? put : post;
        const routeName = isEdit 
            ? route('sekretariat.tanah-di-desa.update', tanahDiDesa.id)
            : route('sekretariat.tanah-di-desa.store');

        action(routeName, {
            preserveScroll: true,
            onError: (err) => {
                if(Object.keys(err).length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terdapat kesalahan pada input Anda. Silakan periksa kembali.',
                    });
                }
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title={isEdit ? "Edit Tanah di Desa" : "Tambah Tanah di Desa"}>
            <Head title={isEdit ? "Edit Tanah di Desa" : "Tambah Tanah di Desa"} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader
                    icon={MapPin}
                    title={isEdit ? "Edit Data Tanah di Desa" : "Tambah Data Tanah di Desa"}
                    subtitle={isEdit ? "Ubah data kepemilikan dan rincian tanah di desa" : "Tambahkan catatan baru buku tanah di desa"}
                    actions={[
                        { label: 'Kembali', icon: ArrowLeft, href: route('sekretariat.tanah-di-desa.index'), variant: 'ghost' },
                    ]}
                />

                <form onSubmit={handleSubmit} className="space-y-6">
                        
                        {/* Section 1: Identitas Pemilik */}
                        <FormCard title="1. Data Identitas dan Pemilik" icon={User}>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField.Input 
                                    label="NOP (Nomor Objek Pajak)" 
                                    name="nop" 
                                    value={data.nop} 
                                    onChange={e => setData('nop', e.target.value)} 
                                    error={errors.nop}
                                    placeholder="Opsional, untuk auto-fill Pajak PBB"
                                />
                                <FormField.Input 
                                    label="Nama Pemegang Hak / Badan Hukum" 
                                    name="nama_pemilik" 
                                    value={data.nama_pemilik} 
                                    onChange={e => setData('nama_pemilik', e.target.value)} 
                                    error={errors.nama_pemilik}
                                    required
                                />
                                <FormField.Input 
                                    label="Tempat Lahir / Berdiri Badan Hukum" 
                                    name="tempat_lahir_berdiri" 
                                    value={data.tempat_lahir_berdiri} 
                                    onChange={e => setData('tempat_lahir_berdiri', e.target.value)} 
                                    error={errors.tempat_lahir_berdiri}
                                />
                                <FormField.Input 
                                    label="Tanggal Lahir / Berdiri Badan Hukum" 
                                    name="tanggal_lahir_berdiri" 
                                    type="date"
                                    value={data.tanggal_lahir_berdiri} 
                                    onChange={e => setData('tanggal_lahir_berdiri', e.target.value)} 
                                    error={errors.tanggal_lahir_berdiri}
                                />
                            </div>
                        </FormCard>

                        {/* Section 2: Status Legalitas */}
                        <FormCard title="2. Status Legalitas dan Riwayat" icon={ShieldCheck}>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField.Input 
                                    label="Kepemilikan (Misal: Hak Milik, Yasan)" 
                                    name="status_kepemilikan" 
                                    value={data.status_kepemilikan} 
                                    onChange={e => setData('status_kepemilikan', e.target.value)} 
                                    error={errors.status_kepemilikan}
                                    required
                                />
                                <FormField.Input 
                                    label="Tanggal Perolehan" 
                                    name="tanggal_perolehan" 
                                    type="date"
                                    value={data.tanggal_perolehan} 
                                    onChange={e => setData('tanggal_perolehan', e.target.value)} 
                                    error={errors.tanggal_perolehan}
                                />
                                <FormField.Input 
                                    label="Nomor Sertifikat (Kosong jika belum)" 
                                    name="no_sertifikat" 
                                    value={data.no_sertifikat} 
                                    onChange={e => setData('no_sertifikat', e.target.value)} 
                                    error={errors.no_sertifikat}
                                />
                                <FormField.Input 
                                    label="Tanggal Penerbitan Sertifikat" 
                                    name="tanggal_penerbitan_sertifikat" 
                                    type="date"
                                    value={data.tanggal_penerbitan_sertifikat} 
                                    onChange={e => setData('tanggal_penerbitan_sertifikat', e.target.value)} 
                                    error={errors.tanggal_penerbitan_sertifikat}
                                />
                                <FormField.Input 
                                    label="Nomor Buku C / Buku Desa" 
                                    name="no_buku_c" 
                                    value={data.no_buku_c} 
                                    onChange={e => setData('no_buku_c', e.target.value)} 
                                    error={errors.no_buku_c}
                                />
                                <FormField.Input 
                                    label="Nomor Persil" 
                                    name="no_persil" 
                                    value={data.no_persil} 
                                    onChange={e => setData('no_persil', e.target.value)} 
                                    error={errors.no_persil}
                                />
                                <FormField.Input 
                                    label="Nomor Kelas" 
                                    name="no_kelas" 
                                    value={data.no_kelas} 
                                    onChange={e => setData('no_kelas', e.target.value)} 
                                    error={errors.no_kelas}
                                />
                            </div>
                        </FormCard>

                        {/* Section 3: Klasifikasi Luas Tanah */}
                        <FormCard title="3. Klasifikasi Luas Tanah (m²)" icon={Ruler}>
                            <div className="bg-blue-50 border border-blue-100 p-4 rounded-2xl mb-6">
                                <div className="flex justify-between items-center">
                                    <div>
                                        <h4 className="font-bold text-blue-900">Total Luas Tanah</h4>
                                        <p className="text-xs text-blue-700 mt-1">Total dari seluruh klasifikasi di bawah ini.</p>
                                    </div>
                                    <div className="text-2xl font-black text-blue-700">
                                        {new Intl.NumberFormat('id-ID').format(
                                            (parseFloat(data.luas_sawah) || 0) +
                                            (parseFloat(data.luas_tegalan) || 0) +
                                            (parseFloat(data.luas_kebun) || 0) +
                                            (parseFloat(data.luas_perumahan) || 0) +
                                            (parseFloat(data.luas_industri) || 0) +
                                            (parseFloat(data.luas_fasilitas_umum) || 0) +
                                            (parseFloat(data.luas_lain_lain) || 0)
                                        )} <span className="text-sm font-bold">m²</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <h4 className="col-span-full font-semibold text-gray-700 mt-2">Pertanian</h4>
                                <FormField.Input label="Sawah" name="luas_sawah" type="number" step="0.01" value={data.luas_sawah} onChange={e => setData('luas_sawah', e.target.value)} error={errors.luas_sawah} />
                                <FormField.Input label="Tegalan" name="luas_tegalan" type="number" step="0.01" value={data.luas_tegalan} onChange={e => setData('luas_tegalan', e.target.value)} error={errors.luas_tegalan} />
                                <FormField.Input label="Kebun" name="luas_kebun" type="number" step="0.01" value={data.luas_kebun} onChange={e => setData('luas_kebun', e.target.value)} error={errors.luas_kebun} />
                                
                                <h4 className="col-span-full font-semibold text-gray-700 mt-4">Non-Pertanian</h4>
                                <FormField.Input label="Perumahan" name="luas_perumahan" type="number" step="0.01" value={data.luas_perumahan} onChange={e => setData('luas_perumahan', e.target.value)} error={errors.luas_perumahan} />
                                <FormField.Input label="Industri" name="luas_industri" type="number" step="0.01" value={data.luas_industri} onChange={e => setData('luas_industri', e.target.value)} error={errors.luas_industri} />
                                <FormField.Input label="Fasilitas Umum" name="luas_fasilitas_umum" type="number" step="0.01" value={data.luas_fasilitas_umum} onChange={e => setData('luas_fasilitas_umum', e.target.value)} error={errors.luas_fasilitas_umum} />
                                <FormField.Input label="Lain-lain" name="luas_lain_lain" type="number" step="0.01" value={data.luas_lain_lain} onChange={e => setData('luas_lain_lain', e.target.value)} error={errors.luas_lain_lain} />
                            </div>
                        </FormCard>

                        {/* Section 4: Lokasi dan Batas */}
                        <FormCard title="4. Lokasi dan Batas-Batas" icon={Map}>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="col-span-full">
                                    <FormField.Textarea 
                                        label="Lokasi / Letak Tanah (RT/RW/Dusun)" 
                                        name="lokasi_tanah" 
                                        value={data.lokasi_tanah} 
                                        onChange={e => setData('lokasi_tanah', e.target.value)} 
                                        error={errors.lokasi_tanah}
                                    />
                                </div>
                                <FormField.Input label="Batas Utara" name="batas_utara" value={data.batas_utara} onChange={e => setData('batas_utara', e.target.value)} error={errors.batas_utara} />
                                <FormField.Input label="Batas Timur" name="batas_timur" value={data.batas_timur} onChange={e => setData('batas_timur', e.target.value)} error={errors.batas_timur} />
                                <FormField.Input label="Batas Selatan" name="batas_selatan" value={data.batas_selatan} onChange={e => setData('batas_selatan', e.target.value)} error={errors.batas_selatan} />
                                <FormField.Input label="Batas Barat" name="batas_barat" value={data.batas_barat} onChange={e => setData('batas_barat', e.target.value)} error={errors.batas_barat} />
                            </div>
                        </FormCard>

                        {/* Keterangan */}
                        <FormCard title="5. Keterangan" icon={Layers}>
                            <FormField.Textarea 
                                label="Keterangan / Catatan Awal" 
                                name="keterangan" 
                                value={data.keterangan} 
                                onChange={e => setData('keterangan', e.target.value)} 
                                error={errors.keterangan}
                                placeholder="Biarkan kosong jika tidak ada keterangan tambahan. Catatan mutasi akan otomatis tercetak di sini nanti."
                            />
                        </FormCard>

                        <div className="flex items-center justify-between">
                            <Link 
                                href={route('sekretariat.tanah-di-desa.index')} 
                                className="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                <ArrowLeft className="w-4 h-4 mr-2" />
                                Batal
                            </Link>
                            <button 
                                type="submit" 
                                disabled={processing}
                                className="flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                            >
                                <Save className="w-4 h-4 mr-2" />
                                {processing ? 'Menyimpan...' : 'Simpan Data'}
                            </button>
                        </div>
                    </form>
            </div>
        </AuthenticatedLayout>
    );
}
