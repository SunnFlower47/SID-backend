import React, { useState, useEffect, useRef } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    FileText, Save, ArrowLeft, Plus, Trash2, 
    Settings, Layout, Info, Type, List, 
    Calendar, CheckSquare, AlignLeft, Layers, Palette, HelpCircle, X,
    ChevronDown, Search, FileEdit, ClipboardCheck, ClipboardList, 
    Stamp, Files, UserCheck, UserPlus, Contact, Fingerprint, Smile, 
    Heart, Map, Building, Building2, Landmark, Tent, Globe, Book, 
    BookOpen, Backpack, Calculator, HardHat, Baby, Accessibility, 
    Cross, Syringe, Gavel, ShieldCheck, Lock, Key, FileWarning, Inbox, 
    Send, Archive, Printer, QrCode, Image, Camera
} from 'lucide-react';
import * as LucideIcons from 'lucide-react';
import { cn } from '@/lib/utils';
import Modal from '@/Components/Shared/Modal';

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
        template_code: suratType?.template_code || '',
        icon: suratType?.icon || 'file-text',
        color: suratType?.color || 'blue',
        is_active: suratType?.is_active ?? true,
        form_json: suratType?.form_json || [],
        file_template: null,
    });

    const [showGuide, setShowGuide] = useState(false);
    const [showIconPicker, setShowIconPicker] = useState(false);
    const [searchIcon, setSearchIcon] = useState('');

    const iconList = [
        // Umum & Dokumen
        'FileText', 'File', 'FileEdit', 'Clipboard', 'ClipboardCheck', 'ClipboardList', 'Stamp', 'Files',
        // Penduduk & Sosial
        'User', 'Users', 'UserCheck', 'UserPlus', 'Contact', 'Fingerprint', 'Smile', 'Heart',
        // Wilayah & Bangunan
        'MapPin', 'Map', 'Home', 'Building', 'Building2', 'Landmark', 'Tent', 'Globe',
        // Pekerjaan & Pendidikan
        'Briefcase', 'GraduationCap', 'Book', 'BookOpen', 'Backpack', 'Calculator', 'HardHat',
        // Kesehatan
        'HeartPulse', 'Stethoscope', 'Activity', 'Baby', 'Accessibility', 'Cross', 'Syringe',
        // Hukum & Keamanan
        'Scale', 'Gavel', 'Shield', 'ShieldCheck', 'Lock', 'Key', 'FileWarning',
        // Komunikasi & Lainnya
        'Mail', 'Phone', 'Inbox', 'Send', 'Archive', 'Printer', 'QrCode', 'Image', 'Camera'
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
            { name: '', label: '', type: 'text', required: false, placeholder: '', options: [] }
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

    return (
        <AuthenticatedLayout user={auth.user} title={isEdit ? 'Edit Jenis Surat' : 'Tambah Jenis Surat'}>
            <Head title={isEdit ? 'Edit Jenis Surat' : 'Tambah Jenis Surat'} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <div className="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <FileText className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">
                                    {isEdit ? 'Edit Jenis Surat' : 'Tambah Jenis Surat'}
                                </h1>
                                <p className="text-blue-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">
                                    {isEdit ? `Memperbarui konfigurasi ${suratType.nama}` : 'Buat konfigurasi surat baru untuk warga'}
                                </p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3">
                            <button 
                                type="button"
                                onClick={() => setShowGuide(true)}
                                className="flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-2xl text-[10px] font-black transition-all uppercase tracking-widest shadow-lg shadow-blue-100"
                            >
                                <HelpCircle className="w-3.5 h-3.5 mr-2" />
                                Panduan Kode Word
                            </button>
                            <Link 
                                href={route('admin.surat-type.index')}
                                className="flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest"
                            >
                                <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                                KEMBALI
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Modal Panduan */}
                <Modal show={showGuide} onClose={() => setShowGuide(false)} maxWidth="2xl">
                    <div className="p-8 bg-white rounded-3xl">
                        <div className="flex items-center justify-between mb-6">
                            <div className="flex items-center gap-3">
                                <div className="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                                    <HelpCircle className="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 className="text-lg font-black text-gray-900 uppercase italic tracking-tighter leading-none">Panduan Variabel Word</h3>
                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Gunakan kode ini di dalam file .docx kamu</p>
                                </div>
                            </div>
                            <button onClick={() => setShowGuide(false)} className="p-2 hover:bg-gray-100 rounded-full transition-all text-gray-400">
                                <X className="w-6 h-6" />
                            </button>
                        </div>

                        <div className="space-y-6 max-h-[60vh] overflow-y-auto pr-4 scrollbar-thin scrollbar-thumb-gray-200">
                            <section>
                                <h4 className="text-[11px] font-black text-blue-600 uppercase tracking-[0.2em] mb-3 flex items-center gap-2">
                                    <div className="w-1.5 h-1.5 bg-blue-600 rounded-full"></div>
                                    Data Penduduk (Dasar)
                                </h4>
                                <div className="grid grid-cols-2 gap-3 text-[10px] font-bold">
                                    {[
                                        { code: '${nama}', desc: 'Nama Lengkap Warga' },
                                        { code: '${nik}', desc: 'NIK (16 Digit)' },
                                        { code: '${nkk}', desc: 'Nomor Kartu Keluarga' },
                                        { code: '${tempat_lahir}', desc: 'Tempat Lahir' },
                                        { code: '${tanggal_lahir}', desc: 'Tanggal Lahir (Format Indo)' },
                                        { code: '${jenis_kelamin}', desc: 'Laki-laki / Perempuan' },
                                        { code: '${agama}', desc: 'Agama' },
                                        { code: '${pekerjaan}', desc: 'Pekerjaan' },
                                        { code: '${pendidikan}', desc: 'Pendidikan Terakhir' },
                                        { code: '${status_perkawinan}', desc: 'Status Kawin' },
                                        { code: '${nama_ayah}', desc: 'Nama Ayah' },
                                        { code: '${nama_ibu}', desc: 'Nama Ibu' },
                                        { code: '${alamat}', desc: 'Alamat (Tanpa RT/RW)' },
                                        { code: '${rt}', desc: 'Nomor RT' },
                                        { code: '${rw}', desc: 'Nomor RW' },
                                        { code: '${dusun}', desc: 'Nama Dusun' },
                                    ].map((item, i) => (
                                        <div key={i} className="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                            <code className="text-blue-600 bg-blue-50 px-2 py-1 rounded-md">{item.code}</code>
                                            <span className="text-gray-500">{item.desc}</span>
                                        </div>
                                    ))}
                                </div>
                            </section>

                            <section>
                                <h4 className="text-[11px] font-black text-green-600 uppercase tracking-[0.2em] mb-3 flex items-center gap-2">
                                    <div className="w-1.5 h-1.5 bg-green-600 rounded-full"></div>
                                    Wilayah & Penandatangan
                                </h4>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-[10px] font-bold">
                                    {[
                                        { code: '${desa}', desc: 'Nama Desa' },
                                        { code: '${kecamatan}', desc: 'Nama Kecamatan' },
                                        { code: '${kabupaten}', desc: 'Nama Kabupaten' },
                                        { code: '${provinsi}', desc: 'Nama Provinsi' },
                                        { code: '${alamat_desa}', desc: 'Alamat Kantor Desa' },
                                        { code: '${nomor_surat}', desc: 'Nomor Surat Lengkap' },
                                        { code: '${tanggal_surat}', desc: 'Tanggal Cetak Indo' },
                                        { code: '${keperluan}', desc: 'Keperluan Surat' },
                                        { code: '${tujuan}', desc: 'Tujuan Surat' },
                                        { code: '${ttd_atas}', desc: 'Jabatan Penandatangan' },
                                        { code: '${ttd_bawah}', desc: 'Nama Penandatangan (Bold)' },
                                    ].map((item, i) => (
                                        <div key={i} className="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                            <code className="text-green-600 bg-green-50 px-2 py-1 rounded-md">{item.code}</code>
                                            <span className="text-gray-500">{item.desc}</span>
                                        </div>
                                    ))}
                                </div>
                            </section>

                            <div className="p-4 bg-yellow-50 rounded-2xl border border-yellow-100">
                                <h5 className="text-[10px] font-black text-yellow-700 uppercase mb-1">💡 Tips Custom Form:</h5>
                                <p className="text-[10px] font-bold text-yellow-600 leading-relaxed uppercase">
                                    Jika kamu menambahkan field baru di <b>JSON Form</b> dengan nama <code>tujuan_sekolah</code>, 
                                    maka di Word kamu bisa memanggilnya dengan kode <code>{`${'${tujuan_sekolah}'}`}</code>.
                                </p>
                            </div>
                        </div>
                    </div>
                </Modal>

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
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Formulir Dinamis (Custom Fields)</h3>
                                </div>
                                <div className="flex flex-wrap gap-2">
                                    <button 
                                        type="button"
                                        onClick={() => {
                                            if (confirm('Muat contoh SKU? Field saat ini akan terhapus.')) {
                                                setData('form_json', [
                                                    { name: 'nama_usaha', label: 'Nama Usaha', type: 'text', required: true },
                                                    { name: 'jenis_usaha', label: 'Jenis Usaha', type: 'text', required: true },
                                                    { name: 'alamat_usaha', label: 'Alamat Usaha', type: 'textarea', required: true }
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
                                                setData('form_json', [
                                                    { name: 'penghasilan', label: 'Penghasilan Per Bulan', type: 'number', required: true },
                                                    { name: 'pekerjaan_ayah', label: 'Pekerjaan Ayah', type: 'text', required: true },
                                                    { name: 'pekerjaan_ibu', label: 'Pekerjaan Ibu', type: 'text', required: true },
                                                    { name: 'alasan', label: 'Alasan Mengajukan', type: 'textarea', required: true }
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
                                            <div key={fIndex} className="bg-gray-50/50 rounded-3xl border border-gray-100 p-5 space-y-4 animate-in slide-in-from-right-4 duration-300">
                                                <div className="flex items-center justify-between">
                                                    <div className="flex items-center gap-2">
                                                        <div className="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-[10px] font-black">
                                                            {fIndex + 1}
                                                        </div>
                                                        <span className="text-[10px] font-black text-gray-900 uppercase tracking-widest">Field Configuration</span>
                                                    </div>
                                                    <button 
                                                        type="button"
                                                        onClick={() => removeField(fIndex)}
                                                        className="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                                    >
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
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
                    </div>

                    {/* Sidebar / Options */}
                    <div className="space-y-6">
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
    );
}
