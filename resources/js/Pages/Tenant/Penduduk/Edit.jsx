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
    ShieldCheck, 
    MapPin, 
    Users, 
    GraduationCap,
    Edit as EditIcon
} from 'lucide-react';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';

// Shared Components
import { PageHeader, FormCard, FormField } from '@/Components/Shared';

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
        status_perkawinan: ['BELUM KAWIN', 'KAWIN TERCATAT', 'KAWIN BELUM TERCATAT', 'CERAI HIDUP TERCATAT', 'CERAI HIDUP BELUM TERCATAT', 'CERAI MATI'],
        kedudukan_keluarga: ['Kepala Keluarga', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Saudara', 'LAINNYA'],
        pekerjaan: [
            'BELUM/TIDAK BEKERJA', 'MENGURUS RUMAH TANGGA', 'PELAJAR/MAHASISWA', 
            'PENSIUNAN', 'PEGAWAI NEGERI SIPIL', 'TENTARA NASIONAL INDONESIA', 
            'KEPOLISIAN NEGARA RI', 'PETANI/PEKEBUN', 'KARYAWAN SWASTA', 
            'BURUH HARIAN LEPAS', 'WIRASWASTA', 'PERANGKAT DESA'
        ],
        golongan_darah: ['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'TIDAK TAHU'],
        warganegara: ['WNI', 'WNA'],
        status_pendidikan: ['SEDANG SEKOLAH', 'TIDAK SEKOLAH', 'TAMAT SEKOLAH', 'PUTUS SEKOLAH'],
        status_asuransi: ['BPJS MANDIRI', 'BPJS PBI/GRATIS', 'NON-BPJS', 'TIDAK ADA'],
        dapat_membaca_huruf: ['HURUF LATIN', 'HURUF ARAB', 'HURUF LAINNYA', 'BELUM/TIDAK DAPAT MEMBACA']
    };

    // Handle manual input state for "LAINNYA"
    const [manualFields, setManualFields] = useState({});

    const toggleManual = (field, isOther) => {
        setManualFields(prev => ({ ...prev, [field]: isOther }));
    };

    const renderSelectWithOther = (label, field, options, required = false) => {
        const isManual = manualFields[field] || (!options.includes(data[field]) && data[field] !== '');
        
        return (
            <FormField label={label} required={required} error={errors[field]}>
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
                        "w-full px-4 py-3 bg-gray-50 border rounded-2xl text-sm font-bold outline-none transition-all focus:bg-white focus:ring-4",
                        errors[field] ? 'border-red-400 focus:ring-red-500/10' : 'border-gray-100 focus:ring-blue-500/10 focus:border-blue-500'
                    )}
                    required={required && !isManual}
                >
                    <option value="">Pilih {label}</option>
                    {options.map(opt => <option key={opt} value={opt}>{opt}</option>)}
                    <option value="LAINNYA" className="text-blue-600 font-bold">--- LAINNYA (KETIK MANUAL) ---</option>
                </select>

                {isManual && (
                    <div className="relative animate-in slide-in-from-top-2 duration-200 mt-2">
                        <input 
                            type="text"
                            placeholder={`Ketik ${label} manual...`}
                            value={data[field]}
                            onChange={e => setData(field, e.target.value.toUpperCase())}
                            className="w-full px-4 py-3 bg-blue-50 border border-blue-200 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-blue-500/10"
                            required={required}
                        />
                        <button 
                            type="button"
                            onClick={() => {
                                toggleManual(field, false);
                                setData(field, options[0]);
                            }}
                            className="absolute right-3 top-3 text-[10px] font-black text-blue-500 hover:text-blue-700"
                        >
                            KEMBALI
                        </button>
                    </div>
                )}
            </FormField>
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
        keterangan: penduduk.keterangan || '',
        golongan_darah: penduduk.golongan_darah || 'TIDAK TAHU',
        warganegara: penduduk.warganegara || 'WNI',
        no_akta_lahir: penduduk.no_akta_lahir || '',
        status_pendidikan: penduduk.status_pendidikan || 'TAMAT SEKOLAH',
        telepon: penduduk.telepon || '',
        cacat_type: penduduk.cacat_type || '',
        sakit_menahun: penduduk.sakit_menahun || '',
        status_asuransi: penduduk.status_asuransi || 'TIDAK ADA',
        dapat_membaca_huruf: penduduk.dapat_membaca_huruf || ''
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
                <PageHeader 
                    title="Edit Data Warga"
                    titleSize="sm"
                    subtitle={
                        <span className="flex items-center gap-2">
                            <ShieldCheck className="w-3 h-3 text-yellow-300" />
                            Mode Penyuntingan Data Aktif
                        </span>
                    }
                    icon={EditIcon}
                    backHref={route('penduduk.index')} // Use index as fallback for the back button on PageHeader
                    actions={[
                        {
                            label: 'LIHAT DETAIL',
                            icon: Eye,
                            href: route('penduduk.show', penduduk.id || 0),
                            variant: 'white'
                        }
                    ]}
                />

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

                <form onSubmit={handleSubmit} className="space-y-6">
                    
                    {/* Data Pribadi */}
                    <FormCard icon={User} title="Informasi Personal">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <FormField label="Nomor Induk Kependudukan (NIK)" required error={errors.nik}>
                                <div className="relative">
                                    <input 
                                        type="text" 
                                        maxLength="16"
                                        value={data.nik}
                                        onChange={e => setData('nik', e.target.value.replace(/\D/g, ''))}
                                        className={cn(
                                            "w-full px-4 py-3 bg-gray-50 border rounded-2xl text-sm font-bold font-mono outline-none transition-all focus:bg-white focus:ring-4",
                                            errors.nik ? 'border-red-400 focus:ring-red-500/10' : 'border-gray-100 focus:ring-blue-500/10 focus:border-blue-500'
                                        )}
                                        required
                                    />
                                    {nikStatus.checking && <div className="absolute right-4 top-3.5 animate-spin w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full"></div>}
                                </div>
                                
                                {data.nik !== penduduk.nik && data.nik.length === 16 && !nikStatus.checking && (
                                    <div className={cn(
                                        "mt-2 p-2 rounded-xl border text-[10px] font-bold uppercase tracking-tight flex items-start gap-1 animate-in slide-in-from-top-2",
                                        nikStatus.exists ? 'bg-red-50 border-red-100 text-red-700' : 'bg-green-50 border-green-100 text-green-700'
                                    )}>
                                        {nikStatus.exists ? (
                                            <><AlertTriangle className="w-3.5 h-3.5 mt-0.5" /> <span>NIK Sudah Digunakan: {nikStatus.data?.nama}</span></>
                                        ) : (
                                            <><CheckCircle className="w-3.5 h-3.5 mt-0.5" /> <span>NIK Tersedia & Valid</span></>
                                        )}
                                    </div>
                                )}
                            </FormField>

                            <FormField.Input 
                                label="Nama Lengkap Warga"
                                required
                                value={data.nama}
                                onChange={e => setData('nama', e.target.value.toUpperCase())}
                                error={errors.nama}
                            />

                            <FormField.Select 
                                label="Jenis Kelamin"
                                required
                                value={data.jenis_kelamin}
                                onChange={e => setData('jenis_kelamin', e.target.value)}
                                error={errors.jenis_kelamin}
                                options={['LAKI-LAKI', 'PEREMPUAN']}
                            />

                            <div className="grid grid-cols-2 gap-4">
                                <FormField.Input 
                                    label="Tempat Lahir"
                                    required
                                    value={data.tempat_lahir}
                                    onChange={e => setData('tempat_lahir', e.target.value.toUpperCase())}
                                    error={errors.tempat_lahir}
                                />
                                <FormField.Input 
                                    label="Tanggal Lahir"
                                    type="date"
                                    required
                                    value={data.tanggal_lahir}
                                    onChange={e => setData('tanggal_lahir', e.target.value)}
                                    error={errors.tanggal_lahir}
                                />
                            </div>

                            {renderSelectWithOther('Agama', 'agama', OPTIONS.agama, true)}
                            {renderSelectWithOther('Status Perkawinan', 'status_perkawinan', OPTIONS.status_perkawinan, true)}
                            {renderSelectWithOther('Hubungan Keluarga', 'kedudukan_keluarga', OPTIONS.kedudukan_keluarga, true)}
                        </div>
                    </FormCard>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {/* Data Orang Tua */}
                        <FormCard icon={Users} title="Data Orang Tua">
                            <div className="space-y-6">
                                <FormField.Input 
                                    label="Nama Ayah"
                                    value={data.nama_ayah}
                                    onChange={e => setData('nama_ayah', e.target.value.toUpperCase())}
                                    error={errors.nama_ayah}
                                />
                                <FormField.Input 
                                    label="Nama Ibu"
                                    value={data.nama_ibu}
                                    onChange={e => setData('nama_ibu', e.target.value.toUpperCase())}
                                    error={errors.nama_ibu}
                                />
                            </div>
                        </FormCard>

                        {/* Pendidikan & Pekerjaan */}
                        <FormCard icon={GraduationCap} title="Profesi & Pendidikan">
                            <div className="space-y-6">
                                {renderSelectWithOther('Pendidikan Terakhir', 'pendidikan', OPTIONS.pendidikan)}
                                {renderSelectWithOther('Pekerjaan Utama', 'pekerjaan', OPTIONS.pekerjaan, true)}
                            </div>
                        </FormCard>
                    </div>

                    {/* Informasi Tambahan, Kontak & Kesehatan */}
                    <FormCard icon={Info} title="Informasi Tambahan, Kontak & Kesehatan">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <FormField.Select
                                label="Golongan Darah"
                                value={data.golongan_darah}
                                onChange={e => setData('golongan_darah', e.target.value)}
                                options={OPTIONS.golongan_darah}
                                error={errors.golongan_darah}
                            />

                            <FormField.Select
                                label="Dapat Membaca Huruf"
                                value={data.dapat_membaca_huruf}
                                onChange={e => setData('dapat_membaca_huruf', e.target.value)}
                                options={OPTIONS.dapat_membaca_huruf}
                                error={errors.dapat_membaca_huruf}
                            />

                            <FormField.Select
                                label="Kewarganegaraan"
                                value={data.warganegara}
                                onChange={e => setData('warganegara', e.target.value)}
                                options={OPTIONS.warganegara}
                                error={errors.warganegara}
                            />

                            <FormField.Input
                                label="Nomor Akta Lahir"
                                value={data.no_akta_lahir}
                                onChange={e => setData('no_akta_lahir', e.target.value.toUpperCase())}
                                error={errors.no_akta_lahir}
                            />

                            <FormField.Select
                                label="Status Pendidikan"
                                value={data.status_pendidikan}
                                onChange={e => setData('status_pendidikan', e.target.value)}
                                options={OPTIONS.status_pendidikan}
                                error={errors.status_pendidikan}
                            />

                            <FormField.Input
                                label="Nomor Telepon/WA"
                                value={data.telepon}
                                onChange={e => setData('telepon', e.target.value.replace(/\D/g, ''))}
                                error={errors.telepon}
                            />

                            <FormField.Input
                                label="Jenis Cacat/Disabilitas"
                                placeholder="Kosongkan jika tidak ada"
                                value={data.cacat_type}
                                onChange={e => setData('cacat_type', e.target.value.toUpperCase())}
                                error={errors.cacat_type}
                            />

                            <FormField.Input
                                label="Penyakit Menahun"
                                placeholder="Kosongkan jika tidak ada"
                                value={data.sakit_menahun}
                                onChange={e => setData('sakit_menahun', e.target.value.toUpperCase())}
                                error={errors.sakit_menahun}
                            />

                            <FormField.Select
                                label="Status Asuransi Kesehatan"
                                value={data.status_asuransi}
                                onChange={e => setData('status_asuransi', e.target.value)}
                                options={OPTIONS.status_asuransi}
                                error={errors.status_asuransi}
                            />
                        </div>
                    </FormCard>

                    {/* Alamat (Read-only view) */}
                    <FormCard icon={MapPin} title="Alamat Domisili (Terkunci)" className="bg-gray-50/70">
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="md:col-span-3">
                                <FormField.Textarea 
                                    label="Alamat Domisili"
                                    readOnly 
                                    rows="2"
                                    value={penduduk.alamat || ''}
                                    inputClassName="bg-white/50 text-gray-500 cursor-not-allowed"
                                />
                                <div className="mt-3 p-3 bg-blue-50 rounded-xl flex items-start gap-3 border border-blue-100">
                                    <Info className="w-4 h-4 text-blue-600 shrink-0 mt-0.5" />
                                    <p className="text-[10px] font-bold text-blue-700 uppercase leading-relaxed">
                                        Perubahan alamat harus dilakukan melalui menu <strong>"Update Alamat Keluarga"</strong> di bagian atas halaman ini untuk menjaga konsistensi data KK.
                                    </p>
                                </div>
                            </div>
                            <FormField.Input 
                                label="RW"
                                readOnly 
                                value={penduduk.rw_label || '-'} 
                                inputClassName="bg-white/50 text-gray-500 cursor-not-allowed"
                            />
                            <FormField.Input 
                                label="RT"
                                readOnly 
                                value={penduduk.rt_label || '-'} 
                                inputClassName="bg-white/50 text-gray-500 cursor-not-allowed"
                            />
                            <FormField.Input 
                                label="Dusun"
                                readOnly 
                                value={penduduk.dusun_label || '-'} 
                                inputClassName="bg-white/50 text-gray-500 cursor-not-allowed"
                            />
                        </div>
                    </FormCard>

                    {/* ACTIONS */}
                    <div className="flex flex-col sm:flex-row items-center justify-end gap-4 pt-4">
                        <Link 
                            href={route('penduduk.show', penduduk.id || 0)}
                            className="w-full sm:w-auto px-8 py-3.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-center shadow-sm"
                        >
                            BATALKAN
                        </Link>
                        <button
                            type="submit"
                            disabled={processing || nikStatus.exists}
                            className="w-full sm:w-auto px-10 py-3.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95"
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
