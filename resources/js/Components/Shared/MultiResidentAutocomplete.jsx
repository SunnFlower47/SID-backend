import React, { useState, useEffect, useRef } from 'react';
import { Search, User, MapPin, Loader2, X, CheckCircle2 } from 'lucide-react';
import axios from 'axios';
import { cn } from '@/lib/utils';

export default function MultiResidentAutocomplete({ onSelect, placeholder, label, className = '' }) {
    const [query, setQuery] = useState('');
    const [results, setResults] = useState([]);
    const [loading, setLoading] = useState(false);
    const [showResults, setShowResults] = useState(false);
    const [selected, setSelected] = useState([]);
    const wrapperRef = useRef(null);

    // Handle click outside to close dropdown
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (wrapperRef.current && !wrapperRef.current.contains(event.target)) {
                setShowResults(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    // Search Logic - Mengikuti cara Layanan Surat yang terbukti jalan
    useEffect(() => {
        if (query.length < 3) {
            setResults([]);
            return;
        }

        const delayDebounceFn = setTimeout(async () => {
            setLoading(true);
            try {
                // Pakai route penduduk.search yang dipakai di menu lain
                const response = await axios.get(route('penduduk.search'), { 
                    params: { q: query } 
                });
                
                // Pastikan data ada
                const data = response.data || [];
                setResults(data);
                setShowResults(true);
            } catch (error) {
                console.error('Search error:', error);
            } finally {
                setLoading(false);
            }
        }, 500);

        return () => clearTimeout(delayDebounceFn);
    }, [query]);

    const handleSelect = (resident) => {
        if (selected.find(s => s.id === resident.id)) return;
        
        const newSelected = [...selected, resident];
        setSelected(newSelected);
        setQuery('');
        setShowResults(false);
        if (onSelect) onSelect(newSelected);
    };

    const removeSelected = (id) => {
        const newSelected = selected.filter(s => s.id !== id);
        setSelected(newSelected);
        if (onSelect) onSelect(newSelected);
    };

    return (
        <div ref={wrapperRef} className={cn("relative space-y-3", className)}>
            {label && <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{label}</label>}
            
            {/* Search Input */}
            <div className="relative">
                <Search className={cn(
                    "absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 transition-colors",
                    loading ? "text-green-500 animate-pulse" : "text-gray-400"
                )} />
                <input
                    type="text"
                    placeholder={placeholder || "Cari Nama atau NIK..."}
                    className="w-full pl-11 pr-12 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                    value={query}
                    onChange={(e) => setQuery(e.target.value)}
                    onFocus={() => query.length >= 3 && setShowResults(true)}
                />
                {loading && (
                    <div className="absolute right-4 top-1/2 -translate-y-1/2">
                        <Loader2 className="w-4 h-4 text-green-600 animate-spin" />
                    </div>
                )}
            </div>

            {/* Dropdown Results - Z-INDEX TINGGI & ABSOLUTE */}
            {showResults && (
                <div className="absolute z-[9999] top-full left-0 right-0 mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden animate-in slide-in-from-top-2 duration-200 max-h-80 overflow-y-auto">
                    {results.length > 0 ? (
                        <div className="p-2">
                            {results.map((res) => {
                                const isAlreadySelected = selected.find(s => s.id === res.id);
                                return (
                                    <button
                                        key={res.id}
                                        type="button"
                                        disabled={isAlreadySelected}
                                        onClick={() => handleSelect(res)}
                                        className={cn(
                                            "w-full p-4 flex items-center justify-between hover:bg-green-50 rounded-xl transition-colors group text-left",
                                            isAlreadySelected && "opacity-50 cursor-not-allowed bg-gray-50"
                                        )}
                                    >
                                        <div className="flex items-center gap-4">
                                            <div className="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center border border-gray-100 group-hover:bg-green-100 group-hover:border-green-200 transition-colors">
                                                <User className="w-5 h-5 text-gray-400 group-hover:text-green-600" />
                                            </div>
                                            <div>
                                                <p className="text-sm font-black text-gray-900 group-hover:text-green-900 uppercase italic tracking-tighter">{res.nama}</p>
                                                <div className="flex items-center gap-2 mt-0.5">
                                                    <span className="text-[10px] font-bold text-gray-400 tracking-wider">{res.nik}</span>
                                                    {(res.rt_label || res.rt) && (
                                                        <span className="flex items-center gap-1 text-[9px] font-bold text-green-600 uppercase">
                                                            <MapPin className="w-3 h-3" />
                                                            RT {res.rt_label || res.rt}
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                        {isAlreadySelected && <CheckCircle2 className="w-4 h-4 text-green-500" />}
                                    </button>
                                );
                            })}
                        </div>
                    ) : (
                        <div className="p-8 text-center">
                            <p className="text-sm font-bold text-gray-400">Data warga tidak ditemukan</p>
                        </div>
                    )}
                </div>
            )}

            {/* Selected List */}
            {selected.length > 0 && (
                <div className="flex flex-wrap gap-2 pt-2">
                    {selected.map((s) => (
                        <div 
                            key={s.id} 
                            className="flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-xl shadow-sm animate-in zoom-in-95 duration-200"
                        >
                            <span className="text-[10px] font-black uppercase tracking-tighter italic">{s.nama}</span>
                            <button 
                                type="button"
                                onClick={() => removeSelected(s.id)}
                                className="hover:bg-white/20 rounded-lg p-0.5 transition-colors"
                            >
                                <X className="w-3 h-3" />
                            </button>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
