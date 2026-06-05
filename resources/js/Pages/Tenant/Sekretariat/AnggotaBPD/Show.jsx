import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/Shared';
import { Users, ArrowLeft, Edit, Trash2, Phone, MapPin, GraduationCap, Layers, FileText, CreditCard, CheckCircle, XCircle, Calendar } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Show({ auth, anggota }) {
    const handleDelete = () => {
        Swal.fire({
            title: 'Hapus Data Anggota BPD?',
            text: `Anda yakin ingin menghapus data anggota ${anggota.nama}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold', cancelButton: 'rounded-xl font-bold' }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('sekretariat.anggota-bpd.destroy', anggota.id));
            }
        });
    };

    const isAktif = anggota.status === 'aktif' || !anggota.status;

    const InfoRow = ({ icon: Icon, label, value }) => {
        if (!value) return null;
        return (
            <div className="flex items-start gap-3 py-3 border-b border-gray-50 last:border-0">
                <div className="p-1.5 bg-gray-50 rounded-lg shrink-0 mt-0.5">
                    <Icon className="w-3.5 h-3.5 text-gray-500" />
                </div>
                <div className="min-w-0">
                    <p className="text-xs text-gray-500 mb-0.5">{label}</p>
                    <p className="text-sm font-semibold text-gray-900 break-words">{value}</p>
                </div>
            </div>
        );
    };

    const formatDate = (dateString) => {
        if (!dateString) return null;
        return new Date(dateString).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'long', year: 'numeric'
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title={`Detail Anggota BPD — ${anggota.nama}`}>
            <Head title={`Detail Anggota BPD — ${anggota.nama}`} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader
                    icon={Users}
                    title="Detail Anggota BPD"
                    subtitle={anggota.nama}
                    actions={[
                        { label: 'Kembali', icon: ArrowLeft, href: route('sekretariat.anggota-bpd.index'), variant: 'ghost' },
                        { label: 'Edit', icon: Edit, href: route('sekretariat.anggota-bpd.edit', anggota.id), variant: 'white' },
                    ]}
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Profile Card */}
                    <div className="lg:col-span-1">
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="bg-gradient-to-br from-indigo-600 to-purple-700 p-8 flex flex-col items-center text-center">
                                <div className="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-3xl flex items-center justify-center mb-4 border-2 border-white/30">
                                    <Users className="w-10 h-10 text-white" />
                                </div>
                                <h2 className="text-xl font-black text-white leading-snug">{anggota.nama}</h2>
                                <p className="text-indigo-100 text-sm mt-1 font-semibold">{anggota.jabatan}</p>
                                <div className="mt-3">
                                    <span className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border ${
                                        isAktif
                                            ? 'bg-emerald-500/20 text-emerald-100 border-emerald-400/30'
                                            : 'bg-gray-500/20 text-gray-100 border-gray-400/30'
                                    }`}>
                                        {isAktif ? <CheckCircle className="w-3 h-3" /> : <XCircle className="w-3 h-3" />}
                                        {isAktif ? 'Aktif' : 'Purna Tugas'}
                                    </span>
                                </div>
                            </div>
                            <div className="p-5">
                                <InfoRow icon={CreditCard} label="NIK" value={anggota.nik} />
                                <InfoRow icon={Users} label="Jenis Kelamin" value={anggota.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'} />
                                <InfoRow icon={MapPin} label="Tempat Lahir" value={anggota.tempat_lahir} />
                                <InfoRow icon={Calendar} label="Tanggal Lahir" value={formatDate(anggota.tanggal_lahir)} />
                                <InfoRow icon={FileText} label="Agama" value={anggota.agama} />
                                <InfoRow icon={GraduationCap} label="Pendidikan Terakhir" value={anggota.pendidikan_terakhir} />
                            </div>
                        </div>

                        {/* Danger Zone */}
                        <div className="bg-white rounded-3xl border border-red-100 shadow-sm p-5 mt-4">
                            <p className="text-xs font-black text-red-700 uppercase tracking-widest mb-3">Hapus Data</p>
                            <p className="text-xs text-gray-500 mb-4">Tindakan ini tidak dapat dibatalkan.</p>
                            <button
                                onClick={handleDelete}
                                className="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 font-bold text-sm rounded-xl hover:bg-red-100 border border-red-200 transition-colors"
                            >
                                <Trash2 className="w-4 h-4" />
                                Hapus Data Anggota
                            </button>
                        </div>
                    </div>

                    {/* Detail Cards */}
                    <div className="lg:col-span-2 space-y-5">
                        {/* SK & Jabatan */}
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                            <p className="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">SK & Jabatan</p>
                            <div className="divide-y divide-gray-50">
                                <InfoRow icon={Layers} label="Jabatan di BPD" value={anggota.jabatan} />
                                <InfoRow icon={FileText} label="Nomor Keputusan Pengangkatan" value={anggota.no_keputusan_pengangkatan} />
                                <InfoRow icon={Calendar} label="Tanggal Keputusan Pengangkatan" value={formatDate(anggota.tanggal_keputusan_pengangkatan)} />
                                
                                {anggota.no_keputusan_pemberhentian && (
                                    <>
                                        <InfoRow icon={FileText} label="Nomor Keputusan Pemberhentian" value={anggota.no_keputusan_pemberhentian} />
                                        <InfoRow icon={Calendar} label="Tanggal Keputusan Pemberhentian" value={formatDate(anggota.tanggal_keputusan_pemberhentian)} />
                                    </>
                                )}
                            </div>
                        </div>

                        {/* Alamat & Kontak */}
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                            <p className="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">Alamat & Kontak</p>
                            <div className="divide-y divide-gray-50">
                                <InfoRow icon={MapPin} label="Jalan / Kampung" value={anggota.alamat} />
                                <InfoRow icon={MapPin} label="RT" value={anggota.rt} />
                                <InfoRow icon={MapPin} label="RW" value={anggota.rw} />
                                <InfoRow icon={MapPin} label="Dusun" value={anggota.dusun} />
                                <InfoRow icon={Phone} label="Nomor HP" value={anggota.no_hp} />
                            </div>
                        </div>

                        {/* Keterangan */}
                        {anggota.keterangan && (
                            <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                                <p className="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">Keterangan</p>
                                <div className="text-sm text-gray-700 bg-gray-50 rounded-xl p-4">
                                    {anggota.keterangan}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
