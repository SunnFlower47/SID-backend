import React, { useState, useEffect } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
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

function SectionHeader({ icon: Icon, title, subtitle, color = 'green' }) {
    const colors = { 
        green: 'bg-green-50 text-green-600', 
        blue: 'bg-blue-50 text-blue-600' 
    };
    return (
        <div className="flex items-center gap-3 mb-8 pb-4 border-b border-gray-100">
            <div className={cn('w-10 h-10 rounded-xl flex items-center justify-center shrink-0', colors[color])}>
                <Icon className="w-5 h-5" />
            </div>
            <div>
                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter leading-none">{title}</h3>
                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{subtitle}</p>
            </div>
        </div>
    );
}

function Field({ label, required, error, children, className }) {
    return (
        <div className={cn("space-y-2", className)}>
            <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                {label} {required && <span className="text-red-500">*</span>}
            </label>
            {children}
            {error && <p className="text-[10px] text-red-500 font-bold mt-1 ml-1 uppercase tracking-widest animate-in fade-in slide-in-from-top-1">{error}</p>}
        </div>
    );
}

function Input({ className, error, ...props }) {
    return (
        <input 
            className={cn(
                'w-full px-4 py-3.5 bg-gray-50 border rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all',
                error ? 'border-red-500' : 'border-gray-100',
                className
            )} 
            {...props} 
        />
    );
}

