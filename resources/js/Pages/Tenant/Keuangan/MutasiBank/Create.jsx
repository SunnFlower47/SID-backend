import React from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormField } from '@/Components/Shared';
import { Landmark, ArrowLeft, Save } from 'lucide-react';
import { cn } from '@/lib/utils';

export default function Create({ auth }) {
    const { data, setData, post, processing, errors } = useForm({
        tanggal_mutasi: new Date().toISOString().split('T')[0],
        jenis_mutasi: 'masuk',
        uraian: '',
        jumlah: '',
        no_bukti: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('keuangan.mutasi-bank.store'));
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Transaksi Bank">
            <div className="space-y-6 pb-20">
                <PageHeader
                    title="Tambah Transaksi"
                    subtitle="Catat setoran atau penarikan baru di rekening desa"
                    icon={Landmark}
                    backHref={route('keuangan.mutasi-bank.index')}
                />

                <div className="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <FormField.Input 
                                label="Tanggal Transaksi" 
                                type="date" 
                                required 
                                error={errors.tanggal_mutasi} 
                                value={data.tanggal_mutasi} 
                                onChange={e => setData('tanggal_mutasi', e.target.value)} 
                            />
                            
                            <div>
                                <label className="block text-[10px] font-black text-gray-500 tracking-widest uppercase mb-2">Jenis Transaksi</label>
                                <div className="grid grid-cols-2 gap-2">
                                    <button 
                                        type="button" 
                                        onClick={() => setData('jenis_mutasi', 'masuk')} 
                                        className={cn(
                                            "py-3 rounded-xl text-xs font-bold transition-all border-2", 
                                            data.jenis_mutasi === 'masuk' ? "bg-green-50 border-green-200 text-green-700 shadow-sm" : "border-gray-100 text-gray-400 hover:bg-gray-50"
                                        )}
                                    >
                                        Setoran (Masuk)
                                    </button>
                                    <button 
                                        type="button" 
                                        onClick={() => setData('jenis_mutasi', 'keluar')} 
                                        className={cn(
                                            "py-3 rounded-xl text-xs font-bold transition-all border-2", 
                                            data.jenis_mutasi === 'keluar' ? "bg-red-50 border-red-200 text-red-700 shadow-sm" : "border-gray-100 text-gray-400 hover:bg-gray-50"
                                        )}
                                    >
                                        Penarikan (Keluar)
                                    </button>
                                </div>
                                {errors.jenis_mutasi && <p className="mt-1 text-xs text-red-500">{errors.jenis_mutasi}</p>}
                            </div>
                        </div>

                        <FormField.Input 
                            label="Uraian" 
                            placeholder="Contoh: Setoran dari Pendapatan Asli Desa" 
                            required 
                            error={errors.uraian} 
                            value={data.uraian} 
                            onChange={e => setData('uraian', e.target.value)} 
                        />

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <FormField.Input 
                                label="Nominal (Rp)" 
                                type="number" 
                                min="0" 
                                placeholder="0" 
                                required 
                                error={errors.jumlah} 
                                value={data.jumlah} 
                                onChange={e => setData('jumlah', e.target.value)} 
                            />
                            
                            <FormField.Input 
                                label="Nomor Bukti" 
                                placeholder="Contoh: BKT-001 (Opsional)" 
                                error={errors.no_bukti} 
                                value={data.no_bukti} 
                                onChange={e => setData('no_bukti', e.target.value)} 
                            />
                        </div>

                        <div className="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                            <Link 
                                href={route('keuangan.mutasi-bank.index')}
                                className="px-6 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-100 transition-colors text-xs tracking-widest uppercase"
                            >
                                Batal
                            </Link>
                            <button 
                                type="submit" 
                                disabled={processing} 
                                className="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-xs tracking-widest uppercase shadow-lg shadow-green-900/20 transition-all disabled:opacity-50 flex items-center gap-2"
                            >
                                <Save className="w-4 h-4" />
                                Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
