import React from 'react';
import { Clock, Info, MapPin, UserPlus } from 'lucide-react';

const KematianForm = ({ data, updateDataTambahan }) => {
    return (
        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden animate-in slide-in-from-bottom-4 duration-500">
            <div className="p-6 border-b border-gray-100 bg-red-50/30 flex items-center gap-3">
                <Clock className="w-5 h-5 text-red-600" />
                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Detail Mutasi Kematian</h3>
            </div>
            <div className="p-8 space-y-8">
                {/* Waktu & Tempat Meninggal */}
                <div className="p-8 bg-red-50/50 border border-red-100 rounded-3xl space-y-6">
                    <h4 className="text-xs font-black text-red-600 uppercase tracking-widest flex items-center gap-2">
                        <Info className="w-4 h-4" /> WAKTU & TEMPAT MENINGGAL
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Meninggal</label>
                            <input type="date" className="w-full px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-red-500 transition-all" 
                                value={data.data_tambahan.kematian_tanggal || data.data_tambahan.tanggal_meninggal || ''} 
                                onChange={(e) => {
                                    const val = e.target.value;
                                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                    const hari = val ? days[new Date(val).getDay()] : (data.data_tambahan.kematian_hari || data.data_tambahan.hari_meninggal || 'Senin');
                                    updateDataTambahan('kematian_tanggal', val);
                                    setTimeout(() => updateDataTambahan('kematian_hari', hari), 0);
                                }} 
                            />
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Hari Meninggal</label>
                            <select className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none text-gray-500 cursor-not-allowed transition-all" 
                                value={data.data_tambahan.kematian_hari || data.data_tambahan.hari_meninggal || 'Senin'} onChange={e => updateDataTambahan('kematian_hari', e.target.value)} tabIndex="-1">
                                {['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'].map(h => <option key={h} value={h}>{h}</option>)}
                            </select>
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jam Meninggal</label>
                            <input type="time" lang="en-GB" className="w-full px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-red-500 transition-all" 
                                value={data.data_tambahan.kematian_jam || data.data_tambahan.jam_meninggal || ''} onChange={e => updateDataTambahan('kematian_jam', e.target.value)} />
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Bertempat Di</label>
                            <input type="text" placeholder="Contoh: Rumah Sakit Umum" className="w-full px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-red-500 transition-all" 
                                value={data.data_tambahan.kematian_bertempat_di || data.data_tambahan.bertempat_di || ''} onChange={e => updateDataTambahan('kematian_bertempat_di', e.target.value)} />
                        </div>
                        <div className="md:col-span-2 space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Penyebab / Alasan</label>
                            <input type="text" placeholder="Contoh: Sakit Tua / Kecelakaan" className="w-full px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-red-500 transition-all" 
                                value={data.data_tambahan.alasan || ''} onChange={e => updateDataTambahan('alasan', e.target.value)} />
                        </div>
                    </div>
                </div>

                {/* Detail Pemakaman */}
                <div className="p-8 bg-gray-50/80 border border-gray-100 rounded-3xl space-y-6">
                    <h4 className="text-xs font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                        <MapPin className="w-4 h-4" /> DETAIL PEMAKAMAN
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Pemakaman</label>
                            <input type="date" className="w-full px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-green-500 transition-all" 
                                value={data.data_tambahan.pemakaman_tanggal || data.data_tambahan.tanggal_pemakaman || ''} 
                                onChange={(e) => {
                                    const val = e.target.value;
                                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                    const hari = val ? days[new Date(val).getDay()] : (data.data_tambahan.pemakaman_hari || data.data_tambahan.hari_pemakaman || 'Senin');
                                    updateDataTambahan('pemakaman_tanggal', val);
                                    setTimeout(() => updateDataTambahan('pemakaman_hari', hari), 0);
                                }} 
                            />
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Hari Pemakaman</label>
                            <select className="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none text-gray-500 cursor-not-allowed transition-all" 
                                value={data.data_tambahan.pemakaman_hari || data.data_tambahan.hari_pemakaman || 'Senin'} onChange={e => updateDataTambahan('pemakaman_hari', e.target.value)} tabIndex="-1">
                                {['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'].map(h => <option key={h} value={h}>{h}</option>)}
                            </select>
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jam Pemakaman</label>
                            <input type="time" lang="en-GB" className="w-full px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-green-500 transition-all" 
                                value={data.data_tambahan.pemakaman_jam || data.data_tambahan.jam_pemakaman || ''} onChange={e => updateDataTambahan('pemakaman_jam', e.target.value)} />
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Lokasi Pemakaman</label>
                            <input type="text" placeholder="Contoh: TPU Desa Cibatu" className="w-full px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-green-500 transition-all" 
                                value={data.data_tambahan.pemakaman_lokasi || data.data_tambahan.lokasi_pemakaman || ''} onChange={e => updateDataTambahan('pemakaman_lokasi', e.target.value)} />
                        </div>
                    </div>
                </div>

                {/* Data Pelapor */}
                <div className="p-8 bg-gray-50/80 border border-gray-100 rounded-3xl space-y-6">
                    <h4 className="text-xs font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                        <UserPlus className="w-4 h-4" /> DATA PELAPOR (OPSIONAL)
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Pelapor</label>
                            <input type="text" className="w-full px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-green-500 transition-all" 
                                value={data.data_tambahan.pelapor_nama || ''} onChange={e => updateDataTambahan('pelapor_nama', e.target.value.toUpperCase())} />
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Hubungan</label>
                            <input type="text" placeholder="Contoh: Anak / Ketua RT" className="w-full px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-green-500 transition-all" 
                                value={data.data_tambahan.pelapor_hubungan || ''} onChange={e => updateDataTambahan('pelapor_hubungan', e.target.value)} />
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Umur Pelapor</label>
                            <input type="number" placeholder="45" className="w-full px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-green-500 transition-all" 
                                value={data.data_tambahan.pelapor_umur || ''} onChange={e => updateDataTambahan('pelapor_umur', e.target.value)} />
                        </div>
                        <div className="space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pekerjaan Pelapor</label>
                            <input type="text" placeholder="Wiraswasta" className="w-full px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-green-500 transition-all" 
                                value={data.data_tambahan.pelapor_pekerjaan || ''} onChange={e => updateDataTambahan('pelapor_pekerjaan', e.target.value)} />
                        </div>
                        <div className="md:col-span-2 space-y-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Pelapor</label>
                            <textarea rows={2} placeholder="Alamat lengkap pelapor..." className="w-full px-5 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-green-500 transition-all resize-none" 
                                value={data.data_tambahan.pelapor_alamat || ''} onChange={e => updateDataTambahan('pelapor_alamat', e.target.value)} />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default KematianForm;
