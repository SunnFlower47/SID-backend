import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, EmptyState, ActionButtons, Pagination } from '@/Components/Shared';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Users, Plus, Search, MapPin, GraduationCap } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Index({ auth, anggotas, filters }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [status, setStatus] = useState(filters?.status || '');
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        const removeStart = router.on('start', () => setIsLoading(true));
        const removeFinish = router.on('finish', () => setIsLoading(false));
        return () => {
            removeStart();
            removeFinish();
        };
    }, []);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('sekretariat.anggota-bpd.index'), { search, status }, { preserveState: true });
    };

    const handleDelete = (item) => {
        Swal.fire({
            title: 'Hapus Data Anggota BPD?',
            text: `Anda yakin ingin menghapus data anggota BPD ${item.nama}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl font-bold',
                cancelButton: 'rounded-xl font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('sekretariat.anggota-bpd.destroy', item.id));
            }
        });
    };

    const StatusBadge = ({ status }) => {
        const isAktif = status === 'aktif' || !status;
        return (
            <span className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold ${
                isAktif
                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-100'
                    : 'bg-gray-100 text-gray-500 border border-gray-200'
            }`}>
                <span className={`w-1.5 h-1.5 rounded-full ${isAktif ? 'bg-emerald-500' : 'bg-gray-400'}`} />
                {isAktif ? 'Aktif' : 'Purna Tugas'}
            </span>
        );
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Buku Data Anggota BPD">
            <Head title="Data Anggota BPD" />
            
            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader
                    icon={Users}
                    title="Buku Data Anggota BPD"
                    subtitle="Pengelolaan data Buku Anggota Badan Permusyawaratan Desa (Lampiran IX Permendagri 47/2016)"
                    actions={[
                        { label: 'Tambah Anggota', icon: Plus, href: route('sekretariat.anggota-bpd.create'), variant: 'white' },
                    ]}
                />

                <form onSubmit={handleSearch} className="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-4 items-end mb-6">
                    <div className="flex-1 w-full space-y-2 text-left">
                        <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Pencarian</label>
                        <div className="relative">
                            <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input
                                type="text"
                                placeholder="Cari nama, NIK, atau jabatan..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                            />
                        </div>
                    </div>
                    <div className="w-full sm:w-64 space-y-2 text-left">
                        <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Status</label>
                        <select
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                            className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                        >
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Purna Tugas / Tidak Aktif</option>
                        </select>
                    </div>
                    <button type="submit" className="flex items-center justify-center gap-2 w-full sm:w-auto px-8 py-3 bg-green-600 text-white rounded-2xl text-[10px] font-black hover:bg-green-700 active:scale-95 transition-all uppercase tracking-widest shadow-md shadow-green-200">
                        <Search className="w-3.5 h-3.5" /> CARI
                    </button>
                </form>

                {isLoading ? (
                    <SkeletonTable rows={5} columns={5} />
                ) : (
                <TableCard
                    icon={Users}
                    title="Daftar Anggota BPD"
                    total={anggotas.total}
                    totalLabel="Anggota"
                >
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm text-left">
                            <thead className="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th className="px-4 py-4 font-black">Identitas Anggota</th>
                                    <th className="px-4 py-4 font-black">Jabatan</th>
                                    <th className="px-4 py-4 font-black">Alamat</th>
                                    <th className="px-4 py-4 font-black">Status</th>
                                    <th className="px-4 py-4 font-black text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100">
                                {anggotas.data.length > 0 ? (
                                    anggotas.data.map((item) => (
                                        <tr key={item.id} className="hover:bg-blue-50/30 transition-colors">
                                            <td className="px-4 py-4">
                                                <Link href={route('sekretariat.anggota-bpd.show', item.id)} className="font-bold text-gray-900 hover:text-blue-600 transition-colors">
                                                    {item.nama}
                                                </Link>
                                                <div className="text-xs text-gray-500 mt-1 flex items-center gap-2">
                                                    {item.nik && <span className="font-mono">{item.nik}</span>}
                                                    {item.nik && <span>&bull;</span>}
                                                    <span>({item.jenis_kelamin === 'L' ? 'L' : 'P'})</span>
                                                    <span>&bull;</span>
                                                    <span className="flex items-center gap-1"><GraduationCap className="w-3 h-3"/> {item.pendidikan_terakhir}</span>
                                                </div>
                                            </td>
                                            <td className="px-4 py-4">
                                                <span className="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold uppercase tracking-wide">
                                                    {item.jabatan}
                                                </span>
                                            </td>
                                            <td className="px-4 py-4">
                                                <div className="flex items-start gap-1.5 max-w-xs">
                                                    <MapPin className="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                                                    <span className="text-gray-600 line-clamp-2">{item.alamat}
                                                        {item.rt && `, RT ${item.rt}`}
                                                        {item.rw && `/RW ${item.rw}`}
                                                        {item.dusun && ` ${item.dusun}`}
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="px-4 py-4">
                                                <StatusBadge status={item.status} />
                                            </td>
                                            <td className="px-4 py-4 text-right">
                                                <ActionButtons
                                                    viewHref={route('sekretariat.anggota-bpd.show', item.id)}
                                                    editHref={route('sekretariat.anggota-bpd.edit', item.id)}
                                                    onDelete={() => handleDelete(item)}
                                                />
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-12">
                                            <EmptyState
                                                icon={Users}
                                                title="Belum Ada Data BPD"
                                                description="Silakan tambah data anggota Badan Permusyawaratan Desa."
                                            />
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {anggotas.links && anggotas.links.length > 3 && (
                        <div className="p-4 border-t border-gray-100 flex justify-center bg-gray-50/50 rounded-b-2xl">
                            <Pagination 
                                links={anggotas.links} 
                                from={anggotas.from} 
                                to={anggotas.to} 
                                total={anggotas.total} 
                            />
                        </div>
                    )}
                </TableCard>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
