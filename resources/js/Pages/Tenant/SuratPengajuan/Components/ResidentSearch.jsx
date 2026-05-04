import React from 'react';
import { User, Search, X, Layers } from 'lucide-react';
import { cn } from '@/lib/utils';

const ResidentSearch = ({ 
    residents, 
    isSearching, 
    searchQuery, 
    setSearchQuery, 
    onSearch,
    onSelectResident, 
    selectedResident, 
    setSelectedResident,
    errors
}) => {
    return (
        <div className="space-y-4">
            {selectedResident ? (
                <div className="flex items-center justify-between p-4 bg-green-50 rounded-2xl border border-green-100 animate-in fade-in duration-300">
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-green-600 shadow-sm border border-green-100">
                            <User className="w-6 h-6" />
                        </div>
                        <div>
                            <h4 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">{selectedResident.nama}</h4>
                            <p className="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{selectedResident.nik}</p>
                            <p className="text-[9px] font-bold text-green-600 uppercase tracking-widest mt-0.5">
                                {selectedResident.dusun} - RW {selectedResident.rw} / RT {selectedResident.rt}
                            </p>
                        </div>
                    </div>
                    <button 
                        type="button"
                        onClick={() => {
                            setSelectedResident(null);
                        }}
                        className="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
                    >
                        <X className="w-5 h-5" />
                    </button>
                </div>
            ) : (
                <div className="relative">
                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <Search className="w-4 h-4 text-gray-400" />
                    </div>
                    <input 
                        type="text"
                        value={searchQuery}
                        onChange={e => {
                            setSearchQuery(e.target.value);
                            onSearch(e.target.value);
                        }}
                        className="w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                        placeholder="Ketik minimal 3 huruf NIK atau Nama warga..."
                    />
                    {isSearching && (
                        <div className="absolute right-4 top-1/2 -translate-y-1/2">
                            <Layers className="w-4 h-4 text-green-600 animate-spin" />
                        </div>
                    )}
                    
                    {residents.length > 0 && (
                        <div className="absolute z-50 left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden animate-in slide-in-from-top-2 duration-200">
                            {residents.map(r => (
                                <button
                                    key={r.id}
                                    type="button"
                                    onClick={() => onSelectResident(r)}
                                    className="w-full px-5 py-4 text-left hover:bg-green-50 flex items-center justify-between group transition-colors"
                                >
                                    <div>
                                        <span className="block text-sm font-black text-gray-900 uppercase italic tracking-tighter group-hover:text-green-700">{r.nama}</span>
                                        <span className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">{r.nik}</span>
                                    </div>
                                    <div className="text-right">
                                        <span className="block text-[9px] font-black text-gray-400 uppercase tracking-widest">{r.dusun}</span>
                                        <span className="block text-[8px] font-bold text-gray-300 uppercase tracking-widest">RW {r.rw} / RT {r.rt}</span>
                                    </div>
                                </button>
                            ))}
                        </div>
                    )}
                </div>
            )}
            {errors.penduduk_id && <p className="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{errors.penduduk_id}</p>}
        </div>
    );
};

export default ResidentSearch;
