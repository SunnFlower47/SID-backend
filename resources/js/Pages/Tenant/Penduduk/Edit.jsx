import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    User, 
    Save, 
    X, 
    Eye, 
    Home, 
    AlertTriangle, 
    CheckCircle, 
    Info, 
    ArrowLeft, 
    ShieldCheck, 
    MapPin, 
    Users, 
    GraduationCap,
    Edit as EditIcon // Beri alias agar tidak konflik dengan nama komponen
} from 'lucide-react';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';

export default function Edit(props) {
    // Ambil data secara sangat aman
    const pageProps = usePage().props || {};
    const auth = props.auth || pageProps.auth || {};
    const penduduk = props.penduduk || {};

    const [nikStatus, setNikStatus] = useState({ checking: false, exists: false, data: null });
    
    // Standard Options
    const OPTIONS = {
        agama: ['ISLAM', 'KRISTEN', 'KATOLIK', 'HINDU', 'BUDDHA', 'KONGHUCU'],
        pendidikan: [
            'TIDAK / BELUM SEKOLAH', 'BELUM TAMAT SD/SEDERAJAT', 'TAMAT SD / SEDERAJAT',
            'SLTP/SEDERAJAT', 'SLTA / SEDERAJAT', 'DIPLOMA I / II',
            'AKADEMI / DIPLOMA III / S. MUDA', 'DIPLOMA IV / STRATA I', 'STRATA II', 'STRATA III'
        ],
        status_perkawinan: ['BELUM KAWIN', 'KAWIN', 'CERAI HIDUP', 'CERAI MATI'],
        kedudukan_keluarga: ['Kepala Keluarga', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Saudara', 'LAINNYA'],
        pekerjaan: [
            'BELUM/TIDAK BEKERJA', 'MENGURUS RUMAH TANGGA', 'PELAJAR/MAHASISWA', 
            'PENSIUNAN', 'PEGAWAI NEGERI SIPIL', 'TENTARA NASIONAL INDONESIA', 
            'KEPOLISIAN NEGARA RI', 'PETANI/PEKEBUN', 'KARYAWAN SWASTA', 
            'BURUH HARIAN LEPAS', 'WIRASWASTA', 'PERANGKAT DESA'
        ],
    };

    // Handle manual input state for "LAINNYA"
    const [manualFields, setManualFields] = useState({});

    const toggleManual = (field, isOther) => {
        setManualFields(prev => ({ ...prev, [field]: isOther }));
    };

    const renderSelectWithOther = (label, field, options, required = false) => {
        const isManual = manualFields[field] || (!options.includes(data[field]) && data[field] !== '');
        
        return (
            <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{label} {required && '*'}</label>
                <select 
                    value={isManual ? 'LAINNYA' : data[field]}
                    onChange={e => {
                        if (e.target.value === 'LAINNYA') {
                            toggleManual(field, true);
                            setData(field, '');
                        } else {
                            toggleManual(field, false);
                            setData(field, e.target.value);
                        }
                    }}
                    className={cn(
                        "w-full px-4 py-3.5 bg-gray-50 border rounded-2xl text-sm font-black outline-none transition-all focus:bg-white focus:ring-4",
                        errors[field] ? 'border-red-300 focus:ring-red-500/10' : 'border-gray-100 focus:ring-blue-500/10 focus:border-blue-500'
                    )}
                    required={required && !isManual}
                >
                    <option value="">Pilih {label}</option>
                    {options.map(opt => <option key={opt} value={opt}>{opt}</option>)}
                    <option value="LAINNYA" className="text-blue-600 font-bold">--- LAINNYA (KETIK MANUAL) ---</option>
                </select>

                {isManual && (
                    <div className="relative animate-in slide-in-from-top-2 duration-200">
                        <input 
                            type="text"
                            placeholder={`Ketik ${label} manual...`}
                            value={data[field]}
                            onChange={e => setData(field, e.target.value.toUpperCase())}
                            className="w-full px-4 py-3.5 bg-blue-50 border border-blue-200 rounded-2xl text-sm font-black outline-none focus:ring-4 focus:ring-blue-500/10"
                            required={required}
                        />
                        <button 
                            type="button"
                            onClick={() => {
                                toggleManual(field, false);
                                setData(field, options[0]);
                            }}
                            className="absolute right-3 top-3.5 text-[10px] font-black text-blue-500 hover:text-blue-700"
                        >
                            KEMBALI
                        </button>
                    </div>
                )}
                {errors[field] && <p className="mt-1 text-[10px] font-black text-red-600 uppercase tracking-widest ml-1">{errors[field]}</p>}
            </div>
        );
    };

    const handleBack = (e) => {
        e.preventDefault();
        window.history.back();
    };

    const { data, setData, put, processing, errors } = useForm({
        nik: penduduk.nik || '',
        nama: penduduk.nama || '',
        jenis_kelamin: penduduk.jenis_kelamin || '',
        tempat_lahir: penduduk.tempat_lahir || '',
        tanggal_lahir: penduduk.tanggal_lahir ? penduduk.tanggal_lahir.split('T')[0] : '',
        agama: penduduk.agama || '',
        pendidikan: penduduk.pendidikan || '',
        pekerjaan: penduduk.pekerjaan || '',
        status_perkawinan: penduduk.status_perkawinan || '',
        kedudukan_keluarga: penduduk.kedudukan_keluarga || '',
        nama_ayah: penduduk.nama_ayah || '',
        nama_ibu: penduduk.nama_ibu || '',
        keterangan: penduduk.keterangan || ''
    });

    // NIK Check
    useEffect(() => {
        const checkNik = async () => {
            if (!data.nik || data.nik === penduduk.nik) {
                setNikStatus({ checking: false, exists: false, data: null });
                return;
            }
            if (data.nik.length === 16) {
                setNikStatus({ checking: true, exists: false, data: null });
                try {
                    const response = await fetch(route('penduduk.check-nik') + `?nik=${data.nik}&exclude_id=${penduduk.id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const result = await response.json();
                    setNikStatus({ checking: false, exists: result.exists, data: result.data });
                } catch (e) {
                    setNikStatus({ checking: false, exists: false, data: null });
                }
            } else {
                setNikStatus({ checking: false, exists: false, data: null });
            }
        };

        const timer = setTimeout(checkNik, 500);
        return () => clearTimeout(timer);
    }, [data.nik, penduduk.nik, penduduk.id]);

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('penduduk.update', penduduk.id), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data penduduk berhasil diperbarui.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            onError: (errs) => {
                if (Object.keys(errs).length > 0) {
                    Swal.fire({
                        title: 'Terjadi Kesalahan Validasi!',
                        html: `<ul class="text-left text-sm text-red-600">${Object.values(errs).map(err => `<li>${err}</li>`).join('')}</ul>`,
                        icon: 'error'
                    });
                }
            }
        });
    };

    return (
        <AuthenticatedLayout title={`Edit Data ${penduduk.nama || 'Warga'}`}>
            <Head title={`Edit - ${penduduk.nama || 'Warga'}`} />
            
            <div className="max-w-7xl mx-auto space-y-6 animate-in fade-in duration-700 pb-20">
                
                {/* 1. CONSISTENT HEADER */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <EditIcon className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none text-left">Edit Data Warga</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 flex items-center gap-2 text-left">
                                    <ShieldCheck className="w-3 h-3 text-yellow-300" />
                                    Mode Penyuntingan Data Aktif
                                </p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <button 
                                onClick={handleBack}
                                className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all active:scale-95 uppercase tracking-widest group"
                            >
                                <ArrowLeft className="w-4 h-4 mr-2 transition-transform group-hover:-translate-x-1" />
                                KEMBALI
                            </button>
                            <Link 
                                href={route('penduduk.show', penduduk.id || 0)}
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest"
                            >
                                <Eye className="w-4 h-4 mr-2" />
                                LIHAT DETAIL
                            </Link>
                        </div>
                    </div>
                </div>

                {penduduk.nkk && (
                    <div className="bg-white rounded-3xl border border-green-100 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center gap-4 animate-in slide-in-from-top-4 duration-500">
                        <div className="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center shadow-inner shrink-0">
                            <Home className="text-green-600 w-6 h-6" />
                        </div>
                        <div className="flex-1">
                            <h3 className="text-sm font-black text-gray-900 uppercase tracking-tight italic">Informasi Kartu Keluarga</h3>
                            <div className="flex flex-wrap gap-x-6 gap-y-1 mt-1">
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-widest">No KK: <span className="font-mono font-black text-gray-900 ml-1">{penduduk.nkk}</span></p>
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-widest">Kepala: <span className="font-black text-green-600 ml-1 italic">{penduduk.nama}</span></p>
                            </div>
                        </div>
                        <Link 
                            href={route('penduduk.family.address.form', penduduk.nkk)}
                            className="text-[10px] font-black text-blue-600 bg-blue-50 px-4 py-2.5 rounded-xl border border-blue-100 hover:bg-blue-600 hover:text-white transition-all uppercase tracking-widest shadow-sm"
                        >
                            Update Alamat Keluarga
                        </Link>
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-8">
                    
                    {/* Data Pribadi */}
                    <div className="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-10">
                        <div className="flex items-center gap-3 mb-8 pb-4 border-b border-gray-50">
                            <div className="p-2 bg-blue-50 rounded-xl">
                                <User className="w-5 h-5 text-blue-600" />
                            </div>
                            <h3 className="text-lg font-black text-gray-900 uppercase tracking-tight italic">Informasi Personal</h3>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nomor Induk Kependudukan (NIK)</label>
                                <div className="relative">
                                    <input 
                                        type="text" 
                                        maxLength="16"
                                        value={data.nik}
                                        onChange={e => setData('nik', e.target.value.replace(/\D/g, ''))}
                                        className={cn(
                                            "w-full px-4 py-3.5 bg-gray-50 border rounded-2xl text-sm font-black outline-none transition-all focus:bg-white focus:ring-4",
                                            errors.nik ? 'border-red-300 focus:ring-red-500/10' : 'border-gray-100 focus:ring-blue-500/10 focus:border-blue-500'
                                        )}
                                        required
                                    />
                                    {nikStatus.checking && <div className="absolute right-4 top-4 animate-spin w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full"></div>}
                                </div>
                                
                                {data.nik !== penduduk.nik && data.nik.length === 16 && !nikStatus.checking && (
                                    <div className={cn(
                                        "mt-2 p-3 rounded-xl border text-[10px] font-black uppercase tracking-widest flex items-center gap-2 animate-in slide-in-from-top-2",
                                        nikStatus.exists ? 'bg-red-50 border-red-100 text-red-600' : 'bg-green-50 border-green-100 text-green-600'
                                    )}>
                                        {nikStatus.exists ? (
                                            <><AlertTriangle className="w-3.5 h-3.5" /> <span>NIK Sudah Digunakan: {nikStatus.data?.nama}</span></>
                                        ) : (
                                            <><CheckCircle className="w-3.5 h-3.5" /> <span>NIK Tersedia & Valid</span></>
                                        )}
                                    </div>
                                )}
                                {errors.nik && <p className="mt-1 text-[10px] font-black text-red-600 uppercase tracking-widest ml-1">{errors.nik}</p>}
                            </div>

                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap Warga</label>
                                <input 
                                    type="text" 
                                    value={data.nama}
                                    onChange={e => setData('nama', e.target.value.toUpperCase())}
                                    className={cn(
                                        "w-full px-4 py-3.5 bg-gray-50 border rounded-2xl text-sm font-black outline-none transition-all focus:bg-white focus:ring-4",
                                        errors.nama ? 'border-red-300 focus:ring-red-500/10' : 'border-gray-100 focus:ring-green-500/10 focus:border-green-500'
                                    )}
                                    required
                                />
                                {errors.nama && <p className="mt-1 text-[10px] font-black text-red-600 uppercase tracking-widest ml-1">{errors.nama}</p>}
                            </div>

                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Kelamin</label>
                                <select 
                                    value={data.jenis_kelamin}
                                    onChange={e => setData('jenis_kelamin', e.target.value)}
                                    className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-black outline-none focus:bg-white focus:border-blue-500"
                                    required
                                >
                                    <option value="LAKI-LAKI">LAKI-LAKI</option>
                                    <option value="PEREMPUAN">PEREMPUAN</option>
                                </select>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tempat Lahir</label>
                                    <input 
                                        type="text" 
                                        value={data.tempat_lahir}
                                        onChange={e => setData('tempat_lahir', e.target.value.toUpperCase())}
                                        className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-black outline-none focus:bg-white focus:border-blue-500"
                                        required
                                    />
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Lahir</label>
                                    <input 
                                        type="date" 
                                        value={data.tanggal_lahir}
                                        onChange={e => setData('tanggal_lahir', e.target.value)}
                                        className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-black outline-none focus:bg-white focus:border-blue-500"
                                        required
                                    />
                                </div>
                            </div>

                            {renderSelectWithOther('Agama', 'agama', OPTIONS.agama, true)}
                            {renderSelectWithOther('Status Perkawinan', 'status_perkawinan', OPTIONS.status_perkawinan, true)}
                            {renderSelectWithOther('Hubungan Keluarga', 'kedudukan_keluarga', OPTIONS.kedudukan_keluarga, true)}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {/* Data Orang Tua */}
                        <div className="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
                            <h3 className="text-sm font-black text-gray-900 mb-6 uppercase tracking-widest flex items-center gap-2 italic">
                                <Users className="w-4 h-4 text-orange-500" />
                                Data Orang Tua
                            </h3>
                            <div className="space-y-6">
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Ayah</label>
                                    <input 
                                        type="text" 
                                        value={data.nama_ayah}
                                        onChange={e => setData('nama_ayah', e.target.value.toUpperCase())}
                                        className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-black outline-none focus:bg-white focus:border-orange-500"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Ibu</label>
                                    <input 
                                        type="text" 
                                        value={data.nama_ibu}
                                        onChange={e => setData('nama_ibu', e.target.value.toUpperCase())}
                                        className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-black outline-none focus:bg-white focus:border-orange-500"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Pendidikan & Pekerjaan */}
                        <div className="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
                            <h3 className="text-sm font-black text-gray-900 mb-6 uppercase tracking-widest flex items-center gap-2 italic">
                                <GraduationCap className="w-4 h-4 text-green-500" />
                                Profesi & Pendidikan
                            </h3>
                            <div className="space-y-6">
                                {renderSelectWithOther('Pendidikan Terakhir', 'pendidikan', OPTIONS.pendidikan)}
                                {renderSelectWithOther('Pekerjaan Utama', 'pekerjaan', OPTIONS.pekerjaan, true)}
                            </div>
                        </div>
                    </div>

                    {/* Alamat (Read-only view) */}
                    <div className="bg-gray-50 rounded-3xl border border-gray-100 p-8 opacity-70">
                        <div className="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <h3 className="text-sm font-black text-gray-900 uppercase tracking-widest italic flex items-center gap-3">
                                <MapPin className="w-5 h-5 text-gray-400" />
                                Alamat Domisili (Terkunci)
                            </h3>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="md:col-span-3">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Lengkap</label>
                                <textarea 
                                    readOnly 
                                    rows="2"
                                    value={penduduk.alamat || ''}
                                    className="w-full px-4 py-3 bg-white/50 border border-gray-200 rounded-2xl text-xs font-bold text-gray-500 cursor-not-allowed"
                                />
                                <div className="mt-3 p-3 bg-blue-50 rounded-xl flex items-start gap-3">
                                    <Info className="w-4 h-4 text-blue-600 shrink-0 mt-0.5" />
                                    <p className="text-[10px] font-bold text-blue-700 uppercase leading-relaxed">
                                        Perubahan alamat harus dilakukan melalui menu <strong>"Update Alamat Keluarga"</strong> di bagian atas halaman ini untuk menjaga konsistensi data KK.
                                    </p>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">RW</label>
                                <input type="text" readOnly value={penduduk.rw_label || '-'} className="w-full px-4 py-3 bg-white/50 border border-gray-200 rounded-2xl text-xs font-bold text-gray-500" />
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">RT</label>
                                <input type="text" readOnly value={penduduk.rt_label || '-'} className="w-full px-4 py-3 bg-white/50 border border-gray-200 rounded-2xl text-xs font-bold text-gray-500" />
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Dusun</label>
                                <input type="text" readOnly value={penduduk.dusun_label || '-'} className="w-full px-4 py-3 bg-white/50 border border-gray-200 rounded-2xl text-xs font-bold text-gray-500" />
                            </div>
                        </div>
                    </div>

                    {/* ACTIONS */}
                    <div className="flex flex-col sm:flex-row items-center justify-end gap-4 pt-6 border-t border-gray-100">
                        <Link 
                            href={route('penduduk.show', penduduk.id || 0)}
                            className="w-full sm:w-auto px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl text-xs font-black uppercase tracking-widest transition-all text-center"
                        >
                            BATALKAN
                        </Link>
                        <button
                            type="submit"
                            disabled={processing || nikStatus.exists}
                            className="w-full sm:w-auto px-12 py-4 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-blue-200 flex items-center justify-center gap-2 active:scale-95"
                        >
                            {processing ? (
                                <><div className="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></div> PROSES...</>
                            ) : (
                                <><Save className="w-4 h-4" /> SIMPAN PERUBAHAN</>
                            )}
                        </button>
                    </div>

                </form>
            </div>
        </AuthenticatedLayout>
    );
}
