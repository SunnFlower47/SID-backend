import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import {
  History,
  Plus,
  RotateCcw,
  RefreshCw,
  Printer,
  XCircle,
  Clock,
  ShieldCheck
} from 'lucide-react';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import MutasiStats from '@/Components/Mutasi/MutasiStats';
import MutasiFilters from '@/Components/Mutasi/MutasiFilters';
import axios from 'axios';
import { format } from 'date-fns';
import { id as localeId } from 'date-fns/locale';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';

// Shared Components
import { PageHeader, TableCard, EmptyState, ActionButtons, Badge } from '@/Components/Shared';

export default function Index({ auth, mutasis, filters, stats }) {
  const [tempSearch, setTempSearch] = useState(filters.search || '');
  const [jenisFilter, setJenisFilter] = useState(filters.jenis_mutasi || 'all');
  const [isProcessing, setIsProcessing] = useState(null);

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

    const extras = [];
    if (m.jenis_mutasi === 'kematian') {
      extras.push({
        icon: Printer,
        href: route('mutasi.print-kematian', m.id),
        className: 'bg-red-50 text-red-600 hover:bg-red-600 hover:text-white',
        title: 'Cetak Surat Kematian',
        target: '_blank'
      });
    }

    const actionButtons = (
      <div className="flex items-center justify-end gap-2">
        <ActionButtons 
            viewHref={route('mutasi.data.show', m.id)}
            editHref={route('mutasi.data.edit', m.id)}
            extras={extras}
        />
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
            <span className="hidden xl:inline">{isPembaruanKK ? 'Undo KK' : isSoftDelete ? 'Undo' : 'Cancel'}</span>
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
              <Badge color="blue" size="sm">BARU</Badge>
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
                <Badge color={
                  m.jenis_mutasi === 'kematian' ? 'red' :
                  m.jenis_mutasi === 'kelahiran' ? 'blue' :
                  m.jenis_mutasi === 'pindah_masuk' ? 'green' :
                  m.jenis_mutasi === 'pindah_keluar' ? 'orange' :
                  m.jenis_mutasi === 'pindah_rt_rw' ? 'purple' : 'teal'
                }>
                  {(m.jenis_mutasi || '').replace('_', ' ')}
                </Badge>
                {m.kategori_mutasi && (
                  <span className="text-[9px] font-bold text-gray-400 uppercase mt-1">
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
        <PageHeader 
          title="Riwayat Mutasi"
          subtitle="Sinkronisasi data kependudukan Desa Cibatu"
          icon={History}
          actions={[
            {
                label: 'TAMBAH MUTASI',
                icon: Plus,
                href: route('mutasi.data.create'),
                variant: 'white'
            }
          ]}
        />

        {/* Quick Stats Grid */}
        <Deferred data="stats" fallback={<SkeletonStats />}>
          <MutasiStats stats={stats} />
        </Deferred>

        {/* Filter Section */}
        <MutasiFilters filters={filters} />

        {/* Main Table Card */}
        <Deferred data="mutasis" fallback={<SkeletonTable columns={5} rows={8} />}>
          <TableCard 
            icon={Clock}
            title="Log Aktivitas Mutasi"
            total={mutasis?.total}
            pagination={mutasis}
            noPadding
          >
            <div className="overflow-x-auto">
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
                      <td colSpan={10}>
                          <EmptyState 
                              title="Riwayat Masih Kosong"
                              message="Belum ada data mutasi warga yang terekam dalam sistem untuk kategori ini."
                          />
                      </td>
                    </tr>
                  ) : (
                    mutasis?.data?.map((m) => renderTableRow(m))
                  )}
                </tbody>
              </table>
            </div>
          </TableCard>
        </Deferred>
      </div>
    </AuthenticatedLayout>
  );
}
