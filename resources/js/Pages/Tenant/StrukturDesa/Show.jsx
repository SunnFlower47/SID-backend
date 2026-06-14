import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { User, Shield, MapPin, Calendar, FileText, Phone, Mail, ArrowLeft, Edit2, CheckCircle, XCircle } from 'lucide-react';

// Shared Components
import { PageHeader, Badge, InfoRow } from '@/Components/Shared';

export default function Show({ auth, strukturDesa }) {
    const detailRows = [
        { label: 'Nama Lengkap', value: strukturDesa.nama, icon: User },
        { label: 'NIK (KTP)', value: strukturDesa.nik || '-', icon: Shield },
        { label: 'Nomor Telepon', value: strukturDesa.no_hp || '-', icon: Phone },
        { label: 'Alamat Email', value: strukturDesa.email || '-', icon: Mail },
        { label: 'Wilayah Tugas', value: `${strukturDesa.dusun_label || 'Pusat'}, RT ${strukturDesa.rt_label}/RW ${strukturDesa.rw_label}`, icon: MapPin },
        { label: 'Masa Jabatan', value: `${strukturDesa.tanggal_pengangkatan ? new Date(strukturDesa.tanggal_pengangkatan).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'} s/d ${strukturDesa.tanggal_berakhir ? new Date(strukturDesa.tanggal_berakhir).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : 'Sekarang'}`, icon: Calendar },
    ];

    return (
        <AuthenticatedLayout user={auth.user} title="Profil Perangkat Desa">
            <Head title={`Profil ${strukturDesa.nama} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                
                {/* Header */}
                <PageHeader 
                    title="Detail Perangkat"
                    subtitle="Informasi Lengkap Struktur Desa"
                    icon={User}
                    backHref={route('struktur-desa.index')}
                    actions={[
                        {
                            label: 'EDIT PROFIL',
                            icon: Edit2,
                            href: route('struktur-desa.edit', strukturDesa.id),
                            variant: 'white'
                        }
                    ]}
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Profil Card */}
                    <div className="lg:col-span-1 space-y-6">
                        <div className="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm text-center relative overflow-hidden">
                            <div className="absolute top-0 right-0 p-4">
                                {strukturDesa.status_aktif ? (
                                    <Badge color="green">AKTIF</Badge>
                                ) : (
                                    <Badge color="gray">NONAKTIF</Badge>
                                )}
                            </div>
                            
                            <div className="w-32 h-44 sm:w-40 sm:h-52 bg-gray-50 rounded-2xl border-4 border-white shadow-xl mx-auto overflow-hidden shrink-0 mb-6">
                                {strukturDesa.foto ? (
                                    <img 
                                        src={strukturDesa.foto_url || `/storage/${strukturDesa.foto}`} 
                                        alt={strukturDesa.nama} 
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    <div className="w-full h-full flex items-center justify-center bg-green-50 text-green-600 font-black text-2xl uppercase italic">
                                        {strukturDesa.nama.charAt(0)}
                                    </div>
                                )}
                            </div>
                            
                            <h2 className="text-xl font-black text-gray-900 uppercase italic tracking-tight leading-tight mb-1">{strukturDesa.nama}</h2>
                            <p className="text-xs font-bold text-green-600 uppercase tracking-widest mb-6">{strukturDesa.jabatan}</p>
                            
                            <div className="grid grid-cols-2 gap-3 pt-6 border-t border-gray-50">
                                <div className="p-3 bg-gray-50 rounded-xl">
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Kategori</p>
                                    <p className="text-[10px] font-black text-gray-700 uppercase italic">{strukturDesa.kategori.replace('_', ' ')}</p>
                                </div>
                                <div className="p-3 bg-gray-50 rounded-xl">
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Urutan</p>
                                    <p className="text-[10px] font-black text-gray-700 uppercase italic">Posisi Ke-{strukturDesa.urutan}</p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                            <h4 className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <MapPin className="w-4 h-4 text-red-500" />
                                Alamat Domisili
                            </h4>
                            <p className="text-xs font-bold text-gray-600 leading-relaxed italic uppercase">
                                {strukturDesa.alamat || 'Alamat belum diatur.'}
                            </p>
                        </div>
                    </div>

                    {/* Detail Information */}
                    <div className="lg:col-span-2 space-y-6">
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50">
                                <h4 className="text-xs font-black text-gray-900 uppercase tracking-widest flex items-center gap-2 italic">
                                    <Shield className="w-4 h-4 text-blue-600" />
                                    Informasi Lengkap Perangkat
                                </h4>
                            </div>
                            <div className="divide-y divide-gray-50">
                                {detailRows.map((row, index) => (
                                    <InfoRow key={index} label={row.label} value={row.value} icon={row.icon} />
                                ))}
                            </div>
                        </div>

                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50">
                                <h4 className="text-xs font-black text-gray-900 uppercase tracking-widest flex items-center gap-2 italic">
                                    <FileText className="w-4 h-4 text-amber-600" />
                                    Tugas & Wewenang Jabatan
                                </h4>
                            </div>
                            <div className="p-8">
                                <div className="prose prose-sm max-w-none">
                                    {strukturDesa.tugas_wewenang ? (
                                        <div className="text-xs font-bold text-gray-600 leading-loose italic whitespace-pre-wrap uppercase tracking-tight">
                                            {strukturDesa.tugas_wewenang}
                                        </div>
                                    ) : (
                                        <div className="flex flex-col items-center justify-center py-10 opacity-30 italic">
                                            <FileText className="w-12 h-12 mb-4" />
                                            <p className="font-black uppercase tracking-widest text-xs">Belum ada rincian tugas</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
