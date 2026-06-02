import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { Save, MapPin, User, FileText, Info, Users, Home, Plus, Trash2, CheckCircle2, AlertCircle } from 'lucide-react';
import KKAutocomplete from '../../Shared/KKAutocomplete';
import WilayahSelect from '../../Shared/WilayahSelect';
import axios from 'axios';
import Swal from 'sweetalert2';

function cn(...classes) {
  return classes.filter(Boolean).join(' ');
}

const MemberNIKInput = ({ value, onChange, error }) => {
  const [status, setStatus] = useState({ type: 'default', message: '' });

  useEffect(() => {
    const checkNik = async () => {
      if (value.length === 16) {
        setStatus({ type: 'loading', message: '' });
        try {
          const res = await axios.get('/penduduk/check-nik', { params: { nik: value } });
          if (res.data.exists) {
            setStatus({ type: 'error', message: 'NIK sudah terdaftar di sistem' });
          } else {
            setStatus({ type: 'valid', message: 'NIK dapat digunakan' });
          }
        } catch (err) {
          setStatus({ type: 'error', message: 'Gagal verifikasi NIK' });
        }
      } else if (value.length > 0) {
        setStatus({ type: 'error', message: 'NIK harus 16 digit' });
      } else {
        setStatus({ type: 'default', message: '' });
      }
    };

    const timer = setTimeout(checkNik, 500);
    return () => clearTimeout(timer);
  }, [value]);

  return (
    <div className="space-y-1 relative">
      <label className="text-[9px] font-black text-gray-400 uppercase ml-1">NIK Anggota</label>
      <input 
        type="text" required maxLength={16} placeholder="16 digit NIK"
        className={cn(
          "w-full px-3 py-2 bg-gray-50 border rounded-xl text-xs font-bold outline-none transition-all",
          status.type === 'error' ? "border-red-300 focus:border-red-500" : 
          status.type === 'valid' ? "border-green-500 focus:border-green-500" : 
          "border-gray-100 focus:border-blue-500"
        )}
        value={value} onChange={(e) => onChange(e.target.value.replace(/\D/g, ''))}
      />
      
      <div className="min-h-[14px] flex items-center px-1">
          {status.type === 'loading' && (
              <div className="flex items-center gap-1.5 animate-pulse">
                  <div className="w-2 h-2 border border-blue-400 border-t-transparent rounded-full animate-spin" />
                  <span className="text-[8px] font-bold text-blue-400 uppercase tracking-tighter">Mengecek...</span>
              </div>
          )}
          {status.type === 'valid' && (
              <div className="flex items-center gap-1 text-green-600 animate-in fade-in duration-300">
                  <CheckCircle2 className="w-2 h-2" />
                  <span className="font-bold leading-none uppercase tracking-wider" style={{ fontSize: '8px' }}>{status.message}</span>
              </div>
          )}
          {(status.type === 'error' || error) && (
              <div className="flex items-center gap-1 text-red-500 animate-in fade-in duration-300">
                  <AlertCircle className="w-2 h-2" />
                  <span className="font-bold leading-none uppercase tracking-wider" style={{ fontSize: '8px' }}>{status.message || error}</span>
              </div>
          )}
      </div>
    </div>
  );
};

