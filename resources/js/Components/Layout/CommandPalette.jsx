import React, { useState, useEffect, useRef } from 'react';
import {
  Search,
  X,
  LayoutDashboard,
  Users,
  Home,
  ArrowLeftRight,
  FileText,
  HeartHandshake,
  Newspaper,
  Store,
  MapPin,
  UsersRound,
  Phone,
  MessageSquare,
  AlertCircle,
  Settings,
  History,
  ShieldAlert,
  Star,
  Globe,
  ChartBar,
  Command,
  ArrowRight,
  Loader2,
  Download,
  Database
} from 'lucide-react';
import { router } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import axios from 'axios';

const menuItems = [
  { name: 'Dashboard', href: 'dashboard', icon: LayoutDashboard, group: 'Utama' },
  { name: 'Data Penduduk', href: 'penduduk.index', icon: Users, group: 'Kependudukan' },
  { name: 'Data Mutasi', href: 'mutasi.data.index', icon: ArrowLeftRight, group: 'Kependudukan' },
  { name: 'Kartu Keluarga', href: 'kk.index', icon: Home, group: 'Kependudukan' },
  { name: 'KK Bermasalah', href: 'kk.bermasalah.index', icon: AlertCircle, group: 'Kependudukan' },
  { name: 'Layanan Surat', href: 'admin.surat-pengajuan.index', icon: FileText, group: 'Layanan' },
  { name: 'Bantuan Sosial', href: 'bantuan-sosial.index', icon: HeartHandshake, group: 'Layanan' },
  { name: 'Pengaduan Warga', href: 'pengaduan.index', icon: AlertCircle, group: 'Layanan' },
  { name: 'Pesan Kontak', href: 'contact-messages.index', icon: MessageSquare, group: 'Layanan' },
  { name: 'Struktur Desa', href: 'struktur-desa.index', icon: UsersRound, group: 'Data Desa' },
  { name: 'Kontak Desa', href: 'kontak-desa.index', icon: Phone, group: 'Data Desa' },
  { name: 'Fasilitas Desa', href: 'fasilitas-desa.index', icon: MapPin, group: 'Data Desa' },
  { name: 'Data UMKM', href: 'umkm.index', icon: Store, group: 'Data Desa' },
  { name: 'Transparansi Desa', href: 'transparansi-desa.index', icon: ChartBar, group: 'Data Desa' },
  { name: 'Berita & Pengumuman', href: 'berita.index', icon: Newspaper, group: 'Web Desa' },
  { name: 'Testimoni Warga', href: 'testimoni.index', icon: Star, group: 'Web Desa' },
  { name: 'Pengaturan Web', href: 'web-desa.settings', icon: Settings, group: 'Web Desa' },
  { name: 'Laporan', href: 'laporan.index', icon: FileText, group: 'Laporan' },
  { name: 'Statistik', href: 'statistics.index', icon: ChartBar, group: 'Laporan' },
  { name: 'Perbandingan', href: 'comparison.index', icon: ArrowLeftRight, group: 'Laporan' },
  { name: 'Import Data', href: 'import.index', icon: Download, group: 'Data Management' },
  { name: 'Sampah Penduduk', href: 'settings.trash.penduduk.index', icon: AlertCircle, group: 'Data Management' },
  { name: 'Export Data', href: 'export-import.index', icon: Download, group: 'Data Management' },
  { name: 'Backup', href: 'backup.index', icon: Database, group: 'Data Management' },
  { name: 'Audit Log', href: 'audit-log.index', icon: History, group: 'Sistem' },
  { name: 'Pengaturan', href: 'settings.index', icon: Settings, group: 'Sistem' },
  { name: 'Master Wilayah', href: 'settings.wilayah.index', icon: MapPin, group: 'Sistem' },
  { name: 'Import Issue Queue', href: 'settings.wilayah.import-conflicts.index', icon: AlertCircle, group: 'Sistem' },
];

