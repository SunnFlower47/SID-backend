import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { User, Shield, MapPin, Calendar, FileText, Camera, Save, ArrowLeft, XCircle } from 'lucide-react';

export default function StrukturDesaForm({ 
    initialData = {}, 
    kategoriOptions = [], 
    masterRwOptions = [], 
    isEdit = false 
}) {
    const { data, setData, post, processing, errors } = useForm({
        _method: isEdit ? 'PUT' : 'POST',
        nama: initialData.nama || '',
        jabatan: initialData.jabatan || '',
        kategori: initialData.kategori || '',
        nik: initialData.nik || '',
        no_hp: initialData.no_hp || '',
        email: initialData.email || '',
        alamat: initialData.alamat || '',
        rt_id: initialData.rt_id || '',
        rw_id: initialData.rw_id || '',
        dusun_id: initialData.dusun_id || '',
        tugas_wewenang: initialData.tugas_wewenang || '',
        tanggal_pengangkatan: initialData.tanggal_pengangkatan || '',
        tanggal_berakhir: initialData.tanggal_berakhir || '',
        status_aktif: initialData.status_aktif ?? true,
        urutan: initialData.urutan || 0,
        foto: null,
    });

    const [preview, setPreview] = useState(initialData.foto ? `/storage/${initialData.foto}` : null);
    const [searchTerm, setSearchTerm] = useState('');
    const [searchResults, setSearchResults] = useState([]);
    const [isSearching, setIsSearching] = useState(false);
    const [selectedResident, setSelectedResident] = useState(null);
    const [showResults, setShowResults] = useState(false);

    // Sync preview with data.foto
    React.useEffect(() => {
        if (!data.foto) {
            setPreview(initialData.foto ? `/storage/${initialData.foto}` : null);
            return;
        }

        if (data.foto instanceof File || data.foto instanceof Blob) {
            const reader = new FileReader();
            reader.onloadend = () => {
                setPreview(reader.result);
            };
            reader.readAsDataURL(data.foto);
        }
    }, [data.foto]);

    // Debounced search for residents
    React.useEffect(() => {
        const timer = setTimeout(() => {
            if (searchTerm.length >= 3) {
                performSearch();
            } else {
                setSearchResults([]);
                setShowResults(false);
            }
        }, 300);

        return () => clearTimeout(timer);
    }, [searchTerm]);

    const performSearch = async () => {
        setIsSearching(true);
        try {
            const response = await fetch(`/mutasi/search-penduduk?query=${encodeURIComponent(searchTerm)}`);
            const results = await response.json();
            setSearchResults(results);
            setShowResults(true);
        } catch (error) {
            console.error('Search error:', error);
        } finally {
            setIsSearching(false);
        }
    };

    const handleSelectResident = (res) => {
        setSelectedResident(res);
        setData({
            ...data,
            nik: res.nik,
            nama: res.nama,
            alamat: res.alamat,
            rw_id: res.rw_id || '',
            rt_id: res.rt_id || '',
            dusun_id: res.dusun_id || '',
        });
        setSearchTerm('');
        setShowResults(false);
    };

    const clearResident = () => {
        setSelectedResident(null);
        setData({
            ...data,
            nik: '',
            nama: '',
            alamat: '',
        });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const routeName = isEdit ? 'struktur-desa.update' : 'struktur-desa.store';
        const routeParam = isEdit ? initialData.id : null;

        post(route(routeName, routeParam), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                // Optional: success handling
            }
        });
    };

    const handleFotoChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('foto', file);
        }
    };

    // Filter RT options based on selected RW
    const availableRts = masterRwOptions.find(rw => rw.id == data.rw_id)?.rts || [];

    return (
        <form onSubmit={handleSubmit} className="space-y-6 text-left pb-10">
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {/* Kiri: Foto & Status */}
                <div className="lg:col-span-1 space-y-6">
                    <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm overflow-hidden relative group">
                        <div className="absolute top-0 right-0 p-4">
                            <span className={`px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest ${data.status_aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>
                                {data.status_aktif ? 'AKTIF' : 'NONAKTIF'}
                            </span>
                        </div>
                        <h4 className="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <Camera className="w-4 h-4 text-green-600" />
                            Foto Perangkat
                        </h4>
                        
                        <div className="flex flex-col items-center">
                            <div className="relative w-48 h-64 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 overflow-hidden flex items-center justify-center group transition-all hover:border-green-500/50">
                                {preview ? (
                                    <img src={preview} alt="Preview" className="w-full h-full object-cover" />
                                ) : (
                                    <div className="text-center p-4">
                                        <Camera className="w-10 h-10 text-gray-300 mx-auto mb-2" />
                                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-tight">Belum ada foto</p>
                                    </div>
                                )}
                                <label className="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                    <div className="text-white text-center">
                                        <Camera className="w-8 h-8 mx-auto mb-2" />
                                        <p className="text-[10px] font-black uppercase tracking-widest">Ubah Foto</p>
                                    </div>
                                    <input type="file" onChange={handleFotoChange} className="hidden" accept="image/*" />
                                </label>
                            </div>
                            <p className="mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest italic text-center px-4">
                                Gunakan foto formal dengan latar belakang polos. Maks. 2MB (JPG/PNG).
                            </p>
                            {errors.foto && <p className="text-red-500 text-[10px] font-bold uppercase mt-2">{errors.foto}</p>}
                        </div>

                        <div className="mt-8 pt-6 border-t border-gray-50">
                            <label className="flex items-center cursor-pointer group">
                                <div className="relative">
                                    <input 
                                        type="checkbox" 
                                        className="sr-only" 
                                        checked={data.status_aktif}
                                        onChange={e => setData('status_aktif', e.target.checked)}
                                    />
                                    <div className={`w-11 h-6 rounded-full shadow-inner transition-all duration-300 ${data.status_aktif ? 'bg-green-500 shadow-green-200' : 'bg-gray-200'}`}></div>
                                    <div className={`absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-md transition-all duration-300 transform ${data.status_aktif ? 'translate-x-5' : 'translate-x-0'}`}></div>
                                </div>
                                <span className="ml-3 text-[11px] font-black text-gray-700 uppercase tracking-widest group-hover:text-green-600 transition-colors">Status Aktif Perangkat</span>
                            </label>
                        </div>
                    </div>
                </div>

                {/* Kanan: Informasi Form */}
                <div className="lg:col-span-2 space-y-6">
                    {/* Seksi Informasi Pribadi */}
                    <div className="bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-sm">
                        <h4 className="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2 italic">
                            <User className="w-4 h-4 text-blue-600" />
                            Identitas Pribadi
                        </h4>
                        
                        {/* Search Resident (NIK/Name) */}
                        <div className="mb-8 p-5 bg-blue-50/50 rounded-2xl border border-blue-100 relative">
                            <label className="text-[10px] font-black text-blue-600 uppercase tracking-widest ml-1 mb-2 block">Cari Data Penduduk (NIK/Nama)</label>
                            <div className="relative group">
                                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    {isSearching ? (
                                        <div className="w-4 h-4 border-2 border-blue-500/30 border-t-blue-500 rounded-full animate-spin"></div>
                                    ) : (
                                        <User className="w-4 h-4 text-blue-400" />
                                    )}
                                </div>
                                <input 
                                    type="text" 
                                    value={searchTerm}
                                    onChange={e => setSearchTerm(e.target.value)}
                                    onFocus={() => searchResults.length > 0 && setShowResults(true)}
                                    className="w-full pl-10 pr-4 py-3 bg-white border-blue-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl text-xs font-bold transition-all shadow-sm placeholder:text-blue-200"
                                    placeholder="Ketik NIK atau Nama untuk mencari..."
                                />

                                {/* Search Results Dropdown */}
                                {showResults && (
                                    <div className="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden animate-in fade-in slide-in-from-top-2 duration-200">
                                        <div className="max-h-60 overflow-y-auto divide-y divide-gray-50">
                                            {searchResults.length > 0 ? searchResults.map((res) => (
                                                <button
                                                    key={res.nik}
                                                    type="button"
                                                    onClick={() => handleSelectResident(res)}
                                                    className="w-full p-4 text-left hover:bg-blue-50 transition-colors flex items-center justify-between group"
                                                >
                                                    <div>
                                                        <p className="text-xs font-black text-gray-900 uppercase italic group-hover:text-blue-600 transition-colors">{res.nama}</p>
                                                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">NIK: {res.nik}</p>
                                                        <p className="text-[9px] font-bold text-gray-400 truncate mt-0.5 max-w-[200px] sm:max-w-xs">{res.alamat}</p>
                                                    </div>
                                                    <div className="w-6 h-6 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                                                        <Save className="w-3 h-3" />
                                                    </div>
                                                </button>
                                            )) : (
                                                <div className="p-8 text-center">
                                                    <XCircle className="w-8 h-8 text-gray-200 mx-auto mb-2" />
                                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tidak ditemukan</p>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Click Away Listener (Simple) */}
                            {showResults && (
                                <div className="fixed inset-0 z-40" onClick={() => setShowResults(false)}></div>
                            )}

                            {selectedResident && (
                                <div className="mt-4 p-4 bg-blue-600 rounded-xl flex items-center justify-between text-white shadow-lg shadow-blue-200 animate-in zoom-in duration-300">
                                    <div className="flex items-center gap-3">
                                        <div className="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                            <Shield className="w-5 h-5 text-white" />
                                        </div>
                                        <div>
                                            <p className="text-xs font-black uppercase italic leading-none">{selectedResident.nama}</p>
                                            <p className="text-[10px] font-bold opacity-80 mt-1 uppercase tracking-widest">NIK: {selectedResident.nik}</p>
                                        </div>
                                    </div>
                                    <button 
                                        type="button" 
                                        onClick={clearResident}
                                        className="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors"
                                    >
                                        <XCircle className="w-4 h-4" />
                                    </button>
                                </div>
                            )}
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Nama Lengkap</label>
                                <input 
                                    type="text" 
                                    value={data.nama}
                                    onChange={e => setData('nama', e.target.value)}
                                    className="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl text-xs font-bold transition-all"
                                    placeholder="Masukkan nama lengkap..."
                                />
                                {errors.nama && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.nama}</p>}
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">NIK (KTP)</label>
                                <input 
                                    type="text" 
                                    value={data.nik}
                                    onChange={e => setData('nik', e.target.value)}
                                    className="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl text-xs font-bold transition-all"
                                    placeholder="16 digit NIK..."
                                    maxLength="16"
                                />
                                {errors.nik && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.nik}</p>}
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">No. WhatsApp/HP</label>
                                <input 
                                    type="text" 
                                    value={data.no_hp}
                                    onChange={e => setData('no_hp', e.target.value)}
                                    className="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl text-xs font-bold transition-all"
                                    placeholder="0812..."
                                />
                                {errors.no_hp && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.no_hp}</p>}
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Email</label>
                                <input 
                                    type="email" 
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    className="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl text-xs font-bold transition-all"
                                    placeholder="alamat@email.com"
                                />
                                {errors.email && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.email}</p>}
                            </div>
                            <div className="md:col-span-2 space-y-2">
                                <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Alamat Lengkap</label>
                                <textarea 
                                    value={data.alamat}
                                    onChange={e => setData('alamat', e.target.value)}
                                    className="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl text-xs font-bold transition-all min-h-[100px]"
                                    placeholder="Alamat domisili..."
                                />
                                {errors.alamat && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.alamat}</p>}
                            </div>
                        </div>
                    </div>

                    {/* Seksi Jabatan & Wilayah */}
                    <div className="bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-sm">
                        <h4 className="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2 italic">
                            <Shield className="w-4 h-4 text-emerald-600" />
                            Jabatan & Struktur Wilayah
                        </h4>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Jabatan Resmi</label>
                                <input 
                                    type="text" 
                                    value={data.jabatan}
                                    onChange={e => setData('jabatan', e.target.value)}
                                    className="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 rounded-xl text-xs font-bold transition-all"
                                    placeholder="Contoh: Kasi Pemerintahan"
                                />
                                {errors.jabatan && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.jabatan}</p>}
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Kategori Posisi</label>
                                <select 
                                    value={data.kategori}
                                    onChange={e => setData('kategori', e.target.value)}
                                    className="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 rounded-xl text-xs font-bold transition-all appearance-none"
                                >
                                    <option value="">PILIH KATEGORI...</option>
                                    {kategoriOptions.map(opt => (
                                        <option key={opt.value} value={opt.value}>{opt.label.toUpperCase()}</option>
                                    ))}
                                </select>
                                {errors.kategori && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.kategori}</p>}
                            </div>
                            
                            <div className="md:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-5 pt-2 border-t border-gray-50">
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1 flex items-center gap-1">
                                        Urutan (Prioritas)
                                    </label>
                                    <input 
                                        type="number"
                                        value={data.urutan}
                                        onChange={e => setData('urutan', parseInt(e.target.value) || 0)}
                                        className="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 rounded-xl text-xs font-bold transition-all"
                                        placeholder="0"
                                        min="0"
                                    />
                                    {errors.urutan && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.urutan}</p>}
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1 flex items-center gap-1">
                                        <MapPin className="w-3 h-3" /> RW
                                    </label>
                                    <select 
                                        value={data.rw_id}
                                        onChange={e => setData({ ...data, rw_id: e.target.value, rt_id: '' })}
                                        className="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 rounded-xl text-xs font-bold transition-all appearance-none"
                                    >
                                        <option value="">PILIH RW...</option>
                                        {masterRwOptions.map(rw => (
                                            <option key={rw.id} value={rw.id}>RW {rw.kode} - {rw.nama}</option>
                                        ))}
                                    </select>
                                    {errors.rw_id && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.rw_id}</p>}
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1 flex items-center gap-1">
                                        <MapPin className="w-3 h-3" /> RT
                                    </label>
                                    <select 
                                        value={data.rt_id}
                                        onChange={e => setData('rt_id', e.target.value)}
                                        className="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 rounded-xl text-xs font-bold transition-all appearance-none"
                                        disabled={!data.rw_id}
                                    >
                                        <option value="">PILIH RT...</option>
                                        {availableRts.map(rt => (
                                            <option key={rt.id} value={rt.id}>RT {rt.kode}</option>
                                        ))}
                                    </select>
                                    {errors.rt_id && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.rt_id}</p>}
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Urutan Tampil</label>
                                    <input 
                                        type="number" 
                                        value={data.urutan}
                                        onChange={e => setData('urutan', e.target.value)}
                                        className="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 rounded-xl text-xs font-bold transition-all"
                                        placeholder="0"
                                    />
                                    {errors.urutan && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.urutan}</p>}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Seksi Masa Jabatan & Wewenang */}
                    <div className="bg-white p-6 sm:p-8 rounded-2xl border border-gray-100 shadow-sm">
                        <h4 className="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2 italic">
                            <Calendar className="w-4 h-4 text-amber-600" />
                            Masa Jabatan & Wewenang
                        </h4>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Tanggal Pengangkatan</label>
                                <input 
                                    type="date" 
                                    value={data.tanggal_pengangkatan}
                                    onChange={e => setData('tanggal_pengangkatan', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 rounded-xl text-xs font-bold transition-all"
                                />
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Tanggal Berakhir</label>
                                <input 
                                    type="date" 
                                    value={data.tanggal_berakhir}
                                    onChange={e => setData('tanggal_berakhir', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 rounded-xl text-xs font-bold transition-all"
                                />
                                {errors.tanggal_berakhir && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.tanggal_berakhir}</p>}
                            </div>
                            <div className="md:col-span-2 space-y-2 pt-2 border-t border-gray-50">
                                <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1 flex items-center gap-2">
                                    <FileText className="w-3 h-3" /> Tugas & Wewenang
                                </label>
                                <textarea 
                                    value={data.tugas_wewenang}
                                    onChange={e => setData('tugas_wewenang', e.target.value)}
                                    className="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 rounded-xl text-xs font-bold transition-all min-h-[150px]"
                                    placeholder="Rincian tugas dan wewenang perangkat..."
                                />
                                {errors.tugas_wewenang && <p className="text-red-500 text-[10px] font-bold uppercase">{errors.tugas_wewenang}</p>}
                            </div>
                        </div>
                    </div>

                    {/* Tombol Aksi */}
                    <div className="flex items-center justify-end gap-3 pt-4">
                        <button 
                            type="button" 
                            onClick={() => window.history.back()}
                            className="px-6 py-3 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2"
                        >
                            <ArrowLeft className="w-3.5 h-3.5" />
                            BATALKAN
                        </button>
                        <button 
                            type="submit" 
                            disabled={processing}
                            className="px-8 py-3 bg-green-600 text-white hover:bg-green-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-green-200 flex items-center gap-2 disabled:opacity-50"
                        >
                            {processing ? (
                                <>
                                    <div className="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                    MENYIMPAN...
                                </>
                            ) : (
                                <>
                                    <Save className="w-3.5 h-3.5" />
                                    SIMPAN PERANGKAT
                                </>
                            )}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    );
}
