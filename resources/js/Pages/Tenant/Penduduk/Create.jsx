import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Users, Save, X, Plus, Trash2, Search, CheckCircle, AlertTriangle, Home, UserPlus, Info } from 'lucide-react';
import Swal from 'sweetalert2';

// Shared Components
import { PageHeader, FormCard, FormField } from '@/Components/Shared';

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
        status_perkawinan: ['BELUM KAWIN', 'KAWIN TERCATAT', 'KAWIN BELUM TERCATAT', 'CERAI HIDUP TERCATAT', 'CERAI HIDUP BELUM TERCATAT', 'CERAI MATI'],
        kedudukan_keluarga: ['Kepala Keluarga', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Pembantu'],
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

    const getEmptyPerson = () => ({
        nik: '', nama: '', jenis_kelamin: 'LAKI-LAKI', tempat_lahir: '', tanggal_lahir: '',
        agama: 'ISLAM', pendidikan: 'TIDAK / BELUM SEKOLAH', pekerjaan: 'BELUM/TIDAK BEKERJA', 
        status_perkawinan: 'BELUM KAWIN', kedudukan_keluarga: 'Anak',
        nama_ayah: '', nama_ibu: '', keterangan: '',
        golongan_darah: 'TIDAK TAHU', warganegara: 'WNI', no_akta_lahir: '',
        status_pendidikan: 'TAMAT SEKOLAH', telepon: '', cacat_type: '',
        sakit_menahun: '', status_asuransi: 'TIDAK ADA',
        dapat_membaca_huruf: ''
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
                <FormField label={label} required={required} error={errors[`${ePrefix}${field}`]}>
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
                        <div className="relative animate-in slide-in-from-top-2 duration-200 mt-2">
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
                </FormField>
            );
        };

        return (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative">
                {!isPrimary && (
                    <button type="button" onClick={() => removeFamilyMember(index)} className="absolute -top-10 right-0 w-10 h-10 bg-red-50 hover:bg-red-100 text-red-600 rounded-2xl border border-red-100 flex items-center justify-center transition-all shadow-sm z-10">
                        <Trash2 className="w-5 h-5" />
                    </button>
                )}
                
                <div className="md:col-span-2 lg:col-span-1">
                    <FormField label="NIK (16 Digit)" required error={errors[`${ePrefix}nik`]}>
                        <div className="relative">
                            <input 
                                type="text" 
                                maxLength="16"
                                value={person.nik}
                                onChange={e => handleChange('nik', e.target.value.replace(/\D/g, ''))}
                                className={`w-full px-4 py-3 bg-gray-50 border rounded-2xl text-sm font-bold font-mono outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all ${errors[`${ePrefix}nik`] ? 'border-red-500' : 'border-gray-100'}`}
                                placeholder="CONTOH: 320XXXXXXXXXXXXX"
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
                    </FormField>
                </div>

                <div className="md:col-span-2">
                    <FormField.Input 
                        label="Nama Lengkap (Sesuai KTP)"
                        required
                        value={person.nama}
                        onChange={e => handleChange('nama', e.target.value.toUpperCase())}
                        error={errors[`${ePrefix}nama`]}
                        placeholder="CONTOH: BUDI SANTOSO"
                    />
                </div>

                <FormField.Select
                    label="Jenis Kelamin"
                    required
                    value={person.jenis_kelamin}
                    onChange={e => handleChange('jenis_kelamin', e.target.value)}
                    options={['LAKI-LAKI', 'PEREMPUAN']}
                />

                <FormField.Input
                    label="Tempat Lahir"
                    required
                    value={person.tempat_lahir}
                    onChange={e => handleChange('tempat_lahir', e.target.value.toUpperCase())}
                    error={errors[`${ePrefix}tempat_lahir`]}
                    placeholder="CONTOH: BANDUNG"
                />

                <FormField.Input
                    label="Tanggal Lahir"
                    type="date"
                    required
                    value={person.tanggal_lahir}
                    onChange={e => handleChange('tanggal_lahir', e.target.value)}
                    error={errors[`${ePrefix}tanggal_lahir`]}
                />

                {renderSelectWithOther('Agama', 'agama', OPTIONS.agama, true)}
                {renderSelectWithOther('Pendidikan Terakhir', 'pendidikan', OPTIONS.pendidikan)}
                {renderSelectWithOther('Status Perkawinan', 'status_perkawinan', OPTIONS.status_perkawinan, true)}
                {renderSelectWithOther('Hubungan Keluarga', 'kedudukan_keluarga', OPTIONS.kedudukan_keluarga, true)}
                {renderSelectWithOther('Pekerjaan', 'pekerjaan', OPTIONS.pekerjaan, true)}

                <FormField.Input
                    label="Nama Ayah"
                    value={person.nama_ayah}
                    onChange={e => handleChange('nama_ayah', e.target.value.toUpperCase())}
                    error={errors[`${ePrefix}nama_ayah`]}
                    placeholder="CONTOH: AGUS"
                />

                <FormField.Input
                    label="Nama Ibu"
                    value={person.nama_ibu}
                    onChange={e => handleChange('nama_ibu', e.target.value.toUpperCase())}
                    error={errors[`${ePrefix}nama_ibu`]}
                    placeholder="CONTOH: SITI"
                />

                {/* Enrichment Fields */}
                <div className="col-span-full border-t border-gray-100 my-4 pt-4">
                    <h4 className="text-xs font-black uppercase tracking-widest text-gray-400">Informasi Tambahan, Kontak & Kesehatan</h4>
                </div>

                <FormField.Select
                    label="Golongan Darah"
                    value={person.golongan_darah}
                    onChange={e => handleChange('golongan_darah', e.target.value)}
                    options={OPTIONS.golongan_darah}
                    error={errors[`${ePrefix}golongan_darah`]}
                />

                <FormField.Select
                    label="Dapat Membaca Huruf"
                    value={person.dapat_membaca_huruf}
                    onChange={e => handleChange('dapat_membaca_huruf', e.target.value)}
                    options={OPTIONS.dapat_membaca_huruf}
                    error={errors[`${ePrefix}dapat_membaca_huruf`]}
                />

                <FormField.Select
                    label="Kewarganegaraan"
                    value={person.warganegara}
                    onChange={e => handleChange('warganegara', e.target.value)}
                    options={OPTIONS.warganegara}
                    error={errors[`${ePrefix}warganegara`]}
                />

                <FormField.Input
                    label="Nomor Akta Lahir"
                    value={person.no_akta_lahir}
                    onChange={e => handleChange('no_akta_lahir', e.target.value.toUpperCase())}
                    error={errors[`${ePrefix}no_akta_lahir`]}
                    placeholder="Kosongkan jika tidak ada"
                />

                <FormField.Select
                    label="Status Pendidikan"
                    value={person.status_pendidikan}
                    onChange={e => handleChange('status_pendidikan', e.target.value)}
                    options={OPTIONS.status_pendidikan}
                    error={errors[`${ePrefix}status_pendidikan`]}
                />

                <FormField.Input
                    label="Nomor Telepon/WA"
                    value={person.telepon}
                    onChange={e => handleChange('telepon', e.target.value.replace(/\D/g, ''))}
                    error={errors[`${ePrefix}telepon`]}
                    placeholder="CONTOH: 08123456789"
                />

                <FormField.Input
                    label="Jenis Cacat/Disabilitas"
                    placeholder="Contoh: Netra, Rungu, dll (Kosongkan jika tidak ada)"
                    value={person.cacat_type}
                    onChange={e => handleChange('cacat_type', e.target.value.toUpperCase())}
                    error={errors[`${ePrefix}cacat_type`]}
                />

                <FormField.Input
                    label="Penyakit Menahun"
                    placeholder="Contoh: Jantung, TBC, dll (Kosongkan jika tidak ada)"
                    value={person.sakit_menahun}
                    onChange={e => handleChange('sakit_menahun', e.target.value.toUpperCase())}
                    error={errors[`${ePrefix}sakit_menahun`]}
                />

                <FormField.Select
                    label="Status Asuransi Kesehatan"
                    value={person.status_asuransi}
                    onChange={e => handleChange('status_asuransi', e.target.value)}
                    options={OPTIONS.status_asuransi}
                    error={errors[`${ePrefix}status_asuransi`]}
                />
            </div>
        );
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Penduduk">
            <div className="max-w-7xl mx-auto space-y-6 animate-in fade-in duration-700 pb-20">
                
                {/* Header Card */}
                <PageHeader 
                    title="Tambah Penduduk"
                    subtitle="Pendaftaran Warga Baru Desa Cibatu"
                    icon={UserPlus}
                    actions={[
                        {
                            label: 'BATAL',
                            icon: X,
                            href: route('penduduk.index'),
                            variant: 'ghost'
                        }
                    ]}
                />

                <form onSubmit={handleSubmit} className="space-y-6">
                    
                    {/* KK Selection Strategy */}
                    <FormCard icon={Home} title="Informasi Kartu Keluarga">
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
                                <FormField label="Cari Kartu Keluarga" required error={errors.nkk}>
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
                                </FormField>

                                {selectedKk && (
                                    <div className="mt-4 p-4 bg-green-50 border border-green-200 rounded-xl flex gap-4 animate-in fade-in slide-in-from-bottom-2 duration-300">
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
                                    <FormField label="NKK Baru" required={true} error={errors.nkk}>
                                        <div className="relative">
                                            <input 
                                                type="text" 
                                                maxLength="16"
                                                value={data.nkk}
                                                onChange={e => setData('nkk', e.target.value.replace(/\D/g, ''))}
                                                className={`w-full px-4 py-3 bg-gray-50 border rounded-2xl text-sm font-bold font-mono outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all ${errors.nkk ? 'border-red-500' : 'border-gray-100'}`}
                                                required
                                            />
                                            {nkkStatus.checking && <div className="absolute right-4 top-3.5 animate-spin w-4 h-4 border-2 border-gray-400 border-t-transparent rounded-full"></div>}
                                        </div>
                                    </FormField>
                                    
                                    {data.nkk.length === 16 && !nkkStatus.checking && (
                                        <div className={`mt-2 p-2 rounded-xl border text-[10px] font-bold uppercase tracking-tight flex items-start ${nkkStatus.exists ? 'bg-red-50 border-red-100 text-red-700' : 'bg-green-50 border-green-100 text-green-700'}`}>
                                            {nkkStatus.exists ? (
                                                <><AlertTriangle className="w-3.5 h-3.5 mr-1.5 shrink-0 mt-0.5" /> <span>NKK Sudah Ada. Silakan pilih opsi "Pilih KK yang Ada"</span></>
                                            ) : (
                                                <><CheckCircle className="w-3.5 h-3.5 mr-1.5 shrink-0 mt-0.5" /> NKK Tersedia</>
                                            )}
                                        </div>
                                    )}
                                </div>

                                <FormField.Input
                                    label="Nama Kepala Keluarga"
                                    required={true}
                                    value={data.nama_kepala_keluarga}
                                    onChange={e => setData('nama_kepala_keluarga', e.target.value.toUpperCase())}
                                    error={errors.nama_kepala_keluarga}
                                    placeholder="CONTOH: BUDI SANTOSO"
                                />

                                <div className="md:col-span-2">
                                    <FormField.Textarea
                                        label="Alamat Domisili"
                                        required={true}
                                        value={data.alamat}
                                        onChange={e => setData('alamat', e.target.value.toUpperCase())}
                                        error={errors.alamat}
                                        placeholder="CONTOH: KP. KARAJAN / NAMA JALAN / NO. RUMAH"
                                    />
                                </div>

                                <FormField.Select
                                    label="RW"
                                    required={true}
                                    value={data.rw_id}
                                    onChange={e => setData('rw_id', e.target.value)}
                                    error={errors.rw_id}
                                    options={masterRwOptions.map(rw => ({ value: rw.id, label: `RW ${rw.kode}` }))}
                                    placeholder="Pilih RW"
                                />

                                <FormField.Select
                                    label="RT"
                                    required={true}
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
                                    error={errors.rt_id}
                                    options={availableRts.map(rt => ({ value: rt.id, label: `RT ${rt.kode} ${rt.dusun ? `- ${rt.dusun}` : ''}` }))}
                                    placeholder="Pilih RT"
                                    disabled={!data.rw_id}
                                />
                            </div>
                        )}
                    </FormCard>

                    {/* Primary Person Data */}
                    <FormCard icon={Users} title="Data Penduduk Utama">
                        {renderPersonForm(data, true)}
                    </FormCard>

                    {/* Additional Family Members */}
                    {data.family_members.map((member, index) => (
                        <FormCard 
                            key={index} 
                            icon={Users} 
                            title={`Anggota Keluarga #${index + 1}`} 
                            className="bg-gray-50/50 animate-in slide-in-from-bottom-4 duration-300"
                        >
                            {renderPersonForm(member, false, index)}
                        </FormCard>
                    ))}

                    <div className="flex flex-col sm:flex-row items-center gap-4 pt-4">
                        <button
                            type="button"
                            onClick={addFamilyMember}
                            className="w-full sm:w-auto px-6 py-3.5 bg-white hover:bg-gray-50 text-gray-800 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all border border-gray-200 flex items-center justify-center shadow-sm"
                        >
                            <Plus className="w-4 h-4 mr-2" />
                            TAMBAH ANGGOTA KELUARGA
                        </button>

                        <button
                            type="submit"
                            disabled={processing || (kkOption === 'manual' && nkkStatus.exists)}
                            className="w-full sm:w-auto px-8 py-3.5 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-green-200 transition-all flex items-center justify-center sm:ml-auto hover:scale-105 active:scale-95"
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
