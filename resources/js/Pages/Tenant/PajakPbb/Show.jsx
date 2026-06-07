import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { MapPin, User, FileText, Landmark, ArrowLeft } from 'lucide-react';
import { PageHeader, TableCard, Badge, EmptyState } from '@/Components/Shared';

export default function Show({ auth, objek }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            title={`Detail PBB - ${objek.nop}`}
        >
            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader 
                    title={`Detail PBB NOP: ${objek.nop}`} 
                    subtitle="Informasi detail wajib pajak dan histori pembayaran SPPT."
                    icon={Landmark}
                    actions={[
                        {
                            label: 'KEMBALI',
                            icon: ArrowLeft,
                            href: route('pajak-pbb.index'),
                            variant: 'white'
                        }
                    ]}
                />

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {/* Info WP */}
                    <div className="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
                        <h3 className="text-sm font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                            <User className="text-green-600" size={18} /> INFORMASI WAJIB PAJAK
                        </h3>
                        <div className="space-y-4">
                            <div>
                                <span className="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">NAMA WAJIB PAJAK</span>
                                <strong className="text-gray-900 text-lg font-black">{objek.nama_wp || '-'}</strong>
                            </div>
                            <div>
                                <span className="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">ALAMAT WP</span>
                                <span className="text-gray-700 font-medium">{objek.alamat_wp || '-'}</span>
                            </div>
                        </div>
                    </div>

                    {/* Info Objek */}
                    <div className="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
                        <h3 className="text-sm font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                            <MapPin className="text-green-600" size={18} /> INFORMASI OBJEK PAJAK
                        </h3>
                        <div className="space-y-4">
                            <div>
                                <span className="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">ALAMAT OBJEK</span>
                                <span className="text-gray-700 font-medium">{objek.alamat_objek || '-'}</span>
                            </div>
                            <div className="flex gap-8">
                                <div className="bg-green-50 p-3 rounded-2xl flex-1 border border-green-100">
                                    <span className="block text-[10px] font-bold text-green-600 uppercase tracking-wider mb-1">LUAS BUMI</span>
                                    <strong className="text-green-900 text-xl font-black">{objek.luas_bumi || 0} <span className="text-sm font-bold">m²</span></strong>
                                </div>
                                <div className="bg-blue-50 p-3 rounded-2xl flex-1 border border-blue-100">
                                    <span className="block text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1">LUAS BANGUNAN</span>
                                    <strong className="text-blue-900 text-xl font-black">{objek.luas_bangunan || 0} <span className="text-sm font-bold">m²</span></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Histori SPPT */}
                <TableCard
                    title="Histori Tagihan (SPPT)"
                    icon={FileText}
                    noPadding={true}
                >
                    {objek.tagihans && objek.tagihans.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full whitespace-nowrap text-left text-sm text-gray-600">
                                <thead className="bg-gray-50 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200">
                                    <tr>
                                        <th className="px-6 py-3 border-r border-gray-200 text-center">TAHUN</th>
                                        <th className="px-6 py-3 border-r border-gray-200 text-right">PBB TERHUTANG</th>
                                        <th className="px-6 py-3 border-r border-gray-200 text-center">JATUH TEMPO</th>
                                        <th className="px-6 py-3 border-r border-gray-200 text-center">STATUS</th>
                                        <th className="px-6 py-3 border-r border-gray-200 text-right">DENDA</th>
                                        <th className="px-6 py-3 border-r border-gray-200 text-center">TANGGAL BAYAR</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {objek.tagihans.map((tagihan) => (
                                        <tr key={tagihan.id} className="hover:bg-gray-50/50 transition-colors">
                                            <td className="px-6 py-4 text-center font-black text-gray-900 text-lg bg-gray-50/30">{tagihan.tahun}</td>
                                            <td className="px-6 py-4 text-right font-bold text-gray-800">Rp {tagihan.pbb_terhutang.toLocaleString('id-ID')}</td>
                                            <td className="px-6 py-4 text-center font-mono text-xs">{tagihan.jatuh_tempo}</td>
                                            <td className="px-6 py-4 text-center">
                                                <Badge color={tagihan.status === 'LUNAS' ? 'green' : 'red'} size="sm">
                                                    {tagihan.status}
                                                </Badge>
                                            </td>
                                            <td className="px-6 py-4 text-right font-bold text-red-500">Rp {tagihan.denda.toLocaleString('id-ID')}</td>
                                            <td className="px-6 py-4 text-center font-mono text-xs text-gray-500">{tagihan.tanggal_bayar || '-'}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <EmptyState 
                            title="Belum Ada Histori"
                            message="Histori tagihan SPPT kosong atau belum disinkronisasi."
                        />
                    )}
                </TableCard>
            </div>
        </AuthenticatedLayout>
    );
}
