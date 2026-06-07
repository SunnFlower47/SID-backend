import React, { useState, useEffect } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { MapPin, Save, ArrowLeft, User, Home, Calendar, Loader2, CheckCircle, XCircle, AlertTriangle } from 'lucide-react';
import { cn } from '@/lib/utils';
import axios from 'axios';

const AGAMA_OPTIONS   = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
const KEPERLUAN_OPTIONS = [
    { value: 'kerja',       label: 'Bekerja' },
    { value: 'sekolah',     label: 'Sekolah / Kuliah' },
    { value: 'ikut_keluarga', label: 'Ikut Keluarga' },
    { value: 'lainnya',     label: 'Lainnya' },
];

const STATUS_PERKAWINAN_OPTIONS = ['BELUM KAWIN', 'KAWIN TERCATAT', 'KAWIN BELUM TERCATAT', 'CERAI HIDUP TERCATAT', 'CERAI HIDUP BELUM TERCATAT', 'CERAI MATI'];
const PEKERJAAN_OPTIONS = [
    'BELUM/TIDAK BEKERJA', 'MENGURUS RUMAH TANGGA', 'PELAJAR/MAHASISWA', 
    'PENSIUNAN', 'PEGAWAI NEGERI SIPIL', 'TENTARA NASIONAL INDONESIA', 
    'KEPOLISIAN NEGARA RI', 'PETANI/PEKEBUN', 'KARYAWAN SWASTA', 
    'BURUH HARIAN LEPAS', 'WIRASWASTA', 'PERANGKAT DESA'
];

