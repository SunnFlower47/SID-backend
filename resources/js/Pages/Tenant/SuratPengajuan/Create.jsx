import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    FileSignature, ArrowLeft, Search, User, 
    Calendar, MapPin, Info, Save, Layers,
    CheckCircle2, Plus, X, ChevronRight,
    FileText, UserPlus, MapPinned, CreditCard,
    Clock
} from 'lucide-react';
import * as LucideIcons from 'lucide-react';
import { cn } from '@/lib/utils';
import axios from 'axios';
import Swal from 'sweetalert2';
import TypeSelector from './Components/TypeSelector';
import ResidentSearch from './Components/ResidentSearch';
import ManualDomisiliForm from './Components/ManualDomisiliForm';
import KematianForm from './Components/KematianForm';

// Shared Components
import { PageHeader } from '@/Components/Shared';

const dayAfter = (days) => {
    const d = new Date();
    d.setDate(d.getDate() + days);
    return d.toISOString().split('T')[0];
};

export default function Create({ auth, suratTypes, wilayah }) {
    const [step, setStep] = useState(1);
    const [selectedType, setSelectedType] = useState(null);
    const [isManualInput, setIsManualInput] = useState(false);
    const [residents, setResidents] = useState([]);
    const [isSearching, setIsSearching] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    const [selectedResident, setSelectedResident] = useState(null);
    const [selectedTemplateIds, setSelectedTemplateIds] = useState([]);

    const { data, setData, post, processing, errors, reset } = useForm({
        jenis_surat: '',
        penduduk_id: '',
        keperluan: '',
        tujuan: '',
        tanggal_surat: new Date().toISOString().split('T')[0],
        keterangan_tambahan: '',
        data_tambahan: {},
        penandatangan: 'kepala_desa'
    });

    // Handle URL parameters for "One-Click Extension"
    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        const typeId = params.get('type');
        const nik = params.get('nik');

        if (typeId && suratTypes.length > 0) {
            const type = suratTypes.find(t => t.id === typeId);
            if (type) {
                handleSelectType(type);
                if (nik && typeId === 'keterangan-domisili') {
                    // Small timeout to ensure handleSelectType state has been processed
                    setTimeout(() => {
                        updateDataTambahan('nik', nik);
                    }, 100);
                }
            }
        }
    }, [suratTypes]);

    const handleSelectType = (type) => {
        setSelectedType(type);
        setData('jenis_surat', type.id);
        
        // Auto-toggle manual input for Domisili
        if (type.id === 'keterangan-domisili') {
            setIsManualInput(true);
        } else {
            setIsManualInput(false);
        }

        // Collect all fields for initialization
        let allFields = [];
        if (Array.isArray(type.form_json)) {
            allFields = [...type.form_json];
        }
        if (type.has_multi_template && Array.isArray(type.templates)) {
            const activeIds = type.templates.filter(t => t.is_active).map(t => t.id);
            setSelectedTemplateIds(activeIds);
            type.templates.filter(t => t.is_active).forEach(t => {
                if (Array.isArray(t.form_json)) {
                    allFields = [...allFields, ...t.form_json];
                }
            });
        } else {
            setSelectedTemplateIds([]);
        }

        // Initialize data_tambahan from flat fields
        const initialData = {};
        allFields.forEach(field => {
            if (field.name) initialData[field.name] = '';
        });
        // Simpan selected template IDs untuk dipakai di print panel
        if (type.has_multi_template && Array.isArray(type.templates)) {
            initialData._selected_template_ids = type.templates.filter(t => t.is_active).map(t => t.id);
        }
        
        // Special fields for Domisili manual input
        if (type.id === 'keterangan-domisili') {
            Object.assign(initialData, {
                nik: '',
                nama: '',
                tempat_lahir: '',
                tanggal_lahir: '',
                jenis_kelamin: 'L',
                agama: 'Islam',
                status_perkawinan: 'Belum Kawin',
                kewarganegaraan: 'Indonesia',
                pekerjaan: '',
                alamat_asal: '',
                alamat_tinggal: '',
                rt_id: '',
                rw_id: '',
                dusun_id: '',
                tanggal_masuk: new Date().toISOString().split('T')[0],
                tanggal_berlaku: dayAfter(90)
            });
        }

        // Special fields for Keterangan Kematian
        if (type.id === 'kematian') {
            Object.assign(initialData, {
                hari_meninggal: 'Senin',
                jam_meninggal: '12:00',
                bertempat_di: 'RUMAH SAKIT',
                alasan: 'Sakit',
                hari_pemakaman: 'Senin',
                tanggal_pemakaman: new Date().toISOString().split('T')[0],
                jam_pemakaman: '15:00',
                lokasi_pemakaman: 'TPU Desa',
                pelapor_nama: '',
                pelapor_hubungan: 'Keluarga',
                pelapor_umur: '',
                pelapor_pekerjaan: '',
                pelapor_alamat: ''
            });
        }
        
        setData('data_tambahan', initialData);
        setStep(2);
    };

    const toggleTemplate = (templateId) => {
        const isCurrentlySelected = selectedTemplateIds.includes(templateId);
        const newSelectedIds = isCurrentlySelected
            ? selectedTemplateIds.filter(id => id !== templateId)
            : [...selectedTemplateIds, templateId];

        setSelectedTemplateIds(newSelectedIds);

        setData(prevData => {
            const newDataTambahan = {
                ...prevData.data_tambahan,
                _selected_template_ids: newSelectedIds
            };
            // Inisialisasi field form jika template baru dipilih
            if (!isCurrentlySelected) {
                const template = selectedType?.templates?.find(t => t.id === templateId);
                if (template && Array.isArray(template.form_json)) {
                    template.form_json.forEach(field => {
                        if (field.name && !(field.name in newDataTambahan)) {
                            newDataTambahan[field.name] = '';
                        }
                    });
                }
            }
            return { ...prevData, data_tambahan: newDataTambahan };
        });
    };

    const searchResidents = async (query) => {
        if (query.length < 3) {
            setResidents([]);
            return;
        }
        setIsSearching(true);
        try {
            // Using a more standard resident search endpoint
            const url = '/penduduk/search';
            const response = await axios.get(url, { params: { q: query } });
            
            // Map results to ensure consistent field names
            const mappedResults = response.data.map(r => ({
                ...r,
                // Ensure RT/RW labels are available if missing from this endpoint
                rt: r.rt_label || (r.rt?.kode || '-'),
                rw: r.rw_label || (r.rw?.kode || '-'),
                dusun: r.dusun_label || (r.dusun?.nama || '-')
            }));

            setResidents(mappedResults);
        } catch (err) {
            console.error('Search error:', err);
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
        if (key === 'nik' && value.length === 16 && selectedType?.id === 'keterangan-domisili') {
            checkDomisiliNik(value);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('admin.surat-pengajuan.store'), {
            onSuccess: () => {
                Swal.fire({
                    title: 'BERHASIL!',
                    text: 'Surat berhasil dibuat.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    };

    const getGradientClasses = (colorName) => {
        if (!colorName) return "from-green-600 via-green-700 to-green-800";
        const gradients = {
            blue: "from-blue-600 via-blue-700 to-blue-800",
            green: "from-green-600 via-green-700 to-green-800",
            purple: "from-purple-600 via-purple-700 to-purple-800",
            orange: "from-orange-600 via-orange-700 to-orange-800",
            red: "from-red-600 via-red-700 to-red-800",
            pink: "from-pink-600 via-pink-700 to-pink-800",
            yellow: "from-yellow-500 via-yellow-600 to-yellow-700",
        };
        return gradients[colorName] || "from-green-600 via-green-700 to-green-800";
    };

    const getDynamicIcon = (iconName) => {
        if (!iconName) return FileSignature;
        return LucideIcons[iconName] || FileSignature;
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Buat Surat Baru">
            <Head title="Buat Surat Baru" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader 
                    title={step === 1 ? 'Pilih Jenis Surat' : 'Isi Detail Surat'}
                    subtitle={step === 1 ? 'Silakan pilih kategori surat yang akan dibuat' : `Konfigurasi pengajuan ${selectedType.nama}`}
                    icon={step === 2 && selectedType ? getDynamicIcon(selectedType.icon) : FileSignature}
                    gradient={step === 2 && selectedType ? getGradientClasses(selectedType.color) : 'from-green-600 via-green-700 to-green-800'}
                    backHref={step === 1 ? route('admin.surat-pengajuan.index') : null}
                    onBack={step === 2 ? () => setStep(1) : undefined}
                    backLabel={step === 2 ? "GANTI JENIS" : "KEMBALI"}
                />

                {step === 1 && (
                    <TypeSelector 
                        suratTypes={suratTypes} 
                        onSelectType={handleSelectType} 
                    />
                )}

                {step === 2 && (
                    <form onSubmit={handleSubmit} className="grid grid-cols-1 xl:grid-cols-3 gap-6 animate-in zoom-in-95 duration-500 items-start">
                        {/* Resident Selection & Base Info */}
                        <div className="xl:col-span-2 space-y-6">
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100">
                                <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                                    <div className="flex items-center gap-3">
                                        <User className="w-5 h-5 text-green-600" />
                                        <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Informasi Penduduk</h3>
                                    </div>
                                    {selectedType.id === 'keterangan-domisili' && (
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
                                            {isManualInput ? <User className="w-3 h-3 mr-2" /> : <UserPlus className="w-3 h-3 mr-2" />}
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
                                        <ManualDomisiliForm 
                                            data={data}
                                            updateDataTambahan={updateDataTambahan}
                                            isCheckingNik={isCheckingNik}
                                            wilayah={wilayah}
                                            checkDomisiliNik={checkDomisiliNik}
                                        />
                                    )}
                                </div>
                            </div>

                            {/* Dedicated Kematian Section */}
                            {selectedType.id === 'kematian' && (
                                <KematianForm 
                                    data={data}
                                    updateDataTambahan={updateDataTambahan}
                                />
                            )}

                            {/* Dynamic Fields Section(s) */}
                            {(() => {
                                const renderFields = (fields) => (
                                    <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {fields.map((field, idx) => (
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
                                                        placeholder={field.placeholder}
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
                                                        placeholder={field.placeholder}
                                                        required={field.required}
                                                    />
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                );

                                const sections = [];
                                
                                // Global fields
                                if (selectedType.form_json && selectedType.form_json.length > 0) {
                                    sections.push(
                                        <div key="global" className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden animate-in slide-in-from-bottom-4 duration-500 mb-6">
                                            <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                                                <Layers className="w-5 h-5 text-green-600" />
                                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Data Umum / Tambahan</h3>
                                            </div>
                                            {renderFields(selectedType.form_json)}
                                        </div>
                                    );
                                }

                                // Sub-template fields — hanya untuk template yang dipilih
                                if (selectedType.has_multi_template && Array.isArray(selectedType.templates)) {
                                    selectedType.templates
                                        .filter(t =>
                                            selectedTemplateIds.includes(t.id) &&
                                            t.is_active &&
                                            Array.isArray(t.form_json) &&
                                            t.form_json.length > 0
                                        )
                                        .forEach(t => {
                                        // Cek gender_filter
                                        if (t.gender_filter === 'L' && selectedResident?.jenis_kelamin === 'P') return;
                                        if (t.gender_filter === 'P' && selectedResident?.jenis_kelamin === 'L') return;
                                        
                                        sections.push(
                                            <div key={`sub_${t.id}`} className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden animate-in slide-in-from-bottom-4 duration-500 mb-6">
                                                <div className="p-6 border-b border-indigo-100 bg-indigo-50/50 flex items-center gap-3">
                                                    <FileText className="w-5 h-5 text-indigo-600" />
                                                    <h3 className="text-sm font-black text-indigo-900 uppercase italic tracking-tighter">DATA TAMBAHAN — {t.kode} • {t.nama}</h3>
                                                </div>
                                                {renderFields(t.form_json)}
                                            </div>
                                        );
                                    });
                                }

                                return sections;
                            })()}

                            {/* Additional Letter Settings */}
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                                <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                                    <Info className="w-5 h-5 text-green-600" />
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Detail Keperluan</h3>
                                </div>
                                <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div className="md:col-span-2 space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Maksud / Keperluan</label>
                                        <textarea 
                                            value={data.keperluan}
                                            onChange={e => setData('keperluan', e.target.value)}
                                            className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                            rows="2"
                                            placeholder="Contoh: Mengurus persyaratan nikah, mendaftar sekolah, dll..."
                                        ></textarea>
                                        {errors.keperluan && <p className="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{errors.keperluan}</p>}
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tujuan / Tujuan Penggunaan</label>
                                        <input 
                                            type="text"
                                            value={data.tujuan}
                                            onChange={e => setData('tujuan', e.target.value)}
                                            className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                            placeholder="Contoh: Kantor KUA Kec. Cibatu"
                                        />
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
                                </div>
                            </div>
                        </div>

                        {/* Sidebar / Submission */}
                        <div className="space-y-6 sticky top-6">
                            {/* Sub-Template Selector — dipindah ke sidebar agar sejajar dengan pengesahan */}
                            {selectedType.has_multi_template && (() => {
                                const genderFilteredTemplates = (selectedType.templates || []).filter(t => {
                                    if (!t.is_active) return false;
                                    const gender = selectedResident?.jenis_kelamin;
                                    if (t.gender_filter === 'L' && gender === 'P') return false;
                                    if (t.gender_filter === 'P' && gender === 'L') return false;
                                    return true;
                                });
                                if (genderFilteredTemplates.length === 0) return null;
                                const selectedCount = selectedTemplateIds.filter(id => genderFilteredTemplates.some(t => t.id === id)).length;
                                return (
                                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden animate-in slide-in-from-right-4 duration-500">
                                        <div className="p-5 border-b border-indigo-100 bg-gradient-to-r from-indigo-50 to-purple-50/50 flex flex-col items-start gap-2">
                                            <div className="flex items-center gap-3 w-full justify-between">
                                                <div className="flex items-center gap-3">
                                                    <Layers className="w-5 h-5 text-indigo-600" />
                                                    <h3 className="text-sm font-black text-indigo-900 uppercase italic tracking-tighter">Pilih Dokumen</h3>
                                                </div>
                                                <span className={cn(
                                                    "text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest",
                                                    selectedCount > 0 ? "bg-indigo-600 text-white" : "bg-gray-100 text-gray-500"
                                                )}>
                                                    {selectedCount}/{genderFilteredTemplates.length} dipilih
                                                </span>
                                            </div>
                                            <p className="text-[9px] font-bold text-indigo-400 uppercase tracking-widest pl-8">Form muncul otomatis sesuai pilihan</p>
                                        </div>
                                        <div className="p-4 flex flex-col gap-3">
                                            {genderFilteredTemplates.map(t => {
                                                const isSelected = selectedTemplateIds.includes(t.id);
                                                const hasExtraFields = Array.isArray(t.form_json) && t.form_json.length > 0;
                                                return (
                                                    <div
                                                        key={t.id}
                                                        onClick={() => toggleTemplate(t.id)}
                                                        className={cn(
                                                            "flex items-start gap-3 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 select-none",
                                                            isSelected
                                                                ? "bg-indigo-50 border-indigo-300 shadow-sm"
                                                                : "bg-gray-50 border-transparent hover:border-gray-200 hover:bg-white hover:shadow-sm"
                                                        )}
                                                    >
                                                        <div className={cn(
                                                            "mt-0.5 shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200",
                                                            isSelected ? "bg-indigo-600 border-indigo-600" : "border-gray-300"
                                                        )}>
                                                            {isSelected && (
                                                                <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={3}>
                                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            )}
                                                        </div>
                                                        <div className="flex-1 min-w-0">
                                                            <div className="flex items-center gap-1.5 flex-wrap mb-1">
                                                                <span className="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[9px] font-black uppercase tracking-widest rounded-lg">
                                                                    {t.kode}
                                                                </span>
                                                                {hasExtraFields && (
                                                                    <span className="px-2 py-0.5 bg-green-50 text-green-600 text-[9px] font-black uppercase tracking-widest rounded-lg">
                                                                        + Form
                                                                    </span>
                                                                )}
                                                                {t.gender_filter !== 'all' && (
                                                                    <span className="px-2 py-0.5 bg-purple-50 text-purple-600 text-[9px] font-black uppercase tracking-widest rounded-lg">
                                                                        {t.gender_filter === 'L' ? '♂' : '♀'}
                                                                    </span>
                                                                )}
                                                            </div>
                                                            <p className="text-xs font-bold text-gray-800 leading-tight">{t.nama}</p>
                                                            {t.deskripsi && (
                                                                <p className="text-[9px] text-gray-400 font-bold mt-0.5 line-clamp-2">{t.deskripsi}</p>
                                                            )}
                                                        </div>
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>
                                );
                            })()}

                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                                <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                                    <FileSignature className="w-5 h-5 text-green-600" />
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Pengesahan</h3>
                                </div>
                                <div className="p-6 space-y-6">
                                    <div className="space-y-3">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Penandatangan</label>
                                        <div className="grid grid-cols-1 gap-3">
                                            {[
                                                { id: 'kepala_desa', label: 'Kepala Desa', icon: <CreditCard className="w-4 h-4" /> },
                                                { id: 'sekretaris_desa', label: 'Sekretaris Desa', icon: <CreditCard className="w-4 h-4" /> }
                                            ].map(signer => (
                                                <button
                                                    key={signer.id}
                                                    type="button"
                                                    onClick={() => setData('penandatangan', signer.id)}
                                                    className={cn(
                                                        "flex items-center justify-between px-5 py-4 rounded-2xl border transition-all text-left",
                                                        data.penandatangan === signer.id 
                                                            ? "bg-green-600 border-green-700 text-white shadow-lg shadow-green-200" 
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
                                                        <span className="text-[10px] font-black uppercase tracking-widest">{signer.label}</span>
                                                    </div>
                                                    {data.penandatangan === signer.id && <CheckCircle2 className="w-4 h-4" />}
                                                </button>
                                            ))}
                                        </div>
                                    </div>

                                    <div className="p-4 bg-yellow-50 rounded-2xl border border-yellow-100">
                                        <div className="flex gap-3">
                                            <Info className="w-4 h-4 text-yellow-600 shrink-0" />
                                            <p className="text-[9px] font-bold text-yellow-700 uppercase tracking-widest leading-relaxed">
                                                Surat akan otomatis berstatus <b className="text-green-700">SELESAI</b> dan dapat langsung diunduh setelah disimpan.
                                            </p>
                                        </div>
                                    </div>

                                    <button 
                                        type="submit"
                                        disabled={processing || (!selectedResident && !isManualInput)}
                                        className="w-full py-4 bg-gradient-to-r from-green-600 to-green-800 text-white rounded-3xl text-sm font-black shadow-xl shadow-green-200 hover:shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 disabled:opacity-50 disabled:scale-100 disabled:shadow-none"
                                    >
                                        {processing ? (
                                            <Layers className="w-5 h-5 animate-spin" />
                                        ) : (
                                            <Save className="w-5 h-5" />
                                        )}
                                        SIMPAN & CETAK SURAT
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
