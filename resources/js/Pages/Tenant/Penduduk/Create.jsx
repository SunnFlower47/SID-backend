import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Users, Save, X, Plus, Trash2, Search, CheckCircle, AlertTriangle, Home, UserPlus, Info } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Create({ auth, existingNKKs, masterRwOptions }) {
    const [kkOption, setKkOption] = useState('existing'); // 'existing' or 'manual'
    const [searchTerm, setSearchTerm] = useState('');
    const [showDropdown, setShowDropdown] = useState(false);
    const [selectedKk, setSelectedKk] = useState(null);
    
    // NIK checking states
    const [nikStatus, setNikStatus] = useState({}); // { index: { checking: false, exists: false, data: null } }
    const [nkkStatus, setNkkStatus] = useState({ checking: false, exists: false, data: null });

    // Standard Options
    const OPTIONS = {
        agama: ['ISLAM', 'KRISTEN', 'KATOLIK', 'HINDU', 'BUDDHA', 'KONGHUCU'],
        pendidikan: [
            'TIDAK / BELUM SEKOLAH', 'BELUM TAMAT SD/SEDERAJAT', 'TAMAT SD / SEDERAJAT',
            'SLTP/SEDERAJAT', 'SLTA / SEDERAJAT', 'DIPLOMA I / II',
            'AKADEMI / DIPLOMA III / S. MUDA', 'DIPLOMA IV / STRATA I', 'STRATA II', 'STRATA III'
        ],
        status_perkawinan: ['BELUM KAWIN', 'KAWIN', 'CERAI HIDUP', 'CERAI MATI'],
        kedudukan_keluarga: ['Kepala Keluarga', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Pembantu'],
        pekerjaan: [
            'BELUM/TIDAK BEKERJA', 'MENGURUS RUMAH TANGGA', 'PELAJAR/MAHASISWA', 
            'PENSIUNAN', 'PEGAWAI NEGERI SIPIL', 'TENTARA NASIONAL INDONESIA', 
            'KEPOLISIAN NEGARA RI', 'PETANI/PEKEBUN', 'KARYAWAN SWASTA', 
            'BURUH HARIAN LEPAS', 'WIRASWASTA', 'PERANGKAT DESA'
        ],
    };

    const getEmptyPerson = () => ({
        nik: '', nama: '', jenis_kelamin: 'LAKI-LAKI', tempat_lahir: '', tanggal_lahir: '',
        agama: 'ISLAM', pendidikan: 'TIDAK / BELUM SEKOLAH', pekerjaan: 'BELUM/TIDAK BEKERJA', 
        status_perkawinan: 'BELUM KAWIN', kedudukan_keluarga: 'Anak',
        nama_ayah: '', nama_ibu: '', keterangan: ''
    });

    const { data, setData, post, processing, errors } = useForm({
        kk_option: 'existing',
        nkk: '',
        nkk_existing: '',
        nama_kepala_keluarga: '',
        alamat: '',
        rw_id: '',
        rt_id: '',
        ...getEmptyPerson(),
        family_members: []
    });

    // Handle manual input state for "LAINNYA"
    const [manualFields, setManualFields] = useState({}); // { 'primary_agama': true, 'member_0_agama': true }

    const toggleManual = (key, field, isOther) => {
        setManualFields(prev => ({ ...prev, [`${key}_${field}`]: isOther }));
    };

    // --- Core Logic Functions ---
    
    useEffect(() => {
        setData(prev => ({
            ...prev,
            kk_option: kkOption,
            // Reset fields based on option
            nkk: '',
            nkk_existing: '',
            alamat: kkOption === 'existing' ? '' : prev.alamat,
            rt_id: kkOption === 'existing' ? '' : prev.rt_id,
            rw_id: kkOption === 'existing' ? '' : prev.rw_id,
        }));
        setSelectedKk(null);
        setSearchTerm('');
    }, [kkOption]);

    const filteredKKs = searchTerm.length > 1 
        ? existingNKKs.filter(kk => 
            kk.nkk.includes(searchTerm) || 
            (kk.kepala_keluarga && kk.kepala_keluarga.toLowerCase().includes(searchTerm.toLowerCase()))
          ).slice(0, 5)
        : [];

    const handleSelectKk = (kk) => {
        setSelectedKk(kk);
        setSearchTerm(kk.nkk);
        setShowDropdown(false);
        setData(prev => ({
            ...prev,
            nkk: kk.nkk,
            nkk_existing: kk.nkk,
            alamat: kk.alamat || '',
        }));
    };

    const [availableRts, setAvailableRts] = useState([]);
    useEffect(() => {
        if (data.rw_id) {
            const rw = masterRwOptions.find(r => String(r.id) === String(data.rw_id));
            setAvailableRts(rw?.rts || []);
            if (data.rt_id && !(rw?.rts || []).find(r => String(r.id) === String(data.rt_id))) {
                setData('rt_id', '');
            }
        } else {
            setAvailableRts([]);
        }
    }, [data.rw_id]);

    const addFamilyMember = () => {
        setData('family_members', [...data.family_members, getEmptyPerson()]);
    };

    // Auto-sync Head of Family name to Primary Resident name when Manual KK
    useEffect(() => {
        if (kkOption === 'manual') {
            setData(prev => ({
                ...prev,
                nama: prev.nama_kepala_keluarga,
                kedudukan_keluarga: 'Kepala Keluarga'
            }));
        }
    }, [data.nama_kepala_keluarga, kkOption]);

    const removeFamilyMember = (index) => {
        const newMembers = [...data.family_members];
        newMembers.splice(index, 1);
        setData('family_members', newMembers);
        const newNikStatus = { ...nikStatus };
        delete newNikStatus[`member_${index}`];
        setNikStatus(newNikStatus);
    };

    const checkNik = async (nik, key) => {
        if (!nik || nik.length !== 16) {
            setNikStatus(prev => ({ ...prev, [key]: { checking: false, exists: false, data: null } }));
            return;
        }
        setNikStatus(prev => ({ ...prev, [key]: { checking: true, exists: false, data: null } }));
        try {
            const response = await fetch(route('penduduk.check-nik') + `?nik=${nik}`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            setNikStatus(prev => ({ ...prev, [key]: { checking: false, exists: result.exists, data: result.data } }));
        } catch (e) {
            setNikStatus(prev => ({ ...prev, [key]: { checking: false, exists: false, data: null } }));
        }
    };

    const checkNkk = async (nkk) => {
        if (!nkk || nkk.length !== 16) {
            setNkkStatus({ checking: false, exists: false, data: null });
            return;
        }
        setNkkStatus({ checking: true, exists: false, data: null });
        try {
            const response = await fetch(route('mutasi.check-nkk') + `?nkk=${nkk}`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            setNkkStatus({ checking: false, exists: result.exists, data: result.data });
        } catch (e) {
            setNkkStatus({ checking: false, exists: false, data: null });
        }
    };

    useEffect(() => {
        const timer = setTimeout(() => checkNik(data.nik, 'primary'), 500);
        return () => clearTimeout(timer);
    }, [data.nik]);

    useEffect(() => {
        if (kkOption === 'manual') {
            const timer = setTimeout(() => checkNkk(data.nkk), 500);
            return () => clearTimeout(timer);
        }
    }, [data.nkk, kkOption]);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (kkOption === 'existing' && !selectedKk) {
            Swal.fire('Error', 'Silakan cari dan pilih Kartu Keluarga (KK) yang sudah ada.', 'error');
            return;
        }
        post(route('penduduk.store'), {
            preserveScroll: true,
            onError: (errs) => {
                if (Object.keys(errs).length > 0) {
                    Swal.fire({
                        title: 'Terjadi Kesalahan Validasi!',
                        html: `<ul class="text-left text-sm text-red-600">${Object.values(errs).map(e => `<li>${e}</li>`).join('')}</ul>`,
                        icon: 'error'
                    });
                }
            }
        });
    };

    const renderPersonForm = (person, isPrimary = true, index = null) => {
        const key = isPrimary ? 'primary' : `member_${index}`;
        const status = nikStatus[key] || { checking: false, exists: false, data: null };
        const ePrefix = isPrimary ? '' : `family_members.${index}.`;
        
        const handleChange = (field, value) => {
            if (isPrimary) {
                setData(field, value);
            } else {
                const newMembers = [...data.family_members];
                newMembers[index][field] = value;
                setData('family_members', newMembers);
            }
        };

        const renderSelectWithOther = (label, field, options, required = false) => {
            const isManual = manualFields[`${key}_${field}`] || (!options.includes(person[field]) && person[field] !== '');
            
            return (
                <div className="space-y-2">
                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{label} {required && '*'}</label>
                    <select 
                        value={isManual ? 'LAINNYA' : person[field]}
                        onChange={e => {
                            if (e.target.value === 'LAINNYA') {
                                toggleManual(key, field, true);
                                handleChange(field, '');
                            } else {
                                toggleManual(key, field, false);
                                handleChange(field, e.target.value);
                            }
                        }}
                        className={`w-full px-4 py-3 bg-gray-50 border rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all ${errors[`${ePrefix}${field}`] ? 'border-red-500' : 'border-gray-100'}`}
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
                                value={person[field]}
                                onChange={e => handleChange(field, e.target.value.toUpperCase())}
                                className="w-full px-4 py-3 bg-blue-50 border border-blue-200 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-blue-500/10"
                                required={required}
                                autoFocus
                            />
                            <button 
                                type="button"
                                onClick={() => {
                                    toggleManual(key, field, false);
                                    handleChange(field, options[0]);
                                }}
                                className="absolute right-3 top-3 text-[10px] font-black text-blue-500 hover:text-blue-700"
                            >
                                KEMBALI
                            </button>
                        </div>
                    )}
                    {errors[`${ePrefix}${field}`] && <p className="mt-1 text-[10px] font-bold text-red-600 uppercase tracking-tight">{errors[`${ePrefix}${field}`]}</p>}
                </div>
            );
        };

        return (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative">
                {!isPrimary && (
                    <button type="button" onClick={() => removeFamilyMember(index)} className="absolute -top-4 -right-4 w-10 h-10 bg-red-50 hover:bg-red-100 text-red-600 rounded-2xl border border-red-100 flex items-center justify-center transition-all shadow-sm z-10">
                        <Trash2 className="w-5 h-5" />
                    </button>
                )}
                
                <div className="md:col-span-2 lg:col-span-1">
                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">NIK (16 Digit) *</label>
                    <div className="relative mt-1">
                        <input 
                            type="text" 
                            maxLength="16"
                            value={person.nik}
                            onChange={e => handleChange('nik', e.target.value.replace(/\D/g, ''))}
                            className={`w-full px-4 py-3 bg-gray-50 border rounded-2xl text-sm font-bold font-mono outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all ${errors[`${ePrefix}nik`] ? 'border-red-500' : 'border-gray-100'}`}
                            required
                        />
                        {status.checking && <div className="absolute right-4 top-3.5 animate-spin w-4 h-4 border-2 border-green-500 border-t-transparent rounded-full"></div>}
                    </div>
                    {person.nik.length === 16 && !status.checking && (
                        <div className={`mt-2 p-2 rounded-xl border text-[10px] font-bold uppercase tracking-tight flex items-start ${status.exists ? 'bg-red-50 border-red-100 text-red-700' : 'bg-green-50 border-green-100 text-green-700'}`}>
                            {status.exists ? (
                                <><AlertTriangle className="w-3.5 h-3.5 mr-1.5 shrink-0 mt-0.5" /> <span>NIK Terdaftar: {status.data?.nama}</span></>
                            ) : (
                                <><CheckCircle className="w-3.5 h-3.5 mr-1.5 shrink-0 mt-0.5" /> NIK Tersedia</>
                            )}
                        </div>
                    )}
                </div>

                <div className="md:col-span-2">
                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap (Sesuai KTP) *</label>
                    <input 
                        type="text" 
                        value={person.nama}
                        onChange={e => handleChange('nama', e.target.value.toUpperCase())}
                        className={`w-full mt-1 px-4 py-3 bg-gray-50 border rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all ${errors[`${ePrefix}nama`] ? 'border-red-500' : 'border-gray-100'}`}
                        required
                    />
                </div>

                <div>
                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Kelamin *</label>
                    <select 
                        value={person.jenis_kelamin}
                        onChange={e => handleChange('jenis_kelamin', e.target.value)}
                        className="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all"
                        required
                    >
                        <option value="LAKI-LAKI">LAKI-LAKI</option>
                        <option value="PEREMPUAN">PEREMPUAN</option>
                    </select>
                </div>

                <div>
                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tempat Lahir *</label>
                    <input 
                        type="text" 
                        value={person.tempat_lahir}
                        onChange={e => handleChange('tempat_lahir', e.target.value.toUpperCase())}
                        className="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all"
                        required
                    />
                </div>

                <div>
                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Lahir *</label>
                    <input 
                        type="date" 
                        value={person.tanggal_lahir}
                        onChange={e => handleChange('tanggal_lahir', e.target.value)}
                        className="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all"
                        required
                    />
                </div>

                {renderSelectWithOther('Agama', 'agama', OPTIONS.agama, true)}
                {renderSelectWithOther('Pendidikan Terakhir', 'pendidikan', OPTIONS.pendidikan)}
                {renderSelectWithOther('Status Perkawinan', 'status_perkawinan', OPTIONS.status_perkawinan, true)}
                {renderSelectWithOther('Hubungan Keluarga', 'kedudukan_keluarga', OPTIONS.kedudukan_keluarga, true)}
                {renderSelectWithOther('Pekerjaan', 'pekerjaan', OPTIONS.pekerjaan, true)}

                <div>
                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Ayah</label>
                    <input 
                        type="text" 
                        value={person.nama_ayah}
                        onChange={e => handleChange('nama_ayah', e.target.value.toUpperCase())}
                        className="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all"
                    />
                </div>

                <div>
                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Ibu</label>
                    <input 
                        type="text" 
                        value={person.nama_ibu}
                        onChange={e => handleChange('nama_ibu', e.target.value.toUpperCase())}
                        className="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all"
                    />
                </div>
            </div>
        );
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Penduduk">
            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                
                {/* Header Card */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 text-white relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <UserPlus className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black tracking-tight uppercase italic leading-none text-left">Tambah Penduduk</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic text-left">Pendaftaran Warga Baru Desa Cibatu</p>
                            </div>
                        </div>
                        <Link 
                            href={route('penduduk.index')}
                            className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all w-fit"
                        >
                            <X className="w-4 h-4 mr-2" /> BATAL
                        </Link>
                    </div>
                </div>

                    <form onSubmit={handleSubmit} className="space-y-6">
                        
                        {/* KK Selection Strategy */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center gap-3">
                            <div className="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                                <Home className="w-4 h-4 text-green-600" />
                            </div>
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter text-left">Informasi Kartu Keluarga</h3>
                        </div>

                        <div className="p-8">
                            <div className="flex bg-gray-100 p-1.5 rounded-2xl mb-8 w-fit mx-auto sm:mx-0">
                                <button
                                    type="button"
                                    onClick={() => setKkOption('existing')}
                                    className={`px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all ${kkOption === 'existing' ? 'bg-white text-green-700 shadow-lg' : 'text-gray-500 hover:text-gray-700'}`}
                                >
                                    Pilih KK yang Ada
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setKkOption('manual')}
                                    className={`px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all ${kkOption === 'manual' ? 'bg-white text-green-700 shadow-lg' : 'text-gray-500 hover:text-gray-700'}`}
                                >
                                    Buat KK Baru
                                </button>
                            </div>

                            {/* Existing KK Section */}
                            {kkOption === 'existing' && (
                                <div className="space-y-4 animate-in fade-in zoom-in-95 duration-300">
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Cari Kartu Keluarga *</label>
                                    <div className="relative">
                                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                                        <input 
                                            type="text"
                                            placeholder="Ketik NKK atau Nama Kepala Keluarga..."
                                            value={searchTerm}
                                            onChange={(e) => {
                                                setSearchTerm(e.target.value);
                                                setShowDropdown(true);
                                                setSelectedKk(null);
                                            }}
                                            className="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none transition-all"
                                        />
                                    
                                    {showDropdown && searchTerm.length > 1 && (
                                        <div className="absolute z-10 w-full mt-2 bg-white rounded-xl shadow-xl border border-gray-100 max-h-60 overflow-y-auto">
                                            {filteredKKs.length > 0 ? (
                                                filteredKKs.map(kk => (
                                                    <div 
                                                        key={kk.nkk}
                                                        onClick={() => handleSelectKk(kk)}
                                                        className="p-3 hover:bg-green-50 cursor-pointer border-b border-gray-50 flex flex-col"
                                                    >
                                                        <span className="font-bold text-gray-900">{kk.kepala_keluarga || 'Tidak diketahui'}</span>
                                                        <span className="text-xs text-gray-500 font-mono mt-0.5">NKK: {kk.nkk} - RT {kk.rt}/RW {kk.rw}</span>
                                                    </div>
                                                ))
                                            ) : (
                                                <div className="p-4 text-center text-gray-500 text-sm">KK tidak ditemukan.</div>
                                            )}
                                        </div>
                                    )}
                                </div>
                                {errors.nkk && <p className="text-sm text-red-600">{errors.nkk}</p>}

                                {selectedKk && (
                                    <div className="mt-4 p-4 bg-green-50 border border-green-200 rounded-xl flex gap-4">
                                        <div className="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center shrink-0">
                                            <CheckCircle className="w-6 h-6" />
                                        </div>
                                        <div>
                                            <h4 className="font-bold text-green-900">KK Terpilih</h4>
                                            <p className="text-sm text-green-800 mt-1"><span className="font-semibold">NKK:</span> {selectedKk.nkk}</p>
                                            <p className="text-sm text-green-800"><span className="font-semibold">Kepala Keluarga:</span> {selectedKk.kepala_keluarga || '-'}</p>
                                            <p className="text-sm text-green-800"><span className="font-semibold">Alamat:</span> {selectedKk.alamat}</p>
                                        </div>
                                    </div>
                                )}
                            </div>
                        )}

                        {/* Manual KK Section */}
                        {kkOption === 'manual' && (
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 animate-in fade-in zoom-in-95 duration-300">
                                <div className="md:col-span-2">
                                    <div className="p-4 bg-blue-50 text-blue-800 rounded-xl border border-blue-200 text-sm flex gap-3">
                                        <Info className="w-5 h-5 shrink-0" />
                                        <p>Data Alamat (RT/RW/Dusun) akan mengikuti data Kepala Keluarga yang diinput ini.</p>
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">NKK Baru *</label>
                                    <div className="relative">
                                        <input 
                                            type="text" 
                                            maxLength="16"
                                            value={data.nkk}
                                            onChange={e => setData('nkk', e.target.value)}
                                            className={`w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 ${errors.nkk ? 'border-red-500' : 'border-gray-300'}`}
                                            required={kkOption === 'manual'}
                                        />
                                        {nkkStatus.checking && <div className="absolute right-3 top-3 animate-spin w-4 h-4 border-2 border-gray-400 border-t-transparent rounded-full"></div>}
                                    </div>
                                    
                                    {data.nkk.length === 16 && !nkkStatus.checking && (
                                        <div className={`mt-2 p-2 rounded-lg border text-sm flex items-start ${nkkStatus.exists ? 'bg-red-50 border-red-200 text-red-700' : 'bg-green-50 border-green-200 text-green-700'}`}>
                                            {nkkStatus.exists ? (
                                                <><AlertTriangle className="w-4 h-4 mr-1 shrink-0 mt-0.5" /> <span><strong>NKK Sudah Ada</strong>. Silakan pilih opsi "Pilih KK yang Ada"</span></>
                                            ) : (
                                                <><CheckCircle className="w-4 h-4 mr-1 shrink-0 mt-0.5" /> <strong>NKK Tersedia</strong></>
                                            )}
                                        </div>
                                    )}
                                    {errors.nkk && <p className="mt-1 text-sm text-red-600">{errors.nkk}</p>}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">Nama Kepala Keluarga *</label>
                                    <input 
                                        type="text" 
                                        value={data.nama_kepala_keluarga}
                                        onChange={e => setData('nama_kepala_keluarga', e.target.value.toUpperCase())}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        required={kkOption === 'manual'}
                                    />
                                    {errors.nama_kepala_keluarga && <p className="mt-1 text-sm text-red-600">{errors.nama_kepala_keluarga}</p>}
                                </div>

                                <div className="md:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap *</label>
                                    <textarea 
                                        value={data.alamat}
                                        onChange={e => setData('alamat', e.target.value.toUpperCase())}
                                        rows="3"
                                        className="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        required={kkOption === 'manual'}
                                    />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">RW *</label>
                                    <select 
                                        value={data.rw_id}
                                        onChange={e => setData('rw_id', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        required={kkOption === 'manual'}
                                    >
                                        <option value="">Pilih RW</option>
                                        {masterRwOptions.map(rw => (
                                            <option key={rw.id} value={rw.id}>RW {rw.kode}</option>
                                        ))}
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">RT *</label>
                                    <select 
                                        value={data.rt_id}
                                        onChange={e => {
                                            const val = e.target.value;
                                            const selectedRt = availableRts.find(r => String(r.id) === String(val));
                                            setData(prev => ({
                                                ...prev,
                                                rt_id: val,
                                                dusun_id: selectedRt ? selectedRt.dusun_id : null
                                            }));
                                        }}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        required={kkOption === 'manual'}
                                        disabled={!data.rw_id}
                                    >
                                        <option value="">Pilih RT</option>
                                        {availableRts.map(rt => (
                                            <option key={rt.id} value={rt.id}>RT {rt.kode} {rt.dusun ? `- ${rt.dusun}` : ''}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                        )}
                    </div>
                </div>

                {/* Primary Person Data */}
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <div className="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                            <h3 className="text-xl font-black text-gray-900 flex items-center">
                                <Users className="w-6 h-6 text-blue-500 mr-2" />
                                Data Penduduk Utama
                            </h3>
                        </div>
                        {renderPersonForm(data, true)}
                    </div>

                    {/* Additional Family Members */}
                    {data.family_members.map((member, index) => (
                        <div key={index} className="bg-gray-50 rounded-2xl shadow-inner border border-gray-200 p-6 sm:p-8 animate-in slide-in-from-bottom-4 duration-300">
                            <h3 className="text-lg font-black text-gray-800 mb-6 pb-4 border-b border-gray-200 flex items-center">
                                <Users className="w-5 h-5 text-gray-500 mr-2" />
                                Anggota Keluarga #{index + 1}
                            </h3>
                                {renderPersonForm(member, false, index)}
                        </div>
                    ))}

                    <div className="flex flex-col sm:flex-row items-center gap-4 pt-4">
                        <button
                            type="button"
                            onClick={addFamilyMember}
                            className="w-full sm:w-auto px-6 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all border border-gray-200 flex items-center justify-center"
                        >
                            <Plus className="w-4 h-4 mr-2" />
                            TAMBAH ANGGOTA KELUARGA
                        </button>

                        <button
                            type="submit"
                            disabled={processing || (kkOption === 'manual' && nkkStatus.exists)}
                            className="w-full sm:w-auto px-8 py-3 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-green-200 transition-all flex items-center justify-center sm:ml-auto"
                        >
                            {processing ? (
                                <><div className="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></div> MENYIMPAN...</>
                            ) : (
                                <><Save className="w-4 h-4 mr-2" /> SIMPAN DATA PENDUDUK</>
                            )}
                        </button>
                    </div>

                    </form>
            </div>
        </AuthenticatedLayout>
    );
}
