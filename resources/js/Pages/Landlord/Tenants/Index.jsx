import { Head, Link, usePage, router } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, TableCard, DataTable, Badge } from '@/Components/Shared';
import { Building2, Plus, Edit, Trash2 } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Index({ tenants }) {
    const { flash } = usePage().props;

    const handleDeactivate = (e, tenantId, tenantName) => {
        e.preventDefault();
        Swal.fire({
            title: 'Nonaktifkan Desa?',
            text: `Akses ke website warga dan admin panel desa "${tenantName}" akan ditutup sementara.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Nonaktifkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('tenants.destroy', tenantId), {
                    preserveScroll: true,
                    onSuccess: () => Swal.fire('Berhasil', 'Desa berhasil dinonaktifkan.', 'success')
                });
            }
        });
    };

    const handleHardDelete = (e, tenantId, tenantName) => {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus Desa Permanen?',
            text: `PERHATIAN: Seluruh database, tabel kependudukan, arsip surat, dan data operator desa "${tenantName}" akan DIHAPUS PERMANEN dari server!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Lanjut Hapus!',
            cancelButtonText: 'Batal',
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Verifikasi Keamanan',
                    text: `Ketikkan ID/Slug desa "${tenantId}" di bawah untuk mengonfirmasi penghapusan permanen:`,
                    input: 'text',
                    inputPlaceholder: tenantId,
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Hapus Permanen!',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Anda harus mengetikkan ID desa untuk melanjutkan!';
                        }
                        if (value.toLowerCase() !== tenantId.toLowerCase()) {
                            return 'ID desa yang dimasukkan tidak cocok!';
                        }
                    }
                }).then((secondResult) => {
                    if (secondResult.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Sedang menghapus database desa, mohon tunggu.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        router.delete(route('tenants.hard-delete', tenantId), {
                            onError: (errors) => {
                                Swal.fire('Gagal!', errors.error || 'Terjadi kesalahan saat menghapus desa.', 'error');
                            },
                            onSuccess: () => {
                                Swal.fire('Terhapus!', 'Desa berhasil dihapus permanen.', 'success');
                            }
                        });
                    }
                });
            }
        });
    };

    const columns = [
        {
            header: 'ID / Slug',
            accessor: 'id',
            className: 'font-bold text-slate-900 text-center',
        },
        {
            header: 'Nama Desa',
            accessor: 'name',
            className: 'font-medium text-slate-700 text-center',
        },
        {
            header: 'Domain Utama (Web)',
            className: 'text-center',
            render: (row) => (
                <div className="flex flex-col items-center gap-1">
                    {row.domains?.map(d => (
                        <a 
                            key={d.id} 
                            href={`http://${d.domain}`} 
                            target="_blank" 
                            rel="noreferrer"
                            className="text-indigo-600 hover:text-indigo-900 hover:underline font-bold text-xs flex items-center gap-1"
                        >
                            {d.domain} ↗
                        </a>
                    ))}
                    {(!row.domains || row.domains.length === 0) && (
                        <span className="text-gray-400 text-xs italic">Tidak ada domain</span>
                    )}
                </div>
            )
        },
        {
            header: 'Status',
            className: 'text-center',
            render: (row) => (
                <div className="flex justify-center">
                    <Badge 
                        color={row.is_active ? 'green' : 'red'}
                        dot={row.is_active ? 'green' : 'red'}
                    >
                        {row.is_active ? 'Aktif' : 'Nonaktif'}
                    </Badge>
                </div>
            )
        },
        {
            header: 'Tgl Registrasi',
            className: 'text-center text-xs text-gray-500',
            render: (row) => new Date(row.created_at).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            })
        },
        {
            header: 'Aksi',
            className: 'text-center',
            render: (row) => (
                <div className="flex items-center justify-center gap-2">
                    <Link 
                        href={route('tenants.edit', row.id)} 
                        className="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 text-slate-650 rounded-xl text-xs font-bold transition-all"
                    >
                        <Edit className="w-3.5 h-3.5" />
                        Edit
                    </Link>
                    {row.is_active ? (
                        <button 
                            onClick={(e) => handleDeactivate(e, row.id, row.name)}
                            className="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-amber-50 hover:text-amber-700 text-slate-650 rounded-xl text-xs font-bold transition-all cursor-pointer"
                        >
                            <Trash2 className="w-3.5 h-3.5 text-amber-500" />
                            Nonaktifkan
                        </button>
                    ) : (
                        <button 
                            onClick={(e) => handleHardDelete(e, row.id, row.name)}
                            className="inline-flex items-center gap-1 px-3 py-1.5 bg-red-550/10 hover:bg-red-600 hover:text-white text-red-700 rounded-xl text-xs font-bold transition-all cursor-pointer"
                        >
                            <Trash2 className="w-3.5 h-3.5" />
                            Hapus Permanen
                        </button>
                    )}
                </div>
            )
        }
    ];

    const actions = [
        {
            label: 'Tambah Desa Baru',
            icon: Plus,
            href: route('tenants.create'),
            variant: 'white'
        }
    ];

    return (
        <LandlordLayout>
            <Head title="Manajemen Desa" />

            <div className="space-y-8">
                {/* Header */}
                <PageHeader 
                    icon={Building2}
                    title="Manajemen Desa"
                    subtitle="Kelola seluruh tenant desa digital yang terdaftar pada ekosistem SaaS."
                    actions={actions}
                    gradient="from-indigo-600 via-indigo-700 to-indigo-800"
                />

                {flash?.success && (
                    <div className="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-bold shadow-sm" role="alert">
                        <span className="block sm:inline">{flash.success}</span>
                    </div>
                )}

                {/* Table Card */}
                <TableCard
                    title="Daftar Desa Terdaftar"
                    icon={Building2}
                    total={tenants.total}
                    totalLabel="Desa"
                    pagination={tenants}
                    noPadding
                >
                    <DataTable 
                        columns={columns}
                        data={tenants.data}
                        borderedBody={true}
                    />
                </TableCard>
            </div>
        </LandlordLayout>
    );
}
