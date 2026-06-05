import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { MapPin, ArrowLeft, Layers, ShieldCheck, Map, User, Ruler, History, Plus, X } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Show({ auth, tanahDiDesa, mutasi }) {
    const [showMutasiForm, setShowMutasiForm] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        pemilik_lama: tanahDiDesa.nama_pemilik,
        pemilik_baru: '',
        tanggal_mutasi: '',
        keterangan: '',
        tempat_lahir_baru: '',
        tanggal_lahir_baru: '',
        status_kepemilikan_baru: '',
        no_sertifikat_baru: '',
        tanggal_penerbitan_sertifikat_baru: '',
    });

    const formatNum = (num) => new Intl.NumberFormat('id-ID').format(num);
    const formatDate = (date) => date ? new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-';

    const handleMutasiSubmit = (e) => {
        e.preventDefault();
        
        post(route('sekretariat.tanah-di-desa.mutasi', tanahDiDesa.id), {
            preserveScroll: true,
            onSuccess: () => {
                setShowMutasiForm(false);
                reset('pemilik_baru', 'tanggal_mutasi', 'keterangan', 'tempat_lahir_baru', 'tanggal_lahir_baru', 'status_kepemilikan_baru', 'no_sertifikat_baru', 'tanggal_penerbitan_sertifikat_baru');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Mutasi tanah berhasil dicatat.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            },
            onError: () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Pastikan form mutasi diisi dengan benar.',
                });
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Detail Tanah di Desa">
            <Head title="Detail Tanah di Desa" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader
                    icon={MapPin}
                    title="Detail Tanah di Desa"
                    subtitle="Informasi lengkap kepemilikan tanah dan riwayat mutasi"
                    actions={[
                        { label: 'Kembali', icon: ArrowLeft, href: route('sekretariat.tanah-di-desa.index'), variant: 'ghost' },
                    ]}
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        {/* Kolom Kiri - Detail Tanah */}
                        <div className="lg:col-span-2 space-y-6">
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                                <div className="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                                    <div className="p-2 bg-blue-50 rounded-xl text-blue-600">
                                        <User className="w-5 h-5" />
                                    </div>
                                    <h3 className="font-bold text-gray-900">Identitas & Legalitas</h3>
                                </div>
                                <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                                    <div>
                                        <span className="text-gray-500 block mb-1">Nama Pemilik (Saat Ini)</span>
                                        <strong className="text-gray-900 text-base">{tanahDiDesa.nama_pemilik}</strong>
                                    </div>
                                    <div>
                                        <span className="text-gray-500 block mb-1">NOP</span>
                                        <span className="text-gray-900 font-medium">{tanahDiDesa.nop || '-'}</span>
                                    </div>
                                    <div>
                                        <span className="text-gray-500 block mb-1">Tempat, Tgl Lahir / Berdiri</span>
                                        <span className="text-gray-900 font-medium">
                                            {tanahDiDesa.tempat_lahir_berdiri || '-'}, {formatDate(tanahDiDesa.tanggal_lahir_berdiri)}
                                        </span>
                                    </div>
                                    <div>
                                        <span className="text-gray-500 block mb-1">Status Kepemilikan</span>
                                        <span className="text-gray-900 font-medium">{tanahDiDesa.status_kepemilikan}</span>
                                    </div>
                                    <div>
                                        <span className="text-gray-500 block mb-1">No Sertifikat & Tanggal</span>
                                        <span className="text-gray-900 font-medium">
                                            {tanahDiDesa.no_sertifikat || '-'} / {formatDate(tanahDiDesa.tanggal_penerbitan_sertifikat)}
                                        </span>
                                    </div>
                                    <div>
                                        <span className="text-gray-500 block mb-1">Tanggal Perolehan</span>
                                        <span className="text-gray-900 font-medium">{formatDate(tanahDiDesa.tanggal_perolehan)}</span>
                                    </div>
                                </div>
                            </div>

                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                                <div className="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                                    <div className="p-2 bg-emerald-50 rounded-xl text-emerald-600">
                                        <Ruler className="w-5 h-5" />
                                    </div>
                                    <h3 className="font-bold text-gray-900">Total Luas: {formatNum(tanahDiDesa.total_luas)} m²</h3>
                                </div>
                                <div className="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div className="bg-gray-50 p-4 rounded-2xl">
                                        <span className="text-gray-500 block mb-1">Sawah</span>
                                        <strong className="text-gray-900">{formatNum(tanahDiDesa.luas_sawah)} m²</strong>
                                    </div>
                                    <div className="bg-gray-50 p-4 rounded-2xl">
                                        <span className="text-gray-500 block mb-1">Tegalan</span>
                                        <strong className="text-gray-900">{formatNum(tanahDiDesa.luas_tegalan)} m²</strong>
                                    </div>
                                    <div className="bg-gray-50 p-4 rounded-2xl">
                                        <span className="text-gray-500 block mb-1">Perumahan</span>
                                        <strong className="text-gray-900">{formatNum(tanahDiDesa.luas_perumahan)} m²</strong>
                                    </div>
                                    <div className="bg-gray-50 p-4 rounded-2xl">
                                        <span className="text-gray-500 block mb-1">Fasilitas Umum</span>
                                        <strong className="text-gray-900">{formatNum(tanahDiDesa.luas_fasilitas_umum)} m²</strong>
                                    </div>
                                </div>
                            </div>

                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                                <div className="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                                    <div className="p-2 bg-orange-50 rounded-xl text-orange-600">
                                        <Map className="w-5 h-5" />
                                    </div>
                                    <h3 className="font-bold text-gray-900">Lokasi & Batas</h3>
                                </div>
                                <div className="p-6">
                                    <div className="mb-4">
                                        <span className="text-gray-500 block mb-1 text-sm">Lokasi / Letak Tanah</span>
                                        <p className="text-gray-900 font-medium">{tanahDiDesa.lokasi_tanah || '-'}</p>
                                    </div>
                                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span className="text-gray-500 block mb-1">Utara</span>
                                            <span className="text-gray-900 font-medium">{tanahDiDesa.batas_utara || '-'}</span>
                                        </div>
                                        <div>
                                            <span className="text-gray-500 block mb-1">Timur</span>
                                            <span className="text-gray-900 font-medium">{tanahDiDesa.batas_timur || '-'}</span>
                                        </div>
                                        <div>
                                            <span className="text-gray-500 block mb-1">Selatan</span>
                                            <span className="text-gray-900 font-medium">{tanahDiDesa.batas_selatan || '-'}</span>
                                        </div>
                                        <div>
                                            <span className="text-gray-500 block mb-1">Barat</span>
                                            <span className="text-gray-900 font-medium">{tanahDiDesa.batas_barat || '-'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {/* Kolom Kanan - Mutasi */}
                        <div className="space-y-6">
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                                <div className="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                                    <div className="flex items-center gap-3">
                                        <div className="p-2 bg-purple-50 rounded-xl text-purple-600">
                                            <History className="w-5 h-5" />
                                        </div>
                                        <h3 className="font-bold text-gray-900">Riwayat Mutasi</h3>
                                    </div>
                                    <button 
                                        onClick={() => setShowMutasiForm(!showMutasiForm)}
                                        className="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white font-bold text-xs rounded-xl hover:bg-purple-700 transition-colors shadow-sm"
                                        title="Catat Mutasi Baru"
                                    >
                                        <Plus className="w-4 h-4" />
                                        Tambah Mutasi
                                    </button>
                                </div>
                                
                                {showMutasiForm && (
                                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-gray-900/40 backdrop-blur-sm animate-in fade-in duration-200">
                                        <div className="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
                                            
                                            {/* Modal Header */}
                                            <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-purple-50/50">
                                                <h4 className="font-bold text-purple-900 flex items-center gap-2">
                                                    <History className="w-5 h-5 text-purple-600" />
                                                    Form Mutasi Tanah
                                                </h4>
                                                <button 
                                                    onClick={() => setShowMutasiForm(false)}
                                                    className="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors"
                                                >
                                                    <X className="w-5 h-5" />
                                                </button>
                                            </div>

                                            {/* Modal Body */}
                                            <div className="p-6 overflow-y-auto">
                                                <form id="mutasiForm" onSubmit={handleMutasiSubmit} className="space-y-6">
                                                    
                                                    <div className="space-y-4">
                                                        <FormField.Input 
                                                            label="Pemilik Lama" 
                                                            name="pemilik_lama" 
                                                            value={data.pemilik_lama} 
                                                            onChange={e => setData('pemilik_lama', e.target.value)} 
                                                            error={errors.pemilik_lama}
                                                            required
                                                        />
                                                        
                                                        <FormField.Input 
                                                            label="Pemilik Baru (Akan update data utama)" 
                                                            name="pemilik_baru" 
                                                            value={data.pemilik_baru} 
                                                            onChange={e => setData('pemilik_baru', e.target.value)} 
                                                            error={errors.pemilik_baru}
                                                            required
                                                        />
                                                    </div>
                                                    
                                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                        <FormField.Input 
                                                            label="Tmpt Lahir / Berdiri (Pemilik Baru)" 
                                                            name="tempat_lahir_baru" 
                                                            value={data.tempat_lahir_baru} 
                                                            onChange={e => setData('tempat_lahir_baru', e.target.value)} 
                                                            error={errors.tempat_lahir_baru}
                                                            placeholder="Opsional"
                                                        />
                                                        <FormField.Input 
                                                            label="Tgl Lahir / Berdiri (Pemilik Baru)" 
                                                            name="tanggal_lahir_baru" 
                                                            type="date"
                                                            value={data.tanggal_lahir_baru} 
                                                            onChange={e => setData('tanggal_lahir_baru', e.target.value)} 
                                                            error={errors.tanggal_lahir_baru}
                                                        />
                                                    </div>

                                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                        <FormField.Input 
                                                            label="Status Kepemilikan Baru" 
                                                            name="status_kepemilikan_baru" 
                                                            value={data.status_kepemilikan_baru} 
                                                            onChange={e => setData('status_kepemilikan_baru', e.target.value)} 
                                                            error={errors.status_kepemilikan_baru}
                                                            placeholder="Opsional, jika berubah"
                                                        />
                                                        <FormField.Input 
                                                            label="Nomor Sertifikat Baru" 
                                                            name="no_sertifikat_baru" 
                                                            value={data.no_sertifikat_baru} 
                                                            onChange={e => setData('no_sertifikat_baru', e.target.value)} 
                                                            error={errors.no_sertifikat_baru}
                                                            placeholder="Opsional, jika ada"
                                                        />
                                                    </div>

                                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                        <FormField.Input 
                                                            label="Tgl Mutasi / Tgl Perolehan" 
                                                            name="tanggal_mutasi" 
                                                            type="date"
                                                            value={data.tanggal_mutasi} 
                                                            onChange={e => setData('tanggal_mutasi', e.target.value)} 
                                                            error={errors.tanggal_mutasi}
                                                            required
                                                        />
                                                        <FormField.Input 
                                                            label="Tgl Terbit Sertifikat" 
                                                            name="tanggal_penerbitan_sertifikat_baru" 
                                                            type="date"
                                                            value={data.tanggal_penerbitan_sertifikat_baru} 
                                                            onChange={e => setData('tanggal_penerbitan_sertifikat_baru', e.target.value)} 
                                                            error={errors.tanggal_penerbitan_sertifikat_baru}
                                                        />
                                                    </div>
                                                    
                                                    <FormField.Textarea 
                                                        label="Dasar Mutasi & Keterangan (Cth: AJB No. 123/2026, Hibah, dll)" 
                                                        name="keterangan" 
                                                        value={data.keterangan} 
                                                        onChange={e => setData('keterangan', e.target.value)} 
                                                        error={errors.keterangan}
                                                        rows={2}
                                                        required
                                                    />
                                                </form>
                                            </div>

                                            {/* Modal Footer */}
                                            <div className="px-6 py-4 border-t border-gray-100 bg-gray-50 flex gap-3 justify-end">
                                                <button 
                                                    type="button"
                                                    onClick={() => setShowMutasiForm(false)}
                                                    className="px-6 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-100 transition-colors"
                                                >
                                                    Batal
                                                </button>
                                                <button 
                                                    type="submit"
                                                    form="mutasiForm"
                                                    disabled={processing}
                                                    className="px-6 py-2.5 text-sm font-bold text-white bg-purple-600 rounded-xl hover:bg-purple-700 transition-colors disabled:opacity-50 shadow-lg shadow-purple-200"
                                                >
                                                    {processing ? 'Menyimpan...' : 'Simpan Mutasi'}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                <div className="p-6">
                                    {mutasi.length === 0 ? (
                                        <div className="text-center py-6 text-gray-500 text-sm">
                                            Belum ada riwayat mutasi.
                                        </div>
                                    ) : (
                                        <div className="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 before:to-transparent">
                                            {mutasi.map((item, idx) => (
                                                <div key={item.id} className="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                                    <div className="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-purple-100 text-purple-600 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                                                        <History className="w-4 h-4" />
                                                    </div>
                                                    <div className="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                                                        <div className="flex items-center justify-between mb-1">
                                                            <div className="font-bold text-gray-900 text-sm">{formatDate(item.tanggal_mutasi)}</div>
                                                        </div>
                                                        <div className="text-xs text-gray-500 mb-2">
                                                            Dari: <span className="font-medium text-gray-700">{item.pemilik_lama}</span><br/>
                                                            Ke: <span className="font-medium text-gray-700">{item.pemilik_baru}</span>
                                                        </div>
                                                        <div className="text-sm text-gray-700 bg-gray-50 p-2 rounded-lg">
                                                            {item.keterangan || 'Tanpa keterangan'}
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                    </div>
            </div>
        </AuthenticatedLayout>
    );
}
