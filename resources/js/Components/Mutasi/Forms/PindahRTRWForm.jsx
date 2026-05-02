import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Save, ArrowRightLeft, Users, MapPin, AlertCircle } from 'lucide-react';
import KKAutocomplete from '../../Shared/KKAutocomplete';
import WilayahSelect from '../../Shared/WilayahSelect';
import Swal from 'sweetalert2';

export default function PindahRTRWForm({ wilayahTree, mutasi = null }) {
  const isEdit = !!mutasi;
  const [selectedKK, setSelectedKK] = useState(isEdit ? { 
    nkk: mutasi.penduduk?.nkk,
    kepala_keluarga: mutasi.penduduk?.nama,
    jumlah_anggota: '?' // We don't have this in mutasi record easily but it's okay for display
  } : null);

  const { data, setData, post, put, processing, errors } = useForm({
    nkk: mutasi?.penduduk?.nkk || '',
    rt_id_tujuan: mutasi?.rt_id || '',
    rw_id_tujuan: mutasi?.rw_id || '',
    dusun_id_tujuan: mutasi?.dusun_id || '',
    alamat_tujuan: mutasi?.alasan_snapshot?.alamat_baru || '', // Usually reasons are used to store some data or use alasan
    asal_tujuan: mutasi?.alasan || 'Pindah RT/RW Antar Dusun',
    tanggal_mutasi: mutasi?.tanggal_mutasi ? new Date(mutasi.tanggal_mutasi).toISOString().split('T')[0] : new Date().toISOString().split('T')[0],
    jenis_mutasi: 'pindah_rt_rw'
  });

  const handleKKSelect = (kk) => {
    setSelectedKK(kk);
    setData('nkk', kk?.nkk || '');
  };

  const handleWilayahChange = (field, value) => {
    // Mapping the fields from WilayahSelect to our form schema
    const fieldMap = {
      'dusun_id': 'dusun_id_tujuan',
      'rw_id': 'rw_id_tujuan',
      'rt_id': 'rt_id_tujuan',
    };
    setData(fieldMap[field] || field, value);
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!data.nkk) {
      Swal.fire({ icon: 'error', title: 'Oops...', text: 'Pilih KK yang akan dipindah terlebih dahulu!' });
      return;
    }
    if (!data.rt_id_tujuan || !data.rw_id_tujuan) {
      Swal.fire({ icon: 'error', title: 'Oops...', text: 'Wilayah tujuan (RT/RW) wajib diisi!' });
      return;
    }

    Swal.fire({
      title: isEdit ? 'Update Data Pindah Wilayah?' : 'Konfirmasi Pindah Wilayah',
      text: isEdit 
        ? `Simpan perubahan data pindah wilayah untuk KK ${selectedKK?.kepala_keluarga}?`
        : `Yakin ingin memindahkan KK ${selectedKK?.kepala_keluarga} beserta ${selectedKK?.jumlah_anggota || 0} anggotanya ke wilayah baru?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#9333ea', // purple-600
      cancelButtonColor: '#9ca3af',
      confirmButtonText: isEdit ? 'Ya, Simpan!' : 'Ya, Pindahkan!'
    }).then((result) => {
      if (result.isConfirmed) {
        const options = {
          onSuccess: () => Swal.fire('Berhasil!', isEdit ? 'Data berhasil diperbarui.' : 'Seluruh anggota KK berhasil dipindah.', 'success'),
          onError: (errs) => Swal.fire('Error', Object.values(errs)[0] || 'Gagal menyimpan.', 'error')
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
      
      {/* 1. Pilih Keluarga */}
      <div className="space-y-4">
        <h4 className="text-sm font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
          <Users className="w-4 h-4 text-purple-500" />
          Data Kartu Keluarga
        </h4>
        {isEdit ? (
          <div className="p-4 bg-purple-50 border border-purple-100 rounded-2xl flex items-center gap-4">
            <div className="w-12 h-12 bg-white rounded-xl flex items-center justify-center border border-purple-100 shadow-sm text-purple-600 font-black text-[10px]">
              KK
            </div>
            <div>
              <p className="text-sm font-bold text-gray-900">{selectedKK?.kepala_keluarga}</p>
              <p className="text-[10px] font-mono text-gray-400">{data.nkk}</p>
            </div>
          </div>
        ) : (
          <>
            <div className="p-4 bg-purple-50 border-l-4 border-purple-500 rounded-r-2xl text-xs font-medium text-purple-800 flex gap-2">
              <AlertCircle className="w-4 h-4 shrink-0" />
              <p>Operasi ini akan memindahkan <strong>seluruh anggota keluarga</strong> di dalam KK tersebut ke alamat/wilayah yang baru.</p>
            </div>
            <KKAutocomplete 
                onSelect={handleKKSelect} 
                placeholder="Ketik NKK atau Nama Kepala Keluarga..." 
                className="[&>div>input]:focus:ring-purple-500/10 [&>div>input]:focus:border-purple-500"
            />
          </>
        )}
        {errors.nkk && <p className="text-xs text-red-500">{errors.nkk}</p>}
      </div>

      {/* 2. Wilayah Tujuan */}
      <div className="p-8 bg-purple-50/50 border border-purple-100 rounded-[32px] space-y-6">
        <h4 className="text-xs font-black text-purple-600 uppercase tracking-widest flex items-center gap-2 mb-4">
          <ArrowRightLeft className="w-4 h-4" />
          Alamat & Wilayah Tujuan
        </h4>

        <div className="space-y-6">
          <div className="space-y-2">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Tujuan Baru</label>
            <textarea 
              rows={2} required
              placeholder="Contoh: Jl. Merdeka No 123 (Kosongkan jika hanya ganti RT/RW)"
              className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all"
              value={data.alamat_tujuan}
              onChange={(e) => setData('alamat_tujuan', e.target.value)}
            />
          </div>

          <WilayahSelect 
            wilayahTree={wilayahTree}
            selectedDusun={data.dusun_id_tujuan}
            selectedRw={data.rw_id_tujuan}
            selectedRt={data.rt_id_tujuan}
            onChange={handleWilayahChange}
          />
        </div>
      </div>

      {/* 3. Detail Ekstra */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 p-8 bg-gray-50 border border-gray-100 rounded-[32px]">
         <div className="space-y-2">
          <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Pindah</label>
          <input 
            type="date" required
            className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-gray-500/10 outline-none"
            value={data.tanggal_mutasi}
            onChange={(e) => setData('tanggal_mutasi', e.target.value)}
          />
        </div>
        <div className="space-y-2">
          <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alasan Pindah</label>
          <input 
            type="text" required
            className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-gray-500/10 outline-none"
            value={data.asal_tujuan}
            onChange={(e) => setData('asal_tujuan', e.target.value)}
          />
        </div>
      </div>

      <div className="pt-4 flex items-center justify-end">
        <button
          type="submit" disabled={processing}
          className="px-10 py-4 bg-purple-600 text-white rounded-2xl text-sm font-black hover:bg-purple-700 transition-all shadow-xl shadow-purple-900/20 flex items-center gap-2 active:scale-95 disabled:opacity-50"
        >
          {processing ? 'Memproses...' : <><Save className="w-4 h-4" /> SIMPAN PINDAH RT/RW</>}
        </button>
      </div>
    </form>
  );
}
