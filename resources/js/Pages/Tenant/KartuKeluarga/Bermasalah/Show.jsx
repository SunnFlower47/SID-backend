import React from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { AlertTriangle, UserPlus, IdCard, CheckCircle, ArrowLeft, Info, UserCheck, Clock, Undo2, UserCircle2 } from 'lucide-react';
import { cn } from '@/lib/utils';
import Lottie from 'lottie-react';
import successAnimation from '@/assets/lottie/success-animation.json';
import Swal from 'sweetalert2';

const LottieComponent = Lottie?.default || Lottie;

export default function Show({ auth, kkRecord, nkk, anggotaAktif, mutasiPenyebab, kkSementara }) {
    const { data, setData, post, processing, errors } = useForm({
        kandidat_id: '',
        nkk_baru: ''
    });

    const status = kkRecord.status_kk;

    const handleTunjukSementara = () => {
        if (!data.kandidat_id) {
            Swal.fire({ icon: 'warning', title: 'Pilih kandidat dulu!', text: 'Silakan pilih salah satu anggota keluarga.' });
            return;
        }

        const kandidat = anggotaAktif.find(a => a.id === parseInt(data.kandidat_id));
        
        Swal.fire({
            title: 'Konfirmasi Penunjukan',
            html: `<p>Tunjuk <strong>${kandidat.nama}</strong> sebagai Kepala Keluarga sementara?</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f97316',
            confirmButtonText: 'Ya, Tunjuk',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                post(route('kk.resolve.sementara', nkk));
            }
        });
    };

    const handleSelesaikanPermanen = (e) => {
        e.preventDefault();
        if (!data.nkk_baru || data.nkk_baru.length !== 16) {
            Swal.fire({ icon: 'warning', title: 'NKK tidak valid!', text: 'Masukkan 16 digit angka NKK baru.' });
            return;
        }

        Swal.fire({
            title: '⚠️ Tindakan Permanen!',
            html: `<p>Seluruh anggota KK akan dipindahkan ke NKK baru <strong>${data.nkk_baru}</strong>. Tindakan ini tidak dapat dibatalkan.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            confirmButtonText: 'Ya, Selesaikan Permanen',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                post(route('kk.resolve.permanen', nkk));
            }
        });
    };

    const handleBatalkanSementara = () => {
        Swal.fire({
            title: 'Batalkan KK Sementara?',
            text: 'Anggota yang ditunjuk akan dikembalikan ke kedudukan semula.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Batalkan',
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(route('kk.batalkan.sementara', nkk));
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title={`Resolusi KK - ${nkk}`}>
            <Head title="Selesaikan KK Bermasalah" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
                
                {/* Header Section */}
                <div className={cn(
                    "rounded-3xl shadow-xl p-5 sm:px-8 sm:py-6 text-white relative overflow-hidden transition-all duration-500",
                    status === 'bermasalah' ? "bg-gradient-to-br from-red-600 via-red-700 to-red-800" :
                    status === 'bermasalah_sementara' ? "bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700" :
                    "bg-gradient-to-br from-emerald-500 via-emerald-600 to-emerald-700"
                )}>
                    <div className="absolute top-0 right-0 -mt-6 -mr-6 w-48 h-48 bg-white opacity-10 rounded-full blur-3xl"></div>
                    <div className="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div className="flex items-center space-x-4">
                            <div className="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/10">
                                <AlertTriangle className="w-8 h-8 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl font-black tracking-tight uppercase italic leading-tight">Resolusi KK</h1>
                                <div className="flex items-center gap-2 mt-1">
                                    <p className="text-white/80 font-mono text-sm tracking-widest">{nkk}</p>
                                    <span className="w-1 h-1 bg-white/30 rounded-full"></span>
                                    <p className="text-[10px] font-bold text-white/60 uppercase tracking-widest">Audit Trail</p>
                                </div>
                            </div>
                        </div>
                        <Link 
                            href={route('kk.show', nkk)}
                            className="inline-flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                        >
                            <ArrowLeft className="w-4 h-4 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>

                {/* Progress Stepper */}
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 px-12">
                    <div className="flex items-center justify-between max-w-2xl mx-auto relative">
                        <div className="absolute top-6 left-0 w-full h-1 bg-gray-100 -z-0 rounded-full"></div>
                        <div className={cn(
                            "absolute top-6 left-0 h-1 transition-all duration-1000 rounded-full -z-0",
                            status === 'bermasalah' ? "w-0" :
                            status === 'bermasalah_sementara' ? "w-1/2 bg-orange-500" :
                            "w-full bg-emerald-500"
                        )}></div>

                        <StepItem 
                            step={1} 
                            label="KK Sementara" 
                            isActive={status === 'bermasalah'} 
                            isCompleted={status !== 'bermasalah'}
                            colorClass="red"
                        />
                        <StepItem 
                            step={2} 
                            label="Input NKK Baru" 
                            isActive={status === 'bermasalah_sementara'} 
                            isCompleted={status === 'resolved'}
                            colorClass="orange"
                        />
                        <StepItem 
                            step={3} 
                            label="Selesai" 
                            isActive={status === 'resolved'} 
                            isCompleted={status === 'resolved'}
                            colorClass="emerald"
                        />
                    </div>
                </div>

                {/* Cause Alert */}
                {mutasiPenyebab && (
                    <div className="bg-red-50/50 border border-red-100 rounded-2xl p-5 flex items-start gap-4">
                        <div className="w-12 h-12 bg-red-100 text-red-600 rounded-xl flex items-center justify-center shrink-0">
                            <Info className="w-6 h-6" />
                        </div>
                        <div>
                            <p className="text-xs font-black text-red-800 uppercase italic">Penyebab Masalah:</p>
                            <p className="text-sm font-bold text-red-700 mt-1">
                                {mutasiPenyebab.jenis_mutasi_label} &mdash; {mutasiPenyebab.penduduk?.nama}
                            </p>
                            <div className="flex items-center gap-3 mt-2">
                                <span className="inline-flex items-center text-[10px] font-black text-red-600 bg-red-100 px-2 py-0.5 rounded-full">
                                    <Clock className="w-3 h-3 mr-1" /> {kkRecord.kk_bermasalah_sejak ? new Date(kkRecord.kk_bermasalah_sejak).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-'}
                                </span>
                            </div>
                        </div>
                    </div>
                )}

                {/* Step 1: Penunjukan Sementara */}
                {status === 'bermasalah' && (
                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-red-500">
                        <div className="p-6 sm:p-8">
                            <div className="flex items-center gap-4 mb-8">
                                <div className="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                                    <UserPlus className="w-6 h-6" />
                                </div>
                                <div>
                                    <h2 className="text-lg font-black text-gray-900 uppercase italic">Langkah 1: Tunjuk KK Sementara</h2>
                                    <p className="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Pilih anggota keluarga aktif untuk diangkat sebagai pemimpin sementara.</p>
                                </div>
                            </div>

                            {anggotaAktif.length === 0 ? (
                                <div className="py-12 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                    <p className="text-sm font-bold text-gray-400 uppercase italic">Tidak ada anggota keluarga aktif yang tersedia.</p>
                                </div>
                            ) : (
                                <div className="space-y-3 mb-8">
                                    {anggotaAktif.map((anggota) => (
                                        <label 
                                            key={anggota.id}
                                            className={cn(
                                                "flex items-center gap-4 p-4 rounded-2xl border-2 transition-all cursor-pointer group",
                                                data.kandidat_id == anggota.id 
                                                    ? (anggota.umur < 17 ? "border-red-600 bg-red-50" : "border-emerald-500 bg-emerald-50 shadow-md scale-[1.01]") 
                                                    : "border-gray-100 hover:border-emerald-200 hover:bg-gray-50"
                                            )}
                                        >
                                            <input 
                                                type="radio" 
                                                name="kandidat_id" 
                                                value={anggota.id}
                                                checked={data.kandidat_id == anggota.id}
                                                onChange={(e) => setData('kandidat_id', e.target.value)}
                                                className="hidden"
                                            />
                                            <div className={cn(
                                                "w-12 h-12 rounded-xl flex items-center justify-center transition-colors shrink-0",
                                                data.kandidat_id == anggota.id 
                                                    ? (anggota.umur < 17 ? "bg-red-600 text-white" : "bg-emerald-500 text-white") 
                                                    : "bg-gray-100 text-gray-400 group-hover:bg-emerald-100"
                                            )}>
                                                <UserCircle2 className="w-7 h-7" />
                                            </div>
                                            <div className="flex-1">
                                                <div className="flex items-center gap-2">
                                                    <h4 className="font-black text-gray-900 uppercase italic text-sm">{anggota.nama}</h4>
                                                    <span className={cn(
                                                        "text-[9px] font-black px-2 py-0.5 rounded-full uppercase",
                                                        anggota.umur < 17 ? "bg-red-100 text-red-700" : "bg-emerald-100 text-emerald-700"
                                                    )}>
                                                        {anggota.umur} TAHUN
                                                    </span>
                                                </div>
                                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">NIK: {anggota.nik} &bull; {anggota.kedudukan_keluarga}</p>
                                            </div>
                                            {data.kandidat_id == anggota.id && (
                                                anggota.umur < 17 ? <AlertTriangle className="w-6 h-6 text-red-600" /> : <CheckCircle className="w-6 h-6 text-emerald-600" />
                                            )}
                                        </label>
                                    ))}
                                </div>
                            )}

                            {data.kandidat_id && anggotaAktif.find(a => a.id == data.kandidat_id)?.umur < 17 ? (
                                <div className="bg-red-50 border-2 border-red-200 rounded-2xl p-6 mb-8 animate-in zoom-in duration-300">
                                    <div className="flex gap-4">
                                        <div className="w-12 h-12 bg-red-600 text-white rounded-xl flex items-center justify-center shrink-0 shadow-lg">
                                            <AlertTriangle className="w-6 h-6" />
                                        </div>
                                        <div>
                                            <h4 className="text-sm font-black text-red-900 uppercase italic">Kandidat Belum Cukup Umur!</h4>
                                            <p className="text-[10px] font-bold text-red-700 uppercase tracking-widest mt-1 leading-relaxed">
                                                Kandidat ini tidak dapat ditunjuk sebagai Kepala Keluarga karena masih di bawah 17 tahun. 
                                            </p>
                                        </div>
                                    </div>
                                    <div className="mt-4 pt-4 border-t border-red-100 space-y-3">
                                        <p className="text-[9px] font-black text-red-800 uppercase tracking-widest">Rekomendasi Tindakan:</p>
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div className="bg-white p-3 rounded-xl border border-red-100">
                                                <p className="text-[10px] font-black text-red-600 uppercase italic">1. Gabung KK Lain</p>
                                                <p className="text-[8px] font-bold text-gray-500 mt-1 uppercase leading-relaxed">Gunakan menu <span className="text-red-600 font-black">Mutasi Pisah KK</span> tipe "Dalam Desa" untuk menggabungkan ke KK kerabat.</p>
                                            </div>
                                            <div className="bg-white p-3 rounded-xl border border-red-100">
                                                <p className="text-[10px] font-black text-red-600 uppercase italic">2. Tunggu Cukup Umur</p>
                                                <p className="text-[8px] font-bold text-gray-500 mt-1 uppercase leading-relaxed">Atau biarkan status tetap bermasalah sampai kandidat mencapai usia 17 tahun untuk ditunjuk.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <button 
                                    onClick={handleTunjukSementara}
                                    disabled={processing || !data.kandidat_id}
                                    className="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-emerald-200 disabled:opacity-50 disabled:shadow-none flex items-center justify-center gap-2"
                                >
                                    <UserCheck className="w-4 h-4" /> TUNJUK KK SEMENTARA
                                </button>
                            )}
                        </div>
                    </div>
                )}

                {/* Step 2: Selesaikan Permanen */}
                {status === 'bermasalah_sementara' && (
                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-orange-500">
                        <div className="p-6 sm:p-8">
                            <div className="flex items-center gap-4 mb-8">
                                <div className="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center">
                                    <IdCard className="w-6 h-6" />
                                </div>
                                <div>
                                    <h2 className="text-lg font-black text-gray-900 uppercase italic">Langkah 2: Input NKK Baru Permanen</h2>
                                    <p className="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Masukkan NKK baru dari Disdukcapil untuk menyelesaikan audit.</p>
                                </div>
                            </div>

                            {kkSementara && (
                                <div className="bg-orange-50 rounded-2xl p-5 border border-orange-100 flex items-center justify-between mb-8">
                                    <div className="flex items-center gap-4">
                                        <div className="w-10 h-10 bg-orange-500 text-white rounded-xl flex items-center justify-center">
                                            <UserCheck className="w-5 h-5" />
                                        </div>
                                        <div>
                                            <p className="text-[10px] font-black text-orange-800 uppercase tracking-widest leading-none mb-1">KK Sementara Aktif</p>
                                            <p className="text-sm font-black text-gray-900 uppercase italic">{kkSementara.nama}</p>
                                        </div>
                                    </div>
                                    <button 
                                        onClick={handleBatalkanSementara}
                                        className="text-[10px] font-black text-red-600 uppercase tracking-widest hover:underline flex items-center gap-1"
                                    >
                                        <Undo2 className="w-3 h-3" /> BATALKAN
                                    </button>
                                </div>
                            )}

                            <form onSubmit={handleSelesaikanPermanen} className="space-y-6">
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nomor Kartu Keluarga Baru</label>
                                    <div className="relative">
                                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <IdCard className="w-5 h-5 text-gray-300" />
                                        </div>
                                        <input 
                                            type="text" 
                                            maxLength={16}
                                            value={data.nkk_baru}
                                            onChange={(e) => setData('nkk_baru', e.target.value.replace(/\D/g, ''))}
                                            placeholder="Masukkan 16 digit NKK baru..."
                                            className={cn(
                                                "w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 font-mono text-lg font-bold tracking-[0.2em]",
                                                errors.nkk_baru && "border-red-500 ring-red-500/10"
                                            )}
                                        />
                                    </div>
                                    {errors.nkk_baru && <p className="text-[10px] font-bold text-red-600 uppercase tracking-widest ml-1">{errors.nkk_baru}</p>}
                                </div>

                                <div className="bg-gray-50 rounded-2xl p-5 space-y-3 border border-gray-100">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                        <Info className="w-3 h-3" /> Catatan Penting
                                    </p>
                                    <ul className="text-[10px] font-bold text-gray-600 uppercase space-y-1 ml-5 list-disc leading-relaxed">
                                        <li>Seluruh anggota keluarga akan dipindahkan ke NKK baru.</li>
                                        <li>NKK lama ({nkk}) akan otomatis diarsipkan.</li>
                                        <li>Tindakan ini permanen dan tidak dapat dibatalkan.</li>
                                    </ul>
                                </div>

                                <button 
                                    type="submit" 
                                    disabled={processing || data.nkk_baru.length !== 16}
                                    className="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-emerald-200 disabled:opacity-50 disabled:shadow-none flex items-center justify-center gap-2"
                                >
                                    <CheckCircle className="w-4 h-4" /> SELESAIKAN PERMANEN
                                </button>
                            </form>
                        </div>
                    </div>
                )}

                {/* Step 3: Resolved Status */}
                {status === 'resolved' && (
                    <div className="bg-white rounded-3xl shadow-sm border border-emerald-100 p-12 text-center space-y-6 animate-in zoom-in duration-500">
                        <div className="w-48 h-48 mx-auto">
                            <LottieComponent animationData={successAnimation} loop={false} />
                        </div>
                        <div>
                            <h2 className="text-2xl font-black text-emerald-800 uppercase italic">Audit Selesai!</h2>
                            <p className="text-xs font-bold text-emerald-600/60 uppercase tracking-widest mt-2 px-12 leading-relaxed">
                                Kartu Keluarga ini telah berhasil diperbarui dan audit selesai secara permanen.
                            </p>
                        </div>
                        <div className="flex justify-center gap-4 pt-6">
                            <Link 
                                href={route('kk.index')}
                                className="px-8 py-4 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-emerald-100 transition-all hover:scale-105 active:scale-95"
                            >
                                KEMBALI KE DAFTAR KK
                            </Link>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}

function StepItem({ step, label, isActive, isCompleted, colorClass }) {
    return (
        <div className="flex flex-col items-center relative z-10 min-w-[80px] sm:min-w-[120px]">
            <div className={cn(
                "w-12 h-12 rounded-2xl flex items-center justify-center text-sm font-black transition-all duration-500 shadow-md",
                isCompleted ? "bg-emerald-500 text-white" : 
                isActive ? `bg-${colorClass}-500 text-white ring-8 ring-${colorClass}-500/10` : 
                "bg-gray-100 text-gray-400"
            )}>
                {isCompleted ? <CheckCircle className="w-6 h-6" /> : step}
            </div>
            <p className={cn(
                "text-[9px] font-black uppercase tracking-widest mt-4 text-center transition-colors duration-500 whitespace-nowrap",
                isCompleted ? "text-emerald-600" : isActive ? `text-${colorClass}-600` : "text-gray-300"
            )}>
                {label}
            </p>
        </div>
    );
}
