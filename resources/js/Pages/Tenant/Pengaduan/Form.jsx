import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save, MessageSquare, AlertCircle, FileText, CheckCircle, Upload } from 'lucide-react';

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

    const InputLabel = ({ label, required }) => (
        <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
            {label} {required && <span className="text-red-500">*</span>}
        </label>
    );

    const ErrorMsg = ({ msg }) => msg ? <p className="text-red-500 text-xs font-bold mt-1.5">{msg}</p> : null;

    return (
        <form onSubmit={handleSubmit} className="space-y-6 sm:space-y-8 animate-in fade-in duration-700 pb-20">
            {/* Header */}
            <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                            {isEdit ? <MessageSquare className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" /> : <FileText className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />}
                        </div>
                        <div>
                            <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight uppercase italic leading-none">
                                {isEdit ? 'Tanggapi Aduan' : 'Tambah Aduan Manual'}
                            </h1>
                            <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">
                                {isEdit ? `Aduan: ${pengaduan.judul}` : 'Masukkan data pelapor dan detail aduan'}
                            </p>
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <Link
                            href={isEdit ? route('pengaduan.show', pengaduan.id) : route('pengaduan.index')}
                            className="flex items-center px-4 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                        >
                            <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                            BATAL
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="flex items-center px-6 py-2.5 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-black/10 hover:scale-105 disabled:opacity-50"
                        >
                            <Save className="w-3.5 h-3.5 mr-2" />
                            {processing ? 'MENYIMPAN...' : 'SIMPAN'}
                        </button>
                    </div>
                </div>
            </div>

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
                                        <div>
                                            <InputLabel label="Status Aduan" required />
                                            <select
                                                value={data.status}
                                                onChange={e => setData('status', e.target.value)}
                                                className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-xl text-sm font-bold text-gray-700 p-3"
                                            >
                                                <option value="baru">Baru</option>
                                                <option value="diproses">Sedang Diproses</option>
                                                <option value="selesai">Selesai</option>
                                                <option value="ditolak">Ditolak</option>
                                            </select>
                                            <ErrorMsg msg={errors.status} />
                                        </div>
                                        <div>
                                            <InputLabel label="Ubah Prioritas" required />
                                            <select
                                                value={data.prioritas}
                                                onChange={e => setData('prioritas', e.target.value)}
                                                className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-xl text-sm font-bold text-gray-700 p-3"
                                            >
                                                <option value="rendah">Rendah</option>
                                                <option value="sedang">Sedang</option>
                                                <option value="tinggi">Tinggi</option>
                                                <option value="darurat">Darurat</option>
                                            </select>
                                            <ErrorMsg msg={errors.prioritas} />
                                        </div>
                                    </div>

                                    <div>
                                        <InputLabel label="Tanggapan Resmi Desa" />
                                        <textarea
                                            value={data.tanggapan}
                                            onChange={e => setData('tanggapan', e.target.value)}
                                            rows="5"
                                            placeholder="Tuliskan tanggapan atau hasil penanganan..."
                                            className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-xl text-sm transition-all p-3"
                                        />
                                        <ErrorMsg msg={errors.tanggapan} />
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
                                    <div>
                                        <InputLabel label="Judul Aduan" required />
                                        <input
                                            type="text"
                                            value={data.judul}
                                            onChange={e => setData('judul', e.target.value)}
                                            placeholder="Contoh: Jalan rusak di RT 01"
                                            className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-xl text-sm transition-all p-3"
                                        />
                                        <ErrorMsg msg={errors.judul} />
                                    </div>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <InputLabel label="Kategori" required />
                                            <select
                                                value={data.kategori}
                                                onChange={e => setData('kategori', e.target.value)}
                                                className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-xl text-sm font-bold text-gray-700 p-3"
                                            >
                                                <option value="infrastruktur">Infrastruktur</option>
                                                <option value="keamanan">Keamanan</option>
                                                <option value="kebersihan">Kebersihan</option>
                                                <option value="administrasi">Administrasi</option>
                                                <option value="lainnya">Lainnya</option>
                                            </select>
                                            <ErrorMsg msg={errors.kategori} />
                                        </div>
                                        <div>
                                            <InputLabel label="Prioritas Awal" required />
                                            <select
                                                value={data.prioritas}
                                                onChange={e => setData('prioritas', e.target.value)}
                                                className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-xl text-sm font-bold text-gray-700 p-3"
                                            >
                                                <option value="rendah">Rendah</option>
                                                <option value="sedang">Sedang</option>
                                                <option value="tinggi">Tinggi</option>
                                                <option value="darurat">Darurat</option>
                                            </select>
                                            <ErrorMsg msg={errors.prioritas} />
                                        </div>
                                    </div>

                                    <div>
                                        <InputLabel label="Deskripsi Lengkap" required />
                                        <textarea
                                            value={data.deskripsi}
                                            onChange={e => setData('deskripsi', e.target.value)}
                                            rows="4"
                                            placeholder="Jelaskan detail aduan..."
                                            className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-xl text-sm transition-all p-3"
                                        />
                                        <ErrorMsg msg={errors.deskripsi} />
                                    </div>

                                    <div>
                                        <InputLabel label="Lokasi Kejadian" />
                                        <input
                                            type="text"
                                            value={data.lokasi}
                                            onChange={e => setData('lokasi', e.target.value)}
                                            placeholder="Nama jalan / RT / Patokan"
                                            className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200 rounded-xl text-sm transition-all p-3"
                                        />
                                        <ErrorMsg msg={errors.lokasi} />
                                    </div>

                                    <div>
                                        <InputLabel label="Upload Foto Bukti" />
                                        <div className="relative">
                                            <input
                                                type="file"
                                                multiple
                                                accept="image/*"
                                                onChange={handleFileChange}
                                                className="w-full bg-gray-50 border border-gray-200 border-dashed rounded-xl text-sm p-4 text-gray-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-all"
                                            />
                                        </div>
                                        <p className="text-[10px] font-bold text-gray-400 mt-2">Format didukung: JPG, PNG, GIF (Maks. 2MB/foto)</p>
                                        <ErrorMsg msg={errors.foto} />
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
                                    <div>
                                        <InputLabel label="Nama Lengkap" required />
                                        <input
                                            type="text"
                                            value={data.nama_pelapor}
                                            onChange={e => setData('nama_pelapor', e.target.value)}
                                            className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 rounded-xl text-sm p-3"
                                        />
                                        <ErrorMsg msg={errors.nama_pelapor} />
                                    </div>
                                    <div>
                                        <InputLabel label="NIK (Opsional)" />
                                        <input
                                            type="text"
                                            value={data.nik_pelapor}
                                            onChange={e => setData('nik_pelapor', e.target.value)}
                                            maxLength="16"
                                            className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 rounded-xl text-sm p-3 font-mono"
                                        />
                                        <ErrorMsg msg={errors.nik_pelapor} />
                                    </div>
                                    <div>
                                        <InputLabel label="Telepon/WhatsApp" />
                                        <input
                                            type="tel"
                                            value={data.telepon}
                                            onChange={e => setData('telepon', e.target.value)}
                                            className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 rounded-xl text-sm p-3"
                                        />
                                        <ErrorMsg msg={errors.telepon} />
                                    </div>
                                    <div>
                                        <InputLabel label="Alamat" required />
                                        <textarea
                                            value={data.alamat}
                                            onChange={e => setData('alamat', e.target.value)}
                                            rows="2"
                                            className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 rounded-xl text-sm p-3"
                                        />
                                        <ErrorMsg msg={errors.alamat} />
                                    </div>
                                    <div>
                                        <InputLabel label="Email (Opsional)" />
                                        <input
                                            type="email"
                                            value={data.email}
                                            onChange={e => setData('email', e.target.value)}
                                            className="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 rounded-xl text-sm p-3"
                                        />
                                        <ErrorMsg msg={errors.email} />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </>
                )}
            </div>
        </form>
    );
}
