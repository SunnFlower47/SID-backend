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

    const getEmptyPerson = () => ({
        nik: '', nama: '', jenis_kelamin: '', tempat_lahir: '', tanggal_lahir: '',
        agama: '', pendidikan: '', pekerjaan: '', status_perkawinan: '', kedudukan_keluarga: '',
        nama_ayah: '', nama_ibu: '', keterangan: ''
    });

    const { data, setData, post, processing, errors } = useForm({
        kk_option: 'existing',
        
        // Manual KK Fields
        nkk: '',
        nama_kepala_keluarga: '',
        alamat: '',
        rw_id: '',
        rt_id: '',
        
        // Primary Person Fields
        ...getEmptyPerson(),
        
        // Additional Family Members
        family_members: []
    });

    // Update kk_option in form when state changes
    useEffect(() => {
        setData('kk_option', kkOption);
    }, [kkOption]);

    // Derived filtered KKs for autocomplete
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
            alamat: kk.alamat || '',
            // In blade, RT/RW IDs were matched, but for existing KK we submit the NKK and backend handles it.
            // We just need nkk to be set.
        }));
    };

    // Auto populate RT based on RW for manual mode
    const [availableRts, setAvailableRts] = useState([]);
    useEffect(() => {
        if (data.rw_id) {
            const rw = masterRwOptions.find(r => String(r.id) === String(data.rw_id));
            setAvailableRts(rw?.rts || []);
            // reset rt_id if not in new list
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

    const removeFamilyMember = (index) => {
        const newMembers = [...data.family_members];
        newMembers.splice(index, 1);
        setData('family_members', newMembers);
        
        // Cleanup nik status
        const newNikStatus = { ...nikStatus };
        delete newNikStatus[`member_${index}`];
        setNikStatus(newNikStatus);
    };

    const handleMemberChange = (index, field, value) => {
        const newMembers = [...data.family_members];
        newMembers[index][field] = value;
        setData('family_members', newMembers);
    };

    // Debounced NIK Check function
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
            const response = await fetch(route('penduduk.check-nkk') + `?nkk=${nkk}`, {
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
        
        // Final validation before submit
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

    // Reusable Form Section component
    const PersonForm = ({ person, isPrimary = true, index = null, prefix = '' }) => {
        const key = isPrimary ? 'primary' : `member_${index}`;
        const status = nikStatus[key] || { checking: false, exists: false, data: null };
        const ePrefix = isPrimary ? '' : `family_members.${index}.`;
        
        const handleChange = (field, value) => {
            if (isPrimary) {
                setData(field, value);
            } else {
                handleMemberChange(index, field, value);
            }
        };

        return (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 relative">
                {!isPrimary && (
                    <button type="button" onClick={() => removeFamilyMember(index)} className="absolute -top-4 -right-4 w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-full flex items-center justify-center transition-colors">
                        <Trash2 className="w-4 h-4" />
                    </button>
                )}
                
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">NIK *</label>
                    <div className="relative">
                        <input 
                            type="text" 
                            maxLength="16"
                            value={person.nik}
                            onChange={e => handleChange('nik', e.target.value)}
                            className={`w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 ${errors[`${ePrefix}nik`] ? 'border-red-500' : 'border-gray-300'}`}
                            required
                        />
                        {status.checking && <div className="absolute right-3 top-3 animate-spin w-4 h-4 border-2 border-gray-400 border-t-transparent rounded-full"></div>}
                    </div>
                    {errors[`${ePrefix}nik`] && <p className="mt-1 text-sm text-red-600">{errors[`${ePrefix}nik`]}</p>}
                    
                    {/* NIK Status Indicator */}
                    {person.nik.length === 16 && !status.checking && (
                        <div className={`mt-2 p-2 rounded-lg border text-sm flex items-start ${status.exists ? 'bg-red-50 border-red-200 text-red-700' : 'bg-green-50 border-green-200 text-green-700'}`}>
                            {status.exists ? (
                                <><AlertTriangle className="w-4 h-4 mr-1 shrink-0 mt-0.5" /> <span><strong>NIK Sudah Ada:</strong> {status.data?.nama}</span></>
                            ) : (
                                <><CheckCircle className="w-4 h-4 mr-1 shrink-0 mt-0.5" /> <strong>NIK Tersedia</strong></>
                            )}
                        </div>
                    )}
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                    <input 
                        type="text" 
                        value={person.nama}
                        onChange={e => handleChange('nama', e.target.value)}
                        className={`w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 ${errors[`${ePrefix}nama`] ? 'border-red-500' : 'border-gray-300'}`}
                        required
                    />
                    {errors[`${ePrefix}nama`] && <p className="mt-1 text-sm text-red-600">{errors[`${ePrefix}nama`]}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin *</label>
                    <select 
                        value={person.jenis_kelamin}
                        onChange={e => handleChange('jenis_kelamin', e.target.value)}
                        className={`w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 ${errors[`${ePrefix}jenis_kelamin`] ? 'border-red-500' : 'border-gray-300'}`}
                        required
                    >
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="LAKI-LAKI">Laki-laki</option>
                        <option value="PEREMPUAN">Perempuan</option>
                    </select>
                    {errors[`${ePrefix}jenis_kelamin`] && <p className="mt-1 text-sm text-red-600">{errors[`${ePrefix}jenis_kelamin`]}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir *</label>
                    <input 
                        type="text" 
                        value={person.tempat_lahir}
                        onChange={e => handleChange('tempat_lahir', e.target.value)}
                        className={`w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 ${errors[`${ePrefix}tempat_lahir`] ? 'border-red-500' : 'border-gray-300'}`}
                        required
                    />
                    {errors[`${ePrefix}tempat_lahir`] && <p className="mt-1 text-sm text-red-600">{errors[`${ePrefix}tempat_lahir`]}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir *</label>
                    <input 
                        type="date" 
                        value={person.tanggal_lahir}
                        onChange={e => handleChange('tanggal_lahir', e.target.value)}
                        className={`w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 ${errors[`${ePrefix}tanggal_lahir`] ? 'border-red-500' : 'border-gray-300'}`}
                        required
                    />
                    {errors[`${ePrefix}tanggal_lahir`] && <p className="mt-1 text-sm text-red-600">{errors[`${ePrefix}tanggal_lahir`]}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Agama *</label>
                    <input 
                        type="text" 
                        value={person.agama}
                        onChange={e => handleChange('agama', e.target.value)}
                        placeholder="Contoh: Islam"
                        className={`w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 ${errors[`${ePrefix}agama`] ? 'border-red-500' : 'border-gray-300'}`}
                        required
                    />
                    {errors[`${ePrefix}agama`] && <p className="mt-1 text-sm text-red-600">{errors[`${ePrefix}agama`]}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan *</label>
                    <input 
                        type="text" 
                        value={person.status_perkawinan}
                        onChange={e => handleChange('status_perkawinan', e.target.value)}
                        placeholder="Contoh: Belum Kawin"
                        className={`w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 ${errors[`${ePrefix}status_perkawinan`] ? 'border-red-500' : 'border-gray-300'}`}
                        required
                    />
                    {errors[`${ePrefix}status_perkawinan`] && <p className="mt-1 text-sm text-red-600">{errors[`${ePrefix}status_perkawinan`]}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Kedudukan Keluarga *</label>
                    <input 
                        type="text" 
                        value={person.kedudukan_keluarga}
                        onChange={e => handleChange('kedudukan_keluarga', e.target.value)}
                        placeholder="Contoh: Kepala Keluarga, Istri, Anak"
                        className={`w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 ${errors[`${ePrefix}kedudukan_keluarga`] ? 'border-red-500' : 'border-gray-300'}`}
                        required
                    />
                    {errors[`${ePrefix}kedudukan_keluarga`] && <p className="mt-1 text-sm text-red-600">{errors[`${ePrefix}kedudukan_keluarga`]}</p>}
                </div>
                
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Pendidikan Terakhir</label>
                    <input 
                        type="text" 
                        value={person.pendidikan}
                        onChange={e => handleChange('pendidikan', e.target.value)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    />
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Pekerjaan *</label>
                    <input 
                        type="text" 
                        value={person.pekerjaan}
                        onChange={e => handleChange('pekerjaan', e.target.value)}
                        className={`w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 ${errors[`${ePrefix}pekerjaan`] ? 'border-red-500' : 'border-gray-300'}`}
                        required
                    />
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
                    <input 
                        type="text" 
                        value={person.nama_ayah}
                        onChange={e => handleChange('nama_ayah', e.target.value)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    />
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
                    <input 
                        type="text" 
                        value={person.nama_ibu}
                        onChange={e => handleChange('nama_ibu', e.target.value)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    />
                </div>
            </div>
        );
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Penduduk">
            <div className="space-y-6 animate-in fade-in duration-500 max-w-5xl mx-auto pb-12">
                
                {/* Header Card */}
                <div className="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div className="flex items-center space-x-4">
                            <div className="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <UserPlus className="w-8 h-8 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-2xl sm:text-3xl font-black">Tambah Penduduk</h1>
                                <p className="text-green-100 mt-1">Registrasi data penduduk baru</p>
                            </div>
                        </div>
                        <Link 
                            href={route('penduduk.index')}
                            className="inline-flex justify-center items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg transition-all"
                        >
                            <X className="w-5 h-5 mr-2" />
                            Batal
                        </Link>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    
                    {/* KK Selection Strategy */}
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <h3 className="text-xl font-black text-gray-900 mb-6 flex items-center">
                            <Home className="w-6 h-6 text-green-500 mr-2" />
                            Informasi Kartu Keluarga
                        </h3>

                        <div className="flex bg-gray-100 p-1 rounded-xl mb-6 w-full sm:w-max">
                            <button
                                type="button"
                                onClick={() => setKkOption('existing')}
                                className={`px-6 py-2.5 rounded-lg text-sm font-bold transition-all ${kkOption === 'existing' ? 'bg-white text-green-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'}`}
                            >
                                Pilih KK yang Ada
                            </button>
                            <button
                                type="button"
                                onClick={() => setKkOption('manual')}
                                className={`px-6 py-2.5 rounded-lg text-sm font-bold transition-all ${kkOption === 'manual' ? 'bg-white text-green-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'}`}
                            >
                                Buat KK Baru
                            </button>
                        </div>

                        {/* Existing KK Section */}
                        {kkOption === 'existing' && (
                            <div className="space-y-4 animate-in fade-in zoom-in-95 duration-300">
                                <label className="block text-sm font-medium text-gray-700">Cari Kartu Keluarga *</label>
                                <div className="relative">
                                    <Search className="absolute left-3 top-3 text-gray-400 w-5 h-5" />
                                    <input 
                                        type="text"
                                        placeholder="Ketik NKK atau Nama Kepala Keluarga..."
                                        value={searchTerm}
                                        onChange={(e) => {
                                            setSearchTerm(e.target.value);
                                            setShowDropdown(true);
                                            setSelectedKk(null);
                                        }}
                                        className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
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
                                        onChange={e => setData('nama_kepala_keluarga', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        required={kkOption === 'manual'}
                                    />
                                    {errors.nama_kepala_keluarga && <p className="mt-1 text-sm text-red-600">{errors.nama_kepala_keluarga}</p>}
                                </div>

                                <div className="md:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap *</label>
                                    <textarea 
                                        value={data.alamat}
                                        onChange={e => setData('alamat', e.target.value)}
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
                                        onChange={e => setData('rt_id', e.target.value)}
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

                    {/* Primary Person Data */}
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <div className="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                            <h3 className="text-xl font-black text-gray-900 flex items-center">
                                <Users className="w-6 h-6 text-blue-500 mr-2" />
                                Data Penduduk Utama
                            </h3>
                        </div>
                        <PersonForm person={data} isPrimary={true} />
                    </div>

                    {/* Additional Family Members */}
                    {data.family_members.map((member, index) => (
                        <div key={index} className="bg-gray-50 rounded-2xl shadow-inner border border-gray-200 p-6 sm:p-8 animate-in slide-in-from-bottom-4 duration-300">
                            <h3 className="text-lg font-black text-gray-800 mb-6 pb-4 border-b border-gray-200 flex items-center">
                                <Users className="w-5 h-5 text-gray-500 mr-2" />
                                Anggota Keluarga #{index + 1}
                            </h3>
                            <PersonForm person={member} isPrimary={false} index={index} />
                        </div>
                    ))}

                    <div className="flex flex-col sm:flex-row items-center gap-4 pt-4">
                        <button
                            type="button"
                            onClick={addFamilyMember}
                            className="w-full sm:w-auto px-6 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl transition-colors border border-gray-200 flex items-center justify-center"
                        >
                            <Plus className="w-5 h-5 mr-2" />
                            Tambah Anggota Keluarga Lainnya
                        </button>

                        <button
                            type="submit"
                            disabled={processing || (kkOption === 'manual' && nkkStatus.exists)}
                            className="w-full sm:w-auto px-8 py-3.5 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-bold rounded-xl shadow-lg shadow-green-200 transition-all flex items-center justify-center sm:ml-auto"
                        >
                            {processing ? (
                                <><div className="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full mr-2"></div> Menyimpan...</>
                            ) : (
                                <><Save className="w-5 h-5 mr-2" /> Simpan Data Penduduk</>
                            )}
                        </button>
                    </div>

                </form>
            </div>
        </AuthenticatedLayout>
    );
}