export default function CommandPalette({ isOpen, onClose }) {
  const [query, setQuery] = useState('');
  const [results, setResults] = useState({ menus: [], citizens: [] });
  const [loading, setLoading] = useState(false);
  const [selectedIndex, setSelectedIndex] = useState(0);
  const inputRef = useRef(null);

  useEffect(() => {
    if (isOpen) {
      setQuery('');
      setSelectedIndex(0);
      setTimeout(() => inputRef.current?.focus(), 100);

      const handleEsc = (e) => {
        if (e.key === 'Escape') onClose();
      };
      window.addEventListener('keydown', handleEsc);
      return () => window.removeEventListener('keydown', handleEsc);
    }
  }, [isOpen]);

  useEffect(() => {
    const search = async () => {
      // Laravel controller expects 'q' and query length >= 3
      if (query.length < 3) {
        const filteredMenus = menuItems.filter(item =>
          item.name.toLowerCase().includes(query.toLowerCase()) ||
          item.group.toLowerCase().includes(query.toLowerCase())
        ).slice(0, 5);
        setResults({ menus: query.length > 0 ? filteredMenus : [], citizens: [] });
        setLoading(false);
        return;
      }

      setLoading(true);
      const filteredMenus = menuItems.filter(item =>
        item.name.toLowerCase().includes(query.toLowerCase()) ||
        item.group.toLowerCase().includes(query.toLowerCase())
      );

      try {
        const response = await axios.get(route('penduduk.search'), { params: { q: query } });
        // Handle both object and array response (depending on Laravel implementation)
        const citizenData = Array.isArray(response.data) ? response.data : (response.data.data || []);

        setResults({
          menus: filteredMenus,
          citizens: citizenData
        });
      } catch (error) {
        console.error('Search error:', error);
        setResults({ menus: filteredMenus, citizens: [] });
      } finally {
        setLoading(false);
      }
    };

    const timeout = setTimeout(search, 400);
    return () => clearTimeout(timeout);
  }, [query]);

  const handleSelect = (item) => {
    if (item.type === 'menu') {
      router.visit(route(item.href));
    } else {
      router.visit(route('penduduk.show', item.id));
    }
    onClose();
  };

  const flattenedResults = [
    ...results.menus.map(m => ({ ...m, type: 'menu' })),
    ...results.citizens.map(c => ({ ...c, type: 'citizen', name: c.nama }))
  ];

  const handleKeyDown = (e) => {
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      setSelectedIndex(prev => (prev < flattenedResults.length - 1 ? prev + 1 : prev));
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      setSelectedIndex(prev => (prev > 0 ? prev - 1 : prev));
    } else if (e.key === 'Enter') {
      e.preventDefault();
      const selected = flattenedResults[selectedIndex];
      if (selected) handleSelect(selected);
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-[100] flex items-start justify-center pt-24 px-4 bg-gray-900/60 backdrop-blur-md animate-in fade-in duration-300" onClick={onClose}>
      <div
        className="w-full max-w-2xl bg-white rounded-[32px] shadow-2xl border border-gray-100 overflow-hidden flex flex-col animate-in zoom-in-95 duration-300"
        onClick={e => e.stopPropagation()}
      >
        {/* Search Input */}
        <div className="p-4 md:p-6 border-b border-gray-50 flex items-center gap-3 md:gap-4 bg-gray-50/30">
          <Search className="w-5 h-5 md:w-6 md:h-6 text-green-600 shrink-0" />
          <input
            ref={inputRef}
            type="text"
            placeholder="NIK / Nama Warga, Menu atau Data Lainnya..."
            className="flex-1 bg-transparent border-none outline-none text-base md:text-xl font-black text-gray-950 placeholder:text-gray-400 placeholder:font-bold italic"
            value={query}
            onChange={e => setQuery(e.target.value)}
            onKeyDown={handleKeyDown}
          />
          <button
            onClick={onClose}
            className="p-2 hover:bg-gray-100 rounded-xl transition-all text-gray-400"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Results area */}
        <div className="flex-1 max-h-[60vh] overflow-y-auto p-4 custom-scrollbar">
          {!query || query.length < 3 ? (
            <div className="py-12 text-center">
              <div className="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <Command className="w-8 h-8 text-green-600" />
              </div>
              <h3 className="text-sm font-black text-gray-950 uppercase tracking-[0.2em]">Pencarian Pintar SID</h3>
              <p className="text-xs text-gray-500 mt-2 font-bold uppercase tracking-tight">Ketik minimal 3 huruf untuk mulai mencari warga...</p>

              {results.menus.length > 0 && (
                <div className="mt-8 text-left max-w-md mx-auto">
                  <h4 className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 px-4">Menu Cepat</h4>
                  {results.menus.map((item, i) => (
                    <button
                      key={i}
                      onClick={() => handleSelect({ ...item, type: 'menu' })}
                      className="flex items-center gap-3 w-full p-3 hover:bg-gray-50 rounded-xl transition-all text-left group"
                    >
                      <div className="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-green-600 group-hover:border-green-200">
                        <item.icon className="w-4 h-4" />
                      </div>
                      <span className="text-xs font-bold text-gray-700">{item.name}</span>
                    </button>
                  ))}
                </div>
              )}
            </div>
          ) : loading ? (
            <div className="py-12 flex flex-col items-center justify-center gap-4">
              <Loader2 className="w-10 h-10 text-green-600 animate-spin" />
              <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest italic">Menyisir database desa...</p>
            </div>
          ) : flattenedResults.length > 0 ? (
            <div className="space-y-6">
              {/* Menus Section */}
              {results.menus.length > 0 && (
                <div>
                  <h4 className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 px-4 flex items-center justify-between">
                    MENU DASHBOARD
                    <span className="px-2 py-0.5 bg-gray-100 rounded-full text-[8px] font-black text-gray-600">{results.menus.length}</span>
                  </h4>
                  <div className="grid grid-cols-1 gap-1">
                    {results.menus.map((item, i) => {
                      const isSelected = selectedIndex === i;
                      const Icon = item.icon;
                      return (
                        <button
                          key={i}
                          onClick={() => handleSelect({ ...item, type: 'menu' })}
                          onMouseEnter={() => setSelectedIndex(i)}
                          className={cn(
                            "flex items-center gap-4 p-4 rounded-2xl text-left transition-all group",
                            isSelected ? "bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg shadow-green-100" : "hover:bg-green-50"
                          )}
                        >
                          <div className={cn(
                            "w-10 h-10 rounded-xl flex items-center justify-center shadow-sm transition-all shrink-0",
                            isSelected ? "bg-white/20 text-white" : "bg-white border border-gray-100 text-gray-400 group-hover:bg-green-600 group-hover:text-white"
                          )}>
                            <Icon className="w-5 h-5" />
                          </div>
                          <div className="flex-1 min-w-0">
                            <p className={cn("text-sm font-black uppercase tracking-tighter", isSelected ? "text-white" : "text-gray-950")}>{item.name}</p>
                            <p className={cn("text-[10px] font-bold uppercase tracking-tight", isSelected ? "text-green-100" : "text-gray-400")}>{item.group}</p>
                          </div>
                          <ArrowRight className={cn("w-4 h-4 transition-all", isSelected ? "text-white translate-x-1" : "text-gray-300 group-hover:text-green-600 group-hover:translate-x-1")} />
                        </button>
                      );
                    })}
                  </div>
                </div>
              )}

              {/* Citizens Section */}
              {results.citizens.length > 0 && (
                <div>
                  <h4 className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 px-4 flex items-center justify-between">
                    DATA PENDUDUK
                    <span className="px-2 py-0.5 bg-gray-100 rounded-full text-[8px] font-black text-gray-600">{results.citizens.length}</span>
                  </h4>
                  <div className="grid grid-cols-1 gap-1">
                    {results.citizens.map((p, i) => {
                      const absoluteIndex = results.menus.length + i;
                      const isSelected = selectedIndex === absoluteIndex;
                      return (
                        <button
                          key={i}
                          onClick={() => handleSelect({ ...p, type: 'citizen' })}
                          onMouseEnter={() => setSelectedIndex(absoluteIndex)}
                          className={cn(
                            "flex items-center gap-4 p-4 rounded-2xl text-left transition-all group",
                            isSelected ? "bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200" : "hover:bg-blue-50"
                          )}
                        >
                          <div className={cn(
                            "w-10 h-10 rounded-xl flex items-center justify-center shadow-sm transition-all shrink-0",
                            isSelected ? "bg-white/20 text-white" : "bg-white border border-gray-100 text-gray-400 group-hover:bg-blue-600 group-hover:text-white"
                          )}>
                            <Users className="w-5 h-5" />
                          </div>
                          <div className="flex-1 min-w-0">
                            <p className={cn("text-sm font-black truncate uppercase tracking-tighter", isSelected ? "text-white" : "text-gray-950")}>{p.nama}</p>
                            <p className={cn("text-[10px] font-bold font-mono", isSelected ? "text-blue-100" : "text-gray-400")}>NIK: {p.nik} • {p.dusun?.nama || 'Cibatu'}</p>
                          </div>
                          <div className="flex items-center gap-3">
                            <span className={cn("text-[8px] font-black px-2 py-0.5 rounded-full uppercase italic tracking-widest", isSelected ? "bg-white/20 text-white" : "bg-blue-100 text-blue-600")}>LIHAT DETAIL</span>
                            <ArrowRight className={cn("w-4 h-4 transition-all", isSelected ? "text-white translate-x-1" : "text-gray-300 group-hover:text-blue-600 group-hover:translate-x-1")} />
                          </div>
                        </button>
                      );
                    })}
                  </div>
                </div>
              )}
            </div>
          ) : (
            <div className="py-20 text-center">
              <AlertCircle className="w-12 h-12 text-gray-100 mx-auto mb-4" />
              <p className="text-sm font-black text-gray-400 uppercase tracking-widest">Tidak ada hasil ditemukan</p>
              <p className="text-xs text-gray-400 mt-2 font-bold uppercase tracking-tight">Gunakan NIK atau Nama yang benar...</p>
            </div>
          )}
        </div>

        {/* Footer shortcuts */}
        <div className="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-center gap-8">
          <div className="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest">
            <span className="bg-white px-2 py-1 rounded border border-gray-200 shadow-sm text-gray-700">Enter</span>
            <span>Pilih</span>
          </div>
          <div className="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest">
            <span className="bg-white px-2 py-1 rounded border border-gray-200 shadow-sm text-gray-700">↑↓</span>
            <span>Navigasi</span>
          </div>
          <div className="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest">
            <span className="bg-white px-2 py-1 rounded border border-gray-200 shadow-sm text-gray-700">ESC</span>
            <span>Tutup</span>
          </div>
        </div>
      </div>
    </div>
  );
}