export default function PindahMasukForm({ wilayahTree, mutasi = null }) {
  const isEdit = !!mutasi;
  const resident = mutasi?.penduduk || {};
  const snapshot = mutasi?.data_snapshot || {};
  
  const [kkOption, setKkOption] = useState(snapshot.kk_option || 'new'); // 'new' or 'existing'
  const [selectedKK, setSelectedKK] = useState(isEdit && snapshot.kk_option === 'existing' ? { 
    nkk: resident.nkk,
    alamat: resident.alamat,
    rt_id: resident.rt_id,
    rw_id: resident.rw_id,
    dusun_id: resident.dusun_id
  } : null);

  const { data, setData, post, put, processing, errors, clearErrors } = useForm({
    nik: resident.nik || '',
    nama: resident.nama || '',
    jenis_kelamin: resident.jenis_kelamin || 'LAKI-LAKI',
    tempat_lahir: resident.tempat_lahir || '',
    tanggal_lahir: resident.tanggal_lahir || '',
    agama: resident.agama || 'ISLAM',
    status_perkawinan: resident.status_perkawinan || 'BELUM KAWIN',
    kedudukan_keluarga: resident.kedudukan_keluarga || 'KEPALA KELUARGA',
    pendidikan: resident.pendidikan || 'TIDAK / BELUM SEKOLAH',
    pekerjaan: resident.pekerjaan || 'BELUM / TIDAK BEKERJA',
    nama_ayah: resident.nama_ayah || '',
    nama_ibu: resident.nama_ibu || '',
    nkk: resident.nkk || '',
    nkk_new: snapshot.kk_option === 'new' ? resident.nkk : '',
    alamat: resident.alamat || '',
    rt_id: resident.rt_id || '',
    rw_id: resident.rw_id || '',
    dusun_id: resident.dusun_id || '',
    kategori_mutasi: mutasi?.kategori_mutasi || 'luar_kota',
    asal_tujuan: mutasi?.asal_tujuan || '',
    alasan: mutasi?.alasan || 'Pindah masuk',
    tanggal_mutasi: mutasi?.tanggal_mutasi ? new Date(mutasi.tanggal_mutasi).toISOString().split('T')[0] : new Date().toISOString().split('T')[0],
    jenis_mutasi: 'pindah_masuk',
    family_members: snapshot.family_members || [], // Batch input array
    golongan_darah: resident.golongan_darah || 'TIDAK TAHU',
    warganegara: resident.warganegara || 'WNI',
    no_akta_lahir: resident.no_akta_lahir || '',
    status_pendidikan: resident.status_pendidikan || 'Tamat Sekolah',
    telepon: resident.telepon || '',
    cacat_type: resident.cacat_type || '',
    sakit_menahun: resident.sakit_menahun || '',
    status_asuransi: resident.status_asuransi || 'TIDAK ADA'
  });

  const [nikStatus, setNikStatus] = useState({ status: 'default', message: '' }); // 'default', 'loading', 'valid', 'error'
  const [nkkStatus, setNkkStatus] = useState({ status: 'default', message: '', data: null });

  useEffect(() => {
    // Skip NIK check if it's the original NIK in edit mode
    if (isEdit && data.nik === resident.nik) {
      setNikStatus({ status: 'default', message: '' });
      return;
    }

    const checkNik = async () => {
      if (data.nik.length === 16) {
        setNikStatus({ status: 'loading', message: 'Memeriksa NIK...' });
        try {
          const res = await axios.get('/penduduk/check-nik', { params: { nik: data.nik } });
          if (res.data.exists) {
            setNikStatus({ status: 'error', message: 'NIK sudah terdaftar' });
          } else {
            setNikStatus({ status: 'valid', message: 'NIK tersedia' });
          }
        } catch (error) {
          setNikStatus({ status: 'error', message: 'Gagal verifikasi NIK' });
        }
      } else if (data.nik.length > 0) {
        setNikStatus({ status: 'error', message: 'NIK harus 16 digit' });
      } else {
        setNikStatus({ status: 'default', message: '' });
      }
    };

    const debounceTimer = setTimeout(checkNik, 500);
    return () => clearTimeout(debounceTimer);
  }, [data.nik]);

  useEffect(() => {
    // Skip NKK check if it's the original NKK in edit mode
    if (isEdit && kkOption === 'new' && data.nkk_new === resident.nkk) {
      setNkkStatus({ status: 'default', message: '', data: null });
      return;
    }

    const checkNkk = async () => {
      if (data.nkk_new.length === 16) {
        setNkkStatus({ status: 'loading', message: 'Mengecek...', data: null });
        try {
          const res = await axios.get(route('mutasi.check-nkk'), { params: { nkk: data.nkk_new } });
          if (res.data && res.data.length > 0) {
            const foundKk = res.data[0];
            setNkkStatus({ 
                status: 'error', 
                message: `NKK sudah ada atas nama ${foundKk.kepala_keluarga}!`,
                data: foundKk
            });
          } else {
            setNkkStatus({ status: 'valid', message: 'NKK dapat digunakan', data: null });
          }
        } catch (error) {
          setNkkStatus({ status: 'error', message: 'Gagal memeriksa NKK.', data: null });
        }
      } else if (data.nkk_new.length > 0) {
        setNkkStatus({ status: 'error', message: 'NKK harus 16 digit.', data: null });
      } else {
        setNkkStatus({ status: 'default', message: '', data: null });
      }
    };

    const timer = setTimeout(() => {
      checkNkk();
    }, 500);

    return () => clearTimeout(timer);
  }, [data.nkk_new]);

  const handleKKSelect = (kk) => {
    setSelectedKK(kk);
    clearErrors();
    if (kk) {
      setData(prev => ({ 
        ...prev, 
        nkk: kk.nkk,
        alamat: kk.alamat || '',
        rt_id: kk.rt_id || '',
        rw_id: kk.rw_id || '',
        dusun_id: kk.dusun_id || ''
      }));
    } else {
      setData(prev => ({ ...prev, nkk: '', alamat: '', rt_id: '', rw_id: '', dusun_id: '' }));
    }
  };

  const handleWilayahChange = (field, value) => {
    setData(field, value);
  };

  const addFamilyMember = () => {
    setData('family_members', [
      ...data.family_members,
      {
        nik: '',
        nama: '',
        jenis_kelamin: 'PEREMPUAN',
        kedudukan_keluarga: 'ISTRI',
        tempat_lahir: '',
        tanggal_lahir: '',
        agama: data.agama || 'ISLAM',
        status_perkawinan: 'KAWIN',
        pendidikan: 'TIDAK / BELUM SEKOLAH',
        pekerjaan: 'MENGURUS RUMAH TANGGA',
        nama_ayah: '',
        nama_ibu: '',
        golongan_darah: 'TIDAK TAHU',
        warganegara: 'WNI',
        no_akta_lahir: '',
        status_pendidikan: 'Tamat Sekolah',
        telepon: '',
        cacat_type: '',
        sakit_menahun: '',
        status_asuransi: 'TIDAK ADA'
      }
    ]);
  };

  const removeFamilyMember = (index) => {
    const updated = [...data.family_members];
    updated.splice(index, 1);
    setData('family_members', updated);
  };

  const updateFamilyMember = (index, field, value) => {
    const updated = [...data.family_members];
    updated[index][field] = value;
    setData('family_members', updated);
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (kkOption === 'existing' && !data.nkk) {
      Swal.fire({ icon: 'error', title: 'Oops...', text: 'Pilih KK tujuan terlebih dahulu!' });
      return;
    }
    if (kkOption === 'new' && !data.nkk_new) {
      Swal.fire({ icon: 'error', title: 'Oops...', text: 'Nomor KK Baru harus diisi!' });
      return;
    }

    if (nikStatus.status === 'error') {
      Swal.fire({ icon: 'error', title: 'NIK Tidak Valid', text: nikStatus.message });
      return;
    }

    if (kkOption === 'new' && nkkStatus.status === 'error') {
      Swal.fire({ icon: 'error', title: 'NKK Tidak Valid', text: nkkStatus.message });
      return;
    }

    const options = {
      onSuccess: () => {
        // Ditangani oleh flash message global di AuthenticatedLayout
      },
      onError: (errors) => {
        const firstError = Object.values(errors)[0];
        Swal.fire({
          icon: 'error',
          title: 'Validasi Gagal',
          text: firstError || 'Periksa kembali data yang diinput.',
          confirmButtonColor: '#ef4444'
        });
      }
    };

    if (isEdit) {
      put(route('mutasi.data.update', mutasi.id), options);
    } else {
      post(route('mutasi.data.store'), options);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-8 animate-in slide-in-from-bottom-4 duration-500">
      
      {/* 1. Konfigurasi KK */}
      <div className="p-8 bg-green-50/50 border border-green-100 rounded-3xl space-y-6">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <h4 className="text-xs font-black text-green-600 uppercase tracking-widest flex items-center gap-2">
            <Home className="w-4 h-4" />
            Konfigurasi Kartu Keluarga di Desa
          </h4>
          <div className="flex bg-white p-1.5 rounded-2xl border border-gray-100 shadow-sm">
            <button
              type="button"
              onClick={() => {
                setKkOption('new');
                setData(prev => ({...prev, nkk: ''}));
                setSelectedKK(null);
                clearErrors();
              }}
              className={`px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all ${
                kkOption === 'new' ? "bg-green-600 text-white shadow-lg shadow-green-900/10" : "text-gray-400 hover:text-gray-600"
              }`}
            >
              Buat KK Baru
            </button>
            <button
              type="button"
              onClick={() => {
                setKkOption('existing');
                setData(prev => ({...prev, nkk_new: ''}));
                clearErrors();
              }}
              className={`px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all ${
                kkOption === 'existing' ? "bg-green-600 text-white shadow-lg shadow-green-900/10" : "text-gray-400 hover:text-gray-600"
              }`}
            >
              Gabung KK Lain
            </button>
          </div>
        </div>

        {kkOption === 'new' ? (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 animate-in slide-in-from-top-4 duration-500">
            <div className="space-y-2">
              <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nomor KK Baru<span className="text-red-500 ml-0.5">*</span></label>
              <input 
                type="text" required maxLength={16}
                placeholder="Masukkan 16 digit No KK..."
                className={cn(
                  "w-full px-4 py-3.5 bg-white border rounded-2xl text-sm font-bold outline-none transition-all",
                  nkkStatus.status === 'error' ? "border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10" : 
                  nkkStatus.status === 'valid' ? "border-green-500 focus:border-green-500 focus:ring-4 focus:ring-green-500/10" : 
                  "border-gray-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                )}
                value={data.nkk_new}
                onChange={(e) => setData('nkk_new', e.target.value.replace(/\D/g, ''))}
              />
              
              <div className="min-h-[20px] px-2 flex items-center justify-between">
                  <div className="flex items-center gap-1.5">
                    {nkkStatus.status === 'loading' && (
                        <div className="flex items-center gap-2 animate-pulse text-blue-500">
                            <div className="w-2.5 h-2.5 border-2 border-current border-t-transparent rounded-full animate-spin" />
                            <span className="text-[8px] font-bold leading-none">Mengecek...</span>
                        </div>
                    )}
                    {nkkStatus.status === 'valid' && (
                        <div className="flex items-center gap-1.5 text-green-600 animate-in slide-in-from-left-2 duration-300">
                            <CheckCircle2 className="w-2.5 h-2.5" />
                            <span className="font-black uppercase tracking-widest leading-none" style={{ fontSize: '8px' }}>{nkkStatus.message}</span>
                        </div>
                    )}
                    {(nkkStatus.status === 'error' || errors.nkk_new) && (
                        <div className="flex items-center gap-1.5 text-red-500 animate-in slide-in-from-left-2 duration-300">
                            <AlertCircle className="w-2.5 h-2.5" />
                            <span className="font-black uppercase tracking-widest leading-none" style={{ fontSize: '8px' }}>{nkkStatus.message || errors.nkk_new}</span>
                        </div>
                    )}
                  </div>

                  {nkkStatus.data && (
                      <button 
                          type="button"
                          onClick={() => {
                              setKkOption('existing');
                              handleKKSelect(nkkStatus.data);
                              setNkkStatus({ status: 'default', message: '', data: null });
                          }}
                          className="px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-[9px] font-black uppercase tracking-widest transition-colors flex items-center gap-1"
                      >
                          <Plus className="w-3 h-3" />
                          Gabung KK ini saja?
                      </button>
                  )}
              </div>
            </div>
            <div className="p-4 bg-green-100/50 rounded-2xl flex gap-3 items-start">
              <Info className="w-5 h-5 text-green-600 shrink-0 mt-0.5" />
              <p className="text-xs text-green-700 font-medium leading-relaxed">
                Gunakan opsi ini jika warga tersebut adalah <strong>Kepala Keluarga</strong> yang baru datang ke desa.
              </p>
            </div>
          </div>
        ) : (
          <div className="space-y-4 animate-in slide-in-from-top-4 duration-500">
            <KKAutocomplete 
                label="Cari KK Tujuan (Existing)"
                onSelect={handleKKSelect} 
                placeholder="Cari NKK atau Nama Kepala Keluarga..." 
                initialSelected={selectedKK}
                className="[&>div>input]:focus:ring-green-500/10 [&>div>input]:focus:border-green-500"
            />
            {data.nkk && (
              <div className="p-4 bg-blue-50 border border-blue-100 rounded-2xl text-xs font-bold text-blue-600 shadow-sm">
                Terpilih NKK: {data.nkk}. Alamat & Wilayah akan mengikuti KK ini.
              </div>
            )}
            {errors.nkk && <p className="text-xs text-red-500">{errors.nkk}</p>}
          </div>
        )}
      </div>

      {/* 2. Data Personal (Warga Utama) */}
      <div className="p-8 bg-gray-50 border border-gray-100 rounded-3xl space-y-6">
        <h4 className="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
          <User className="w-4 h-4" />
          Data Warga Utama (Kepala Keluarga / Pemindah)
        </h4>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="space-y-2 relative">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">NIK<span className="text-red-500 ml-0.5">*</span></label>
            <input 
              type="text" required maxLength={16}
              className={cn(
                "w-full px-4 py-3.5 bg-white border rounded-2xl text-sm font-bold outline-none transition-all",
                nikStatus.status === 'error' ? "border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10" : 
                nikStatus.status === 'valid' ? "border-green-500 focus:border-green-500 focus:ring-4 focus:ring-green-500/10" : 
                "border-gray-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
              )}
              value={data.nik}
              onChange={(e) => setData('nik', e.target.value.replace(/\D/g, ''))}
              placeholder="16 Digit NIK"
            />
            
            <div className="min-h-[20px] px-2 flex items-center">
                {nikStatus.status === 'loading' && (
                    <div className="flex items-center gap-2 animate-pulse text-blue-500">
                        <div className="w-2.5 h-2.5 border-2 border-current border-t-transparent rounded-full animate-spin" />
                        <span className="text-[8px] font-bold leading-none">Memverifikasi...</span>
                    </div>
                )}
                {nikStatus.status === 'valid' && (
                    <div className="flex items-center gap-1 text-green-600 animate-in slide-in-from-left-2 duration-300">
                        <CheckCircle2 className="w-2.5 h-2.5" />
                        <span className="font-black uppercase tracking-widest leading-none" style={{ fontSize: '8px' }}>{nikStatus.message}</span>
                    </div>
                )}
                {(nikStatus.status === 'error' || errors.nik) && (
                    <div className="flex items-center gap-1 text-red-500 animate-in slide-in-from-left-2 duration-300">
                        <AlertCircle className="w-2.5 h-2.5" />
                        <span className="font-black uppercase tracking-widest leading-none" style={{ fontSize: '8px' }}>{nikStatus.message || errors.nik}</span>
                    </div>
                )}
            </div>
          </div>
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap<span className="text-red-500 ml-0.5">*</span></label>
            <input 
              type="text" required
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/10"
              value={data.nama}
              onChange={(e) => setData('nama', e.target.value.toUpperCase())}
            />
          </div>
          
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Kelamin</label>
            <select className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.jenis_kelamin} onChange={(e) => setData('jenis_kelamin', e.target.value)}>
              <option value="LAKI-LAKI">LAKI-LAKI</option>
              <option value="PEREMPUAN">PEREMPUAN</option>
            </select>
          </div>
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Hubungan Keluarga</label>
            <select className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.kedudukan_keluarga} onChange={(e) => setData('kedudukan_keluarga', e.target.value)}>
              <option value="KEPALA KELUARGA">KEPALA KELUARGA</option>
              <option value="ISTRI">ISTRI</option>
              <option value="ANAK">ANAK</option>
              <option value="CUCU">CUCU</option>
              <option value="ORANG TUA">ORANG TUA</option>
              <option value="MERTUA">MERTUA</option>
              <option value="LAINNYA">LAINNYA</option>
            </select>
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tempat Lahir<span className="text-red-500 ml-0.5">*</span></label>
            <input type="text" required className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.tempat_lahir} onChange={(e) => setData('tempat_lahir', e.target.value.toUpperCase())} />
          </div>
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Lahir<span className="text-red-500 ml-0.5">*</span></label>
            <input type="date" required className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.tanggal_lahir} onChange={(e) => setData('tanggal_lahir', e.target.value)} />
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Agama</label>
            <select className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.agama} onChange={(e) => setData('agama', e.target.value)}>
              <option value="ISLAM">ISLAM</option>
              <option value="KRISTEN">KRISTEN</option>
              <option value="KATHOLIK">KATHOLIK</option>
              <option value="HINDU">HINDU</option>
              <option value="BUDHA">BUDHA</option>
              <option value="KONGHUCU">KONGHUCU</option>
            </select>
          </div>
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Status Perkawinan</label>
            <select className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.status_perkawinan} onChange={(e) => setData('status_perkawinan', e.target.value)}>
              <option value="BELUM KAWIN">BELUM KAWIN</option>
              <option value="KAWIN">KAWIN</option>
              <option value="CERAI HIDUP">CERAI HIDUP</option>
              <option value="CERAI MATI">CERAI MATI</option>
            </select>
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pendidikan<span className="text-red-500 ml-0.5">*</span></label>
            <input type="text" placeholder="Contoh: SMA / S1" required className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.pendidikan} onChange={(e) => setData('pendidikan', e.target.value.toUpperCase())} />
          </div>
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pekerjaan<span className="text-red-500 ml-0.5">*</span></label>
            <input type="text" placeholder="Contoh: WIRASWASTA" required className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.pekerjaan} onChange={(e) => setData('pekerjaan', e.target.value.toUpperCase())} />
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Ayah<span className="text-red-500 ml-0.5">*</span></label>
            <input type="text" placeholder="Nama Ayah Kandung" required className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.nama_ayah} onChange={(e) => setData('nama_ayah', e.target.value.toUpperCase())} />
          </div>
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Ibu<span className="text-red-500 ml-0.5">*</span></label>
            <input type="text" placeholder="Nama Ibu Kandung" required className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.nama_ibu} onChange={(e) => setData('nama_ibu', e.target.value.toUpperCase())} />
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Golongan Darah</label>
            <select
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.golongan_darah}
              onChange={(e) => setData('golongan_darah', e.target.value)}
            >
              <option value="TIDAK TAHU">TIDAK TAHU</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="AB">AB</option>
              <option value="O">O</option>
              <option value="A+">A+</option>
              <option value="A-">A-</option>
              <option value="B+">B+</option>
              <option value="B-">B-</option>
              <option value="AB+">AB+</option>
              <option value="AB-">AB-</option>
              <option value="O+">O+</option>
              <option value="O-">O-</option>
            </select>
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kewarganegaraan</label>
            <select
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.warganegara}
              onChange={(e) => setData('warganegara', e.target.value)}
            >
              <option value="WNI">WNI</option>
              <option value="WNA">WNA</option>
            </select>
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nomor Akta Kelahiran</label>
            <input 
              type="text"
              placeholder="Contoh: 12345/LU/2026"
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.no_akta_lahir}
              onChange={(e) => setData('no_akta_lahir', e.target.value)}
            />
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Status Pendidikan (Sekolah)</label>
            <select
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.status_pendidikan}
              onChange={(e) => setData('status_pendidikan', e.target.value)}
            >
              <option value="Tamat Sekolah">Tamat Sekolah / Tidak Sekolah Lagi</option>
              <option value="Sedang Sekolah">Sedang Sekolah</option>
              <option value="Putus Sekolah">Putus Sekolah</option>
              <option value="Belum Sekolah">Belum Sekolah</option>
            </select>
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">No. Telepon / WhatsApp</label>
            <input 
              type="text"
              placeholder="Contoh: 0812xxxxxxxx"
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.telepon}
              onChange={(e) => {
                const val = e.target.value.replace(/[^0-9]/g, '');
                setData('telepon', val);
              }}
            />
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Cacat / Disabilitas (Jika Ada)</label>
            <input 
              type="text"
              placeholder="Contoh: Cacat Fisik (kosongkan jika tidak ada)"
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.cacat_type}
              onChange={(e) => setData('cacat_type', e.target.value)}
            />
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Penyakit Menahun (Jika Ada)</label>
            <input 
              type="text"
              placeholder="Contoh: Jantung (kosongkan jika tidak ada)"
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.sakit_menahun}
              onChange={(e) => setData('sakit_menahun', e.target.value)}
            />
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Asuransi Kesehatan (BPJS)</label>
            <select
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.status_asuransi}
              onChange={(e) => setData('status_asuransi', e.target.value)}
            >
              <option value="TIDAK ADA">TIDAK ADA</option>
              <option value="BPJS MANDIRI">BPJS MANDIRI</option>
              <option value="BPJS PBI / GRATIS">BPJS PBI / GRATIS</option>
              <option value="NON-BPJS / SWASTA">NON-BPJS / SWASTA</option>
            </select>
          </div>
        </div>
      </div>

      {/* 2.5 Batch Input: Anggota Keluarga Tambahan */}
      <div className="p-8 bg-blue-50/30 border border-blue-100 rounded-3xl space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h4 className="text-xs font-black text-blue-600 uppercase tracking-widest flex items-center gap-2">
              <Users className="w-4 h-4" />
              Anggota Keluarga Ikut Pindah (Opsional)
            </h4>
            <p className="text-xs font-medium text-gray-500 mt-1">Tambahkan data istri/anak jika pindah bersamaan (Batch Input).</p>
          </div>
          <button 
            type="button" 
            onClick={addFamilyMember}
            className="flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 shadow-lg shadow-blue-900/10 transition-all active:scale-95"
          >
            <Plus className="w-4 h-4" />
            Tambah Anggota
          </button>
        </div>

        {data.family_members.length > 0 ? (
          <div className="space-y-4">
            {data.family_members.map((member, index) => (
              <div key={index} className="p-5 bg-white border border-blue-100 rounded-2xl shadow-sm relative animate-in zoom-in-95 duration-300">
                <div className="absolute top-4 right-4 flex items-center justify-center w-6 h-6 rounded-full bg-blue-50 text-blue-600 font-black text-[10px]">{index + 1}</div>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 pr-10">
                  <MemberNIKInput 
                    value={member.nik} 
                    onChange={(val) => updateFamilyMember(index, 'nik', val)}
                    error={errors[`family_members.${index}.nik`]}
                  />
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Nama Lengkap<span className="text-red-500 ml-0.5">*</span></label>
                    <input 
                      type="text" required placeholder="Nama Lengkap"
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.nama} onChange={(e) => updateFamilyMember(index, 'nama', e.target.value.toUpperCase())}
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Hubungan</label>
                    <select 
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.kedudukan_keluarga} onChange={(e) => updateFamilyMember(index, 'kedudukan_keluarga', e.target.value)}
                    >
                      <option value="ISTRI">ISTRI</option>
                      <option value="ANAK">ANAK</option>
                      <option value="CUCU">CUCU</option>
                      <option value="ORANG TUA">ORANG TUA</option>
                      <option value="MERTUA">MERTUA</option>
                      <option value="LAINNYA">LAINNYA</option>
                    </select>
                  </div>

                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Jenis Kelamin</label>
                    <select 
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.jenis_kelamin} onChange={(e) => updateFamilyMember(index, 'jenis_kelamin', e.target.value)}
                    >
                      <option value="LAKI-LAKI">LAKI-LAKI</option>
                      <option value="PEREMPUAN">PEREMPUAN</option>
                    </select>
                  </div>
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Tempat Lahir<span className="text-red-500 ml-0.5">*</span></label>
                    <input 
                      type="text" required placeholder="Tempat Lahir"
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.tempat_lahir} onChange={(e) => updateFamilyMember(index, 'tempat_lahir', e.target.value.toUpperCase())}
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Tanggal Lahir<span className="text-red-500 ml-0.5">*</span></label>
                    <input 
                      type="date" required
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.tanggal_lahir} onChange={(e) => updateFamilyMember(index, 'tanggal_lahir', e.target.value)}
                    />
                  </div>

                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Agama</label>
                    <select 
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.agama} onChange={(e) => updateFamilyMember(index, 'agama', e.target.value)}
                    >
                      <option value="ISLAM">ISLAM</option>
                      <option value="KRISTEN">KRISTEN</option>
                      <option value="KATHOLIK">KATHOLIK</option>
                      <option value="HINDU">HINDU</option>
                      <option value="BUDHA">BUDHA</option>
                      <option value="KONGHUCU">KONGHUCU</option>
                    </select>
                  </div>
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Status</label>
                    <select 
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.status_perkawinan} onChange={(e) => updateFamilyMember(index, 'status_perkawinan', e.target.value)}
                    >
                      <option value="BELUM KAWIN">BELUM KAWIN</option>
                      <option value="KAWIN">KAWIN</option>
                      <option value="CERAI HIDUP">CERAI HIDUP</option>
                      <option value="CERAI MATI">CERAI MATI</option>
                    </select>
                  </div>
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Pendidikan</label>
                    <input 
                      type="text" placeholder="Pendidikan"
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.pendidikan} onChange={(e) => updateFamilyMember(index, 'pendidikan', e.target.value.toUpperCase())}
                    />
                  </div>

                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Pekerjaan</label>
                    <input 
                      type="text" placeholder="Pekerjaan"
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.pekerjaan} onChange={(e) => updateFamilyMember(index, 'pekerjaan', e.target.value.toUpperCase())}
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Nama Ayah</label>
                    <input 
                      type="text" placeholder="Ayah"
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.nama_ayah} onChange={(e) => updateFamilyMember(index, 'nama_ayah', e.target.value.toUpperCase())}
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Nama Ibu</label>
                    <input 
                      type="text" placeholder="Ibu"
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.nama_ibu} onChange={(e) => updateFamilyMember(index, 'nama_ibu', e.target.value.toUpperCase())}
                    />
                  </div>

                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Golongan Darah</label>
                    <select 
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.golongan_darah} onChange={(e) => updateFamilyMember(index, 'golongan_darah', e.target.value)}
                    >
                      <option value="TIDAK TAHU">TIDAK TAHU</option>
                      <option value="A">A</option>
                      <option value="B">B</option>
                      <option value="AB">AB</option>
                      <option value="O">O</option>
                    </select>
                  </div>
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Kewarganegaraan</label>
                    <select 
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.warganegara} onChange={(e) => updateFamilyMember(index, 'warganegara', e.target.value)}
                    >
                      <option value="WNI">WNI</option>
                      <option value="WNA">WNA</option>
                    </select>
                  </div>
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">No. Akta Lahir</label>
                    <input 
                      type="text" placeholder="No Akta Lahir"
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.no_akta_lahir} onChange={(e) => updateFamilyMember(index, 'no_akta_lahir', e.target.value)}
                    />
                  </div>

                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Status Sekolah</label>
                    <select 
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.status_pendidikan} onChange={(e) => updateFamilyMember(index, 'status_pendidikan', e.target.value)}
                    >
                      <option value="Tamat Sekolah">Tamat Sekolah</option>
                      <option value="Sedang Sekolah">Sedang Sekolah</option>
                      <option value="Putus Sekolah">Putus Sekolah</option>
                      <option value="Belum Sekolah">Belum Sekolah</option>
                    </select>
                  </div>
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">No. Telepon / WA</label>
                    <input 
                      type="text" placeholder="No Telepon"
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.telepon} onChange={(e) => updateFamilyMember(index, 'telepon', e.target.value.replace(/\D/g, ''))}
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Jenis Cacat</label>
                    <input 
                      type="text" placeholder="Jenis Cacat"
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.cacat_type} onChange={(e) => updateFamilyMember(index, 'cacat_type', e.target.value)}
                    />
                  </div>

                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">Penyakit Menahun</label>
                    <input 
                      type="text" placeholder="Penyakit Menahun"
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.sakit_menahun} onChange={(e) => updateFamilyMember(index, 'sakit_menahun', e.target.value)}
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="text-[9px] font-black text-gray-400 uppercase">BPJS / Asuransi</label>
                    <select 
                      className="w-full px-3 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:bg-white focus:border-blue-500"
                      value={member.status_asuransi} onChange={(e) => updateFamilyMember(index, 'status_asuransi', e.target.value)}
                    >
                      <option value="TIDAK ADA">TIDAK ADA</option>
                      <option value="BPJS MANDIRI">BPJS MANDIRI</option>
                      <option value="BPJS PBI / GRATIS">BPJS PBI / GRATIS</option>
                      <option value="NON-BPJS / SWASTA">NON-BPJS / SWASTA</option>
                    </select>
                  </div>
                </div>
                <div className="mt-4 pt-4 border-t border-gray-50 flex justify-end">
                  <button type="button" onClick={() => removeFamilyMember(index)} className="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <Trash2 className="w-3.5 h-3.5" /> Hapus
                  </button>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="p-8 text-center border-2 border-dashed border-blue-100 rounded-2xl bg-white">
            <p className="text-sm font-bold text-gray-400">Tidak ada anggota keluarga tambahan.</p>
            <p className="text-xs font-medium text-gray-400 mt-1">Klik tombol di atas untuk menambahkan (opsional).</p>
          </div>
        )}
      </div>

      {/* 3. Detail Alamat & Wilayah */}
      <div className="p-8 bg-gray-50 border border-gray-100 rounded-3xl space-y-6">
        <h4 className="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 mb-4">
          <MapPin className="w-4 h-4" />
          Alamat & Wilayah (Tujuan di Desa)
        </h4>

        {/* Gunakan Komponen WilayahSelect */}
        <div className="space-y-6">
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Lengkap<span className="text-red-500 ml-0.5">*</span></label>
            <textarea 
              rows={2} required
              readOnly={kkOption === 'existing'}
              placeholder="Contoh: Jl. Merdeka No 123"
              className={`w-full px-4 py-3.5 border rounded-2xl text-sm font-bold outline-none transition-all ${
                kkOption === 'existing' ? "bg-gray-100 border-gray-100 text-gray-500" : "bg-white border-gray-100 focus:ring-4 focus:ring-green-500/10 focus:border-green-500"
              }`}
              value={data.alamat}
              onChange={(e) => setData('alamat', e.target.value)}
            />
            {errors.alamat && <p className="text-xs text-red-500">{errors.alamat}</p>}
          </div>

          <WilayahSelect 
            wilayahTree={wilayahTree}
            selectedDusun={data.dusun_id}
            selectedRw={data.rw_id}
            selectedRt={data.rt_id}
            onChange={handleWilayahChange}
            disabled={kkOption === 'existing'}
          />
          {(errors.dusun_id || errors.rw_id || errors.rt_id) && (
            <p className="text-xs text-red-500">Mohon lengkapi pilihan wilayah RT/RW/Dusun.</p>
          )}
        </div>
      </div>

      {/* 4. Informasi Kepindahan */}
      <div className="p-8 bg-orange-50/30 border border-orange-100 rounded-3xl space-y-6">
        <h4 className="text-xs font-black text-orange-600 uppercase tracking-widest flex items-center gap-2">
          <FileText className="w-4 h-4" />
          Detail Kepindahan (Asal)
        </h4>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kategori Pindah</label>
            <select className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.kategori_mutasi} onChange={(e) => setData('kategori_mutasi', e.target.value)}>
              <option value="luar_kota">Luar Kota/Kabupaten</option>
              <option value="dalam_kota">Dalam Kota/Kabupaten</option>
              <option value="luar_negeri">Luar Negeri</option>
              <option value="dalam_desa">Dalam Desa (Pindahan Antar Dusun/RT)</option>
            </select>
          </div>
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Asal Pindah<span className="text-red-500 ml-0.5">*</span></label>
            <input type="text" required placeholder="Contoh: Bandung, Jawa Barat" className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10" value={data.asal_tujuan} onChange={(e) => setData('asal_tujuan', e.target.value)} />
          </div>
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Pindah Masuk<span className="text-red-500 ml-0.5">*</span></label>
            <input type="date" required className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.tanggal_mutasi} onChange={(e) => setData('tanggal_mutasi', e.target.value)} />
          </div>
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alasan Pindah<span className="text-red-500 ml-0.5">*</span></label>
            <input type="text" required className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-orange-500" value={data.alasan} onChange={(e) => setData('alasan', e.target.value)} />
          </div>
        </div>
      </div>

      {/* Action Buttons */}
      <div className="pt-4 flex items-center justify-end gap-4">
        <button
          type="submit"
          disabled={processing}
          className="px-10 py-3.5 bg-gradient-to-r from-green-500 to-green-700 text-white rounded-xl text-[11px] font-bold uppercase tracking-widest hover:scale-[1.02] transition-all shadow-xl shadow-green-200 flex items-center gap-2 active:scale-95 disabled:opacity-50"
        >
          {processing ? 'MENYIMPAN...' : (
            <>
              <Save className="w-4 h-4" />
              SIMPAN PINDAH MASUK
            </>
          )}
        </button>
      </div>
    </form>
  );
}
