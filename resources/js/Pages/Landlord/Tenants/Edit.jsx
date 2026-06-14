import { Head, Link, useForm, router } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { Building2, Edit, AlertTriangle } from 'lucide-react';
import Swal from 'sweetalert2';

export default function EditPage({ tenant }) {
    const { data, setData, put, processing, errors } = useForm({
        name: tenant.name,
        is_active: tenant.is_active,
    });

    const submit = (e) => {
        e.preventDefault();
        put(route('tenants.update', tenant.id));
    };

    const handleHardDelete = () => {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: `Seluruh database kependudukan, arsip surat, dan data operator desa "${tenant.name}" akan dihapus secara permanen dari server!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Lanjut Hapus!',
            cancelButtonText: 'Batal',
            focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed) {
                // Lapis Kedua: Verifikasi dengan mengetik slug desa
                Swal.fire({
                    title: 'Verifikasi Keamanan',
                    text: `Ketikkan ID/Slug desa "${tenant.id}" di bawah untuk mengonfirmasi penghapusan permanen:`,
                    input: 'text',
                    inputPlaceholder: tenant.id,
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Hapus Permanen!',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Anda harus mengetikkan ID desa untuk melanjutkan!';
                        }
                        if (value.toLowerCase() !== tenant.id.toLowerCase()) {
                            return 'ID desa yang dimasukkan tidak cocok!';
                        }
                    }
                }).then((secondResult) => {
                    if (secondResult.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses Penghapusan...',
                            text: 'Silakan tunggu beberapa saat, server sedang menghapus database desa.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        router.delete(route('tenants.hard-delete', tenant.id), {
                            onError: (errors) => {
                                Swal.fire('Gagal!', errors.error || 'Terjadi kesalahan saat menghapus desa.', 'error');
                            },
                            onSuccess: () => {
                                Swal.close();
                            }
                        });
                    }
                });
            }
        });
    };

    return (
        <LandlordLayout>
            <Head title={`Edit Desa ${tenant.name}`} />

            <div className="space-y-8">
                {/* Header */}
                <PageHeader 
                    icon={Building2}
                    title={`Edit Desa: ${tenant.name}`}
                    subtitle="Ubah informasi dasar atau status aktifasi desa."
                    backHref={route('tenants.index')}
                    gradient="from-indigo-600 via-indigo-700 to-indigo-800"
                />

                <form onSubmit={submit} className="space-y-6">
                    <FormCard 
                        icon={Edit}
                        title="Detail Informasi Desa"
                    >
                        <div className="space-y-6">
                            <FormField.Input 
                                label="ID / Slug (Tidak bisa diubah)"
                                value={tenant.id}
                                disabled
                                className="bg-gray-100/50"
                                readOnly
                            />

                            <FormField.Input 
                                label="Nama Desa"
                                value={data.name}
                                onChange={e => setData('name', e.target.value)}
                                error={errors.name}
                                required
                            />

                            <div className="flex items-center ml-1">
                                <input 
                                    type="checkbox" 
                                    id="is_active"
                                    checked={data.is_active}
                                    onChange={e => setData('is_active', e.target.checked)}
                                    className="w-4 h-4 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500"
                                />
                                <label htmlFor="is_active" className="ml-2.5 text-sm font-bold text-slate-700">
                                    Aktif (Bisa diakses oleh operator dan warga)
                                </label>
                            </div>
                        </div>
                    </FormCard>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <Link 
                            href={route('tenants.index')} 
                            className="inline-flex justify-center py-3 px-6 rounded-2xl border border-gray-200 text-sm font-bold text-gray-500 hover:bg-gray-100 transition-colors"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex justify-center py-3 px-8 border border-transparent shadow-lg shadow-indigo-600/20 text-sm font-bold rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all disabled:opacity-50"
                        >
                            {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                        </button>
                    </div>
                </form>

                {/* Zona Bahaya */}
                <div className="mt-12 pt-8 border-t border-red-100">
                    <FormCard 
                        icon={AlertTriangle}
                        title="Zona Bahaya (Danger Zone)"
                    >
                        <div className="space-y-4">
                            <div>
                                <h4 className="text-sm font-bold text-red-700">Hapus Desa Secara Permanen</h4>
                                <p className="text-xs text-gray-500 mt-1">
                                    Tindakan ini tidak bisa dibatalkan. Menghapus desa ini akan melenyapkan seluruh database kependudukan, arsip, file surat, dan semua record terkait secara permanen dari server.
                                </p>
                            </div>
                            <button
                                type="button"
                                onClick={handleHardDelete}
                                className="inline-flex justify-center py-2.5 px-5 border border-transparent text-xs font-bold rounded-xl text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-500/20 transition-all shadow-md shadow-red-600/10"
                            >
                                Hapus Desa Permanen
                            </button>
                        </div>
                    </FormCard>
                </div>
            </div>
        </LandlordLayout>
    );
}
