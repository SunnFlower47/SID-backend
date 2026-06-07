import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { Save, Split, Users, User, CheckCircle2, AlertCircle, Info, Home, Navigation, Globe, CheckSquare, Square, ChevronRight } from 'lucide-react';
import ResidentAutocomplete from '../../Shared/ResidentAutocomplete';
import KKAutocomplete from '../../Shared/KKAutocomplete';
import WilayahSelect from '../../Shared/WilayahSelect';
import axios from 'axios';
import Swal from 'sweetalert2';

function cn(...classes) {
  return classes.filter(Boolean).join(' ');
}

const KEDUDUKAN_OPTIONS = [
    'KEPALA KELUARGA',
    'ISTRI',
    'ANAK',
    'MENANTU',
    'CUCU',
    'ORANG TUA',
    'MERTUA',
    'FAMILI LAIN',
    'PEMBANTU',
    'LAINNYA'
];

export default function PisahKKForm({ wilayahTree, mutasi = null }) {
  const isEdit = !!mutasi;
  const snapshot = mutasi?.data_snapshot || {};
  
  const [selectedResident, setSelectedResident] = useState(mutasi?.penduduk || null);
  const [familyMembers, setFamilyMembers] = useState([]);
  const [loadingMembers, setLoadingMembers] = useState(false);
  const [nkkStatus, setNkkStatus] = useState({ status: 'default', message: '' });
  
  // Parse address parts for edit mode if outside village
  const parseAddress = (addr) => {
    if (!addr) return { jalan: '', rt: '', rw: '', desa: '', kecamatan: '', kabupaten: '', provinsi: '' };
    const parts = addr.split(', ').map(p => p.trim());
    return {
        jalan: parts[0] || '',
        rt: parts[1]?.match(/RT (\d+)/)?.[1] || '',
        rw: parts[1]?.match(/RW (\d+)/)?.[1] || '',
        desa: parts[2]?.replace('Desa ', '') || '',
        kecamatan: parts[3]?.replace('Kec. ', '') || '',
        kabupaten: parts[4]?.replace('Kab. ', '') || '',
        provinsi: parts[5]?.replace('Prov. ', '') || '',
    };
  };

  const [addressDetails, setAddressDetails] = useState(isEdit && mutasi.kategori_mutasi !== 'dalam_desa' 
    ? parseAddress(mutasi.asal_tujuan) 
    : {
        jalan: '',
        rt: '',
        rw: '',
        desa: '',
        kecamatan: '',
        kabupaten: '',
        provinsi: '',
    }
  );

  const { data, setData, post, put, processing, errors, clearErrors } = useForm({
    penduduk_id: mutasi?.penduduk_id || '',
    kategori_mutasi: mutasi?.kategori_mutasi || 'dalam_desa',
    kk_option: snapshot.kk_option || 'new',
    nkk_existing: snapshot.nkk_existing || '',
    nkk_existing_id: snapshot.nkk_existing || '',
    nkk_baru: snapshot.nkk_baru || '',
    nkk_tujuan: snapshot.nkk_tujuan || '',
    alamat: mutasi?.asal_tujuan || '',
    rt_id: mutasi?.rt_id || '',
    rw_id: mutasi?.rw_id || '',
    dusun_id: mutasi?.dusun_id || '',
    kedudukan_keluarga_pisah: snapshot.kedudukan_baru_kepala || 'KEPALA KELUARGA',
    status_perkawinan_pisah: snapshot.status_perkawinan_baru_kepala || '',
    tanggal_mutasi: mutasi?.tanggal_mutasi ? mutasi.tanggal_mutasi.substring(0, 10) : new Date().toISOString().split('T')[0],
    alasan: mutasi?.alasan || 'Pisah KK Baru',
    jenis_mutasi: 'pisah_kk',
    anggota_pisah_data: snapshot.anggota_pindah || [] 
  });

  // 1. Build Address String for outside moves
  useEffect(() => {
    if (data.kategori_mutasi === 'dalam_desa' && data.kk_option === 'new') return;

    const parts = [];
    if (data.kategori_mutasi !== 'dalam_desa') {
      if (addressDetails.jalan) parts.push(addressDetails.jalan);
      if (addressDetails.rt || addressDetails.rw) parts.push(`RT ${addressDetails.rt}/RW ${addressDetails.rw}`);
      if (addressDetails.desa) parts.push(`Desa ${addressDetails.desa}`);
      if (addressDetails.kecamatan) parts.push(`Kec. ${addressDetails.kecamatan}`);
      if (addressDetails.kabupaten) parts.push(`Kab. ${addressDetails.kabupaten}`);
      if (addressDetails.provinsi) parts.push(`Prov. ${addressDetails.provinsi}`);
    }
    
    if (parts.length > 0) {
        setData('alamat', parts.filter(Boolean).join(', '));
    }
  }, [addressDetails, data.kategori_mutasi, data.kk_option]);

  // 2. Real-time NKK Check for new KK
  useEffect(() => {
    const checkNKK = async () => {
      if (data.nkk_baru.length === 16 && data.kk_option === 'new') {
        setNkkStatus({ status: 'loading', message: 'Mengecek...' });
        try {
          const res = await axios.get('/mutasi/check-nkk', { params: { nkk: data.nkk_baru } });
          if (res.data.exists) {
            setNkkStatus({ status: 'error', message: 'NKK sudah terdaftar' });
          } else {
            setNkkStatus({ status: 'valid', message: 'NKK dapat digunakan' });
          }
        } catch (error) {
          setNkkStatus({ status: 'error', message: 'Gagal verifikasi' });
        }
      } else {
        setNkkStatus({ status: 'default', message: '' });
      }
    };
    const timer = setTimeout(checkNKK, 500);
    return () => clearTimeout(timer);
  }, [data.nkk_baru, data.kk_option]);

  const handleResidentSelect = async (resident) => {
    setSelectedResident(resident);
    setData(prev => ({
        ...prev,
        penduduk_id: resident?.id || '',
        alamat: resident?.alamat || '',
        anggota_pisah_data: isEdit ? prev.anggota_pisah_data : [],
        status_perkawinan_pisah: resident?.status_perkawinan || 'KAWIN'
    }));
    if (!isEdit) setFamilyMembers([]);
    clearErrors();

    if (resident?.id) {
      setLoadingMembers(true);
      try {
        const nkk = resident.nkk || resident.kartu_keluarga?.nkk;
        const response = await axios.get('/mutasi/get-anggota-keluarga', { 
          params: { nkk, exclude_id: resident.id } 
        });
        setFamilyMembers(response.data);
      } catch (error) {
        console.error('Error fetching family members', error);
      } finally {
        setLoadingMembers(false);
      }
    }
  };

  // Fetch family members on edit mode init
  useEffect(() => {
    if (isEdit && selectedResident?.id && familyMembers.length === 0) {
        handleResidentSelect(selectedResident);
    }
  }, [isEdit, selectedResident]);

  const toggleMember = (member) => {
    const current = [...data.anggota_pisah_data];
    const index = current.findIndex(m => m.id === member.id);
    
    if (index > -1) {
      setData('anggota_pisah_data', current.filter(m => m.id !== member.id));
    } else {
      setData('anggota_pisah_data', [...current, { 
        id: member.id, 
        nama: member.nama, 
        nik: member.nik, 
        kedudukan_keluarga: member.kedudukan_keluarga 
      }]);
    }
  };

  const updateMemberRole = (id, role) => {
    setData('anggota_pisah_data', data.anggota_pisah_data.map(m => 
      m.id === id ? { ...m, kedudukan_keluarga: role } : m
    ));
  };

  const toggleSelectAll = () => {
    if (data.anggota_pisah_data.length === familyMembers.length) {
      setData('anggota_pisah_data', []);
    } else {
      setData('anggota_pisah_data', familyMembers.map(m => ({
        id: m.id,
        nama: m.nama,
        nik: m.nik,
        kedudukan_keluarga: m.kedudukan_keluarga
      })));
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!data.penduduk_id) {
      Swal.fire({ icon: 'error', title: 'Oops...', text: 'Pilih Warga yang akan dipisah KK-nya!' });
      return;
    }

    Swal.fire({
      title: isEdit ? 'Update Data Pisah KK?' : 'Konfirmasi Pisah KK',
      text: isEdit 
        ? `Simpan perubahan data pisah KK untuk ${selectedResident?.nama}?`
        : `Proses pemisahan KK untuk ${selectedResident?.nama} beserta ${data.anggota_pisah_data.length} anggota keluarga?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#059669',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: isEdit ? 'Ya, Simpan!' : 'Ya, Proses Sekarang!'
    }).then((result) => {
      if (result.isConfirmed) {
        const options = {
          onSuccess: () => {
              // Ditangani oleh flash message global di AuthenticatedLayout
          },
          onError: (errs) => Swal.fire('Gagal!', Object.values(errs)[0] || 'Terjadi kesalahan.', 'error')
        };

        if (isEdit) {
            put(route('mutasi.data.update', mutasi.id), options);
        } else {
            post(route('mutasi.data.store'), options);
        }
      }
    });
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-10 animate-in slide-in-from-bottom-4 duration-500">
      
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div className="space-y-4">
          <h4 className="text-sm font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
            <User className="w-4 h-4 text-emerald-500" />
            Warga Utama
          </h4>
          {isEdit ? (
            <div className="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4">
              <div className="w-12 h-12 bg-white rounded-xl flex items-center justify-center border border-emerald-100 shadow-sm text-emerald-600 font-black">
                KK
              </div>
              <div>
                <p className="text-sm font-bold text-gray-900">{selectedResident?.nama}</p>
                <p className="text-[10px] font-mono text-gray-400">{selectedResident?.nik}</p>
              </div>
            </div>
          ) : (
            <ResidentAutocomplete 
              onSelect={handleResidentSelect} 
              placeholder="Cari warga (NIK/Nama)..."
              className="[&>div>input]:focus:ring-emerald-500/10 [&>div>input]:focus:border-emerald-500"
            />
          )}
          {selectedResident && (
             <div className="p-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl flex gap-3 items-start animate-in fade-in zoom-in-95">
                <div className="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-900/20">
                    <User className="w-5 h-5 text-white" />
                </div>
                <div>
                    <p className="text-xs font-black text-emerald-900 uppercase tracking-widest">{selectedResident.nama}</p>
                    <p className="text-[10px] font-bold text-emerald-600 mt-0.5">{selectedResident.nik} • {selectedResident.kedudukan_keluarga}</p>
                </div>
             </div>
          )}
        </div>

        <div className="space-y-4">
          <div className="flex items-center justify-between">
            <h4 className="text-sm font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
              <Users className="w-4 h-4 text-blue-500" />
              Pilih Anggota Keluarga yang Ikut
            </h4>
            {familyMembers.length > 0 && (
              <button 
                type="button" onClick={toggleSelectAll}
                className="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline"
              >
                {data.anggota_pisah_data.length === familyMembers.length ? 'Batal Semua' : 'Pilih Semua'}
              </button>
            )}
          </div>
          
          <div className="bg-white border border-gray-100 rounded-[24px] min-h-[120px] max-h-[220px] overflow-y-auto shadow-sm">
            {loadingMembers ? (
              <div className="p-8 text-center text-[11px] text-gray-400 font-bold animate-pulse">Memuat data keluarga...</div>
            ) : familyMembers.length > 0 ? (
              <div className="divide-y divide-gray-50">
                {familyMembers.map((member) => {
                    const isSelected = data.anggota_pisah_data.some(m => m.id === member.id);
                    return (
                        <div 
                            key={member.id} 
                            onClick={() => toggleMember(member)}
                            className={cn(
                                "p-4 flex items-center justify-between hover:bg-gray-50 cursor-pointer transition-colors group",
                                isSelected && "bg-emerald-50/30"
                            )}
                        >
                            <div className="flex flex-col">
                            <span className={cn("text-sm font-bold text-gray-900 group-hover:text-emerald-600 transition-colors", isSelected && "text-emerald-700")}>{member.nama}</span>
                            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">{member.nik} • {member.kedudukan_keluarga}</span>
                            </div>
                            {isSelected ? (
                            <CheckSquare className="w-5 h-5 text-emerald-600" />
                            ) : (
                            <Square className="w-5 h-5 text-gray-200" />
                            )}
                        </div>
                    )
                })}
              </div>
            ) : (
              <div className="p-8 text-center text-[11px] text-gray-400 font-bold italic">
                {selectedResident ? 'Tidak ada anggota keluarga lain.' : 'Pilih warga terlebih dahulu.'}
              </div>
            )}
          </div>
        </div>
      </div>

      {(selectedResident || data.anggota_pisah_data.length > 0) && (
        <div className="p-8 bg-white border border-gray-100 rounded-3xl shadow-sm space-y-8 animate-in slide-in-from-top-4 duration-500">
           <h4 className="text-xs font-black text-gray-900 uppercase tracking-widest flex items-center gap-2 border-b border-gray-50 pb-4">
            <Users className="w-4 h-4 text-emerald-500" />
            Penyesuaian Status di KK Baru
          </h4>
          
          <div className="space-y-4">
             {/* Main Resident Adjustment */}
             {selectedResident && (
                <div className="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-emerald-50/20 rounded-2xl border border-emerald-100">
                    <div className="flex-1">
                        <p className="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Pimpinan Mutasi (Kepala)</p>
                        <p className="text-sm font-bold text-gray-900">{selectedResident.nama}</p>
                    </div>
                    <div className="flex gap-4">
                        <div className="space-y-1">
                            <label className="text-[9px] font-black text-gray-400 uppercase ml-1">Status di KK Baru</label>
                            <select
                                className="px-4 py-2 bg-white border border-gray-100 rounded-xl text-xs font-bold outline-none focus:border-emerald-500"
                                value={data.kedudukan_keluarga_pisah}
                                onChange={(e) => setData('kedudukan_keluarga_pisah', e.target.value)}
                            >
                                {KEDUDUKAN_OPTIONS.map(opt => <option key={opt} value={opt}>{opt}</option>)}
                            </select>
                        </div>
                        <div className="space-y-1">
                            <label className="text-[9px] font-black text-gray-400 uppercase ml-1">Status Perkawinan</label>
                            <select
                                className="px-4 py-2 bg-white border border-gray-100 rounded-xl text-xs font-bold outline-none focus:border-emerald-500"
                                value={data.status_perkawinan_pisah}
                                onChange={(e) => setData('status_perkawinan_pisah', e.target.value)}
                            >
                                <option value="BELUM KAWIN">BELUM KAWIN</option>
                                <option value="KAWIN TERCATAT">KAWIN TERCATAT</option>
                                <option value="KAWIN BELUM TERCATAT">KAWIN BELUM TERCATAT</option>
                                <option value="CERAI HIDUP TERCATAT">CERAI HIDUP TERCATAT</option>
                                <option value="CERAI HIDUP BELUM TERCATAT">CERAI HIDUP BELUM TERCATAT</option>
                                <option value="CERAI MATI">CERAI MATI</option>
                            </select>
                        </div>
                    </div>
                </div>
             )}

             {/* Following Members Adjustment */}
             {data.anggota_pisah_data.map((member) => (
                <div key={member.id} className="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50/50 rounded-2xl border border-gray-100 animate-in slide-in-from-left-2">
                    <div className="flex-1">
                        <p className="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Anggota Ikut</p>
                        <p className="text-sm font-bold text-gray-900">{member.nama}</p>
                    </div>
                    <div className="space-y-1">
                        <label className="text-[9px] font-black text-gray-400 uppercase ml-1">Status di KK Baru</label>
                        <select
                            className="px-4 py-2 bg-white border border-gray-100 rounded-xl text-xs font-bold outline-none focus:border-emerald-500"
                            value={member.kedudukan_keluarga}
                            onChange={(e) => updateMemberRole(member.id, e.target.value)}
                        >
                            {KEDUDUKAN_OPTIONS.map(opt => <option key={opt} value={opt}>{opt}</option>)}
                        </select>
                    </div>
                </div>
             ))}

             {data.anggota_pisah_data.length === 0 && !selectedResident && (
                <p className="text-center py-8 text-[11px] text-gray-400 font-bold italic">Belum ada warga yang dipilih.</p>
             )}
          </div>
        </div>
      )}

      <div className="p-8 bg-gray-50 border border-gray-100 rounded-3xl space-y-8 shadow-sm">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <h4 className="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
            <Home className="w-4 h-4" />
            Pengaturan Kartu Keluarga Baru
          </h4>
          <div className="flex bg-white p-1 rounded-xl border border-gray-100">
            {[
              { id: 'dalam_desa', label: 'Dalam Desa', icon: Home },
              { id: 'dalam_kota', label: 'Dalam Kota', icon: Navigation },
              { id: 'luar_kota', label: 'Luar Kota', icon: Navigation },
            ].map((cat) => (
              <button
                key={cat.id} type="button"
                onClick={() => setData('kategori_mutasi', cat.id)}
                className={cn(
                  "px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2",
                  data.kategori_mutasi === cat.id ? "bg-emerald-600 text-white shadow-lg shadow-emerald-900/20" : "text-gray-400 hover:text-gray-600"
                )}
              >
                <cat.icon className="w-3 h-3" />
                {cat.label}
              </button>
            ))}
          </div>
        </div>

        {data.kategori_mutasi === 'dalam_desa' && (
          <div className="flex bg-white/50 p-1.5 rounded-2xl border border-gray-100 w-fit animate-in fade-in duration-300">
            <button
              type="button"
              onClick={() => setData('kk_option', 'new')}
              className={cn(
                "px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all",
                data.kk_option === 'new' ? "bg-emerald-600 text-white shadow-md" : "text-gray-400 hover:text-gray-600"
              )}
            >
              Buat KK Baru
            </button>
            <button
              type="button"
              onClick={() => setData('kk_option', 'existing')}
              className={cn(
                "px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all",
                data.kk_option === 'existing' ? "bg-emerald-600 text-white shadow-md" : "text-gray-400 hover:text-gray-600"
              )}
            >
              Gabung KK Lain
            </button>
          </div>
        )}

        <div className="space-y-6">
          {data.kategori_mutasi === 'dalam_desa' ? (
            data.kk_option === 'new' ? (
              <div className="space-y-6 animate-in slide-in-from-top-2">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div className="space-y-2">
                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nomor KK Baru<span className="text-red-500 ml-0.5">*</span></label>
                    <input 
                      type="text" required maxLength={16}
                      placeholder="Masukkan 16 digit No KK..."
                      className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-emerald-500"
                      value={data.nkk_baru}
                      onChange={(e) => setData('nkk_baru', e.target.value.replace(/\D/g, ''))}
                    />
                    <div className="min-h-[16px] px-1">
                      {nkkStatus.status === 'loading' && (
                        <div className="flex items-center gap-1.5 animate-pulse text-blue-500">
                          <div className="w-2 h-2 border-2 border-current border-t-transparent rounded-full animate-spin" />
                          <span className="font-bold leading-none" style={{ fontSize: '8px' }}>Mengecek...</span>
                        </div>
                      )}
                      {nkkStatus.status === 'valid' && (
                        <div className="flex items-center gap-1 text-green-600 animate-in fade-in">
                          <CheckCircle2 className="w-2 h-2" />
                          <span className="font-bold leading-none" style={{ fontSize: '8px' }}>{nkkStatus.message}</span>
                        </div>
                      )}
                      {nkkStatus.status === 'error' && (
                        <div className="flex items-center gap-1 text-red-500 animate-in fade-in">
                          <AlertCircle className="w-2 h-2" />
                          <span className="font-bold leading-none" style={{ fontSize: '8px' }}>{nkkStatus.message}</span>
                        </div>
                      )}
                    </div>
                  </div>
                  <div className="space-y-2">
                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Tinggal Baru<span className="text-red-500 ml-0.5">*</span></label>
                    <input 
                      type="text" required
                      className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-emerald-500"
                      value={data.alamat}
                      onChange={(e) => setData('alamat', e.target.value)}
                    />
                  </div>
                </div>
                
                <div className="space-y-4">
                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Lokasi Wilayah Baru</label>
                    <WilayahSelect 
                        wilayahTree={wilayahTree}
                        selectedDusun={data.dusun_id}
                        selectedRw={data.rw_id}
                        selectedRt={data.rt_id}
                        onChange={(f, v) => setData(f, v)}
                    />
                </div>
              </div>
            ) : (
              <div className="space-y-4 animate-in slide-in-from-top-2">
                <KKAutocomplete 
                    onSelect={(kk) => setData(prev => ({...prev, nkk_existing: kk?.nkk || '', nkk_existing_id: kk?.nkk || ''}))} 
                    placeholder="Cari NKK atau Nama Kepala Keluarga Tujuan..." 
                    className="[&>div>input]:focus:ring-emerald-500/10 [&>div>input]:focus:border-emerald-500"
                />
                <div className="p-4 bg-blue-50/50 border border-blue-100 rounded-2xl flex gap-3 items-start">
                  <AlertCircle className="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
                  <p className="text-[11px] text-blue-700 font-medium leading-relaxed">
                    Warga akan digabungkan ke KK tujuan tersebut. Alamat dan Wilayah akan otomatis mengikuti KK yang dipilih.
                  </p>
                </div>
              </div>
            )
          ) : (
            <div className="space-y-6 animate-in fade-in slide-in-from-top-2">
                <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div className="md:col-span-2 space-y-2">
                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jalan / Gang / No. Rumah</label>
                    <input 
                    type="text" placeholder="Contoh: Jl. Sudirman No. 123"
                    className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-emerald-500"
                    value={addressDetails.jalan}
                    onChange={(e) => setAddressDetails({ ...addressDetails, jalan: e.target.value.toUpperCase() })}
                    />
                </div>
                <div className="space-y-2">
                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-center block">RT</label>
                    <input 
                    type="text" maxLength={3} placeholder="000"
                    className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold text-center outline-none focus:border-emerald-500"
                    value={addressDetails.rt}
                    onChange={(e) => setAddressDetails({ ...addressDetails, rt: e.target.value.replace(/\D/g, '') })}
                    />
                </div>
                <div className="space-y-2">
                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-center block">RW</label>
                    <input 
                    type="text" maxLength={3} placeholder="000"
                    className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold text-center outline-none focus:border-emerald-500"
                    value={addressDetails.rw}
                    onChange={(e) => setAddressDetails({ ...addressDetails, rw: e.target.value.replace(/\D/g, '') })}
                    />
                </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div className="space-y-2">
                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Desa/Kelurahan</label>
                    <input 
                    type="text" className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-emerald-500"
                    value={addressDetails.desa}
                    onChange={(e) => setAddressDetails({ ...addressDetails, desa: e.target.value.toUpperCase() })}
                    />
                </div>
                <div className="space-y-2">
                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kecamatan</label>
                    <input 
                    type="text" className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-emerald-500"
                    value={addressDetails.kecamatan}
                    onChange={(e) => setAddressDetails({ ...addressDetails, kecamatan: e.target.value.toUpperCase() })}
                    />
                </div>
                <div className="space-y-2">
                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kabupaten/Kota</label>
                    <input 
                    type="text" className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-emerald-500"
                    value={addressDetails.kabupaten}
                    onChange={(e) => setAddressDetails({ ...addressDetails, kabupaten: e.target.value.toUpperCase() })}
                    />
                </div>
                <div className="space-y-2">
                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Provinsi</label>
                    <input 
                    type="text" className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-emerald-500"
                    value={addressDetails.provinsi}
                    onChange={(e) => setAddressDetails({ ...addressDetails, provinsi: e.target.value.toUpperCase() })}
                    />
                </div>
                </div>

              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">No KK Tujuan di Luar Wilayah</label>
                <input 
                  type="text" maxLength={16} placeholder="Masukkan No KK tujuan di wilayah baru"
                  className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-emerald-500"
                  value={data.nkk_tujuan}
                  onChange={(e) => setData('nkk_tujuan', e.target.value.replace(/\D/g, ''))}
                />
              </div>

              <div className="p-5 bg-emerald-50/50 rounded-2xl border border-dashed border-emerald-200">
                <span className="text-[10px] font-black text-emerald-400 uppercase tracking-widest block mb-2">Preview Alamat Luar Wilayah:</span>
                <p className="text-xs font-bold text-emerald-900 italic">
                  {data.alamat || "Lengkapi detail alamat di atas..."}
                </p>
              </div>
            </div>
          )}
        </div>
      </div>

      <div className="p-8 bg-white border border-gray-100 rounded-3xl shadow-sm space-y-4">
        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alasan Pisah KK<span className="text-red-500 ml-0.5">*</span></label>
        <input 
          type="text" required placeholder="Contoh: Membentuk Rumah Tangga Baru, Pindah Domisili, dll..."
          className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:bg-white focus:border-emerald-500 transition-all"
          value={data.alasan}
          onChange={(e) => setData('alasan', e.target.value)}
        />
      </div>

      <div className="pt-4 flex items-center justify-end">
        <button
          type="submit" disabled={processing || !selectedResident}
          className="px-10 py-3.5 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white rounded-xl text-[11px] font-bold uppercase tracking-widest hover:scale-[1.02] transition-all shadow-xl shadow-emerald-200 flex items-center gap-2 active:scale-95 disabled:opacity-50"
        >
          {processing ? (
            <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
          ) : <Save className="w-4 h-4" />}
          SIMPAN DATA PISAH KK
        </button>
      </div>
    </form>
  );
}
