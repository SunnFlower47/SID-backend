import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormCard } from '@/Components/Shared';
import { MapPin, User, Home, Info, ClipboardList, Edit, ArrowLeft, CheckCircle, Clock, XCircle, FileText, Calendar, Activity } from 'lucide-react';
import { cn } from '@/lib/utils';
import { format } from 'date-fns';
import { id as localeId } from 'date-fns/locale';

const STATUS_CONFIG = {
    aktif: { label: 'AKTIF', icon: CheckCircle, bg: 'bg-green-100', text: 'text-green-700' },
    expired: { label: 'EXPIRED', icon: Clock, bg: 'bg-orange-100', text: 'text-orange-700' },
    dicabut: { label: 'DICABUT', icon: XCircle, bg: 'bg-red-100', text: 'text-red-700' },
};

function StatusBadge({ status }) {
    const cfg = STATUS_CONFIG[status] || STATUS_CONFIG.expired;
    const Icon = cfg.icon;
    return (
        <span className={cn('inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black tracking-widest uppercase', cfg.bg, cfg.text)}>
            <Icon className="w-4 h-4" />{cfg.label}
        </span>
    );
}

function DetailItem({ label, value, color = 'text-gray-900' }) {
    return (
        <div className="flex justify-between items-center text-sm gap-4 py-3 border-b border-gray-50 last:border-0 last:pb-0">
            <span className="font-bold text-gray-400 uppercase tracking-widest shrink-0 text-[11px]">{label}</span>
            <span className={cn('font-black text-right truncate', color)} title={value}>{value || '-'}</span>
        </div>
    );
}

