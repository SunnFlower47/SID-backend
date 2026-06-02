import React, { useEffect, useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Save, Baby, MapPin, Calendar, Heart, Users, CheckCircle, CheckCircle2, AlertCircle } from 'lucide-react';
import KKAutocomplete from '../../Shared/KKAutocomplete';
import axios from 'axios';
import Swal from 'sweetalert2';

export default function KelahiranForm({ mutasi = null }) {
  const isEdit = !!mutasi;
  const baby = mutasi?.penduduk || {};

  const { data, setData, post, put, processing, errors } = useForm({
    nkk: baby.nkk || '',
    nama_bayi: baby.nama || '',
    nik_bayi: baby.nik || '', 
    jenis_kelamin_bayi: baby.jenis_kelamin || 'LAKI-LAKI',
    tempat_lahir: baby.tempat_lahir || 'CIBATU',
    tanggal_lahir: baby.tanggal_lahir || new Date().toISOString().split('T')[0],
    agama_bayi: baby.agama || 'ISLAM',
    status_perkawinan_bayi: baby.status_perkawinan || 'BELUM KAWIN',
    kedudukan_keluarga_bayi: baby.kedudukan_keluarga || 'ANAK',
    pendidikan_bayi: baby.pendidikan || 'TIDAK / BELUM SEKOLAH',
    pekerjaan_bayi: baby.pekerjaan || 'BELUM / TIDAK BEKERJA',
    nama_ayah: baby.nama_ayah || '',
    nama_ibu: baby.nama_ibu || '',
    alamat_bayi: baby.alamat || '',
    rt_id_bayi: baby.rt_id || '',
    rw_id_bayi: baby.rw_id || '',
    dusun_id_bayi: baby.dusun_id || '',
    keterangan_bayi: mutasi?.alasan || '',
    tanggal_mutasi: mutasi?.tanggal_mutasi ? new Date(mutasi.tanggal_mutasi).toISOString().split('T')[0] : new Date().toISOString().split('T')[0],
    jenis_mutasi: 'kelahiran',
    golongan_darah: baby.golongan_darah || 'TIDAK TAHU',
    no_akta_lahir: baby.no_akta_lahir || '',
    status_asuransi: baby.status_asuransi || 'TIDAK ADA',
    cacat_type: baby.cacat_type || '',
    sakit_menahun: baby.sakit_menahun || '',
    telepon: baby.telepon || ''
  });

  const [selectedKK, setSelectedKK] = useState(isEdit ? { 
    nkk: baby.nkk, 
    alamat: baby.alamat,
    rt_label: baby.rt_label,
    rw_label: baby.rw_label,
    dusun_label: baby.dusun_label
  } : null);

  const [nikChecking, setNikChecking] = useState(false);
  const [nikError, setNikError] = useState('');
  const [nikValid, setNikValid] = useState(false);

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!data.nkk) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Pilih Kartu Keluarga terlebih dahulu!',
        confirmButtonColor: '#3b82f6'
      });
      return;
    }
    if (nikError) {
      Swal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        text: nikError,
        confirmButtonColor: '#ef4444'
      });
      return;
    }

    const action = isEdit ? put(route('mutasi.data.update', mutasi.id)) : post(route('mutasi.data.store'));

    action.then(() => {
        // Success handled by onSuccess/onError in options below
    });

    // Actually, Inertia put/post takes options as second/third arg.
    // Let's refactor to use it properly.
    const options = {
      onSuccess: () => {
        // Ditangani oleh flash message global di AuthenticatedLayout
      },
      onError: (errors) => {
        const firstError = Object.values(errors)[0];
        Swal.fire({
          icon: 'error',
          title: 'Validasi Gagal',
          text: firstError || 'Mohon periksa kembali isian form.',
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

  useEffect(() => {
    // Only check NIK if it changed and it's not the original NIK in edit mode
    if (isEdit && data.nik_bayi === baby.nik) {
        setNikError('');
        setNikValid(false);
        return;
    }
    
    const checkNik = async () => {
      const nik = data.nik_bayi;
      if (!nik) {
        setNikError('');
        setNikValid(false);
        return;
      }

      if (nik.length > 0 && nik.length < 16) {
        setNikError('NIK harus tepat 16 digit angka.');
        setNikValid(false);
        return;
      }

      if (nik.length === 16) {
        setNikChecking(true);
        setNikValid(false);
        try {
          const response = await axios.get('/penduduk/check-nik', { params: { nik } });
          if (response.data.exists) {
            setNikError('NIK sudah terdaftar. Gunakan NIK yang lain.');
            setNikValid(false);
          } else {
            setNikError('');
            setNikValid(true);
          }
        } catch (error) {
          console.error("Gagal mengecek NIK", error);
        } finally {
          setNikChecking(false);
        }
      }
    };

    const debounceTimer = setTimeout(checkNik, 500);
    return () => clearTimeout(debounceTimer);
  }, [data.nik_bayi]);

  const handleKKSelect = async (kk) => {
    setSelectedKK(kk);
    setData(prev => ({ 
      ...prev, 
      nkk: kk?.nkk || '',
      alamat_bayi: kk?.alamat || '',
      rt_id_bayi: kk?.rt_id || '',
      rw_id_bayi: kk?.rw_id || '',
      dusun_id_bayi: kk?.dusun_id || '',
    }));

    if (kk?.nkk) {
      try {
        const response = await axios.get('/mutasi/get-anggota-keluarga', { 
          params: { nkk: kk.nkk } 
        });
        const members = response.data;
        const ayah = members.find((m) => 
          m.kedudukan_keluarga?.toUpperCase() === 'KEPALA KELUARGA' || 
          m.kedudukan_keluarga?.toUpperCase() === 'SUAMI'
        );
        const ibu = members.find((m) => 
          m.kedudukan_keluarga?.toUpperCase() === 'ISTRI'
        );
        
        if (ayah || ibu) {
          setData(prev => ({
            ...prev,
            nama_ayah: ayah?.nama || prev.nama_ayah,
            nama_ibu: ibu?.nama || prev.nama_ibu,
          }));
        }
      } catch (error) {
        console.error('Error fetching family members', error);
      }
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-8 animate-in slide-in-from-bottom-4 duration-500">
      {/* 1. Pilih Keluarga */}
      <div className="space-y-4">
        <h4 className="text-sm font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
          <Users className="w-4 h-4 text-blue-500" />
          Data Kartu Keluarga
        </h4>
        {isEdit ? (
          <div className="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-center gap-4">
            <div className="w-12 h-12 bg-white rounded-xl flex items-center justify-center border border-blue-100 shadow-sm text-blue-600 font-black">
              KK
            </div>
            <div>
              <p className="text-sm font-bold text-gray-900">NKK: {data.nkk}</p>
              <p className="text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                RT {selectedKK?.rt_label} / RW {selectedKK?.rw_label}
              </p>
            </div>
          </div>
        ) : (
          <KKAutocomplete 
              onSelect={handleKKSelect} 
              placeholder="Ketik NKK atau Nama Kepala Keluarga..." 
          />
        )}
      </div>

      {/* 2. Data Bayi */}
      <div className="p-8 bg-blue-50/50 border border-blue-100 rounded-3xl space-y-6">
        <h4 className="text-xs font-black text-blue-600 uppercase tracking-widest flex items-center gap-2">
          <Baby className="w-4 h-4" />
          Informasi Bayi
        </h4>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap Bayi<span className="text-red-500 ml-0.5">*</span></label>
            <input 
              type="text"
              required
              placeholder="Nama Lengkap..."
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.nama_bayi}
              onChange={(e) => setData('nama_bayi', e.target.value.toUpperCase())}
            />
            {errors.nama_bayi && <p className="text-xs text-red-500 mt-1 ml-1">{errors.nama_bayi}</p>}
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">NIK Bayi<span className="text-red-500 ml-0.5">*</span></label>
            <div className="relative">
              <input 
                type="text"
                required
                maxLength={16}
                placeholder="Masukkan 16 Digit NIK..."
                className={`w-full px-4 py-3.5 bg-white border rounded-2xl text-sm font-bold focus:ring-4 outline-none transition-all ${nikError ? 'border-red-500 focus:ring-red-500/10 text-red-600' : nikValid ? 'border-green-500 focus:ring-green-500/10 text-green-700' : 'border-gray-100 focus:ring-blue-500/10 focus:border-blue-500'}`}
                value={data.nik_bayi}
                onChange={(e) => {
                  const val = e.target.value.replace(/[^0-9]/g, '');
                  setData('nik_bayi', val);
                }}
              />
            </div>
            <div className="min-h-[16px] px-1 mt-1">
              {nikChecking && (
                <div className="flex items-center gap-1.5 animate-pulse text-blue-500">
                    <div className="w-2.5 h-2.5 border-2 border-current border-t-transparent rounded-full animate-spin" />
                    <span className="text-[8px] font-bold uppercase tracking-wider">Mengecek...</span>
                </div>
              )}
              {nikError && (
                <div className="flex items-center gap-1 text-red-500 animate-in fade-in duration-300">
                    <AlertCircle className="w-2 h-2" />
                    <span className="font-bold uppercase tracking-wider" style={{ fontSize: '8px' }}>{nikError}</span>
                </div>
              )}
              {nikValid && (
                <div className="flex items-center gap-1 text-green-600 animate-in fade-in duration-300">
                    <CheckCircle2 className="w-2 h-2" />
                    <span className="font-bold uppercase tracking-wider" style={{ fontSize: '8px' }}>NIK Tersedia</span>
                </div>
              )}
            </div>
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Kelamin</label>
            <select
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.jenis_kelamin_bayi}
              onChange={(e) => setData('jenis_kelamin_bayi', e.target.value)}
            >
              <option value="LAKI-LAKI">LAKI-LAKI</option>
              <option value="PEREMPUAN">PEREMPUAN</option>
            </select>
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Lahir<span className="text-red-500 ml-0.5">*</span></label>
            <div className="relative">
              <Calendar className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
              <input 
                type="date"
                required
                className="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
                value={data.tanggal_lahir}
                onChange={(e) => setData('tanggal_lahir', e.target.value)}
              />
            </div>
            {errors.tanggal_lahir && <p className="text-xs text-red-500 mt-1 ml-1">{errors.tanggal_lahir}</p>}
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tempat Lahir<span className="text-red-500 ml-0.5">*</span></label>
            <input 
              type="text"
              required
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.tempat_lahir}
              onChange={(e) => setData('tempat_lahir', e.target.value.toUpperCase())}
            />
            {errors.tempat_lahir && <p className="text-xs text-red-500 mt-1 ml-1">{errors.tempat_lahir}</p>}
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Agama</label>
            <select
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none"
              value={data.agama_bayi}
              onChange={(e) => setData('agama_bayi', e.target.value)}
            >
              <option value="ISLAM">ISLAM</option>
              <option value="KRISTEN">KRISTEN</option>
              <option value="KATOLIK">KATOLIK</option>
              <option value="HINDU">HINDU</option>
              <option value="BUDHA">BUDHA</option>
              <option value="KONGHUCU">KONGHUCU</option>
            </select>
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Status Perkawinan</label>
            <select
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none"
              value={data.status_perkawinan_bayi}
              onChange={(e) => setData('status_perkawinan_bayi', e.target.value)}
            >
              <option value="BELUM KAWIN">BELUM KAWIN</option>
              <option value="KAWIN">KAWIN</option>
            </select>
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kedudukan Keluarga</label>
            <select
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none"
              value={data.kedudukan_keluarga_bayi}
              onChange={(e) => setData('kedudukan_keluarga_bayi', e.target.value)}
            >
              <option value="ANAK">ANAK</option>
              <option value="CUCU">CUCU</option>
              <option value="LAINNYA">LAINNYA</option>
            </select>
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pendidikan</label>
            <input 
              type="text" 
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" 
              value={data.pendidikan_bayi} 
              onChange={(e) => setData('pendidikan_bayi', e.target.value.toUpperCase())} 
            />
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pekerjaan</label>
            <input 
              type="text" 
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" 
              value={data.pekerjaan_bayi} 
              onChange={(e) => setData('pekerjaan_bayi', e.target.value.toUpperCase())} 
            />
          </div>
        </div>

        {/* Alamat Info */}
        <div className="mt-6 pt-6 border-t border-blue-100/50">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="md:col-span-2 space-y-2">
              <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Bayi (Otomatis dari KK)<span className="text-red-500 ml-0.5">*</span></label>
              <textarea 
                rows={2} required
                className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-medium outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500"
                value={data.alamat_bayi}
                onChange={(e) => setData('alamat_bayi', e.target.value)}
              />
              {errors.alamat_bayi && <p className="text-xs text-red-500 mt-1 ml-1">{errors.alamat_bayi}</p>}
            </div>

            <div className="space-y-2">
              <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Wilayah Otomatis (Sesuai KK)</label>
              <div className="px-4 py-3.5 bg-white border border-blue-100 rounded-2xl text-xs font-bold text-blue-600 flex items-center gap-2 shadow-sm">
                <MapPin className="w-3 h-3" />
                {selectedKK ? `RT ${selectedKK.rt_label || selectedKK.rt}/RW ${selectedKK.rw_label || selectedKK.rw} - ${selectedKK.dusun_label || selectedKK.dusun}` : 'Pilih KK terlebih dahulu untuk memuat wilayah'}
              </div>
            </div>

            <div className="space-y-2">
              <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Keterangan Mutasi</label>
              <input 
                type="text" 
                placeholder="Catatan tambahan (opsional)..."
                className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-medium outline-none focus:ring-4 focus:ring-blue-500/10" 
                value={data.keterangan_bayi} 
                onChange={(e) => setData('keterangan_bayi', e.target.value)} 
              />
            </div>
          </div>
        </div>
      </div>

      {/* 2b. Informasi Kesehatan & Kontak (Opsional) */}
      <div className="p-8 bg-gray-50 border border-gray-100 rounded-3xl space-y-6">
        <h4 className="text-xs font-black text-gray-700 uppercase tracking-widest flex items-center gap-2">
          <Heart className="w-4 h-4 text-red-500" />
          Kesehatan & Kontak Tambahan (Opsional)
        </h4>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
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

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">No. Telepon / WA Orang Tua</label>
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
              placeholder="Contoh: Cacat Fisik, Netra (kosongkan jika tidak ada)"
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.cacat_type}
              onChange={(e) => setData('cacat_type', e.target.value)}
            />
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Penyakit Menahun (Jika Ada)</label>
            <input 
              type="text"
              placeholder="Contoh: Asma, Jantung (kosongkan jika tidak ada)"
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none"
              value={data.sakit_menahun}
              onChange={(e) => setData('sakit_menahun', e.target.value)}
            />
          </div>
        </div>
      </div>

      {/* 3. Data Orang Tua */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 p-8 bg-gray-50 border border-gray-100 rounded-3xl">
        <div className="md:col-span-2">
           <h4 className="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 mb-2">
            <Heart className="w-4 h-4 text-rose-400" />
            Informasi Orang Tua
          </h4>
        </div>

        <div className="space-y-2">
          <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Ayah<span className="text-red-500 ml-0.5">*</span></label>
          <input 
            type="text"
            required
            className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rose-500/10 focus:border-rose-300 transition-all outline-none"
            value={data.nama_ayah}
            onChange={(e) => setData('nama_ayah', e.target.value.toUpperCase())}
          />
        </div>

        <div className="space-y-2">
          <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Ibu<span className="text-red-500 ml-0.5">*</span></label>
          <input 
            type="text"
            required
            className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-rose-500/10 focus:border-rose-300 transition-all outline-none"
            value={data.nama_ibu}
            onChange={(e) => setData('nama_ibu', e.target.value.toUpperCase())}
          />
        </div>

        <div className="space-y-2">
          <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Input Mutasi<span className="text-red-500 ml-0.5">*</span></label>
          <input 
            type="date"
            required
            className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-gray-500/10 outline-none"
            value={data.tanggal_mutasi}
            onChange={(e) => setData('tanggal_mutasi', e.target.value)}
          />
        </div>
      </div>

      {/* Action Buttons */}
      <div className="pt-4 flex items-center justify-end gap-4">
        <button
          type="submit"
          disabled={processing}
          className="px-10 py-3.5 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-xl text-[11px] font-bold uppercase tracking-widest hover:scale-[1.02] transition-all shadow-xl shadow-blue-200 flex items-center gap-2 active:scale-95 disabled:opacity-50"
        >
          {processing ? 'MENYIMPAN...' : (
            <>
              <Save className="w-4 h-4" />
              SIMPAN DATA KELAHIRAN
            </>
          )}
        </button>
      </div>
    </form>
  );
}