export default function Form({ auth, domisili, rtList, rwList, dusunList }) {
    const isEdit = !!domisili;
    const formatDateForInput = (dateStr) => {
        if (!dateStr) return '';
        try {
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return '';
            return d.toISOString().split('T')[0];
        } catch (e) {
            return '';
        }
    };

    const { data, setData, post, put, processing, errors } = useForm({
        nik:                domisili?.nik ?? '',
        nama:               domisili?.nama ?? '',
        tempat_lahir:       domisili?.tempat_lahir ?? '',
        tanggal_lahir:      formatDateForInput(domisili?.tanggal_lahir),
        jenis_kelamin:      domisili?.jenis_kelamin ?? '',
        agama:              domisili?.agama ?? '',
        status_perkawinan:  domisili?.status_perkawinan ?? 'BELUM KAWIN',
        kewarganegaraan:    domisili?.kewarganegaraan ?? 'Indonesia',
        pekerjaan:          domisili?.pekerjaan ?? 'BELUM/TIDAK BEKERJA',
        asal_daerah:        domisili?.asal_daerah ?? '',
        alamat_asal:        domisili?.alamat_asal ?? '',
        rt_id:              domisili?.rt_id ?? '',
        rw_id:              domisili?.rw_id ?? '',
        dusun_id:           domisili?.dusun_id ?? '',
        alamat_tinggal:     domisili?.alamat_tinggal ?? '',
        keperluan_domisili: domisili?.keperluan_domisili ?? '',
        tanggal_masuk:      formatDateForInput(domisili?.tanggal_masuk) || new Date().toISOString().split('T')[0],
        catatan:            domisili?.catatan ?? '',
    });

    const [nikStatus, setNikStatus] = useState(null); // null | 'checking' | 'available' | 'blocked' | 'duplicate'

    // Filtered lists for dependent dropdowns
    const filteredRws = rwList;

    const filteredRts = data.rw_id 
        ? rtList?.filter(rt => String(rt.rw_id) === String(data.rw_id)) 
        : rtList;

    // Geographic selection handlers

    const handleRwChange = (rwId) => {
        setData(prev => ({
            ...prev,
            rw_id: rwId,
            rt_id: '' // Reset child on parent change
        }));
    };

    const handleRtChange = (rtId) => {
        const rt = rtList?.find(r => String(r.id) === String(rtId));
        setData(prev => ({
            ...prev,
            rt_id: rtId,
            rw_id: rt?.rw_id ? String(rt.rw_id) : prev.rw_id,
            dusun_id: rt?.dusun_id ? String(rt.dusun_id) : prev.dusun_id
        }));
    };

    // NIK check (debounced)
    useEffect(() => {
        if (isEdit || data.nik.length !== 16) { setNikStatus(null); return; }
        setNikStatus('checking');
        const timer = setTimeout(async () => {
            try {
                const res = await axios.get(route('domisili.check-nik'), { params: { nik: data.nik } });
                setNikStatus(res.data.status);
            } catch { setNikStatus(null); }
        }, 600);
        return () => clearTimeout(timer);
    }, [data.nik]);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(route('domisili.update', domisili.id));
        } else {
            post(route('domisili.store'));
        }
    };

    const [manualFields, setManualFields] = useState({});

    const toggleManual = (field, isManual) => {
        setManualFields(prev => ({ ...prev, [field]: isManual }));
    };

    const renderSelectWithOther = (label, field, options, placeholder = '', required = false) => {
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
                    className={`w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold uppercase outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all ${errors[field] ? 'border-red-500' : ''}`}
                    required={required && !isManual}
                >
                    <option value="">{placeholder}</option>
                    {options.map(opt => <option key={opt} value={opt}>{opt}</option>)}
                    <option value="LAINNYA" className="text-blue-600 font-bold">--- LAINNYA (KETIK MANUAL) ---</option>
                </select>

                {isManual && (
                    <div className="relative animate-in slide-in-from-top-2 duration-200 mt-2">
                        <input
                            type="text"
                            value={data[field]}
                            onChange={e => setData(field, e.target.value.toUpperCase())}
                            className="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-bold uppercase focus:ring-2 focus:ring-blue-500 bg-white"
                            placeholder={`Ketik ${label.toLowerCase()}...`}
                            required={required}
                        />
                        <button
                            type="button"
                            onClick={() => {
                                toggleManual(field, false);
                                setData(field, '');
                            }}
                            className="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-400 hover:text-red-500 flex items-center gap-1"
                        >
                            <XCircle className="w-3 h-3" /> BATAL
                        </button>
                    </div>
                )}
            </FormField>
        );
    };

    const nikIndicator = () => {
        if (nikStatus === 'checking')  return <span className="flex items-center gap-1 text-[10px] font-bold text-gray-400"><Loader2 className="w-3 h-3 animate-spin" /> Memeriksa NIK...</span>;
        if (nikStatus === 'available') return <span className="flex items-center gap-1 text-[10px] font-bold text-green-600"><CheckCircle className="w-3 h-3" /> NIK tersedia</span>;
        if (nikStatus === 'blocked')   return <span className="flex items-center gap-1 text-[10px] font-bold text-red-600"><XCircle className="w-3 h-3" /> NIK terdaftar sebagai penduduk TETAP — tidak bisa didaftarkan!</span>;
        if (nikStatus === 'duplicate') return <span className="flex items-center gap-1 text-[10px] font-bold text-orange-600"><AlertTriangle className="w-3 h-3" /> NIK sudah memiliki domisili AKTIF</span>;
        return null;
    };

    return (
        <AuthenticatedLayout user={auth.user} title={isEdit ? 'Edit Domisili' : 'Daftar Pendatang'}>
            <Head title={isEdit ? 'Edit Data Domisili' : 'Daftar Pendatang Baru'} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader 
                    title={isEdit ? 'Edit Data Domisili' : 'Daftar Pendatang Baru'}
                    subtitle="Pendaftaran Warga Sementara Desa Cibatu"
                    icon={MapPin}
                    backHref={route('domisili.index')}
                />

                <div className="w-full">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Section 1: Data KTP */}
                        <FormCard
                            title="Data Identitas (KTP)"
                            icon={User}
                            iconColor="text-green-600"
                            iconBg="bg-green-50"
                        >
                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest -mt-4 mb-6 ml-[3.25rem]">Sesuai Kartu Tanda Penduduk Asal</p>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <FormField.Input
                                        label="NIK *"
                                        value={data.nik}
                                        onChange={e => setData('nik', e.target.value)}
                                        maxLength={16}
                                        placeholder="16 digit NIK"
                                        error={errors.nik}
                                        className={cn(nikStatus === 'blocked' && 'border-red-400 ring-1 ring-red-400')}
                                    />
                                    <div className="mt-2 pl-1">{nikIndicator()}</div>
                                </div>
                                
                                <FormField.Input
                                    label="Nama Lengkap *"
                                    value={data.nama}
                                    onChange={e => setData('nama', e.target.value)}
                                    placeholder="Nama sesuai KTP"
                                    error={errors.nama}
                                />
                                <FormField.Input
                                    label="Tempat Lahir"
                                    value={data.tempat_lahir}
                                    onChange={e => setData('tempat_lahir', e.target.value)}
                                    placeholder="Kota/Kabupaten"
                                    error={errors.tempat_lahir}
                                />
                                <FormField.Input
                                    type="date"
                                    label="Tanggal Lahir"
                                    value={data.tanggal_lahir}
                                    onChange={e => setData('tanggal_lahir', e.target.value)}
                                    error={errors.tanggal_lahir}
                                />
                                <FormField.Select
                                    label="Jenis Kelamin *"
                                    value={data.jenis_kelamin}
                                    onChange={e => setData('jenis_kelamin', e.target.value)}
                                    error={errors.jenis_kelamin}
                                    options={[
                                        { value: '', label: 'PILIH JENIS KELAMIN' },
                                        { value: 'L', label: 'Laki-Laki' },
                                        { value: 'P', label: 'Perempuan' }
                                    ]}
                                />
                                {renderSelectWithOther('Status Perkawinan', 'status_perkawinan', STATUS_PERKAWINAN_OPTIONS, 'PILIH STATUS PERKAWINAN', true)}
                                <FormField.Select
                                    label="Agama"
                                    value={data.agama}
                                    onChange={e => setData('agama', e.target.value)}
                                    error={errors.agama}
                                    options={[
                                        { value: '', label: 'PILIH AGAMA' },
                                        ...AGAMA_OPTIONS.map(a => ({ value: a, label: a }))
                                    ]}
                                />
                                <FormField.Input
                                    label="Kewarganegaraan"
                                    value={data.kewarganegaraan}
                                    onChange={e => setData('kewarganegaraan', e.target.value)}
                                    placeholder="Indonesia"
                                    error={errors.kewarganegaraan}
                                />
                                {renderSelectWithOther('Pekerjaan', 'pekerjaan', PEKERJAAN_OPTIONS, 'PILIH PEKERJAAN', false)}
                                <FormField.Input
                                    label="Asal Daerah"
                                    value={data.asal_daerah}
                                    onChange={e => setData('asal_daerah', e.target.value)}
                                    placeholder="Kota/Kabupaten asal"
                                    error={errors.asal_daerah}
                                />
                                <div className="sm:col-span-2">
                                    <FormField.Textarea
                                        label="Alamat Asal (sesuai KTP)"
                                        value={data.alamat_asal}
                                        onChange={e => setData('alamat_asal', e.target.value)}
                                        placeholder="Alamat lengkap sesuai KTP..."
                                        error={errors.alamat_asal}
                                        rows={2}
                                    />
                                </div>
                            </div>
                        </FormCard>

                        {/* Section 2: Data Domisili */}
                        <FormCard
                            title="Data Domisili di Desa"
                            icon={Home}
                            iconColor="text-blue-600"
                            iconBg="bg-blue-50"
                        >
                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest -mt-4 mb-6 ml-[3.25rem]">Lokasi & keperluan tinggal sementara</p>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <FormField.Select
                                    label="RW *"
                                    value={data.rw_id}
                                    onChange={e => handleRwChange(e.target.value)}
                                    error={errors.rw_id}
                                    options={[
                                        { value: '', label: 'PILIH RW' },
                                        ...(filteredRws?.map(rw => ({ value: rw.id, label: `RW ${rw.kode}` })) || [])
                                    ]}
                                />
                                <FormField.Select
                                    label="RT *"
                                    value={data.rt_id}
                                    onChange={e => handleRtChange(e.target.value)}
                                    error={errors.rt_id}
                                    disabled={!data.rw_id}
                                    options={[
                                        { value: '', label: 'PILIH RT' },
                                        ...(filteredRts?.map(rt => ({ value: rt.id, label: `RT ${rt.kode}` })) || [])
                                    ]}
                                />
                                <FormField.Select
                                    label="Keperluan Domisili"
                                    value={data.keperluan_domisili}
                                    onChange={e => setData('keperluan_domisili', e.target.value)}
                                    error={errors.keperluan_domisili}
                                    options={[
                                        { value: '', label: 'PILIH KEPERLUAN' },
                                        ...KEPERLUAN_OPTIONS
                                    ]}
                                />
                                <FormField.Input
                                    type="date"
                                    label="Tanggal Masuk *"
                                    value={data.tanggal_masuk}
                                    onChange={e => setData('tanggal_masuk', e.target.value)}
                                    error={errors.tanggal_masuk}
                                />
                                <div className="sm:col-span-2">
                                    <FormField.Textarea
                                        label="Alamat Tinggal di Desa *"
                                        value={data.alamat_tinggal}
                                        onChange={e => setData('alamat_tinggal', e.target.value)}
                                        placeholder="Alamat lengkap tempat tinggal sementara di desa..."
                                        error={errors.alamat_tinggal}
                                        rows={2}
                                    />
                                </div>
                                <div className="sm:col-span-2">
                                    <FormField.Textarea
                                        label="Catatan Tambahan"
                                        value={data.catatan}
                                        onChange={e => setData('catatan', e.target.value)}
                                        placeholder="Catatan atau informasi tambahan (opsional)..."
                                        error={errors.catatan}
                                        rows={2}
                                    />
                                </div>
                            </div>
                        </FormCard>

                        {/* Info Box */}
                        {!isEdit && (
                            <div className="bg-green-50 border border-green-100 rounded-3xl p-8 flex items-start gap-6 animate-in slide-in-from-bottom-4 duration-500">
                                <div className="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm shrink-0">
                                    <Calendar className="w-6 h-6 text-green-600" />
                                </div>
                                <div>
                                    <p className="text-xs font-black text-green-900 uppercase tracking-widest italic">Masa Berlaku Otomatis</p>
                                    <p className="text-sm text-green-700 mt-2 leading-relaxed font-medium">Domisili akan berlaku selama <strong>3 bulan</strong> dari tanggal masuk. Nomor surat akan digenerate otomatis. Perpanjangan dilakukan secara manual dari halaman utama.</p>
                                </div>
                            </div>
                        )}

                        {/* Actions */}
                        <div className="flex flex-col sm:flex-row items-center gap-4 pt-4">
                            <Link href={route('domisili.index')} className="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gray-100 text-gray-700 text-[10px] font-black uppercase tracking-widest hover:bg-gray-200 transition-all border border-gray-200 flex items-center justify-center">
                                <ArrowLeft className="w-4 h-4 mr-2" /> Batal
                            </Link>
                            <button type="submit" disabled={processing || nikStatus === 'blocked'}
                                className={cn('w-full sm:w-auto px-10 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl flex items-center justify-center gap-2 sm:ml-auto',
                                    processing || nikStatus === 'blocked'
                                        ? 'bg-gray-200 text-gray-400 cursor-not-allowed shadow-none'
                                        : 'bg-green-600 text-white hover:bg-green-700 shadow-green-200 active:scale-95'
                                )}>
                                {processing ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
                                {processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Daftarkan Pendatang Baru')}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
