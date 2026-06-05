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
            type.templates.filter(t => t.is_active).forEach(t => {
                if (Array.isArray(t.form_json)) {
                    allFields = [...allFields, ...t.form_json];
                }
            });
        }

        // Initialize data_tambahan from flat fields
        const initialData = {};
        allFields.forEach(field => {
            if (field.name) initialData[field.name] = '';
        });
        
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
        const newData = {
            ...data.data_tambahan,
            [key]: value
        };
        setData('data_tambahan', newData);

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

    return (
        <AuthenticatedLayout user={auth.user} title="Buat Surat Baru">
            <Head title="Buat Surat Baru" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader 
                    title={step === 1 ? 'Pilih Jenis Surat' : 'Isi Detail Surat'}
                    subtitle={step === 1 ? 'Silakan pilih kategori surat yang akan dibuat' : `Konfigurasi pengajuan ${selectedType.nama}`}
                    icon={FileSignature}
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
                    <form onSubmit={handleSubmit} className="grid grid-cols-1 xl:grid-cols-3 gap-6 animate-in zoom-in-95 duration-500">
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

                                // Sub-template fields
                                if (selectedType.has_multi_template && Array.isArray(selectedType.templates)) {
                                    selectedType.templates.filter(t => t.is_active && Array.isArray(t.form_json) && t.form_json.length > 0).forEach(t => {
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
                        <div className="space-y-6">
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
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
