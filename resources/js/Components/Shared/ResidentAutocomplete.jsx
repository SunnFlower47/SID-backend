import React, { useState, useEffect, useRef } from 'react';
import { Search, User, MapPin, Loader2, X } from 'lucide-react';
import axios from 'axios';

export default function ResidentAutocomplete({ onSelect, placeholder, label, initialSelected, className = '' }) {
  const [query, setQuery] = useState('');
  const [results, setResults] = useState([]);
  const [loading, setLoading] = useState(false);
  const [showResults, setShowResults] = useState(false);
  const [selected, setSelected] = useState(initialSelected || null);
  const wrapperRef = useRef(null);

  useEffect(() => {
    if (initialSelected) {
      setSelected(initialSelected);
    }
  }, [initialSelected]);

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (wrapperRef.current && !wrapperRef.current.contains(event.target)) {
        setShowResults(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  useEffect(() => {
    if (query.length < 3) {
      setResults([]);
      return;
    }

    const delayDebounceFn = setTimeout(async () => {
      setLoading(true);
      try {
        const response = await axios.get('/mutasi/search-penduduk', { params: { query } });
        setResults(response.data);
      } catch (error) {
        console.error('Search error', error);
      } finally {
        setLoading(false);
        setShowResults(true);
      }
    }, 500);

    return () => clearTimeout(delayDebounceFn);
  }, [query]);

  const handleSelect = (resident) => {
    setSelected(resident);
    setQuery('');
    setShowResults(false);
    if (onSelect) onSelect(resident);
  };

  const handleClear = () => {
    setSelected(null);
    if (onSelect) onSelect(null);
  };

  if (selected) {
    return (
      <div className={`space-y-2 ${className}`}>
        {label && <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{label}</label>}
        <div className="flex items-center justify-between p-4 bg-teal-50 border border-teal-200 rounded-2xl animate-in zoom-in-95 duration-200">
          <div className="flex items-center gap-4">
            <div className="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center border border-teal-200">
              <User className="w-5 h-5 text-teal-600" />
            </div>
            <div>
              <p className="text-sm font-black text-teal-900 uppercase">{selected.nama}</p>
              <p className="text-[10px] font-bold text-teal-500 tracking-wider">NIK: {selected.nik}</p>
              <p className="text-[10px] font-medium text-teal-400 truncate max-w-[300px]">Alamat: {selected.alamat}</p>
            </div>
          </div>
          <button 
            type="button"
            onClick={handleClear}
            className="p-2 hover:bg-teal-100 rounded-lg text-teal-400 hover:text-teal-600 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>
      </div>
    );
  }

  return (
    <div ref={wrapperRef} className={`relative space-y-2 ${className}`}>
      {label && <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{label}</label>}
      <div className="relative">
        <Search className={`absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 transition-colors ${loading ? 'text-teal-500 animate-pulse' : 'text-gray-400'}`} />
        <input
          type="text"
          placeholder={placeholder || "Cari Nama atau NIK Warga..."}
          className="w-full pl-12 pr-12 py-3.5 bg-white border border-gray-100 rounded-2xl text-sm font-medium focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all outline-none"
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          onFocus={() => query.length >= 3 && setShowResults(true)}
        />
        {loading && <Loader2 className="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-teal-500 animate-spin" />}
      </div>

      {showResults && (
        <div className="absolute z-[100] top-full left-0 right-0 mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden animate-in slide-in-from-top-2 duration-200 max-h-80 overflow-y-auto">
          {results.length > 0 ? (
            <div className="p-2">
              {results.map((res) => (
                <button
                  key={res.id}
                  type="button"
                  onClick={() => handleSelect(res)}
                  className="w-full p-4 flex items-center gap-4 hover:bg-teal-50/50 rounded-xl transition-colors group text-left"
                >
                  <div className="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center border border-gray-100 group-hover:bg-teal-100 group-hover:border-teal-200 transition-colors">
                    <User className="w-5 h-5 text-gray-400 group-hover:text-teal-600" />
                  </div>
                  <div className="flex-1">
                    <p className="text-sm font-black text-gray-900 group-hover:text-teal-900 uppercase">{res.nama}</p>
                    <div className="flex items-center gap-3 mt-0.5">
                      <span className="text-[10px] font-bold text-gray-400 tracking-wider">NIK: {res.nik}</span>
                      <span className="flex items-center gap-1 text-[10px] font-bold text-teal-400 uppercase">
                        <MapPin className="w-3 h-3" />
                        RT {res.rt_label}/RW {res.rw_label}
                      </span>
                    </div>
                  </div>
                </button>
              ))}
            </div>
          ) : (
            <div className="p-8 text-center">
              <p className="text-sm font-bold text-gray-400">Data warga tidak ditemukan</p>
            </div>
          )}
        </div>
      )}
    </div>
  );
}
