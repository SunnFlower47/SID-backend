import React, { useEffect, useState } from 'react';
import { MapPin } from 'lucide-react';

export default function WilayahSelect({ 
  wilayahTree = [], 
  selectedDusun, 
  selectedRw, 
  selectedRt, 
  onChange,
  disabled = false
}) {
  const [availableRws, setAvailableRws] = useState([]);
  const [availableRts, setAvailableRts] = useState([]);

  // Sync available RWs when Dusun changes
  useEffect(() => {
    if (selectedDusun) {
      const dusun = wilayahTree.find(d => d.id == selectedDusun);
      setAvailableRws(dusun ? dusun.rws : []);
    } else {
      setAvailableRws([]);
    }
  }, [selectedDusun, wilayahTree]);

  // Sync available RTs when RW changes
  useEffect(() => {
    if (selectedRw && selectedDusun) {
      const dusun = wilayahTree.find(d => d.id == selectedDusun);
      const rw = dusun?.rws.find(r => r.id == selectedRw);
      setAvailableRts(rw ? rw.rts : []);
    } else {
      setAvailableRts([]);
    }
  }, [selectedRw, selectedDusun, wilayahTree]);

  const handleDusunChange = (e) => {
    onChange('dusun_id', e.target.value);
    onChange('rw_id', ''); // Reset RW
    onChange('rt_id', ''); // Reset RT
  };

  const handleRwChange = (e) => {
    onChange('rw_id', e.target.value);
    onChange('rt_id', ''); // Reset RT
  };

  const handleRtChange = (e) => {
    onChange('rt_id', e.target.value);
  };

  return (
    <div className="space-y-4">
      <div className="flex items-center gap-2 mb-2">
        <MapPin className="w-4 h-4 text-gray-400" />
        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pilih Wilayah (Dusun / RW / RT)</label>
      </div>
      
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {/* Dusun */}
        <div className="space-y-2">
          <select 
            required
            disabled={disabled}
            className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none disabled:bg-gray-50 disabled:text-gray-400"
            value={selectedDusun || ''}
            onChange={handleDusunChange}
          >
            <option value="">Pilih Dusun</option>
            {wilayahTree.map(dusun => (
              <option key={dusun.id} value={dusun.id}>{dusun.nama}</option>
            ))}
          </select>
        </div>

        {/* RW */}
        <div className="space-y-2">
          <select 
            required
            disabled={disabled || !selectedDusun}
            className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none disabled:bg-gray-50 disabled:text-gray-400"
            value={selectedRw || ''}
            onChange={handleRwChange}
          >
            <option value="">Pilih RW</option>
            {availableRws.map(rw => (
              <option key={rw.id} value={rw.id}>{rw.kode}</option>
            ))}
          </select>
        </div>

        {/* RT */}
        <div className="space-y-2">
          <select 
            required
            disabled={disabled || !selectedRw}
            className="w-full px-4 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none disabled:bg-gray-50 disabled:text-gray-400"
            value={selectedRt || ''}
            onChange={handleRtChange}
          >
            <option value="">Pilih RT</option>
            {availableRts.map(rt => (
              <option key={rt.id} value={rt.id}>{rt.kode}</option>
            ))}
          </select>
        </div>
      </div>
    </div>
  );
}