export default function Show({ auth, domisili }) {
    const formatDate = (dateString, formatStr = 'dd MMMM yyyy') => {
        if (!dateString) return '-';
        try {
            return format(new Date(dateString), formatStr, { locale: localeId });
        } catch (e) {
            return '-';
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} title={`Detail Domisili - ${domisili.nama}`}>
            <Head title={`Detail Domisili - ${domisili.nama}`} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader 
                    title="Detail Domisili"
                    subtitle="Informasi lengkap penduduk domisili"
                    icon={User}
                    backHref={route('domisili.index')}
                    actions={[
                        {
                            label: 'EDIT DATA',
                            icon: Edit,
                            href: route('domisili.edit', domisili.id),
                            variant: 'white'
                        }
                    ]}
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 items-start">
                    {/* LEFT SIDE: SUMMARY CARD */}
                    <div className="lg:col-span-1 space-y-6">
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
                            <div className="h-24 bg-blue-600 relative">
                                <div className="absolute inset-0 bg-gradient-to-b from-blue-600 to-blue-700 opacity-50"></div>
                                <div className="absolute -bottom-12 left-1/2 -translate-x-1/2">
                                    <div className="w-24 h-24 bg-white rounded-[28px] p-1.5 shadow-2xl border border-blue-50">
                                        <div className="w-full h-full bg-blue-50 rounded-[20px] flex items-center justify-center overflow-hidden">
                                            <User className="w-12 h-12 text-blue-300" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="pt-14 pb-8 px-6 text-center">
                                <h2 className="text-lg font-black text-gray-900 uppercase tracking-tight italic mb-3">{domisili.nama}</h2>
                                <div className="flex justify-center gap-2 mb-8">
                                    <StatusBadge status={domisili.status} />
                                </div>

                                <div className="grid grid-cols-1 gap-4">
                                    <div className="p-5 bg-gray-50/50 rounded-3xl border border-gray-100 text-center group hover:bg-white hover:shadow-md transition-all">
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">NIK Pendatang</p>
                                        <p className="text-lg font-black text-gray-950 font-mono tracking-wider">{domisili.nik}</p>
                                    </div>
                                    <div className="p-5 bg-gray-50/50 rounded-3xl border border-gray-100 text-center group hover:bg-white hover:shadow-md transition-all">
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nomor Surat Domisili</p>
                                        <p className="text-sm font-black text-gray-700 uppercase tracking-widest mb-2">{domisili.nomor_surat || 'BELUM TERBIT'}</p>
                                        {domisili.surat_pengajuan_id && (
                                            <Link
                                                href={route('surat-pengajuan.show', domisili.surat_pengajuan_id)}
                                                className="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition-all shadow-sm"
                                                title="Lihat detail surat, cetak PDF, atau lakukan TTE"
                                            >
                                                <FileText className="w-3.5 h-3.5" /> Lihat / Cetak Surat
                                            </Link>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* TIMELINE */}
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-lg p-6 space-y-4">
                            <h3 className="text-[10px] font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                                <Activity className="w-4 h-4 text-gray-400" />
                                Timeline Sistem
                            </h3>
                            <div className="space-y-3 pt-2">
                                <div className="flex justify-between items-center text-[10px]">
                                    <span className="font-bold text-gray-400 uppercase">Input Sistem</span>
                                    <span className="font-black text-gray-800 uppercase italic">{formatDate(domisili.created_at, 'dd/MM/yyyy')}</span>
                                </div>
                                <div className="flex justify-between items-center text-[10px]">
                                    <span className="font-bold text-gray-400 uppercase">Update Terakhir</span>
                                    <span className="font-black text-gray-800 uppercase italic">{formatDate(domisili.updated_at, 'dd/MM/yyyy')}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* RIGHT SIDE: FULL DETAILS */}
                    <div className="lg:col-span-2 space-y-6 md:space-y-8">
                        {/* Informasi Pribadi */}
                        <FormCard icon={User} title="Informasi Pribadi" className="h-full">
                            <DetailItem label="Tempat & Tanggal Lahir" value={`${domisili.tempat_lahir || '-'}, ${formatDate(domisili.tanggal_lahir)}`} />
                            <DetailItem label="Jenis Kelamin" value={domisili.jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan'} />
                            <DetailItem label="Agama" value={domisili.agama} />
                            <DetailItem label="Status Perkawinan" value={domisili.status_perkawinan} />
                            <DetailItem label="Pekerjaan" value={domisili.pekerjaan} />
                            <DetailItem label="Kewarganegaraan" value={domisili.kewarganegaraan} />
                        </FormCard>

                        {/* Detail Domisili */}
                        <FormCard icon={FileText} title="Administrasi Domisili" className="h-full">
                            <DetailItem label="Tanggal Masuk" value={formatDate(domisili.tanggal_masuk)} />
                            <DetailItem label="Berlaku Sampai" value={formatDate(domisili.tanggal_berlaku)} />
                            <DetailItem label="Sisa Waktu" value={`${domisili.sisa_hari_berlaku} Hari`} color={domisili.sisa_hari_berlaku > 30 ? 'text-green-600' : (domisili.sisa_hari_berlaku > 0 ? 'text-orange-600' : 'text-red-600')} />
                            <DetailItem label="Perpanjangan Ke" value={domisili.perpanjangan_ke} />
                            <DetailItem label="Keperluan" value={domisili.keperluan_domisili || '-'} />
                        </FormCard>

                        {/* Lokasi & Alamat */}
                        <FormCard icon={MapPin} title="Lokasi & Alamat" className="md:col-span-2">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Wilayah / Kota Asal</p>
                                    <p className="text-sm font-black text-blue-700 mb-4">{domisili.asal_daerah || '-'}</p>
                                    
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Alamat Asal Lengkap</p>
                                    <p className="text-sm font-bold text-gray-700 leading-relaxed">{domisili.alamat_asal || '-'}</p>
                                </div>
                                
                                <div className="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Lokasi Domisili (Cibatu)</p>
                                    <p className="text-sm font-black text-green-700 mb-1">
                                        RT {domisili.rt_label} / RW {domisili.rw_label}
                                    </p>
                                    <p className="text-xs font-bold text-gray-600 mb-4 uppercase tracking-widest">
                                        DUSUN {domisili.dusun_label}
                                    </p>
                                    
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Alamat Tinggal Sementara</p>
                                    <p className="text-sm font-bold text-gray-700 leading-relaxed">{domisili.alamat_tinggal || '-'}</p>
                                </div>
                            </div>
                        </FormCard>

                        {/* Catatan */}
                        {domisili.catatan && (
                            <FormCard icon={ClipboardList} title="Catatan Admin" className="md:col-span-2">
                                <div className="bg-yellow-50 rounded-2xl p-6 border border-yellow-100">
                                    <p className="text-sm font-medium text-yellow-800 italic leading-relaxed">
                                        "{domisili.catatan}"
                                    </p>
                                </div>
                            </FormCard>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
