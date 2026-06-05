import React from 'react';
import { CreditCard } from 'lucide-react';
import { cn } from '@/lib/utils';

const ManualDomisiliForm = ({ data, updateDataTambahan, isCheckingNik, wilayah, checkDomisiliNik }) => {
    return (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 animate-in fade-in duration-500">
            <div className="space-y-2">
                 <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">NIK Pendatang</label>
                 <div className="relative">
                     <div className="absolute left-4 top-1/2 -translate-y-1/2">
                         <CreditCard className={cn("w-4 h-4 transition-colors", isCheckingNik ? "text-green-600 animate-pulse" : "text-gray-400")} />
                     </div>
                     <input 
                         type="text"
                         maxLength={16}
                         value={data.data_tambahan.nik || ''}
                         onChange={e => {
                             const val = e.target.value.replace(/[^0-9]/g, '');
                             updateDataTambahan('nik', val);
                             if (val.length === 16) checkDomisiliNik(val);
                         }}
                         className="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                         placeholder="Masukkan NIK KTP asal..."
                     />
                     {isCheckingNik && (
                         <div className="absolute right-4 top-1/2 -translate-y-1/2">
                             <div className="w-4 h-4 border-2 border-green-600 border-t-transparent rounded-full animate-spin"></div>
                         </div>
                     )}
                 </div>
            </div>
            <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                <input 
                    type="text"
                    value={data.data_tambahan.nama || ''}
                    onChange={e => updateDataTambahan('nama', e.target.value)}
                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                    placeholder="Masukkan nama sesuai KTP..."
                />
            </div>
            <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tempat Lahir</label>
                <input 
                    type="text"
                    value={data.data_tambahan.tempat_lahir || ''}
                    onChange={e => updateDataTambahan('tempat_lahir', e.target.value)}
                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                    placeholder="Contoh: Bandung"
                />
            </div>
            <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Lahir</label>
                <input 
                    type="date"
                    value={data.data_tambahan.tanggal_lahir || ''}
                    onChange={e => updateDataTambahan('tanggal_lahir', e.target.value)}
                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                />
            </div>
            <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Kelamin</label>
                <div className="flex gap-4 p-2 bg-gray-50 rounded-2xl">
                    {['L', 'P'].map(jk => (
                        <button
                            key={jk}
                            type="button"
                            onClick={() => updateDataTambahan('jenis_kelamin', jk)}
                            className={cn(
                                "flex-1 py-2 rounded-xl text-[10px] font-black transition-all uppercase tracking-widest",
                                data.data_tambahan.jenis_kelamin === jk ? "bg-white text-green-700 shadow-sm" : "text-gray-400 hover:bg-white/50"
                            )}
                        >
                            {jk === 'L' ? 'Laki-laki' : 'Perempuan'}
                        </button>
                    ))}
                </div>
            </div>
            <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Agama</label>
                <select 
                    value={data.data_tambahan.agama || ''}
                    onChange={e => updateDataTambahan('agama', e.target.value)}
                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                >
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Budha">Budha</option>
                    <option value="Khonghucu">Khonghucu</option>
                </select>
            </div>
            <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Status Perkawinan</label>
                <select 
                    value={data.data_tambahan.status_perkawinan || ''}
                    onChange={e => updateDataTambahan('status_perkawinan', e.target.value)}
                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                >
                    <option value="Belum Kawin">Belum Kawin</option>
                    <option value="Kawin">Kawin</option>
                    <option value="Cerai Hidup">Cerai Hidup</option>
                    <option value="Cerai Mati">Cerai Mati</option>
                </select>
            </div>
            <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pekerjaan</label>
                <input 
                    type="text"
                    value={data.data_tambahan.pekerjaan || ''}
                    onChange={e => updateDataTambahan('pekerjaan', e.target.value)}
                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                    placeholder="Contoh: Karyawan Swasta"
                />
            </div>
            <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kewarganegaraan</label>
                <input 
                    type="text"
                    value={data.data_tambahan.kewarganegaraan || ''}
                    onChange={e => updateDataTambahan('kewarganegaraan', e.target.value)}
                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                    placeholder="Indonesia"
                />
            </div>
            <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tanggal Masuk <span className="text-red-500">*</span></label>
                <input 
                    type="date"
                    value={data.data_tambahan.tanggal_masuk || ''}
                    onChange={e => updateDataTambahan('tanggal_masuk', e.target.value)}
                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                    required
                />
            </div>
            <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">RW / RT Tujuan</label>
                <div className="flex gap-2">
                    <select 
                        value={data.data_tambahan.rw_id || ''}
                        onChange={e => {
                            updateDataTambahan('rw_id', e.target.value);
                            updateDataTambahan('rt_id', '');
                            updateDataTambahan('dusun_id', '');
                        }}
                        className="flex-1 px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                    >
                        <option value="">Pilih RW...</option>
                        {wilayah.rw.map(rw => <option key={rw.id} value={rw.id}>{rw.kode}</option>)}
                    </select>
                    <select 
                        value={data.data_tambahan.rt_id || ''}
                        onChange={e => {
                            const rtId = e.target.value;
                            updateDataTambahan('rt_id', rtId);
                            const selectedRt = wilayah.rt.find(r => String(r.id) === String(rtId));
                            if (selectedRt) {
                                updateDataTambahan('dusun_id', selectedRt.dusun_id);
                            } else {
                                updateDataTambahan('dusun_id', '');
                            }
                        }}
                        className="flex-1 px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                    >
                        <option value="">Pilih RT...</option>
                        {wilayah.rt
                            .filter(r => String(r.rw_id) === String(data.data_tambahan.rw_id))
                            .map(r => <option key={r.id} value={r.id}>{r.kode}</option>)}
                    </select>
                </div>
            </div>
            <div className="space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Dusun Tujuan</label>
                <select 
                    value={data.data_tambahan.dusun_id || ''}
                    disabled
                    className="w-full px-5 py-3.5 bg-gray-100 border-none rounded-2xl text-sm font-bold text-gray-500 cursor-not-allowed transition-all shadow-inner"
                >
                    <option value="">(Terisi Otomatis)</option>
                    {wilayah.dusun.map(d => <option key={d.id} value={d.id}>{d.nama}</option>)}
                </select>
            </div>
            <div className="md:col-span-2 space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kota / Kabupaten Asal <span className="text-red-500">*</span></label>
                <input 
                    type="text"
                    value={data.data_tambahan.asal_daerah || ''}
                    onChange={e => updateDataTambahan('asal_daerah', e.target.value)}
                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                    placeholder="Contoh: Bandung / Jakarta Selatan"
                    required
                />
            </div>
            <div className="md:col-span-2 space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Asal</label>
                <textarea 
                    value={data.data_tambahan.alamat_asal || ''}
                    onChange={e => updateDataTambahan('alamat_asal', e.target.value)}
                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                    rows="2"
                    placeholder="Alamat lengkap di daerah asal..."
                ></textarea>
            </div>
            <div className="md:col-span-2 space-y-2">
                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Tinggal Sekarang</label>
                <textarea 
                    value={data.data_tambahan.alamat_tinggal || ''}
                    onChange={e => updateDataTambahan('alamat_tinggal', e.target.value)}
                    className="w-full px-5 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                    rows="2"
                    placeholder="Alamat domisili saat ini di Desa Cibatu..."
                ></textarea>
            </div>
        </div>
    );
};

export default ManualDomisiliForm;
