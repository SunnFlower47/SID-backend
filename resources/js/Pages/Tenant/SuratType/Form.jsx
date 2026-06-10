import React, { useState, useEffect, useRef, useLayoutEffect } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    FileText, Save, ArrowLeft, Plus, Trash2, 
    Settings, Layout, Info, Type, List, 
    Calendar, CheckSquare, AlignLeft, Layers, Palette, HelpCircle, X,
    ChevronDown, ChevronUp, Search, FileEdit, ClipboardCheck, ClipboardList, 
    Stamp, Files, UserCheck, UserPlus, Contact, Fingerprint, Smile, 
    Heart, Map, Building, Building2, Landmark, Tent, Globe, Book, 
    BookOpen, Backpack, Calculator, HardHat, Baby, Accessibility, 
    Cross, Syringe, Gavel, ShieldCheck, Lock, Key, FileWarning, Inbox, 
    Send, Archive, Printer, QrCode, Image, Camera,
    Eye, AlertTriangle, CheckCircle2, HelpCircle as HelpCircleIcon, Loader2
} from 'lucide-react';
import * as LucideIcons from 'lucide-react';
import { cn } from '@/lib/utils';
import Modal from '@/Components/Shared/Modal';
import { PageHeader } from '@/Components/Shared';
import SubTemplateManager from './Components/SubTemplateManager';

