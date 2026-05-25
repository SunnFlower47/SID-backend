import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Save, UserX, Clock, MapPin, ClipboardList } from 'lucide-react';
import ResidentAutocomplete from '../../Shared/ResidentAutocomplete';
import Swal from 'sweetalert2';

export default function KematianForm({ mutasi = null }) {
  const isEdit = !!mutasi;
  const deathData = mutasi?.data_kematian || {};
  const burialData = mutasi?.data_pemakaman || {};
  const pelaporData = mutasi?.data_pelapor || {};

  const [selectedResident, setSelectedResident] = useState(mutasi?.penduduk || null);

  const { data, setData, post, put, processing, errors } = useForm({
    penduduk_id: mutasi?.penduduk_id || '',
    tanggal_mutasi: mutasi?.tanggal_mutasi ? new Date(mutasi.tanggal_mutasi).toISOString().split('T')[0] : new Date().toISOString().split('T')[0],
    hari_meninggal: deathData.hari || 'Senin',
    jam_meninggal: deathData.jam || '12:00',
    bertempat_di: deathData.bertempat_di || 'RUMAH SAKIT',
    alasan: mutasi?.alasan || 'Sakit',
    hari_pemakaman: burialData.hari || 'Senin',
    tanggal_pemakaman: burialData.tanggal ? new Date(burialData.tanggal).toISOString().split('T')[0] : new Date().toISOString().split('T')[0],
    jam_pemakaman: burialData.jam || '15:00',
    lokasi_pemakaman: burialData.lokasi || 'TPU Desa',
    pelapor_nama: pelaporData.nama || '',
    pelapor_hubungan: pelaporData.hubungan || 'Keluarga',
    pelapor_umur: pelaporData.umur || '',
    pelapor_pekerjaan: pelaporData.pekerjaan || '',
    pelapor_alamat: pelaporData.alamat || '',
    jenis_mutasi: 'kematian'
  });

  const handleResidentSelect = (resident) => {
    setSelectedResident(resident);
    setData('penduduk_id', resident?.id || '');
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!data.penduduk_id) {
      Swal.fire({ icon: 'error', title: 'Oops...', text: 'Pilih Warga yang meninggal terlebih dahulu!' });
      return;
    }

    Swal.fire({
      title: isEdit ? 'Update Data Kematian?' : 'Konfirmasi Kematian',
      text: isEdit 
        ? `Simpan perubahan data kematian untuk ${selectedResident?.nama}?`
        : `Anda yakin akan memproses data kematian untuk ${selectedResident?.nama}? Data warga akan dihapus dari daftar aktif.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: isEdit ? '#2563eb' : '#ef4444',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: isEdit ? 'Ya, Simpan Perubahan!' : 'Ya, Proses Kematian!'
    }).then((result) => {
      if (result.isConfirmed) {
        const options = {
          onSuccess: () => {
            // Ditangani oleh flash message global di AuthenticatedLayout
          },
          onError: (errs) => {
            Swal.fire('Error', Object.values(errs)[0] || 'Gagal menyimpan data.', 'error');
          }
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
      
      {/* 1. Pilih Warga */}
      <div className="space-y-4">
        <h4 className="text-sm font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
          <UserX className="w-4 h-4 text-red-500" />
          Data Warga
        </h4>
        {isEdit ? (
          <div className="p-4 bg-gray-50 border border-gray-100 rounded-2xl flex items-center gap-4">
            <div className="w-12 h-12 bg-white rounded-xl flex items-center justify-center border border-gray-100 shadow-sm text-red-500 font-black">
              RIP
            </div>
            <div>
              <p className="text-sm font-bold text-gray-900">{selectedResident?.nama}</p>
              <p className="text-[10px] font-mono text-gray-400">{selectedResident?.nik}</p>
            </div>
          </div>
        ) : (
          <ResidentAutocomplete 
              onSelect={handleResidentSelect} 
              placeholder="Cari NIK atau Nama Warga yang meninggal..." 
              className="[&>div>input]:focus:ring-red-500/10 [&>div>input]:focus:border-red-500"
          />
        )}
        {errors.penduduk_id && <p className="text-xs text-red-500">{errors.penduduk_id}</p>}
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
        {/* 2. Waktu & Tempat Meninggal */}
        <div className="p-8 bg-red-50/50 border border-red-100 rounded-3xl space-y-6">
          <h4 className="text-xs font-black text-red-600 uppercase tracking-widest flex items-center gap-2">
            <Clock className="w-4 h-4" />
            Waktu & Tempat Meninggal
          </h4>
          <div className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal</label>
                <input type="date" required className="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" 
                  value={data.tanggal_mutasi} 
                  onChange={(e) => {
                    const val = e.target.value;
                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const hari = val ? days[new Date(val).getDay()] : data.hari_meninggal;
                    setData(d => ({ ...d, tanggal_mutasi: val, hari_meninggal: hari }));
                  }} 
                />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Hari</label>
                <select className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none text-gray-500 cursor-not-allowed" 
                  value={data.hari_meninggal} onChange={e => setData('hari_meninggal', e.target.value)} tabIndex="-1">
                  {['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'].map(h => <option key={h} value={h}>{h}</option>)}
                </select>
              </div>
            </div>
            <div className="space-y-2">
              <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jam Meninggal</label>
              <input type="time" lang="en-GB" required className="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.jam_meninggal} onChange={e => setData('jam_meninggal', e.target.value)} />
            </div>
            <div className="space-y-2">
              <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Bertempat Di</label>
              <input type="text" required placeholder="Contoh: Rumah Sakit Umum" className="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.bertempat_di} onChange={e => setData('bertempat_di', e.target.value)} />
            </div>
            <div className="space-y-2">
              <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Penyebab / Alasan</label>
              <input type="text" required placeholder="Contoh: Sakit Tua" className="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.alasan} onChange={e => setData('alasan', e.target.value)} />
            </div>
          </div>
        </div>

        {/* 3. Detail Pemakaman & Pelapor */}
        <div className="space-y-8">
          <div className="p-8 bg-gray-50 border border-gray-100 rounded-3xl space-y-6">
            <h4 className="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
              <MapPin className="w-4 h-4" />
              Detail Pemakaman
            </h4>
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal</label>
                <input type="date" required className="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" 
                  value={data.tanggal_pemakaman} 
                  onChange={(e) => {
                    const val = e.target.value;
                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const hari = val ? days[new Date(val).getDay()] : data.hari_pemakaman;
                    setData(d => ({ ...d, tanggal_pemakaman: val, hari_pemakaman: hari }));
                  }} 
                />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Hari</label>
                <select className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none text-gray-500 cursor-not-allowed" 
                  value={data.hari_pemakaman} onChange={e => setData('hari_pemakaman', e.target.value)} tabIndex="-1">
                  {['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'].map(h => <option key={h} value={h}>{h}</option>)}
                </select>
              </div>
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jam</label>
              <input type="time" lang="en-GB" required className="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.jam_pemakaman} onChange={e => setData('jam_pemakaman', e.target.value)} />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Lokasi</label>
                <input type="text" required placeholder="Lokasi Pemakaman" className="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.lokasi_pemakaman} onChange={e => setData('lokasi_pemakaman', e.target.value)} />
              </div>
            </div>
          </div>

          <div className="p-8 bg-gray-50 border border-gray-100 rounded-3xl space-y-6">
            <h4 className="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
              <ClipboardList className="w-4 h-4" />
              Pelapor (Opsional)
            </h4>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Pelapor</label>
                <input type="text" className="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.pelapor_nama} onChange={e => setData('pelapor_nama', e.target.value.toUpperCase())} />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Hubungan</label>
                <input type="text" placeholder="Contoh: Anak / Ketua RT" className="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.pelapor_hubungan} onChange={e => setData('pelapor_hubungan', e.target.value)} />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Umur Pelapor</label>
                <input type="number" placeholder="Contoh: 45" className="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.pelapor_umur} onChange={e => setData('pelapor_umur', e.target.value)} />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pekerjaan Pelapor</label>
                <input type="text" placeholder="Contoh: Wiraswasta" className="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none" value={data.pelapor_pekerjaan} onChange={e => setData('pelapor_pekerjaan', e.target.value)} />
              </div>
              <div className="md:col-span-2 space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Pelapor</label>
                <textarea rows={2} placeholder="Alamat lengkap pelapor..." className="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-medium outline-none" value={data.pelapor_alamat} onChange={e => setData('pelapor_alamat', e.target.value)} />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="pt-4 flex items-center justify-end">
        <button
          type="submit" disabled={processing}
          className="px-10 py-3.5 bg-gradient-to-r from-red-500 to-red-700 text-white rounded-xl text-[11px] font-bold uppercase tracking-widest hover:scale-[1.02] transition-all shadow-xl shadow-red-200 flex items-center gap-2 active:scale-95 disabled:opacity-50"
        >
          {processing ? 'MEMPROSES...' : <><Save className="w-4 h-4" /> SIMPAN KEMATIAN</>}
        </button>
      </div>
    </form>
  );
}
