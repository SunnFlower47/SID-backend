import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save, MessageSquare, AlertCircle, FileText, CheckCircle, Upload } from 'lucide-react';

import { PageHeader, FormField } from '@/Components/Shared';

export default function PengaduanForm({ isEdit, pengaduan = {} }) {
    const { data, setData, post, put, processing, errors } = useForm(
        isEdit 
        ? {
            status: pengaduan.status || 'baru',
            prioritas: pengaduan.prioritas || 'rendah',
            tanggapan: pengaduan.tanggapan || '',
        } 
        : {
            nama_pelapor: '',
            nik_pelapor: '',
            telepon: '',
            email: '',
            alamat: '',
            kategori: 'infrastruktur',
            judul: '',
            deskripsi: '',
            lokasi: '',
            prioritas: 'rendah',
            foto: [], // Array of files
        }
    );

    const handleSubmit = (e) => {
        e.preventDefault();

        if (isEdit) {
            put(route('pengaduan.update', pengaduan.id), {
                preserveScroll: true
            });
        } else {
            post(route('pengaduan.store'), {
                preserveScroll: true
            });
        }
    };

    const handleFileChange = (e) => {
        const files = Array.from(e.target.files);
        setData('foto', files);
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6 sm:space-y-8 animate-in fade-in duration-700 pb-20">
            {/* Header */}
            <PageHeader
                title={isEdit ? 'Tanggapi Aduan' : 'Tambah Aduan Manual'}
                subtitle={isEdit ? `Aduan: ${pengaduan.judul}` : 'Masukkan data pelapor dan detail aduan'}
                icon={isEdit ? MessageSquare : FileText}
                actions={[
                    {
                        label: 'BATAL',
                        icon: ArrowLeft,
                        href: isEdit ? route('pengaduan.show', pengaduan.id) : route('pengaduan.index'),
                        variant: 'ghost'
                    },
                    {
                        label: processing ? 'MENYIMPAN...' : 'SIMPAN',
                        icon: Save,
                        onClick: handleSubmit,
                        disabled: processing,
                        variant: 'white'
                    }
                ]}
            />

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                {isEdit ? (
                    // ---------------- EDIT MODE (TANGGAPAN & STATUS) ----------------
                    <>
                        <div className="lg:col-span-2 space-y-6">
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                                <div className="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                                    <AlertCircle className="w-5 h-5 text-green-600" />
                                    <h2 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Proses Aduan</h2>
                                </div>
                                <div className="space-y-5">
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <FormField.Select
                                            label="Status Aduan"
                                            value={data.status}
                                            onChange={e => setData('status', e.target.value)}
                                            error={errors.status}
                                            required
                                            options={[
                                                { value: 'baru', label: 'Baru' },
                                                { value: 'diproses', label: 'Sedang Diproses' },
                                                { value: 'selesai', label: 'Selesai' },
                                                { value: 'ditolak', label: 'Ditolak' }
                                            ]}
                                        />
                                        <FormField.Select
                                            label="Ubah Prioritas"
                                            value={data.prioritas}
                                            onChange={e => setData('prioritas', e.target.value)}
                                            error={errors.prioritas}
                                            required
                                            options={[
                                                { value: 'rendah', label: 'Rendah' },
                                                { value: 'sedang', label: 'Sedang' },
                                                { value: 'tinggi', label: 'Tinggi' },
                                                { value: 'darurat', label: 'Darurat' }
                                            ]}
                                        />
                                    </div>

                                    <div>
                                        <FormField.Textarea
                                            label="Tanggapan Resmi Desa"
                                            value={data.tanggapan}
                                            onChange={e => setData('tanggapan', e.target.value)}
                                            error={errors.tanggapan}
                                            rows={5}
                                            placeholder="Tuliskan tanggapan atau hasil penanganan..."
                                        />
                                        <p className="text-[10px] font-bold text-gray-400 mt-2">Tanggapan ini akan dapat dilihat oleh pelapor.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Sidebar Edit Info */}
                        <div className="space-y-6">
                            <div className="bg-gray-50 rounded-3xl border border-gray-200 p-6">
                                <h3 className="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">Ringkasan Aduan</h3>
                                <p className="text-sm font-bold text-gray-900 mb-2">{pengaduan.judul}</p>
                                <p className="text-xs text-gray-600 line-clamp-3 mb-4">{pengaduan.deskripsi}</p>
                                <div className="border-t border-gray-200 pt-4 mt-4 space-y-2">
                                    <div className="flex justify-between">
                                        <span className="text-[10px] font-bold text-gray-400 uppercase">Pelapor</span>
                                        <span className="text-xs font-bold text-gray-900">{pengaduan.nama_pelapor}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-[10px] font-bold text-gray-400 uppercase">Kategori</span>
                                        <span className="text-xs font-bold text-gray-900">{pengaduan.kategori}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-[10px] font-bold text-gray-400 uppercase">Tanggal</span>
                                        <span className="text-xs font-bold text-gray-900">{new Date(pengaduan.created_at).toLocaleDateString('id-ID')}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </>
                ) : (
                    // ---------------- CREATE MODE (MANUAL ENTRY) ----------------
                    <>
                        <div className="lg:col-span-2 space-y-6">
                            {/* Detail Aduan */}
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                                <div className="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                                    <FileText className="w-5 h-5 text-green-600" />
                                    <h2 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Detail Aduan</h2>
                                </div>
                                <div className="space-y-5">
                                    <FormField.Input
                                        label="Judul Aduan"
                                        value={data.judul}
                                        onChange={e => setData('judul', e.target.value)}
                                        error={errors.judul}
                                        required
                                        placeholder="Contoh: Jalan rusak di RT 01"
                                    />

                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <FormField.Select
                                            label="Kategori"
                                            value={data.kategori}
                                            onChange={e => setData('kategori', e.target.value)}
                                            error={errors.kategori}
                                            required
                                            options={[
                                                { value: 'infrastruktur', label: 'Infrastruktur' },
                                                { value: 'keamanan', label: 'Keamanan' },
                                                { value: 'kebersihan', label: 'Kebersihan' },
                                                { value: 'administrasi', label: 'Administrasi' },
                                                { value: 'lainnya', label: 'Lainnya' }
                                            ]}
                                        />
                                        <FormField.Select
                                            label="Prioritas Awal"
                                            value={data.prioritas}
                                            onChange={e => setData('prioritas', e.target.value)}
                                            error={errors.prioritas}
                                            required
                                            options={[
                                                { value: 'rendah', label: 'Rendah' },
                                                { value: 'sedang', label: 'Sedang' },
                                                { value: 'tinggi', label: 'Tinggi' },
                                                { value: 'darurat', label: 'Darurat' }
                                            ]}
                                        />
                                    </div>

                                    <FormField.Textarea
                                        label="Deskripsi Lengkap"
                                        value={data.deskripsi}
                                        onChange={e => setData('deskripsi', e.target.value)}
                                        error={errors.deskripsi}
                                        required
                                        rows={4}
                                        placeholder="Jelaskan detail aduan..."
                                    />

                                    <FormField.Input
                                        label="Lokasi Kejadian"
                                        value={data.lokasi}
                                        onChange={e => setData('lokasi', e.target.value)}
                                        error={errors.lokasi}
                                        placeholder="Nama jalan / RT / Patokan"
                                    />

                                    <div>
                                        <FormField label="Upload Foto Bukti" error={errors.foto}>
                                            <div className="relative">
                                                <input
                                                    type="file"
                                                    multiple
                                                    accept="image/*"
                                                    onChange={handleFileChange}
                                                    className="w-full bg-gray-50 border border-gray-200 border-dashed rounded-xl text-sm p-4 text-gray-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-all"
                                                />
                                            </div>
                                        </FormField>
                                        <p className="text-[10px] font-bold text-gray-400 mt-2">Format didukung: JPG, PNG, GIF (Maks. 2MB/foto)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Sidebar Pelapor */}
                        <div className="space-y-6">
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                                <div className="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                                    <CheckCircle className="w-5 h-5 text-green-600" />
                                    <h2 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Data Pelapor</h2>
                                </div>
                                <div className="space-y-4">
                                    <FormField.Input
                                        label="Nama Lengkap"
                                        value={data.nama_pelapor}
                                        onChange={e => setData('nama_pelapor', e.target.value)}
                                        error={errors.nama_pelapor}
                                        required
                                    />
                                    <FormField.Input
                                        label="NIK (Opsional)"
                                        value={data.nik_pelapor}
                                        onChange={e => setData('nik_pelapor', e.target.value)}
                                        error={errors.nik_pelapor}
                                        maxLength={16}
                                        inputClassName="font-mono"
                                    />
                                    <FormField.Input
                                        label="Telepon/WhatsApp"
                                        type="tel"
                                        value={data.telepon}
                                        onChange={e => setData('telepon', e.target.value)}
                                        error={errors.telepon}
                                    />
                                    <FormField.Textarea
                                        label="Alamat"
                                        value={data.alamat}
                                        onChange={e => setData('alamat', e.target.value)}
                                        error={errors.alamat}
                                        required
                                        rows={2}
                                    />
                                    <FormField.Input
                                        label="Email (Opsional)"
                                        type="email"
                                        value={data.email}
                                        onChange={e => setData('email', e.target.value)}
                                        error={errors.email}
                                    />
                                </div>
                            </div>
                        </div>
                    </>
                )}
            </div>
        </form>
    );
}
