import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import {
  History,
  Search,
  Plus,
  Eye,
  RotateCcw,
  Baby,
  UserX,
  MapPin,
  Split,
  ChevronLeft,
  ChevronRight,
  RefreshCw,
  Printer,
  XCircle,
  Edit,
  Filter,
  FileSpreadsheet,
  Clock,
  ShieldCheck
} from 'lucide-react';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import Pagination from '@/Components/Shared/Pagination';
import MutasiStats from '@/Components/Mutasi/MutasiStats';
import MutasiFilters from '@/Components/Mutasi/MutasiFilters';
import axios from 'axios';
import { format } from 'date-fns';
import { id as localeId } from 'date-fns/locale';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';
a
const LottieComponent = Lottie?.default || Lottie;

export default function Index({ auth, mutasis, filters, stats }) {
  const [search, setSearch] = useState(filters.search || '');
  const [tempSearch, setTempSearch] = useState(filters.search || '');
  const [jenisFilter, setJenisFilter] = useState(filters.jenis_mutasi || 'all');
  const [isProcessing, setIsProcessing] = useState(null);
  const [showFilters, setShowFilters] = useState(filters.search || filters.jenis_mutasi ? true : false);

  const handleSearch = () => {
    router.get(route('mutasi.data.index'), { search: tempSearch, jenis_mutasi: jenisFilter === 'all' ? '' : jenisFilter }, { preserveState: true, replace: true });
  };

  const handleFilterChange = (type) => {
    setJenisFilter(type);
  };

  const handleAction = (m) => {
    // Gunakan attribute dari backend (bukan hardcode list)
    const isSoftDelete = m.is_soft_delete_type ?? false;
    const isPembaruanKK = m.is_pembaruan_kk ?? false;

    const actionLabel = isPembaruanKK
      ? 'Undo Pembaruan KK'
      : isSoftDelete ? 'Undo (Kembalikan Data)' : 'Cancel (Batalkan Mutasi)';
    const confirmMsg = isPembaruanKK
      ? `Kedudukan ${m.penduduk?.nama} akan dikembalikan ke status sebelumnya dan KK akan kembali berstatus bermasalah.`
      : isSoftDelete
        ? `Apakah Anda yakin ingin melakukan Undo pada mutasi ${m.penduduk?.nama}? Data penduduk akan dikembalikan ke status aktif.`
        : `Apakah Anda yakin ingin membatalkan mutasi ${m.penduduk?.nama}? Data yang baru dibuat akan dihapus secara permanen.`;

    Swal.fire({
      title: isPembaruanKK ? 'UNDO PEMBARUAN KK' : isSoftDelete ? 'UNDO MUTASI' : 'BATALKAN MUTASI',
      html: `${confirmMsg}<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini akan memproses ulang data kependudukan</small>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: isSoftDelete ? '#10b981' : '#ef4444',
      cancelButtonColor: '#f3f4f6',
      confirmButtonText: `YA, ${isSoftDelete ? 'UNDO SEKARANG' : 'BATALKAN'}!`,
      cancelButtonText: 'KEMBALI',
      background: '#ffffff',
      customClass: {
        popup: 'rounded-3xl border-none shadow-2xl',
        title: `font-black tracking-tighter uppercase italic ${isSoftDelete ? 'text-emerald-600' : 'text-rose-600'}`,
        confirmButton: `rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg ${isSoftDelete ? 'shadow-emerald-200' : 'shadow-rose-200'}`,
        cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
      }
    }).then(async (result) => {
      if (result.isConfirmed) {
        setIsProcessing(m.id);
        try {
          const response = isSoftDelete
            ? await axios.post(route('mutasi.undo', m.id))
            : await axios.delete(route('mutasi.cancel', m.id));

          if (response.data.success) {
            Swal.fire({
              icon: 'success',
              title: 'BERHASIL!',
              text: response.data.message || (actionLabel + ' berhasil'),
              showConfirmButton: true,
              confirmButtonColor: '#10b981',
              customClass: {
                popup: 'rounded-3xl shadow-2xl',
                title: 'font-black uppercase italic tracking-tighter'
              }
            });
            router.reload();
          }
        } catch (error) {
          Swal.fire({
            icon: 'error',
            title: 'TERJADI KESALAHAN!',
            text: error.response?.data?.message || 'Gagal memproses permintaan',
            customClass: { popup: 'rounded-3xl' }
          });
        } finally {
          setIsProcessing(null);
        }
      }
    });
  };

  const renderTableHead = () => {
    switch (jenisFilter) {
      case 'kematian':
        return (
          <>
            <th className="px-8 py-4">Penduduk</th>
            <th className="px-6 py-4">Tgl Meninggal</th>
            <th className="px-6 py-4">Hari / Jam</th>
            <th className="px-6 py-4">Tempat</th>
            <th className="px-6 py-4">Penyebab</th>
          </>
        );
      case 'kelahiran':
        return (
          <>
            <th className="px-8 py-4">Nama Bayi</th>
            <th className="px-6 py-4">Jenis Kelamin</th>
            <th className="px-6 py-4">Tgl Lahir</th>
            <th className="px-6 py-4">Orang Tua</th>
            <th className="px-6 py-4">No KK</th>
          </>
        );
      case 'pindah_rt_rw':
        return (
          <>
            <th className="px-8 py-4">No KK</th>
            <th className="px-6 py-4">Asal (RT/RW)</th>
            <th className="px-6 py-4">Tujuan (RT/RW)</th>
            <th className="px-6 py-4">Tgl Pindah</th>
            <th className="px-6 py-4">Alasan</th>
          </>
        );
      default:
        return (
          <>
            <th className="px-8 py-4">Tanggal</th>
            <th className="px-6 py-4">Warga Terkait</th>
            <th className="px-6 py-4 w-48">Jenis Mutasi</th>
            <th className="px-6 py-4 w-48">Asal / Tujuan</th>
            <th className="px-6 py-4">Alasan</th>
          </>
        );
    }
  };

  const renderTableRow = (m) => {
    // Gunakan attribute dari backend
    const isSoftDelete = m.is_soft_delete_type ?? false;
    const isPembaruanKK = m.is_pembaruan_kk ?? false;

    const isUndoBlocked = m.is_undo_blocked ?? false;

    const actionButtons = (
      <div className="flex items-center justify-end gap-2">
        {m.jenis_mutasi === 'kematian' && (
          <a
            href={route('mutasi.print-kematian', m.id)}
            target="_blank"
            className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm active:scale-95"
            title="Cetak Surat Kematian"
          >
            <Printer className="w-4 h-4" />
          </a>
        )}
        <Link
          href={route('mutasi.data.show', m.id)}
          className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm active:scale-95"
          title="Lihat Detail"
        >
          <Eye className="w-4 h-4" />
        </Link>
        <Link
          href={route('mutasi.data.edit', m.id)}
          className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-all shadow-sm active:scale-95"
          title="Edit Data"
        >
          <Edit className="w-4 h-4" />
        </Link>
        {isUndoBlocked ? (
          <div className="relative group">
            <span
              className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-400 font-black text-[9px] uppercase tracking-wider cursor-not-allowed opacity-60"
              title="KK sudah diselesaikan permanen — Undo terkunci"
            >
              <ShieldCheck className="w-3 h-3" />
              Terkunci
            </span>
          </div>
        ) : (
          <button
            onClick={() => handleAction(m)}
            disabled={isProcessing === m.id}
            className={cn(
              "flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all font-black text-[9px] uppercase tracking-wider active:scale-95 shadow-sm",
              isSoftDelete
                ? "bg-green-50 text-green-600 hover:bg-green-600 hover:text-white"
                : "bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white",
              isProcessing === m.id && "opacity-50 cursor-not-allowed"
            )}
            title={isPembaruanKK ? "Undo Pembaruan KK" : isSoftDelete ? "Undo / Kembalikan Data" : "Cancel / Batalkan"}
          >
            {isProcessing === m.id ? (
              <RefreshCw className="w-3 h-3 animate-spin" />
            ) : isSoftDelete ? (
              <RotateCcw className="w-3 h-3" />
            ) : (
              <XCircle className="w-3 h-3" />
            )}
            {isPembaruanKK ? 'Undo KK' : isSoftDelete ? 'Undo' : 'Cancel'}
          </button>
        )}
      </div>
    );

    switch (jenisFilter) {
      case 'kematian':
        const deathData = m.data_kematian || {};
        return (
          <tr key={m.id} className="hover:bg-red-50/20 transition-colors group border-b border-gray-50">
            <td className="px-8 py-4">
              <p className="text-sm font-bold text-gray-900">{m.penduduk?.nama || <span className="text-red-500">Data Terhapus</span>}</p>
              <p className="text-[10px] font-mono text-gray-400">{m.penduduk?.nik || '-'}</p>
            </td>
            <td className="px-6 py-4 text-xs font-bold text-gray-600">
              {m.tanggal_mutasi ? format(new Date(m.tanggal_mutasi), 'dd MMM yyyy', { locale: localeId }) : '-'}
            </td>
            <td className="px-6 py-4">
              <p className="text-xs font-black text-gray-700">{deathData.hari || '-'}</p>
              <p className="text-[10px] text-gray-400 font-bold uppercase">{deathData.jam || '-'}</p>
            </td>
            <td className="px-6 py-4 text-xs text-gray-500 font-medium italic">
              {deathData.bertempat_di || '-'}
            </td>
            <td className="px-6 py-4">
              <p className="w-[150px] sm:w-[200px] text-xs text-gray-500 truncate" title={m.alasan}>
                {m.alasan || '-'}
              </p>
            </td>
            <td className="px-8 py-4 text-right">{actionButtons}</td>
          </tr>
        );
      case 'kelahiran':
        return (
          <tr key={m.id} className="hover:bg-blue-50/20 transition-colors group border-b border-gray-50">
            <td className="px-8 py-4">
              <p className="text-sm font-bold text-gray-900">{m.penduduk?.nama || '-'}</p>
              <span className="px-2 py-0.5 bg-blue-50 text-blue-600 rounded-full text-[9px] font-black uppercase">BARU</span>
            </td>
            <td className="px-6 py-4 text-xs font-bold text-gray-600">{m.penduduk?.jenis_kelamin || '-'}</td>
            <td className="px-6 py-4 text-xs text-gray-600">
              {m.penduduk?.tanggal_lahir ? format(new Date(m.penduduk.tanggal_lahir), 'dd MMM yyyy', { locale: localeId }) : '-'}
            </td>
            <td className="px-6 py-4">
              <p className="text-[10px] text-gray-400 font-black">AYAH: {m.penduduk?.nama_ayah || '-'}</p>
              <p className="text-[10px] text-gray-400 font-black">IBU: {m.penduduk?.nama_ibu || '-'}</p>
            </td>
            <td className="px-6 py-4 text-xs font-mono font-bold text-gray-500">{m.penduduk?.nkk || '-'}</td>
            <td className="px-8 py-4 text-right">{actionButtons}</td>
          </tr>
        );
      case 'pindah_rt_rw':
        const [asal, tujuan] = (m.asal_tujuan || '').split(' ? ');
        return (
          <tr key={m.id} className="hover:bg-purple-50/20 transition-colors group border-b border-gray-50">
            <td className="px-8 py-4 text-sm font-bold text-gray-900 font-mono">{m.penduduk?.nkk || '-'}</td>
            <td className="px-6 py-4 text-xs font-bold text-purple-600">{asal || '-'}</td>
            <td className="px-6 py-4 text-xs font-bold text-blue-600">{tujuan || '-'}</td>
            <td className="px-6 py-4 text-xs text-gray-600">
              {m.tanggal_mutasi ? format(new Date(m.tanggal_mutasi), 'dd MMM yyyy', { locale: localeId }) : '-'}
            </td>
            <td className="px-6 py-4">
              <p className="w-[150px] sm:w-[200px] text-xs text-gray-500 italic truncate" title={m.alasan}>
                "{m.alasan || '-'}"
              </p>
            </td>
            <td className="px-8 py-4 text-right">{actionButtons}</td>
          </tr>
        );
      default:
        return (
          <tr key={m.id} className="hover:bg-gray-50 transition-colors group border-b border-gray-50">
            <td className="px-8 py-4">
              <p className="text-xs font-black text-gray-900">
                {m.tanggal_mutasi ? format(new Date(m.tanggal_mutasi), 'dd MMM yyyy', { locale: localeId }) : '-'}
              </p>
            </td>
            <td className="px-6 py-4">
              <p className="text-sm font-bold text-gray-900">
                {m.penduduk?.nama || <span className="text-red-500">Data Terhapus</span>}
              </p>
              <p className="text-[10px] font-mono text-gray-400">{m.penduduk?.nik || '-'}</p>
            </td>
            <td className="px-6 py-4">
              <div className="flex flex-col items-start gap-1">
                <span className={cn(
                  "px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider inline-block",
                  m.jenis_mutasi === 'kematian' ? "bg-red-50 text-red-600" :
                    m.jenis_mutasi === 'kelahiran' ? "bg-blue-50 text-blue-600" :
                      m.jenis_mutasi === 'pindah_masuk' ? "bg-green-50 text-green-600" :
                        m.jenis_mutasi === 'pindah_keluar' ? "bg-orange-50 text-orange-600" :
                          m.jenis_mutasi === 'pindah_rt_rw' ? "bg-purple-50 text-purple-600" :
                            "bg-teal-50 text-teal-600"
                )}>
                  {(m.jenis_mutasi || '').replace('_', ' ')}
                </span>
                {m.kategori_mutasi && (
                  <span className="text-[9px] font-bold text-gray-400 uppercase">
                    {m.kategori_mutasi.replace('_', ' ')}
                  </span>
                )}
              </div>
            </td>
            <td className="px-6 py-4">
              <p className="w-[150px] sm:w-[200px] text-xs font-bold text-gray-600 truncate" title={m.asal_tujuan}>
                {m.asal_tujuan || '-'}
              </p>
            </td>
            <td className="px-6 py-4">
              <p className="w-[150px] sm:w-[200px] text-xs text-gray-500 italic truncate" title={m.alasan}>
                {m.alasan ? `"${m.alasan}"` : '-'}
              </p>
            </td>
            <td className="px-8 py-4 text-right">{actionButtons}</td>
          </tr>
        );
    }
  };

  return (
    <AuthenticatedLayout user={auth.user} title="Riwayat Mutasi">
      <Head title="Riwayat Mutasi" />

      <div className="space-y-5 sm:space-y-6 animate-in fade-in duration-700 pb-20">

        {/* 1. CONSISTENT PREMIUM HEADER */}
        <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
          <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
          <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div className="flex items-center space-x-4">
              <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                <History className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
              </div>
              <div>
                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Riwayat Mutasi</h1>
                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">Sinkronisasi data kependudukan Desa Cibatu</p>
              </div>
            </div>
            <div className="flex flex-wrap gap-2 sm:gap-3">
              <div className="hidden sm:flex items-center px-5 py-2.5 bg-green-500/30 backdrop-blur-md border border-green-400/30 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-inner">
                Total: {mutasis?.total || 0}
              </div>
              <Link
                href={route('mutasi.data.create')}
                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest"
              >
                <Plus className="w-4 h-4 mr-2" />
                TAMBAH
              </Link>
            </div>
          </div>
        </div>

        {/* Quick Stats Grid */}
        <Deferred data="stats" fallback={<SkeletonStats />}>
          <MutasiStats stats={stats} />
        </Deferred>

        {/* Filter Section */}
        <MutasiFilters filters={filters} />

        {/* Main Table Card */}
        <Deferred data="mutasis" fallback={<SkeletonTable columns={5} rows={8} />}>
          <div className="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden flex flex-col min-h-[600px]">
            {/* Table Header Section */}
            <div className="p-8 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
              <h3 className="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3">
                <Clock className="w-6 h-6 text-green-600" />
                Log Aktivitas Mutasi
              </h3>
              <div className="flex items-center gap-2">
                <span className="px-4 py-1.5 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest">
                  {mutasis?.total || 0} Records
                </span>
              </div>
            </div>

            {/* Table Content */}
            <div className="flex-1 overflow-x-auto">
              <table className="w-full text-left border-collapse">
                <thead className="bg-gray-50/50 border-b border-gray-50">
                  <tr className="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                    {renderTableHead()}
                    <th className="px-8 py-5 text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-50">
                  {mutasis?.data?.length === 0 ? (
                    <tr>
                      <td colSpan={10} className="py-24 text-center">
                        <div className="flex flex-col items-center gap-6">
                          <div className="w-56 h-56 mx-auto opacity-80">
                            <LottieComponent animationData={noDataAnimation} loop={true} />
                          </div>
                          <div className="space-y-2">
                            <h3 className="text-2xl font-black text-gray-900 uppercase italic tracking-tighter">Riwayat Masih Kosong</h3>
                            <p className="text-xs text-gray-500 max-w-xs mx-auto font-bold uppercase tracking-widest leading-relaxed px-4">
                              Belum ada data mutasi warga yang terekam dalam sistem untuk kategori ini.
                            </p>
                          </div>
                        </div>
                      </td>
                    </tr>
                  ) : (
                    mutasis?.data?.map((m) => renderTableRow(m))
                  )}
                </tbody>
              </table>
            </div>

            {/* Pagination Component */}
            <div className="p-6 border-t border-gray-50 bg-gray-50/30">
              <Pagination
                links={mutasis?.links}
                from={mutasis?.from}
                to={mutasis?.to}
                total={mutasis?.total}
              />
            </div>
          </div>
        </Deferred>
      </div>
    </AuthenticatedLayout>
  );
}