function Select({ className, children, error, ...props }) {
    return (
        <select 
            className={cn(
                'w-full px-4 py-3.5 bg-gray-50 border rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all',
                error ? 'border-red-500' : 'border-gray-100',
                className
            )} 
            {...props}
        >
            {children}
        </select>
    );
}

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
        status_perkawinan:  domisili?.status_perkawinan ?? 'Belum Kawin',
        kewarganegaraan:    domisili?.kewarganegaraan ?? 'Indonesia',
        pekerjaan:          domisili?.pekerjaan ?? '',
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
    const filteredRws = data.dusun_id 
        ? rwList?.filter(rw => rtList?.some(rt => String(rt.rw_id) === String(rw.id) && String(rt.dusun_id) === String(data.dusun_id)))
        : rwList;

    const filteredRts = (data.dusun_id && data.rw_id)
        ? rtList?.filter(rt => String(rt.dusun_id) === String(data.dusun_id) && String(rt.rw_id) === String(data.rw_id))
        : (data.rw_id ? rtList?.filter(rt => String(rt.rw_id) === String(data.rw_id)) : rtList);

    // Geographic selection handlers
    const handleDusunChange = (dusunId) => {
        setData(prev => ({
            ...prev,
            dusun_id: dusunId,
            rw_id: '', // Reset children on parent change
            rt_id: ''
        }));
    };

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

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <MapPin className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none text-left">
                                    {isEdit ? 'Edit Data Domisili' : 'Daftar Pendatang Baru'}
                                </h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic text-left">
                                    Pendaftaran Warga Sementara Desa Cibatu
                                </p>
                            </div>
                        </div>
                        <Link 
                            href={route('domisili.index')}
                            className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all w-fit"
                        >
                            <ArrowLeft className="w-4 h-4 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>

                <div className="w-full">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Section 1: Data KTP */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-8">
                            <div className="flex items-center gap-3 mb-8 pb-4 border-b border-gray-50">
                                <div className="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                                    <User className="w-5 h-5 text-green-600" />
                                </div>
                                <div>
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Data Identitas (KTP)</h3>
                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sesuai Kartu Tanda Penduduk Asal</p>
                                </div>
                            </div>
                            
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <Field label="NIK" required error={errors.nik}>
                                    <Input value={data.nik} onChange={e => setData('nik', e.target.value)} maxLength={16}
                                        placeholder="16 digit NIK" error={errors.nik} className={cn(nikStatus === 'blocked' && 'border-red-400 ring-1 ring-red-400')} />
                                    <div className="mt-2">{nikIndicator()}</div>
                                </Field>
                                <Field label="Nama Lengkap" required error={errors.nama}>
                                    <Input value={data.nama} onChange={e => setData('nama', e.target.value)} placeholder="Nama sesuai KTP" error={errors.nama} />
                                </Field>
                                <Field label="Tempat Lahir" error={errors.tempat_lahir}>
                                    <Input value={data.tempat_lahir} onChange={e => setData('tempat_lahir', e.target.value)} placeholder="Kota/Kabupaten" error={errors.tempat_lahir} />
                                </Field>
                                <Field label="Tanggal Lahir" error={errors.tanggal_lahir}>
                                    <Input type="date" value={data.tanggal_lahir} onChange={e => setData('tanggal_lahir', e.target.value)} error={errors.tanggal_lahir} />
                                </Field>
                                <Field label="Jenis Kelamin" required error={errors.jenis_kelamin}>
                                    <Select value={data.jenis_kelamin} onChange={e => setData('jenis_kelamin', e.target.value)} error={errors.jenis_kelamin}>
                                        <option value="">-- Pilih --</option>
                                        <option value="L">Laki-Laki</option>
                                        <option value="P">Perempuan</option>
                                    </Select>
                                </Field>
                                <Field label="Status Perkawinan" required error={errors.status_perkawinan}>
                                    <Select value={data.status_perkawinan} onChange={e => setData('status_perkawinan', e.target.value)} error={errors.status_perkawinan}>
                                        <option value="Belum Kawin">Belum Kawin</option>
                                        <option value="Kawin">Kawin</option>
                                        <option value="Cerai Hidup">Cerai Hidup</option>
                                        <option value="Cerai Mati">Cerai Mati</option>
                                    </Select>
                                </Field>
                                <Field label="Agama" error={errors.agama}>
                                    <Select value={data.agama} onChange={e => setData('agama', e.target.value)} error={errors.agama}>
                                        <option value="">-- Pilih --</option>
                                        {AGAMA_OPTIONS.map(a => <option key={a} value={a}>{a}</option>)}
                                    </Select>
                                </Field>
                                <Field label="Kewarganegaraan" error={errors.kewarganegaraan}>
                                    <Input value={data.kewarganegaraan} onChange={e => setData('kewarganegaraan', e.target.value)} placeholder="Indonesia" error={errors.kewarganegaraan} />
                                </Field>
                                <Field label="Pekerjaan" error={errors.pekerjaan}>
                                    <Input value={data.pekerjaan} onChange={e => setData('pekerjaan', e.target.value)} placeholder="Pekerjaan saat ini" error={errors.pekerjaan} />
                                </Field>
                                <Field label="Asal Daerah" error={errors.asal_daerah}>
                                    <Input value={data.asal_daerah} onChange={e => setData('asal_daerah', e.target.value)} placeholder="Kota/Kabupaten asal" error={errors.asal_daerah} />
                                </Field>
                                <div className="sm:col-span-2">
                                    <Field label="Alamat Asal (sesuai KTP)" error={errors.alamat_asal}>
                                        <textarea 
                                            rows={2} 
                                            value={data.alamat_asal} 
                                            onChange={e => setData('alamat_asal', e.target.value)}
                                            placeholder="Alamat lengkap sesuai KTP..."
                                            className={cn(
                                                "w-full px-4 py-3.5 bg-gray-50 border rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all resize-none",
                                                errors.alamat_asal ? 'border-red-500' : 'border-gray-100'
                                            )} 
                                        />
                                    </Field>
                                </div>
                            </div>
                        </div>

                        {/* Section 2: Data Domisili */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-8">
                            <SectionHeader icon={Home} title="Data Domisili di Desa" subtitle="Lokasi & keperluan tinggal sementara" color="blue" />
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <Field label="Dusun" error={errors.dusun_id}>
                                    <Select value={data.dusun_id} onChange={e => handleDusunChange(e.target.value)} error={errors.dusun_id}>
                                        <option value="">-- Pilih Dusun --</option>
                                        {dusunList?.map(d => <option key={d.id} value={d.id}>{d.nama}</option>)}
                                    </Select>
                                </Field>
                                <Field label="RW" required error={errors.rw_id}>
                                    <Select value={data.rw_id} onChange={e => handleRwChange(e.target.value)} error={errors.rw_id}>
                                        <option value="">-- Pilih RW --</option>
                                        {filteredRws?.map(rw => <option key={rw.id} value={rw.id}>RW {rw.kode}</option>)}
                                    </Select>
                                </Field>
                                <Field label="RT" required error={errors.rt_id}>
                                    <Select value={data.rt_id} onChange={e => handleRtChange(e.target.value)} error={errors.rt_id}>
                                        <option value="">-- Pilih RT --</option>
                                        {filteredRts?.map(rt => <option key={rt.id} value={rt.id}>RT {rt.kode}</option>)}
                                    </Select>
                                </Field>
                                <Field label="Keperluan Domisili" error={errors.keperluan_domisili}>
                                    <Select value={data.keperluan_domisili} onChange={e => setData('keperluan_domisili', e.target.value)} error={errors.keperluan_domisili}>
                                        <option value="">-- Pilih Keperluan --</option>
                                        {KEPERLUAN_OPTIONS.map(k => <option key={k.value} value={k.value}>{k.label}</option>)}
                                    </Select>
                                </Field>
                                <Field label="Tanggal Masuk" required error={errors.tanggal_masuk}>
                                    <Input type="date" value={data.tanggal_masuk} onChange={e => setData('tanggal_masuk', e.target.value)} error={errors.tanggal_masuk} />
                                </Field>
                                <div className="sm:col-span-2">
                                    <Field label="Alamat Tinggal di Desa" required error={errors.alamat_tinggal}>
                                        <textarea 
                                            rows={2} 
                                            value={data.alamat_tinggal} 
                                            onChange={e => setData('alamat_tinggal', e.target.value)}
                                            placeholder="Alamat lengkap tempat tinggal sementara di desa..."
                                            className={cn(
                                                "w-full px-4 py-3.5 bg-gray-50 border rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all resize-none",
                                                errors.alamat_tinggal ? 'border-red-500' : 'border-gray-100'
                                            )} 
                                        />
                                    </Field>
                                </div>
                                <div className="sm:col-span-2">
                                    <Field label="Catatan Tambahan" error={errors.catatan}>
                                        <textarea 
                                            rows={2} 
                                            value={data.catatan} 
                                            onChange={e => setData('catatan', e.target.value)}
                                            placeholder="Catatan atau informasi tambahan (opsional)..."
                                            className={cn(
                                                "w-full px-4 py-3.5 bg-gray-50 border rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all resize-none",
                                                errors.catatan ? 'border-red-500' : 'border-gray-100'
                                            )} 
                                        />
                                    </Field>
                                </div>
                            </div>
                        </div>

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
