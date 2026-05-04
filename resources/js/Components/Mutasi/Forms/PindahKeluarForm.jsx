import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { Save, UserMinus, MapPin, Users, Info, CheckSquare, Square, Globe, Home, Navigation } from 'lucide-react';
import ResidentAutocomplete from '../../Shared/ResidentAutocomplete';
import axios from 'axios';
import Swal from 'sweetalert2';

function cn(...classes) {
  return classes.filter(Boolean).join(' ');
}

export default function PindahKeluarForm({ mutasi = null }) {
  const isEdit = !!mutasi;
  const snapshot = mutasi?.data_snapshot || {};
  
  const [selectedResident, setSelectedResident] = useState(mutasi?.penduduk || null);
  const [familyMembers, setFamilyMembers] = useState([]);
  const [loadingFamily, setLoadingFamily] = useState(false);

  // Parse address parts for edit mode
  const parseAddress = (addr, category) => {
    if (!addr) return { jalan: '', rt: '', rw: '', desa: '', kecamatan: '', kabupaten: '', provinsi: '', negara: '', alamat_ln: '' };
    const parts = addr.split(', ').map(p => p.trim());
    
    if (category === 'luar_negeri') {
      return {
        negara: parts[0]?.replace('Negara: ', '') || '',
        alamat_ln: parts[1] || '',
        jalan: '', rt: '', rw: '', desa: '', kecamatan: '', kabupaten: '', provinsi: ''
      };
    }
    
    return {
        jalan: parts[0] || '',
        rt: parts[1]?.match(/RT (\d+)/)?.[1] || '',
        rw: parts[1]?.match(/RW (\d+)/)?.[1] || '',
        desa: parts[2]?.replace('Desa ', '') || '',
        kecamatan: parts[3]?.replace('Kec. ', '') || '',
        kabupaten: parts[4]?.replace('Kab. ', '') || '',
        provinsi: parts[5]?.replace('Prov. ', '') || '',
        negara: '', alamat_ln: ''
    };
  };

  const [addressDetails, setAddressDetails] = useState(isEdit 
    ? parseAddress(mutasi.asal_tujuan, mutasi.kategori_mutasi)
    : {
        jalan: '', rt: '', rw: '', desa: '', kecamatan: '', kabupaten: '', provinsi: '', negara: '', alamat_ln: ''
    }
  );

  const { data, setData, post, put, processing, errors, clearErrors } = useForm({
    penduduk_id: mutasi?.penduduk_id || '',
    kategori_mutasi: mutasi?.kategori_mutasi || 'luar_kota', // dalam_kota, luar_kota, luar_negeri
    asal_tujuan: mutasi?.asal_tujuan || '',
    alasan: mutasi?.alasan || 'Pindah Domisili',
    tanggal_mutasi: mutasi?.tanggal_mutasi ? new Date(mutasi.tanggal_mutasi).toISOString().split('T')[0] : new Date().toISOString().split('T')[0],
    jenis_mutasi: 'pindah_keluar',
    anggota_pindah: snapshot.anggota_pindah_ids || [] 
  });

  // Build asal_tujuan string automatically
  useEffect(() => {
    const parts = [];
    if (data.kategori_mutasi === 'luar_negeri') {
      if (addressDetails.negara) parts.push(`Negara: ${addressDetails.negara}`);
      if (addressDetails.alamat_ln) parts.push(addressDetails.alamat_ln);
    } else {
      if (addressDetails.jalan) parts.push(addressDetails.jalan);
      if (addressDetails.rt || addressDetails.rw) parts.push(`RT ${addressDetails.rt}/RW ${addressDetails.rw}`);
      if (addressDetails.desa) parts.push(`Desa ${addressDetails.desa}`);
      if (addressDetails.kecamatan) parts.push(`Kec. ${addressDetails.kecamatan}`);
      if (addressDetails.kabupaten) parts.push(`Kab. ${addressDetails.kabupaten}`);
      if (addressDetails.provinsi) parts.push(`Prov. ${addressDetails.provinsi}`);
    }
    
    setData('asal_tujuan', parts.filter(Boolean).join(', '));
  }, [addressDetails, data.kategori_mutasi]);

  const handleResidentSelect = async (resident) => {
    setSelectedResident(resident);
    setData(prev => ({
        ...prev,
        penduduk_id: resident?.id || '',
        anggota_pindah: isEdit ? prev.anggota_pindah : []
    }));
    if (!isEdit) setFamilyMembers([]);
    clearErrors();

    if (resident?.id) {
      setLoadingFamily(true);
      try {
        // Fetch family members via existing endpoint
        const nkk = resident.nkk || resident.kartu_keluarga?.nkk;
        const response = await axios.get('/mutasi/get-anggota-keluarga', { 
          params: { nkk, exclude_id: resident.id } 
        });
        setFamilyMembers(response.data);
      } catch (error) {
        console.error('Error fetching family members', error);
      } finally {
        setLoadingFamily(false);
      }
    }
  };

  // Fetch family members on edit mode init
  useEffect(() => {
    if (isEdit && selectedResident?.id && familyMembers.length === 0) {
        handleResidentSelect(selectedResident);
    }
  }, [isEdit, selectedResident]);

  const toggleMember = (id) => {
    const current = [...data.anggota_pindah];
    if (current.includes(id)) {
      setData('anggota_pindah', current.filter(i => i !== id));
    } else {
      setData('anggota_pindah', [...current, id]);
    }
  };

  const toggleSelectAll = () => {
    if (data.anggota_pindah.length === familyMembers.length) {
      setData('anggota_pindah', []);
    } else {
      setData('anggota_pindah', familyMembers.map(m => m.id));
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!data.penduduk_id) {
      Swal.fire({ icon: 'error', title: 'Oops...', text: 'Pilih Warga yang akan pindah terlebih dahulu!' });
      return;
    }

    if (!data.asal_tujuan) {
      Swal.fire({ icon: 'error', title: 'Alamat Kosong', text: 'Mohon lengkapi detail alamat tujuan!' });
      return;
    }

    Swal.fire({
      title: isEdit ? 'Update Data Pindah Keluar?' : 'Konfirmasi Mutasi',
      text: isEdit 
        ? `Simpan perubahan data pindah keluar untuk ${selectedResident?.nama}?`
        : `Proses pindah keluar untuk ${selectedResident?.nama} beserta ${data.anggota_pindah.length} anggota keluarga?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#f97316',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: isEdit ? 'Ya, Simpan!' : 'Ya, Proses Sekarang!'
    }).then((result) => {
      if (result.isConfirmed) {
        const options = {
          onSuccess: () => Swal.fire('Berhasil!', isEdit ? 'Data berhasil diperbarui.' : 'Data pindah keluar berhasil diproses.', 'success'),
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
    <form onSubmit={handleSubmit} className="space-y-8 animate-in slide-in-from-bottom-4 duration-500">
      
      {/* 1. Header & Resident Selector */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div className="space-y-4">
          <h4 className="text-sm font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
            <UserMinus className="w-4 h-4 text-orange-500" />
            Warga yang Pindah
          </h4>
          {isEdit ? (
            <div className="p-4 bg-orange-50 border border-orange-100 rounded-2xl flex items-center gap-4">
              <div className="w-12 h-12 bg-white rounded-xl flex items-center justify-center border border-orange-100 shadow-sm text-orange-600 font-black text-[10px]">
                OUT
              </div>
              <div>
                <p className="text-sm font-bold text-gray-900">{selectedResident?.nama}</p>
                <p className="text-[10px] font-mono text-gray-400">{selectedResident?.nik}</p>
              </div>
            </div>
          ) : (
            <>
              <ResidentAutocomplete 
                onSelect={handleResidentSelect} 
                placeholder="Cari NIK atau Nama..."
                className="[&>div>input]:focus:ring-orange-500/10 [&>div>input]:focus:border-orange-500"
              />
              <div className="p-4 bg-orange-50/80 border border-orange-100 rounded-2xl flex gap-3 items-start">
                <Info className="w-5 h-5 text-orange-500 shrink-0 mt-0.5" />
                <p className="text-[11px] text-orange-700 font-medium leading-relaxed">
                  Warga yang dipilih akan dinonaktifkan dari daftar penduduk aktif setelah proses ini selesai.
                </p>
              </div>
            </>
          )}
        </div>

        <div className="space-y-4">
          <div className="flex items-center justify-between">
            <h4 className="text-sm font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
              <Users className="w-4 h-4 text-blue-500" />
              Anggota Keluarga Ikut
            </h4>
            {familyMembers.length > 0 && (
              <button 
                type="button" onClick={toggleSelectAll}
                className="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline"
              >
                {data.anggota_pindah.length === familyMembers.length ? 'Batal Semua' : 'Pilih Semua'}
              </button>
            )}
          </div>
          
          <div className="bg-white border border-gray-100 rounded-[24px] min-h-[120px] max-h-[220px] overflow-y-auto shadow-sm">
            {loadingFamily ? (
              <div className="p-8 text-center text-[11px] text-gray-400 font-bold animate-pulse">Memuat data keluarga...</div>
            ) : familyMembers.length > 0 ? (
              <div className="divide-y divide-gray-50">
                {familyMembers.map((member) => (
                  <div 
                    key={member.id} 
                    onClick={() => toggleMember(member.id)}
                    className="p-4 flex items-center justify-between hover:bg-gray-50 cursor-pointer transition-colors group"
                  >
                    <div className="flex flex-col">
                      <span className="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{member.nama}</span>
                      <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">{member.nik} • {member.kedudukan_keluarga}</span>
                    </div>
                    {data.anggota_pindah.includes(member.id) ? (
                      <CheckSquare className="w-5 h-5 text-blue-600" />
                    ) : (
                      <Square className="w-5 h-5 text-gray-200" />
                    )}
                  </div>
                ))}
              </div>
            ) : (
              <div className="p-8 text-center text-[11px] text-gray-400 font-bold italic">
                {selectedResident ? 'Tidak ada anggota keluarga lain.' : 'Pilih warga terlebih dahulu.'}
              </div>
            )}
          </div>
        </div>
      </div>

      {/* 2. Address Details */}
      <div className="p-8 bg-gray-50/50 border border-gray-100 rounded-3xl space-y-8">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <h4 className="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
            <MapPin className="w-4 h-4" />
            Detail Alamat Tujuan
          </h4>
          <div className="flex bg-white p-1 rounded-xl border border-gray-100 shadow-sm">
            {[
              { id: 'dalam_kota', label: 'Dalam Kota', icon: Home },
              { id: 'luar_kota', label: 'Luar Kota', icon: Navigation },
              { id: 'luar_negeri', label: 'Luar Negeri', icon: Globe }
            ].map((cat) => (
              <button
                key={cat.id} type="button"
                onClick={() => setData('kategori_mutasi', cat.id)}
                className={cn(
                  "px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2",
                  data.kategori_mutasi === cat.id ? "bg-orange-600 text-white shadow-lg shadow-orange-900/20" : "text-gray-400 hover:text-gray-600"
                )}
              >
                <cat.icon className="w-3 h-3" />
                {cat.label}
              </button>
            ))}
          </div>
        </div>

        <div className="space-y-6">
          {data.kategori_mutasi === 'luar_negeri' ? (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 animate-in slide-in-from-top-2">
              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Negara Tujuan</label>
                <input 
                  type="text" required placeholder="Contoh: Malaysia, Jepang, dll"
                  className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all"
                  value={addressDetails.negara}
                  onChange={(e) => setAddressDetails({ ...addressDetails, negara: e.target.value.toUpperCase() })}
                />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Detail Alamat (Kota/Distrik)</label>
                <input 
                  type="text" placeholder="Detail alamat di luar negeri..."
                  className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all"
                  value={addressDetails.alamat_ln}
                  onChange={(e) => setAddressDetails({ ...addressDetails, alamat_ln: e.target.value.toUpperCase() })}
                />
              </div>
            </div>
          ) : (
            <>
              <div className="grid grid-cols-1 md:grid-cols-4 gap-6 animate-in slide-in-from-top-2">
                <div className="md:col-span-2 space-y-2">
                  <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jalan / Gang / No. Rumah</label>
                  <input 
                    type="text" placeholder="Contoh: Jl. Sudirman No. 123"
                    className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:border-orange-500 outline-none"
                    value={addressDetails.jalan}
                    onChange={(e) => setAddressDetails({ ...addressDetails, jalan: e.target.value.toUpperCase() })}
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-center block">RT</label>
                  <input 
                    type="text" maxLength={3} placeholder="000"
                    className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold text-center outline-none focus:border-orange-500"
                    value={addressDetails.rt}
                    onChange={(e) => setAddressDetails({ ...addressDetails, rt: e.target.value.replace(/\D/g, '') })}
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-center block">RW</label>
                  <input 
                    type="text" maxLength={3} placeholder="000"
                    className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold text-center outline-none focus:border-orange-500"
                    value={addressDetails.rw}
                    onChange={(e) => setAddressDetails({ ...addressDetails, rw: e.target.value.replace(/\D/g, '') })}
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-4 gap-6 animate-in slide-in-from-top-4 duration-500">
                <div className="space-y-2">
                  <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Desa/Kelurahan</label>
                  <input 
                    type="text" className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-orange-500"
                    value={addressDetails.desa}
                    onChange={(e) => setAddressDetails({ ...addressDetails, desa: e.target.value.toUpperCase() })}
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kecamatan</label>
                  <input 
                    type="text" className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-orange-500"
                    value={addressDetails.kecamatan}
                    onChange={(e) => setAddressDetails({ ...addressDetails, kecamatan: e.target.value.toUpperCase() })}
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kabupaten/Kota</label>
                  <input 
                    type="text" className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-orange-500"
                    value={addressDetails.kabupaten}
                    onChange={(e) => setAddressDetails({ ...addressDetails, kabupaten: e.target.value.toUpperCase() })}
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Provinsi</label>
                  <input 
                    type="text" className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:border-orange-500"
                    value={addressDetails.provinsi}
                    onChange={(e) => setAddressDetails({ ...addressDetails, provinsi: e.target.value.toUpperCase() })}
                  />
                </div>
              </div>
            </>
          )}
        </div>

        <div className="p-5 bg-orange-50/50 rounded-2xl border border-dashed border-orange-200">
          <div className="flex items-center gap-2 mb-2">
            <span className="text-[10px] font-black text-orange-400 uppercase tracking-widest">Preview Alamat Tujuan:</span>
          </div>
          <p className="text-xs font-bold text-orange-900 italic leading-relaxed">
            {data.asal_tujuan || <span className="text-gray-300 font-medium">Lengkapi detail alamat di atas...</span>}
          </p>
        </div>
      </div>

      {/* 3. Reason & Date */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-8 p-8 bg-white border border-gray-100 rounded-3xl shadow-sm">
        <div className="space-y-2">
          <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Pindah</label>
          <input 
            type="date" required
            className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:bg-white focus:border-orange-500 transition-all"
            value={data.tanggal_mutasi}
            onChange={(e) => setData('tanggal_mutasi', e.target.value)}
          />
        </div>
        <div className="md:col-span-2 space-y-2">
          <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alasan Pindah</label>
          <input 
            type="text" required placeholder="Contoh: Ikut Suami, Pekerjaan, Pendidikan, dll..."
            className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:bg-white focus:border-orange-500 transition-all"
            value={data.alasan}
            onChange={(e) => setData('alasan', e.target.value)}
          />
        </div>
      </div>

      {/* Submit Button */}
      <div className="pt-4 flex items-center justify-end">
        <button
          type="submit" disabled={processing}
          className="px-10 py-3.5 bg-gradient-to-r from-orange-400 to-orange-600 text-white rounded-xl text-[11px] font-bold uppercase tracking-widest hover:scale-[1.02] transition-all shadow-xl shadow-orange-200 flex items-center gap-2 active:scale-95 disabled:opacity-50"
        >
          {processing ? (
            <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
          ) : <Save className="w-4 h-4" />}
          SIMPAN DATA PINDAH KELUAR
        </button>
      </div>
    </form>
  );
}