export default function Form({ auth, suratType = null }) {
    const isEdit = !!suratType;
    const pickerRef = useRef(null);
    
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (pickerRef.current && !pickerRef.current.contains(event.target)) {
                setShowIconPicker(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []);
    
    const { data, setData, post, put, processing, errors } = useForm({
        id: suratType?.id || '',
        nama: suratType?.nama || '',
        kode: suratType?.kode || '',
        deskripsi: suratType?.deskripsi || '',
        persyaratan: suratType?.persyaratan || '',
        has_template: suratType?.has_template ?? true,
        has_multi_template: suratType?.has_multi_template ?? false,
        template_code: suratType?.template_code || '',
        icon: suratType?.icon || 'file-text',
        color: suratType?.color || 'blue',
        is_active: suratType?.is_active ?? true,
        is_public: suratType?.is_public ?? true,
        form_json: (suratType?.form_json || []).map((f, i) => ({
            ...f,
            _id: f._id || `f_${Date.now()}_${i}`,
        })),
        file_template: null,
    });

    const [showIconPicker, setShowIconPicker] = useState(false);
    const [searchIcon, setSearchIcon]         = useState('');
    const [editingOrder, setEditingOrder]     = useState(null);
    const [previewModal, setPreviewModal]     = useState(false);
    const [previewData, setPreviewData]       = useState(null);   // { variables, total, file, ... }
    const [previewLoading, setPreviewLoading] = useState(false);
    const [previewError, setPreviewError]     = useState(null);

    const fetchPreview = async () => {
        if (!suratType?.id) return;
        setPreviewLoading(true);
        setPreviewError(null);
        setPreviewModal(true);
        try {
            const res = await fetch(route('admin.surat-type.preview-template', suratType.id));
            const json = await res.json();
            if (!res.ok) throw new Error(json.error || 'Gagal memuat preview.');
            setPreviewData(json);
        } catch (e) {
            setPreviewError(e.message);
        } finally {
            setPreviewLoading(false);
        }
    };

    const iconList = [
        // Umum & Dokumen
        'FileText', 'File', 'FileEdit', 'Clipboard', 'ClipboardCheck', 'ClipboardList', 'Stamp', 'Files', 'Folder', 'FolderOpen', 'Newspaper', 'BookCopy',
        // Penduduk & Sosial
        'User', 'Users', 'UserCheck', 'UserPlus', 'Contact', 'Fingerprint', 'Smile', 'Heart', 'VenetianMask', 'UsersRound', 'Baby',
        // Wilayah & Bangunan
        'MapPin', 'Map', 'Home', 'Building', 'Building2', 'Landmark', 'Tent', 'Globe', 'Store', 'Church', 'Castle', 'Fence', 'Trees', 'Flag', 'School',
        // Pekerjaan & Pendidikan
        'Briefcase', 'GraduationCap', 'Book', 'BookOpen', 'Backpack', 'Calculator', 'HardHat', 'Factory', 'Wrench', 'Hammer', 'Tractor', 'Truck',
        // Kesehatan
        'HeartPulse', 'Stethoscope', 'Activity', 'Accessibility', 'Cross', 'Syringe', 'Pill', 'Skull', 'Bone', 'Thermometer',
        // Hukum & Keamanan
        'Scale', 'Gavel', 'Shield', 'ShieldCheck', 'Lock', 'Key', 'FileWarning', 'AlertTriangle', 'Eye', 'BadgeAlert', 'BadgeCheck',
        // Keuangan & Ekonomi
        'CreditCard', 'Wallet', 'Banknote', 'Coins', 'Receipt', 'ShoppingBag', 'ShoppingCart',
        // Komunikasi & Lainnya
        'Mail', 'Phone', 'Inbox', 'Send', 'Archive', 'Printer', 'QrCode', 'Image', 'Camera', 'Wifi', 'Megaphone', 'Bell', 'Calendar', 'Clock'
    ];

    const filteredIcons = iconList.filter(icon => 
        icon.toLowerCase().includes(searchIcon.toLowerCase())
    );

    const DynamicIcon = ({ name, className }) => {
        const IconComponent = LucideIcons[name] || FileText;
        return <IconComponent className={className} />;
    };

    const addField = () => {
        setData('form_json', [
            ...data.form_json,
            {
                _id: `f_${Date.now()}_${Math.random().toString(36).slice(2)}`,
                name: '', label: '', type: 'text', required: false, placeholder: '', options: []
            }
        ]);
    };

    const removeField = (index) => {
        const newFields = [...data.form_json];
        newFields.splice(index, 1);
        setData('form_json', newFields);
    };

    const updateField = (index, key, value) => {
        const newFields = [...data.form_json];
        newFields[index][key] = value;
        setData('form_json', newFields);
    };

    const addOption = (fieldIndex) => {
        const newFields = [...data.form_json];
        newFields[fieldIndex].options.push('');
        setData('form_json', newFields);
    };

    const updateOption = (fieldIndex, optionIndex, value) => {
        const newFields = [...data.form_json];
        newFields[fieldIndex].options[optionIndex] = value;
        setData('form_json', newFields);
    };

    const removeOption = (fieldIndex, optionIndex) => {
        const newFields = [...data.form_json];
        newFields[fieldIndex].options.splice(optionIndex, 1);
        setData('form_json', newFields);
    };

    // ── Refs untuk FLIP animation ──────────────────────────────────────────────
    const cardRefs = useRef({});
    const pendingFlip = useRef(null);

    const moveField = (index, direction) => {
        const targetIndex = index + direction;
        if (targetIndex < 0 || targetIndex >= data.form_json.length) return;

        // FIRST: rekam posisi sebelum swap
        pendingFlip.current = {
            type: 'swap',
            fromRect: cardRefs.current[index]?.getBoundingClientRect(),
            toRect: cardRefs.current[targetIndex]?.getBoundingClientRect(),
            fromIndex: index,
            toIndex: targetIndex,
        };

        const newFields = [...data.form_json];
        [newFields[index], newFields[targetIndex]] = [newFields[targetIndex], newFields[index]];
        setData('form_json', newFields);
    };

    // Pindah ke posisi tertentu (dari input angka langsung)
    const moveToPosition = (fromIndex, toIndex) => {
        if (fromIndex === toIndex) return;

        // Rekam semua posisi card sebelum move
        pendingFlip.current = {
            type: 'move',
            allRects: data.form_json.map((_, i) => cardRefs.current[i]?.getBoundingClientRect()),
        };

        const newFields = [...data.form_json];
        const [moved] = newFields.splice(fromIndex, 1);
        newFields.splice(toIndex, 0, moved);
        setData('form_json', newFields);
    };

    // LAST → INVERT → PLAY  (runs after React commits DOM, before browser paint)
    useLayoutEffect(() => {
        const flip = pendingFlip.current;
        if (!flip) return;
        pendingFlip.current = null;

        // Helper: terapkan FLIP ke array elemen + delta masing-masing
        const applyFlip = (els, deltas, duration) => {
            const valid = els.filter(Boolean);
            if (!valid.length) return;
            // INVERT — terapkan posisi lama seketika
            valid.forEach((el, i) => { el.style.transition = 'none'; el.style.transform = `translateY(${deltas[i]}px)`; });
            valid[0].getBoundingClientRect(); // force reflow
            // PLAY — animasikan ke posisi baru
            const ease = `transform ${duration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
            valid.forEach(el => { el.style.transition = ease; el.style.transform = ''; });
            return setTimeout(() => valid.forEach(el => { el.style.transition = ''; el.style.transform = ''; }), duration + 10);
        };

        let timer;
        if (flip.type === 'swap') {
            // 2-card swap (tombol panah)
            const movedEl     = cardRefs.current[flip.toIndex];
            const displacedEl = cardRefs.current[flip.fromIndex];
            if (!movedEl || !displacedEl) return;
            const fromDelta = flip.fromRect.top - movedEl.getBoundingClientRect().top;
            const toDelta   = flip.toRect.top   - displacedEl.getBoundingClientRect().top;
            timer = applyFlip([movedEl, displacedEl], [fromDelta, toDelta], 240);
        } else if (flip.type === 'move') {
            // Multi-card move (input angka langsung)
            const els = [], deltas = [];
            flip.allRects.forEach((oldRect, i) => {
                const el = cardRefs.current[i];
                if (!el || !oldRect) return;
                const delta = oldRect.top - el.getBoundingClientRect().top;
                if (delta === 0) return; // card tidak bergerak, skip
                els.push(el);
                deltas.push(delta);
            });
            timer = applyFlip(els, deltas, 280);
        }

        return () => clearTimeout(timer);
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [data.form_json]);

    const handleSubmit = (e) => {
        e.preventDefault();
        
        if (isEdit) {
            // Kita tembak ke route POST khusus update agar tidak perlu spoofing _method
            post(route('admin.surat-type.update.post', suratType.id), {
                forceFormData: true,
            });
        } else {
            post(route('admin.surat-type.store'));
        }
    };

    const fieldTypes = [
        { value: 'text', label: 'Teks Pendek', icon: <Type className="w-4 h-4" /> },
        { value: 'number', label: 'Angka', icon: <Layers className="w-4 h-4" /> },
        { value: 'date', label: 'Tanggal', icon: <Calendar className="w-4 h-4" /> },
        { value: 'select', label: 'Pilihan (Dropdown)', icon: <List className="w-4 h-4" /> },
        { value: 'textarea', label: 'Teks Panjang', icon: <AlignLeft className="w-4 h-4" /> }
    ];

    return (<>
        <AuthenticatedLayout user={auth.user} title={isEdit ? 'Edit Jenis Surat' : 'Tambah Jenis Surat'}>
            <Head title={isEdit ? 'Edit Jenis Surat' : 'Tambah Jenis Surat'} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader 
                    title={isEdit ? 'Edit Jenis Surat' : 'Tambah Jenis Surat'}
                    subtitle={isEdit ? `Memperbarui konfigurasi ${suratType.nama}` : 'Buat konfigurasi surat baru untuk warga'}
                    icon={FileText}
                    gradient="from-green-600 via-green-700 to-green-800"
                    backHref={route('admin.surat-type.index')}
                    actions={[
                        {
                            label: 'PANDUAN SURAT',
                            icon: HelpCircle,
                            href: route('admin.surat-type.panduan'),
                            variant: 'white'
                        }
                    ]}
                />

                <form onSubmit={handleSubmit} className="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    {/* Main Config */}
                    <div className="xl:col-span-2 space-y-6">
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                                <Settings className="w-5 h-5 text-blue-600" />
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Konfigurasi Dasar</h3>
                            </div>
                            <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">ID Jenis Surat (Unique)</label>
                                    <input 
                                        type="text"
                                        value={data.id}
                                        onChange={e => setData('id', e.target.value)}
                                        disabled={isEdit}
                                        className={cn(
                                            "w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all",
                                            isEdit && "opacity-50 cursor-not-allowed"
                                        )}
                                        placeholder="Contoh: sku, domisili, sktm"
                                    />
                                    <p className="text-[9px] text-gray-400 font-bold uppercase mt-1 ml-1 tracking-widest italic">
                                        * Gunakan huruf kecil dan tanda hubung (slug).
                                    </p>
                                    {errors.id && <p className="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{errors.id}</p>}
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Jenis Surat</label>
                                    <input 
                                        type="text"
                                        value={data.nama}
                                        onChange={e => setData('nama', e.target.value)}
                                        className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                        placeholder="Contoh: Surat Keterangan Usaha"
                                    />
                                    {errors.nama && <p className="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{errors.nama}</p>}
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kode Surat (No. Surat)</label>
                                    <input 
                                        type="text"
                                        value={data.kode}
                                        onChange={e => setData('kode', e.target.value)}
                                        className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                        placeholder="Contoh: SKU, DOM, SKTM"
                                    />
                                    {errors.kode && <p className="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{errors.kode}</p>}
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kode Template Blade</label>
                                    <input 
                                        type="text"
                                        value={data.template_code}
                                        onChange={e => setData('template_code', e.target.value)}
                                        className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                        placeholder="Contoh: sku.blade.php -> isi 'sku'"
                                    />
                                    {errors.template_code && <p className="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{errors.template_code}</p>}
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Upload Template Word (.docx)</label>
                                    <div className="relative group">
                                        <input 
                                            type="file"
                                            onChange={e => setData('file_template', e.target.files[0])}
                                            className="hidden"
                                            id="file_template"
                                            accept=".docx"
                                        />
                                        <label 
                                            htmlFor="file_template"
                                            className="flex items-center justify-between w-full px-5 py-3.5 bg-blue-50/50 border-2 border-dashed border-blue-200 rounded-2xl text-sm font-bold text-blue-600 cursor-pointer hover:bg-blue-100/50 transition-all group-hover:border-blue-400"
                                        >
                                            <div className="flex items-center gap-3">
                                                <Layers className="w-5 h-5 text-blue-500" />
                                                <span>{data.file_template ? data.file_template.name : (suratType?.file_template || 'Pilih file .docx...')}</span>
                                            </div>
                                            <Plus className="w-4 h-4" />
                                        </label>
                                    </div>
                                    <p className="text-[9px] text-gray-400 font-bold uppercase mt-1 ml-1 tracking-widest italic">
                                        * Gunakan variabel seperti {'${nama}'}, {'${nik}'} di dalam file Word.
                                    </p>
                                    {/* Peringatan jika mode multi-template aktif */}
                                    {data.has_multi_template && (
                                        <div className="mt-2 flex items-start gap-2 px-3 py-2.5 bg-amber-50 border border-amber-200 rounded-xl">
                                            <svg className="w-3.5 h-3.5 text-amber-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clipRule="evenodd" /></svg>
                                            <p className="text-[9px] font-black text-amber-700 uppercase tracking-widest leading-relaxed">
                                                Mode Multi-Template aktif — template ini <span className="text-amber-900">tidak dipakai</span> saat cetak. Upload file .docx di masing-masing sub-template di bawah.
                                            </p>
                                        </div>
                                    )}
                                    {/* Tombol Preview Variabel — hanya tampil di Edit mode & sudah ada file template */}
                                    {isEdit && suratType?.file_template && (
                                        <button
                                            type="button"
                                            onClick={fetchPreview}
                                            className="flex items-center gap-2 mt-2 px-4 py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all border border-indigo-100"
                                        >
                                            <Eye className="w-3.5 h-3.5" />
                                            PREVIEW VARIABEL TEMPLATE
                                        </button>
                                    )}
                                    {errors.file_template && <p className="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{errors.file_template}</p>}
                                </div>
                                <div className="md:col-span-2 space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Persyaratan Dokumen (Pisahkan dengan baris baru)</label>
                                    <textarea 
                                        value={data.persyaratan}
                                        onChange={e => setData('persyaratan', e.target.value)}
                                        className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                        rows="3"
                                        placeholder="Contoh: &#10;1. Fotocopy KTP&#10;2. Fotocopy KK"
                                    ></textarea>
                                    {errors.persyaratan && <p className="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{errors.persyaratan}</p>}
                                </div>
                                <div className="md:col-span-2 space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Deskripsi Singkat</label>
                                    <textarea 
                                        value={data.deskripsi}
                                        onChange={e => setData('deskripsi', e.target.value)}
                                        className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                        rows="2"
                                        placeholder="Jelaskan fungsi surat ini..."
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        {/* Form Builder */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div className="flex items-center gap-3">
                                    <Layout className="w-5 h-5 text-blue-600" />
                                    <div>
                                        <div className="flex items-center gap-2">
                                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Formulir Dinamis (Custom Fields)</h3>
                                            {data.has_multi_template && (
                                                <span className="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[8px] font-black uppercase tracking-widest rounded-full border border-indigo-200">🌐 GLOBAL</span>
                                            )}
                                        </div>
                                        {data.has_multi_template && (
                                            <p className="text-[9px] text-indigo-500 font-bold mt-0.5">
                                                Field di sini bersifat global — tampil &amp; dipakai oleh semua sub-template
                                            </p>
                                        )}
                                    </div>
                                </div>
                                <div className="flex flex-wrap gap-2">
                                    <button 
                                        type="button"
                                        onClick={() => {
                                            if (confirm('Muat contoh SKU? Field saat ini akan terhapus.')) {
                                                const t = Date.now();
                                                setData('form_json', [
                                                    { _id: `f_${t}_0`, name: 'nama_usaha',   label: 'Nama Usaha',    type: 'text',     required: true, placeholder: '', options: [] },
                                                    { _id: `f_${t}_1`, name: 'jenis_usaha',  label: 'Jenis Usaha',   type: 'text',     required: true, placeholder: '', options: [] },
                                                    { _id: `f_${t}_2`, name: 'alamat_usaha', label: 'Alamat Usaha',  type: 'textarea', required: true, placeholder: '', options: [] },
                                                ]);
                                            }
                                        }}
                                        className="px-3 py-1.5 bg-green-50 text-green-600 rounded-lg text-[8px] font-black hover:bg-green-100 transition-all uppercase tracking-widest border border-green-100"
                                    >
                                        Contoh SKU
                                    </button>
                                    <button 
                                        type="button"
                                        onClick={() => {
                                            if (confirm('Muat contoh SKTM? Field saat ini akan terhapus.')) {
                                                const t = Date.now();
                                                setData('form_json', [
                                                    { _id: `f_${t}_0`, name: 'penghasilan',    label: 'Penghasilan Per Bulan', type: 'number',   required: true, placeholder: '', options: [] },
                                                    { _id: `f_${t}_1`, name: 'pekerjaan_ayah', label: 'Pekerjaan Ayah',        type: 'text',     required: true, placeholder: '', options: [] },
                                                    { _id: `f_${t}_2`, name: 'pekerjaan_ibu',  label: 'Pekerjaan Ibu',         type: 'text',     required: true, placeholder: '', options: [] },
                                                    { _id: `f_${t}_3`, name: 'alasan',         label: 'Alasan Mengajukan',     type: 'textarea', required: true, placeholder: '', options: [] },
                                                ]);
                                            }
                                        }}
                                        className="px-3 py-1.5 bg-purple-50 text-purple-600 rounded-lg text-[8px] font-black hover:bg-purple-100 transition-all uppercase tracking-widest border border-purple-100"
                                    >
                                        Contoh SKTM
                                    </button>
                                    <button 
                                        type="button"
                                        onClick={addField}
                                        className="flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl text-[9px] font-black hover:bg-blue-700 transition-all uppercase tracking-widest shadow-lg shadow-blue-200"
                                    >
                                        <Plus className="w-3 h-3 mr-2" />
                                        TAMBAH FIELD
                                    </button>
                                </div>
                            </div>
                            <div className="p-6 space-y-4">
                                {data.form_json.length === 0 ? (
                                    <div className="text-center py-12 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                                        <Layers className="w-12 h-12 text-gray-300 mx-auto mb-3" />
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Belum ada field tambahan</p>
                                        <p className="text-[9px] text-gray-400 mt-1 uppercase tracking-widest">Gunakan field tambahan untuk data yang tidak ada di master penduduk</p>
                                    </div>
                                ) : (
                                    <div className="space-y-4">
                                        {data.form_json.map((field, fIndex) => (
                                            <div
                                                key={field._id || fIndex}
                                                ref={el => { cardRefs.current[fIndex] = el; }}
                                                className="bg-gray-50/50 rounded-3xl border border-gray-100 p-5 space-y-4"
                                            >
                                                <div className="flex items-center justify-between">
                                                    <div className="flex items-center gap-2">
                                                        {/* Badge urutan — klik untuk ketik langsung */}
                                                        {editingOrder?.index === fIndex ? (
                                                            <input
                                                                type="number"
                                                                min="1"
                                                                max={data.form_json.length}
                                                                value={editingOrder.value}
                                                                autoFocus
                                                                onChange={e => setEditingOrder({ index: fIndex, value: e.target.value })}
                                                                onBlur={() => {
                                                                    const target = parseInt(editingOrder.value, 10) - 1;
                                                                    if (!isNaN(target) && target >= 0 && target < data.form_json.length) {
                                                                        moveToPosition(fIndex, target);
                                                                    }
                                                                    setEditingOrder(null);
                                                                }}
                                                                onKeyDown={e => {
                                                                    if (e.key === 'Enter') e.target.blur();
                                                                    if (e.key === 'Escape') setEditingOrder(null);
                                                                }}
                                                                className={cn(
                                                                    "w-8 h-8 rounded-lg text-[10px] font-black text-center border-2 focus:outline-none",
                                                                    "[appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none",
                                                                    parseInt(editingOrder.value) >= 1 && parseInt(editingOrder.value) <= data.form_json.length
                                                                        ? "bg-blue-600 text-white border-blue-600 focus:ring-2 focus:ring-blue-300"
                                                                        : "bg-red-50 text-red-600 border-red-400 focus:ring-2 focus:ring-red-300 animate-pulse"
                                                                )}
                                                            />
                                                        ) : (
                                                            <div
                                                                className="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-[10px] font-black cursor-pointer hover:bg-blue-600 hover:text-white transition-all select-none"
                                                                title={`Field ke-${fIndex + 1} dari ${data.form_json.length}. Klik untuk ubah urutan.`}
                                                                onClick={() => setEditingOrder({ index: fIndex, value: fIndex + 1 })}
                                                            >
                                                                {fIndex + 1}
                                                            </div>
                                                        )}
                                                        <span className="text-[10px] font-black text-gray-900 uppercase tracking-widest">Field Configuration</span>
                                                    </div>
                                                    <div className="flex items-center gap-1">
                                                        {/* Tombol pindah urutan */}
                                                        <button
                                                            type="button"
                                                            onClick={() => moveField(fIndex, -1)}
                                                            disabled={fIndex === 0}
                                                            title="Geser ke atas"
                                                            className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all disabled:opacity-25 disabled:cursor-not-allowed"
                                                        >
                                                            <ChevronUp className="w-4 h-4" />
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() => moveField(fIndex, 1)}
                                                            disabled={fIndex === data.form_json.length - 1}
                                                            title="Geser ke bawah"
                                                            className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all disabled:opacity-25 disabled:cursor-not-allowed"
                                                        >
                                                            <ChevronDown className="w-4 h-4" />
                                                        </button>
                                                        <div className="w-px h-5 bg-gray-200 mx-1" />
                                                        <button 
                                                            type="button"
                                                            onClick={() => removeField(fIndex)}
                                                            className="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                        >
                                                            <Trash2 className="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                </div>

                                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div className="space-y-1">
                                                        <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Database Name</label>
                                                        <input 
                                                            type="text"
                                                            value={field.name}
                                                            onChange={e => updateField(fIndex, 'name', e.target.value)}
                                                            className="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                                            placeholder="nama_field"
                                                        />
                                                    </div>
                                                    <div className="space-y-1">
                                                        <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Label Form</label>
                                                        <input 
                                                            type="text"
                                                            value={field.label}
                                                            onChange={e => updateField(fIndex, 'label', e.target.value)}
                                                            className="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                                            placeholder="Label Input"
                                                        />
                                                    </div>
                                                    <div className="space-y-1">
                                                        <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Tipe Input</label>
                                                        <select 
                                                            value={field.type}
                                                            onChange={e => updateField(fIndex, 'type', e.target.value)}
                                                            className="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                                        >
                                                            {fieldTypes.map(t => (
                                                                <option key={t.value} value={t.value}>{t.label}</option>
                                                            ))}
                                                        </select>
                                                    </div>
                                                </div>

                                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div className="space-y-1">
                                                        <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Placeholder</label>
                                                        <input 
                                                            type="text"
                                                            value={field.placeholder}
                                                            onChange={e => updateField(fIndex, 'placeholder', e.target.value)}
                                                            className="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                                            placeholder="Contoh: Masukkan alasan..."
                                                        />
                                                    </div>
                                                    <div className="flex items-center gap-3 pt-5">
                                                        <button 
                                                            type="button"
                                                            onClick={() => updateField(fIndex, 'required', !field.required)}
                                                            className={cn(
                                                                "flex items-center px-4 py-2 rounded-xl text-[9px] font-black transition-all border",
                                                                field.required ? "bg-red-50 text-red-600 border-red-100" : "bg-gray-100 text-gray-400 border-transparent"
                                                            )}
                                                        >
                                                            <CheckSquare className="w-3 h-3 mr-2" />
                                                            WAJIB DIISI
                                                        </button>
                                                    </div>
                                                </div>

                                                {field.type === 'select' && (
                                                    <div className="pt-4 border-t border-gray-200/50 space-y-3">
                                                        <div className="flex items-center justify-between">
                                                            <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Opsi Pilihan (Options)</label>
                                                            <button 
                                                                type="button"
                                                                onClick={() => addOption(fIndex)}
                                                                className="text-[9px] font-black text-blue-600 hover:text-blue-700 uppercase tracking-widest"
                                                            >
                                                                + Tambah Opsi
                                                            </button>
                                                        </div>
                                                        <div className="flex flex-wrap gap-2">
                                                            {field.options.map((opt, oIndex) => (
                                                                <div key={oIndex} className="flex items-center bg-white border border-gray-200 rounded-lg pr-1 pl-3 py-1 gap-2">
                                                                    <input 
                                                                        type="text"
                                                                        value={opt}
                                                                        onChange={e => updateOption(fIndex, oIndex, e.target.value)}
                                                                        className="border-none p-0 text-[10px] font-bold focus:ring-0 w-20"
                                                                        placeholder="Opsi..."
                                                                    />
                                                                    <button 
                                                                        type="button"
                                                                        onClick={() => removeOption(fIndex, oIndex)}
                                                                        className="text-gray-300 hover:text-red-500 transition-colors"
                                                                    >
                                                                        <Trash2 className="w-3 h-3" />
                                                                    </button>
                                                                </div>
                                                            ))}
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Sub-Template Manager */}
                        {isEdit && data.has_multi_template && (
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mt-6">
                                <div className="p-6">
                                    <SubTemplateManager suratType={suratType} />
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Sidebar / Options */}
                    <div className="space-y-6 sticky top-24 self-start">
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                                <Info className="w-5 h-5 text-blue-600" />
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Status & Visibilitas</h3>
                            </div>
                            <div className="p-6 space-y-6">
                                <div className="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <div>
                                        <p className="text-[10px] font-black text-gray-900 uppercase tracking-widest">Status Aktif</p>
                                        <p className="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Tersedia di menu layanan</p>
                                    </div>
                                    <button 
                                        type="button"
                                        onClick={() => setData('is_active', !data.is_active)}
                                        className={cn(
                                            "w-12 h-6 rounded-full transition-all relative",
                                            data.is_active ? "bg-green-500 shadow-lg shadow-green-200" : "bg-gray-300"
                                        )}
                                    >
                                        <div className={cn(
                                            "absolute top-1 w-4 h-4 bg-white rounded-full transition-all shadow-sm",
                                            data.is_active ? "left-7" : "left-1"
                                        )}></div>
                                    </button>
                                </div>

                                <div className="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <div>
                                        <p className="text-[10px] font-black text-gray-900 uppercase tracking-widest">Dapat Diajukan Online</p>
                                        <p className="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Bisa diajukan warga di web desa</p>
                                    </div>
                                    <button 
                                        type="button"
                                        onClick={() => setData('is_public', !data.is_public)}
                                        className={cn(
                                            "w-12 h-6 rounded-full transition-all relative",
                                            data.is_public ? "bg-green-500 shadow-lg shadow-green-200" : "bg-gray-300"
                                        )}
                                    >
                                        <div className={cn(
                                            "absolute top-1 w-4 h-4 bg-white rounded-full transition-all shadow-sm",
                                            data.is_public ? "left-7" : "left-1"
                                        )}></div>
                                    </button>
                                </div>

                                <div className="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <div>
                                        <p className="text-[10px] font-black text-gray-900 uppercase tracking-widest">Punya Template</p>
                                        <p className="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Cetak PDF otomatis</p>
                                    </div>
                                    <button 
                                        type="button"
                                        onClick={() => setData('has_template', !data.has_template)}
                                        className={cn(
                                            "w-12 h-6 rounded-full transition-all relative",
                                            data.has_template ? "bg-blue-500 shadow-lg shadow-blue-200" : "bg-gray-300"
                                        )}
                                    >
                                        <div className={cn(
                                            "absolute top-1 w-4 h-4 bg-white rounded-full transition-all shadow-sm",
                                            data.has_template ? "left-7" : "left-1"
                                        )}></div>
                                    </button>
                                </div>

                                <div className={cn(
                                    "p-4 rounded-2xl border transition-all",
                                    data.has_multi_template
                                        ? "bg-indigo-50 border-indigo-200"
                                        : "bg-gray-50 border-gray-100"
                                )}>
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <p className={cn(
                                                "text-[10px] font-black uppercase tracking-widest",
                                                data.has_multi_template ? "text-indigo-900" : "text-gray-900"
                                            )}>Multi Template (Sub-Template)</p>
                                            <p className="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Banyak file Word dalam 1 surat</p>
                                        </div>
                                        <button 
                                            type="button"
                                            onClick={() => setData('has_multi_template', !data.has_multi_template)}
                                            className={cn(
                                                "w-12 h-6 rounded-full transition-all relative shrink-0",
                                                data.has_multi_template ? "bg-indigo-500 shadow-lg shadow-indigo-200" : "bg-gray-300"
                                            )}
                                        >
                                            <div className={cn(
                                                "absolute top-1 w-4 h-4 bg-white rounded-full transition-all shadow-sm",
                                                data.has_multi_template ? "left-7" : "left-1"
                                            )}></div>
                                        </button>
                                    </div>
                                    {data.has_multi_template && (
                                        <div className="mt-3 pt-3 border-t border-indigo-100 space-y-1">
                                            <p className="text-[9px] font-black text-indigo-700 uppercase tracking-widest flex items-center gap-1">
                                                <svg className="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clipRule="evenodd" /></svg>
                                                Cara pakai:
                                            </p>
                                            <p className="text-[9px] text-indigo-600 font-bold leading-relaxed">
                                                1. Simpan form ini dulu<br />
                                                2. Buka kembali halaman Edit<br />
                                                3. Kelola sub-template di bagian bawah
                                            </p>
                                            <p className="text-[9px] text-indigo-400 font-bold mt-1">
                                                ⚠ Template utama &amp; Form JSON bersifat global untuk semua sub-template
                                            </p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                                <Palette className="w-5 h-5 text-blue-600" />
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Estetika Ikon</h3>
                            </div>
                            <div className="p-6 grid grid-cols-2 gap-4">
                                <div className="space-y-1">
                                    <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Warna Aksen</label>
                                    <select 
                                        value={data.color}
                                        onChange={e => setData('color', e.target.value)}
                                        className="w-full px-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                    >
                                        <option value="blue">Blue</option>
                                        <option value="green">Green</option>
                                        <option value="purple">Purple</option>
                                        <option value="orange">Orange</option>
                                        <option value="red">Red</option>
                                        <option value="pink">Pink</option>
                                        <option value="yellow">Yellow</option>
                                    </select>
                                </div>
                                <div className="space-y-1 relative" ref={pickerRef}>
                                    <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Pilih Ikon</label>
                                    <div 
                                        onClick={() => setShowIconPicker(!showIconPicker)}
                                        className="w-full px-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 flex items-center justify-between cursor-pointer hover:bg-gray-100 transition-all"
                                    >
                                        <div className="flex items-center gap-2">
                                            <div className={cn("w-6 h-6 rounded-lg flex items-center justify-center text-white", `bg-${data.color}-500`)}>
                                                <DynamicIcon name={data.icon} className="w-4 h-4" />
                                            </div>
                                            <span className="uppercase tracking-widest text-[9px]">{data.icon}</span>
                                        </div>
                                        <ChevronDown className={cn("w-3 h-3 transition-all", showIconPicker && "rotate-180")} />
                                    </div>

                                    {showIconPicker && (
                                        <div className="absolute z-50 bottom-full mb-2 w-[250px] right-0 bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 animate-in fade-in slide-in-from-bottom-2 duration-200">
                                            <div className="relative mb-3">
                                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-400" />
                                                <input 
                                                    type="text" 
                                                    placeholder="Cari icon..."
                                                    value={searchIcon}
                                                    onChange={(e) => setSearchIcon(e.target.value)}
                                                    className="w-full pl-8 pr-4 py-2 bg-gray-50 border-none rounded-xl text-[10px] font-bold focus:ring-1 focus:ring-blue-500"
                                                />
                                            </div>
                                            <div className="grid grid-cols-4 gap-2 max-h-40 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-200">
                                                {filteredIcons.map((iconName) => (
                                                    <button
                                                        key={iconName}
                                                        type="button"
                                                        onClick={() => {
                                                            setData('icon', iconName);
                                                            setShowIconPicker(false);
                                                        }}
                                                        className={cn(
                                                            "p-2 rounded-lg flex flex-col items-center gap-1 transition-all",
                                                            data.icon === iconName ? "bg-blue-600 text-white" : "hover:bg-blue-50 text-gray-400 hover:text-blue-600"
                                                        )}
                                                    >
                                                        <DynamicIcon name={iconName} className="w-5 h-5" />
                                                    </button>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        <button 
                            type="submit"
                            disabled={processing}
                            className="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-3xl text-sm font-black shadow-xl shadow-blue-200 hover:shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 disabled:opacity-50"
                        >
                            {processing ? (
                                <Layers className="w-5 h-5 animate-spin" />
                            ) : (
                                <Save className="w-5 h-5" />
                            )}
                            SIMPAN KONFIGURASI
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>

        {/* ── Modal Preview Variabel Template ─────────────────────────────── */}
        {previewModal && (
            <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-in fade-in duration-200">
                <div className="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
                    {/* Header */}
                    <div className="flex items-center justify-between p-6 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-blue-50">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center">
                                <Eye className="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h2 className="text-sm font-black text-gray-900 uppercase tracking-tighter">Preview Variabel Template</h2>
                                {previewData && (
                                    <p className="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-0.5">
                                        {previewData.file} &mdash; {previewData.total} variabel ditemukan
                                    </p>
                                )}
                            </div>
                        </div>
                        <button onClick={() => { setPreviewModal(false); setPreviewData(null); setPreviewError(null); }}
                            className="p-2 hover:bg-white rounded-xl transition-all text-gray-400 hover:text-gray-700">
                            <X className="w-5 h-5" />
                        </button>
                    </div>

                    {/* Body */}
                    <div className="flex-1 overflow-y-auto p-6 space-y-5">
                        {previewLoading && (
                            <div className="flex flex-col items-center justify-center py-16 gap-3 text-gray-400">
                                <Loader2 className="w-8 h-8 animate-spin text-indigo-500" />
                                <p className="text-[10px] font-black uppercase tracking-widest">Membaca file template...</p>
                            </div>
                        )}

                        {previewError && (
                            <div className="flex items-start gap-3 p-4 bg-red-50 rounded-2xl border border-red-100">
                                <AlertTriangle className="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                                <p className="text-xs font-bold text-red-600">{previewError}</p>
                            </div>
                        )}

                        {previewData && !previewLoading && (() => {
                            const systemVars  = previewData.variables.filter(v => v.category === 'system');
                            const formVars    = previewData.variables.filter(v => v.category === 'form');
                            const unknownVars = previewData.variables.filter(v => v.category === 'unknown');

                            return (
                                <>
                                    {/* Legend */}
                                    <div className="flex flex-wrap gap-2">
                                        {[
                                            { color: 'bg-emerald-100 text-emerald-700 border-emerald-200', label: 'Sistem (otomatis)' },
                                            { color: 'bg-blue-100 text-blue-700 border-blue-200', label: 'Form custom' },
                                            { color: 'bg-amber-100 text-amber-700 border-amber-200', label: 'Tidak dikenali' },
                                        ].map(l => (
                                            <span key={l.label} className={`px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border ${l.color}`}>{l.label}</span>
                                        ))}
                                    </div>

                                    {/* Peringatan jika ada variabel tidak dikenali */}
                                    {unknownVars.length > 0 && (
                                        <div className="flex items-start gap-3 p-4 bg-amber-50 rounded-2xl border border-amber-100">
                                            <AlertTriangle className="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                                            <div>
                                                <p className="text-[10px] font-black text-amber-700 uppercase tracking-widest">
                                                    {unknownVars.length} variabel tidak dikenali
                                                </p>
                                                <p className="text-[9px] text-amber-600 mt-1">
                                                    Tambahkan sebagai field di Form Builder, atau pastikan nama variabel sesuai dengan data sistem.
                                                </p>
                                            </div>
                                        </div>
                                    )}

                                    {/* Semua variabel */}
                                    <div>
                                        <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Daftar Semua Variabel</p>
                                        <div className="flex flex-wrap gap-2">
                                            {previewData.variables.map(v => {
                                                const styles = {
                                                    system:  'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                    form:    'bg-blue-50 text-blue-700 border-blue-200',
                                                    unknown: 'bg-amber-50 text-amber-700 border-amber-200',
                                                };
                                                const icons = {
                                                    system:  <CheckCircle2 className="w-3 h-3" />,
                                                    form:    <FileText className="w-3 h-3" />,
                                                    unknown: <AlertTriangle className="w-3 h-3" />,
                                                };
                                                return (
                                                    <span key={v.name}
                                                        title={v.label}
                                                        className={`flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-black border ${styles[v.category]}`}>
                                                        {icons[v.category]}
                                                        {'${'}{v.name}{'}'}
                                                    </span>
                                                );
                                            })}
                                        </div>
                                    </div>

                                    {/* Summary */}
                                    <div className="grid grid-cols-3 gap-3">
                                        {[
                                            { count: systemVars.length,  label: 'Sistem',       color: 'text-emerald-600', bg: 'bg-emerald-50 border-emerald-100' },
                                            { count: formVars.length,    label: 'Form Custom',  color: 'text-blue-600',    bg: 'bg-blue-50 border-blue-100' },
                                            { count: unknownVars.length, label: 'Tdk Dikenali', color: 'text-amber-600',   bg: 'bg-amber-50 border-amber-100' },
                                        ].map(s => (
                                            <div key={s.label} className={`p-4 rounded-2xl border text-center ${s.bg}`}>
                                                <p className={`text-2xl font-black ${s.color}`}>{s.count}</p>
                                                <p className="text-[9px] font-black text-gray-500 uppercase tracking-widest mt-1">{s.label}</p>
                                            </div>
                                        ))}
                                    </div>
                                </>
                            );
                        })()}
                    </div>
                </div>
            </div>
        )}
    </>);
}
