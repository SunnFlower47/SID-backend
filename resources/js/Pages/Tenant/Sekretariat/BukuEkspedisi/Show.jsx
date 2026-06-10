import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/Shared';
import { Send, Calendar, Hash, FileText, User, MapPin, AlignLeft, ArrowLeft } from 'lucide-react';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

dayjs.locale('id');

export default function Show({ bukuEkspedisi }) {
    const DetailRow = ({ icon: Icon, label, value, className = '' }) => (
        <div className={`flex items-start gap-4 p-4 hover:bg-gray-50/50 transition-colors ${className}`}>
            <div className="mt-1 bg-blue-50 p-2 rounded-xl text-blue-600 shrink-0">
                <Icon className="w-5 h-5" />
            </div>
            <div>
                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{label}</p>
                <div className="text-sm font-medium text-gray-900 whitespace-pre-line leading-relaxed">
                    {value || '-'}
                </div>
            </div>
        </div>
    );

    return (
        <AuthenticatedLayout title="Detail Ekspedisi">
            <Head title={`Ekspedisi: ${bukuEkspedisi.nomor_surat}`} />

            <div className="space-y-6 pb-20">
                <PageHeader
                    icon={Send}
                    title="Detail Buku Ekspedisi"
                    subtitle="Informasi lengkap catatan pengiriman surat/barang"
                    backHref={route('sekretariat.buku-ekspedisi.index')}
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div className="lg:col-span-2 space-y-6">
                        {/* Informasi Utama */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center gap-4">
                                <div className="w-12 h-12 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center shrink-0">
                                    <FileText className="w-6 h-6 text-blue-600" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-gray-900 text-lg">Informasi Surat</h3>
                                    <p className="text-xs text-gray-500">Rincian surat yang dikirimkan</p>
                                </div>
                            </div>
                            <div className="divide-y divide-gray-50">
                                <DetailRow 
                                    icon={Hash} 
                                    label="Nomor Surat" 
                                    value={bukuEkspedisi.nomor_surat} 
                                />
                                <DetailRow 
                                    icon={Calendar} 
                                    label="Tanggal Surat" 
                                    value={dayjs(bukuEkspedisi.tanggal_surat).format('DD MMMM YYYY')} 
                                />
                                <DetailRow 
                                    icon={AlignLeft} 
                                    label="Isi Singkat / Perihal" 
                                    value={bukuEkspedisi.isi_singkat} 
                                />
                            </div>
                        </div>

                        {/* Keterangan */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center gap-4">
                                <div className="w-12 h-12 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center shrink-0">
                                    <AlignLeft className="w-6 h-6 text-amber-600" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-gray-900 text-lg">Keterangan Tambahan</h3>
                                    <p className="text-xs text-gray-500">Catatan terkait pengiriman ini</p>
                                </div>
                            </div>
                            <div className="p-6">
                                <p className="text-sm text-gray-700 whitespace-pre-line leading-relaxed">
                                    {bukuEkspedisi.keterangan || <span className="italic text-gray-400">Tidak ada keterangan tambahan.</span>}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="space-y-6">
                        {/* Informasi Pengiriman */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center gap-4">
                                <div className="w-12 h-12 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center shrink-0">
                                    <Send className="w-6 h-6 text-green-600" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-gray-900 text-lg">Detail Pengiriman</h3>
                                    <p className="text-xs text-gray-500">Tujuan dan status penerimaan</p>
                                </div>
                            </div>
                            <div className="divide-y divide-gray-50">
                                <DetailRow 
                                    icon={Calendar} 
                                    label="Tanggal Pengiriman" 
                                    value={dayjs(bukuEkspedisi.tanggal_pengiriman).format('DD MMMM YYYY')} 
                                />
                                <DetailRow 
                                    icon={MapPin} 
                                    label="Tujuan Pengiriman" 
                                    value={bukuEkspedisi.tujuan} 
                                />
                                <DetailRow 
                                    icon={User} 
                                    label="Nama Penerima" 
                                    value={bukuEkspedisi.penerima || <span className="italic text-gray-400">Belum ada data penerima</span>} 
                                />
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex flex-col gap-3">
                            <Link
                                href={route('sekretariat.buku-ekspedisi.edit', bukuEkspedisi.id)}
                                className="w-full flex items-center justify-center py-3 bg-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-md shadow-blue-200"
                            >
                                Edit Data Ekspedisi
                            </Link>
                            <Link
                                href={route('sekretariat.buku-ekspedisi.index')}
                                className="w-full flex items-center justify-center py-3 bg-gray-50 text-gray-600 border border-gray-200 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-100 hover:text-gray-900 transition-all"
                            >
                                <ArrowLeft className="w-4 h-4 mr-2" />
                                Kembali ke Daftar
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
