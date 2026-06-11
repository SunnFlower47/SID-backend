import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    FileSignature, ArrowLeft, Search, User, 
    Calendar, MapPin, Info, Save, Layers,
    CheckCircle2, X, FileText, UserPlus, 
    CreditCard, Settings2, Download, Stamp
} from 'lucide-react';
import { cn } from '@/lib/utils';
import axios from 'axios';
import Swal from 'sweetalert2';
import ResidentSearch from './Components/ResidentSearch';
import ManualDomisiliForm from './Components/ManualDomisiliForm';
import KematianForm from './Components/KematianForm';

// Shared Components
import { PageHeader } from '@/Components/Shared';

export default function Edit({ auth, suratPengajuan, suratTypes, wilayah }) {
    const [selectedType, setSelectedType] = useState(null);
    const [residents, setResidents] = useState([]);
    const [isSearching, setIsSearching] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    const [selectedResident, setSelectedResident] = useState(suratPengajuan.penduduk);
    const [isManualInput, setIsManualInput] = useState(suratPengajuan.penduduk_id === null);

    const { data, setData, put, processing, errors } = useForm({
        jenis_surat: suratPengajuan.jenis_surat,
        penduduk_id: suratPengajuan.penduduk_id,
        keperluan: suratPengajuan.keperluan || '',
        tujuan: suratPengajuan.tujuan || '',
        tanggal_surat: suratPengajuan.tanggal_surat.split('T')[0],
        keterangan_tambahan: suratPengajuan.keterangan_tambahan || '',
        data_tambahan: (() => {
            let dt = suratPengajuan.data_tambahan || {};
            
            // For domisili forms, clean up any dm_ prefixed keys to prevent doubling in DB
            if (['keterangan-domisili', 'domisili'].includes(suratPengajuan.jenis_surat)) {
                let cleaned = {};
                
                // First, recover old dm_ keys to standard keys if standard is missing
                if (dt.dm_nik && !dt.nik) {
                    cleaned = {
                        nik: dt.dm_nik,
                        nama: dt.dm_nama,
                        tempat_lahir: dt.dm_tempat_lahir,
                        tanggal_lahir: dt.dm_tanggal_lahir,
                        jenis_kelamin: dt.dm_jenis_kelamin === 'Laki-laki' ? 'L' : (dt.dm_jenis_kelamin === 'Perempuan' ? 'P' : dt.dm_jenis_kelamin),
                        agama: dt.dm_agama,
                        status_perkawinan: dt.dm_status_perkawinan,
                        kewarganegaraan: dt.dm_kewarganegaraan,
                        pekerjaan: dt.dm_pekerjaan,
                        asal_daerah: dt.dm_asal_daerah,
                        alamat_asal: dt.dm_alamat_asal,
                        alamat_tinggal: dt.dm_alamat_tinggal,
                        tanggal_masuk: dt.dm_tanggal_masuk,
                        tanggal_berlaku: dt.dm_tanggal_berlaku,
                        perpanjangan_ke: dt.dm_perpanjangan_ke || 0,
                        keperluan: dt.dm_keperluan || ''
                    };
                }

                // Add all non-dm_ keys from dt into cleaned
                for (let key in dt) {
                    if (!key.startsWith('dm_')) {
                        cleaned[key] = dt[key];
                    }
                }

                // Recover missing rt_id, rw_id, dusun_id from string dm_ values
                if (!cleaned.dusun_id && dt.dm_dusun && wilayah?.dusun) {
                    const match = wilayah.dusun.find(d => d.nama === dt.dm_dusun);
                    if (match) cleaned.dusun_id = match.id;
                }
                if (!cleaned.rw_id && dt.dm_rw && wilayah?.rw) {
                    const match = wilayah.rw.find(r => String(r.kode) === String(dt.dm_rw) || Number(r.kode) === Number(dt.dm_rw));
                    if (match) cleaned.rw_id = match.id;
                }
                if (!cleaned.rt_id && dt.dm_rt && wilayah?.rt) {
                    const rts = wilayah.rt.filter(r => String(r.kode) === String(dt.dm_rt) || Number(r.kode) === Number(dt.dm_rt));
                    if (rts.length > 0) {
                        const rwMatch = dt.dm_rw ? wilayah?.rw?.find(r => String(r.kode) === String(dt.dm_rw) || Number(r.kode) === Number(dt.dm_rw)) : null;
                        if (rwMatch) {
                            const exactRt = rts.find(r => String(r.rw_id) === String(rwMatch.id));
                            if (exactRt) cleaned.rt_id = exactRt.id;
                        } else {
                            cleaned.rt_id = rts[0].id;
                        }
                    }
                }
                
                return Object.keys(cleaned).length > 0 
                    ? cleaned 
                    : {
                        nik: suratPengajuan.nik_pengaju || '',
                        nama: suratPengajuan.nama_pengaju || ''
                    };
            }

            // For other types
            return Object.keys(dt).length > 0 
                ? dt 
                : {
                    nik: suratPengajuan.nik_pengaju || '',
                    nama: suratPengajuan.nama_pengaju || ''
                };
        })(),
        penandatangan: suratPengajuan.penandatangan || 'kepala_desa'
    });

    useEffect(() => {
        // Initialize selected type and dynamic fields
        const type = suratTypes.find(t => t.id === suratPengajuan.jenis_surat);
        if (type) {
            handleTypeSetup(type, true);
        }
    }, []);

    const handleTypeSetup = (type, isInitial = false) => {
        setSelectedType(type);
        
        // Flatten form_json
        let flatFields = [];
        if (Array.isArray(type.form_json)) {
            type.form_json.forEach(item => {
                if (item.fields && Array.isArray(item.fields)) {
                    flatFields = [...flatFields, ...item.fields];
                } else {
                    flatFields.push(item);
                }
            });
        }

        setSelectedType({
            ...type,
            form_json: flatFields
        });

        if (!isInitial) {
            setData('jenis_surat', type.id);
            // Initialize empty data_tambahan for new fields if switching types
            const newDataTambahan = {};
            flatFields.forEach(field => {
                if (field.name) newDataTambahan[field.name] = '';
            });
            setData('data_tambahan', newDataTambahan);
        }
    };

    const searchResidents = async (query) => {
        if (query.length < 3) {
            setResidents([]);
            return;
        }
        setIsSearching(true);
        try {
            const response = await axios.get(route('admin.surat-pengajuan.search-penduduk'), { params: { q: query } });
            setResidents(response.data);
        } catch (err) {
            console.error(err);
        } finally {
            setIsSearching(false);
        }
    };

    const handleSelectResident = (resident) => {
        setSelectedResident(resident);
        setData('penduduk_id', resident.id);
        setResidents([]);
        setSearchQuery('');
    };

    const [isCheckingNik, setIsCheckingNik] = useState(false);

    const checkDomisiliNik = async (nik) => {
        if (nik.length !== 16) return;
        setIsCheckingNik(true);
        try {
            const response = await axios.get(route('admin.surat-pengajuan.check-domisili'), { params: { nik } });
            if (response.data.exists) {
                Swal.fire({
                    title: 'WARGA TERDAFTAR!',
                    text: 'NIK ini sudah terdaftar sebagai penduduk domisili. Data akan otomatis terisi dan masa berlaku akan diperbarui.',
                    icon: 'info',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                });
                
                // Auto fill the data_tambahan
                setData('data_tambahan', {
                    ...data.data_tambahan,
                    ...response.data.data,
                    nik: nik // Keep current nik
                });
            }
        } catch (err) {
            console.error(err);
        } finally {
            setIsCheckingNik(false);
        }
    };

    const updateDataTambahan = (key, value) => {
        setData(prevData => ({
            ...prevData,
            data_tambahan: {
                ...prevData.data_tambahan,
                [key]: value
            }
        }));

        // Trigger NIK check for domisili manual input
        if (key === 'nik' && value.length === 16 && ['keterangan-domisili', 'domisili'].includes(selectedType?.id)) {
            checkDomisiliNik(value);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('admin.surat-pengajuan.update', suratPengajuan.id), {
            onSuccess: () => {
                Swal.fire({
                    title: 'BERHASIL!',
                    text: 'Perubahan surat berhasil disimpan.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Edit Pengajuan Surat">
            <Head title={`Edit Surat - ${suratPengajuan.nomor_surat || 'Draft'}`} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader 
                    title="Edit Pengajuan Surat"
                    subtitle={`NOMOR: ${suratPengajuan.nomor_surat || 'DRAFT'}`}
                    icon={FileSignature}
                    backHref={route('admin.surat-pengajuan.index')}
                    backLabel="BATAL"
                />

                <form onSubmit={handleSubmit} className="grid grid-cols-1 xl:grid-cols-3 gap-6 animate-in zoom-in-95 duration-500">
                    <div className="xl:col-span-2 space-y-6">
                        {/* Resident Selection */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between text-left">
                                <div className="flex items-center gap-3">
                                    <User className="w-5 h-5 text-green-600" />
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Informasi Penduduk</h3>
                                </div>
                                {['keterangan-domisili', 'domisili'].includes(data.jenis_surat) && (
                                    <button 
                                        type="button"
                                        onClick={() => {
                                            setIsManualInput(!isManualInput);
                                            setSelectedResident(null);
                                            setData('penduduk_id', '');
                                        }}
                                        className={cn(
                                            "flex items-center px-4 py-2 rounded-xl text-[9px] font-black transition-all border uppercase tracking-widest",
                                            isManualInput ? "bg-yellow-400 text-yellow-900 border-yellow-500" : "bg-gray-100 text-gray-500 border-transparent hover:bg-gray-200"
                                        )}
                                    >
                                        {isManualInput ? 'CARI WARGA' : 'INPUT MANUAL (NON-WARGA)'}
                                    </button>
                                )}
                            </div>
                            <div className="p-6">
                                {!isManualInput ? (
                                    <ResidentSearch 
                                        residents={residents}
                                        isSearching={isSearching}
                                        searchQuery={searchQuery}
                                        setSearchQuery={setSearchQuery}
                                        onSearch={searchResidents}
                                        onSelectResident={handleSelectResident}
                                        selectedResident={selectedResident}
                                        setSelectedResident={setSelectedResident}
                                        errors={errors}
                                    />
                                ) : (
                                    <div className="animate-in fade-in duration-300">
                                        {['keterangan-domisili', 'domisili'].includes(data.jenis_surat) ? (
                                            <ManualDomisiliForm 
                                                data={data}
                                                updateDataTambahan={updateDataTambahan}
                                                isCheckingNik={isCheckingNik}
                                                wilayah={wilayah}
                                                checkDomisiliNik={checkDomisiliNik}
                                            />
                                        ) : data.jenis_surat === 'kematian' ? (
                                            <KematianForm 
                                                data={data}
                                                updateDataTambahan={updateDataTambahan}
                                                wilayah={wilayah}
                                            />
                                        ) : (
                                            <div className="text-center p-6 text-gray-500 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                                <p className="text-xs font-bold uppercase tracking-widest">Warga belum dipilih</p>
                                                <p className="text-[10px] mt-1">Silakan klik "CARI WARGA" untuk memilih penduduk.</p>
                                            </div>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Letter Details */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3 text-left">
                                <Settings2 className="w-5 h-5 text-green-600" />
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Konfigurasi Surat</h3>
                            </div>
                            <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Surat</label>
                                    <select 
                                        value={data.jenis_surat}
                                        onChange={e => {
                                            const type = suratTypes.find(t => t.id === e.target.value);
                                            if (type) handleTypeSetup(type);
                                        }}
                                        className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                    >
                                        {suratTypes.map(t => <option key={t.id} value={t.id}>{t.nama}</option>)}
                                    </select>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Surat</label>
                                    <input 
                                        type="date"
                                        value={data.tanggal_surat}
                                        onChange={e => setData('tanggal_surat', e.target.value)}
                                        className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                    />
                                </div>
                                <div className="md:col-span-2 space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Keperluan / Maksud</label>
                                    <textarea 
                                        value={data.keperluan}
                                        onChange={e => setData('keperluan', e.target.value)}
                                        className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                        rows="2"
                                    ></textarea>
                                </div>
                                {data.tujuan !== undefined && (
                                    <div className="md:col-span-2 space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tujuan Penggunaan</label>
                                        <input 
                                            type="text"
                                            value={data.tujuan}
                                            onChange={e => setData('tujuan', e.target.value)}
                                            className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                        />
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Dedicated Kematian Section */}
                        {selectedType?.id === 'kematian' && (
                            <KematianForm 
                                data={data}
                                updateDataTambahan={updateDataTambahan}
                            />
                        )}

                        {/* Dynamic Fields */}
                        {selectedType?.form_json && selectedType.form_json.length > 0 && (
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden animate-in slide-in-from-bottom-4 duration-500">
                                <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3 text-left">
                                    <FileText className="w-5 h-5 text-green-600" />
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Data Spesifik {selectedType.nama}</h3>
                                </div>
                                <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                                    {selectedType.form_json.map((field, idx) => (
                                        <div key={idx} className={cn("space-y-2", field.type === 'textarea' && "md:col-span-2")}>
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                                {field.label} {field.required && <span className="text-red-500">*</span>}
                                            </label>
                                            
                                            {field.type === 'textarea' ? (
                                                <textarea 
                                                    value={data.data_tambahan[field.name] || ''}
                                                    onChange={e => updateDataTambahan(field.name, e.target.value)}
                                                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                                    rows="3"
                                                    required={field.required}
                                                ></textarea>
                                            ) : field.type === 'select' ? (
                                                <select 
                                                    value={data.data_tambahan[field.name] || ''}
                                                    onChange={e => updateDataTambahan(field.name, e.target.value)}
                                                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                                    required={field.required}
                                                >
                                                    <option value="">Pilih {field.label}...</option>
                                                    {field.options?.map((opt, oIdx) => (
                                                        <option key={oIdx} value={opt}>{opt}</option>
                                                    ))}
                                                </select>
                                            ) : (
                                                <input 
                                                    type={field.type}
                                                    value={data.data_tambahan[field.name] || ''}
                                                    onChange={e => updateDataTambahan(field.name, e.target.value)}
                                                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                                    required={field.required}
                                                />
                                            )}
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Sidebar Submission */}
                    <div className="space-y-6">
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3 text-left">
                                <Save className="w-5 h-5 text-green-600" />
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Simpan Perubahan</h3>
                            </div>
                            <div className="p-6 space-y-6 text-left">
                                <div className="space-y-3">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Penandatangan</label>
                                    <div className="grid grid-cols-1 gap-3">
                                        {[
                                            { id: 'kepala_desa',     label: 'Kepala Desa',      icon: <CreditCard className="w-4 h-4" />, desc: 'Tanda tangan basah / stempel' },
                                            { id: 'sekretaris_desa', label: 'Sekretaris Desa',  icon: <CreditCard className="w-4 h-4" />, desc: 'Tanda tangan basah / stempel' },
                                            { id: 'tte',             label: 'TTE BSrE',          icon: <Stamp className="w-4 h-4" />,      desc: 'Tanda Tangan Elektronik Tersertifikasi' },
                                        ].map(signer => (
                                            <button
                                                key={signer.id}
                                                type="button"
                                                onClick={() => setData('penandatangan', signer.id)}
                                                className={cn(
                                                    "flex items-center justify-between px-5 py-4 rounded-2xl border transition-all text-left",
                                                    data.penandatangan === signer.id 
                                                        ? signer.id === 'tte'
                                                            ? "bg-indigo-600 border-indigo-700 text-white shadow-lg shadow-indigo-200"
                                                            : "bg-green-600 border-green-700 text-white shadow-lg shadow-green-200" 
                                                        : "bg-gray-50 border-transparent text-gray-600 hover:bg-gray-100"
                                                )}
                                            >
                                                <div className="flex items-center gap-3">
                                                    <div className={cn(
                                                        "w-8 h-8 rounded-lg flex items-center justify-center",
                                                        data.penandatangan === signer.id ? "bg-white/20" : "bg-white"
                                                    )}>
                                                        {signer.icon}
                                                    </div>
                                                    <div>
                                                        <span className="block text-[10px] font-black uppercase tracking-widest">{signer.label}</span>
                                                        <span className={cn(
                                                            "block text-[8px] font-bold uppercase tracking-widest mt-0.5",
                                                            data.penandatangan === signer.id ? "text-white/70" : "text-gray-400"
                                                        )}>{signer.desc}</span>
                                                    </div>
                                                </div>
                                                {data.penandatangan === signer.id && <CheckCircle2 className="w-4 h-4 shrink-0" />}
                                            </button>
                                        ))}
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Keterangan Internal</label>
                                    <textarea 
                                        value={data.keterangan_tambahan}
                                        onChange={e => setData('keterangan_tambahan', e.target.value)}
                                        className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-[11px] font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                        rows="3"
                                        placeholder="Catatan tambahan untuk admin lain..."
                                    ></textarea>
                                </div>

                                <button 
                                    type="submit"
                                    disabled={processing}
                                    className="w-full py-4 bg-gradient-to-r from-green-600 to-green-800 text-white rounded-3xl text-sm font-black shadow-xl shadow-green-200 hover:shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 disabled:opacity-50"
                                >
                                    {processing ? <Layers className="w-5 h-5 animate-spin" /> : <Save className="w-5 h-5" />}
                                    SIMPAN PERUBAHAN
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
