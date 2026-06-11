import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { 
    ShieldCheck, Fingerprint, Lock, CheckCircle2, 
    XCircle, Clock, Download, Loader2, AlertTriangle,
    Stamp, Eye, EyeOff
} from 'lucide-react';
import { cn } from '@/lib/utils';

/**
 * TtePanel — Panel Tanda Tangan Elektronik (TTE) BSrE
 * Ditampilkan di halaman Detail Surat Pengajuan.
 *
 * Menangani:
 * - Tampilan status sertifikat Kepala Desa (ISSUE / EXPIRED / dll)
 * - Form input Passphrase untuk proses TTE
 * - Tombol download PDF yang sudah ber-TTE
 */
export default function TtePanel({ suratPengajuan, bsreConfigured = false }) {
    const p = suratPengajuan;

    // State untuk cek status sertifikat
    const [statusLoading, setStatusLoading] = useState(false);
    const [certStatus, setCertStatus]       = useState(null); // { is_active, status, nama, message }
    const [showPass, setShowPass]           = useState(false);

    // Form untuk proses TTE
    const { data, setData, post, processing, errors, reset } = useForm({
        nik:        '',
        passphrase: '',
    });

    const checkCertStatus = async () => {
        if (!data.nik || data.nik.length !== 16) {
            setCertStatus({ is_active: false, message: 'Masukkan NIK 16 digit terlebih dahulu.' });
            return;
        }
        setStatusLoading(true);
        setCertStatus(null);
        try {
            const res = await fetch(route('admin.tte.status', p.id) + '?nik=' + data.nik);
            const json = await res.json();
            setCertStatus(json);
        } catch {
            setCertStatus({ is_active: false, message: 'Gagal terhubung ke server.' });
        } finally {
            setStatusLoading(false);
        }
    };

    const handleSign = (e) => {
        e.preventDefault();
        post(route('admin.tte.sign', p.id), {
            onSuccess: () => reset('passphrase'),
        });
    };

    // — Sudah ber-TTE: tampilkan panel sukses —
    if (p.is_tte) {
        return (
            <div className="bg-gradient-to-br from-emerald-50 to-green-50 rounded-3xl p-6 border border-emerald-200 space-y-4">
                <div className="flex items-center gap-3">
                    <div className="w-10 h-10 bg-emerald-600 rounded-2xl flex items-center justify-center">
                        <Stamp className="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h3 className="text-[10px] font-black text-emerald-900 uppercase tracking-widest">
                            Tanda Tangan Elektronik (TTE)
                        </h3>
                        <p className="text-[9px] font-bold text-emerald-600 uppercase tracking-widest mt-0.5">
                            Tersertifikasi BSrE · BSSN
                        </p>
                    </div>
                </div>

                <div className="bg-white/70 rounded-2xl p-4 border border-emerald-100 space-y-2">
                    <div className="flex items-center gap-2">
                        <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                        <p className="text-xs font-black text-emerald-800">Surat Telah Ditandatangani Secara Elektronik</p>
                    </div>
                    <div className="pl-6 space-y-1 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                        <p>Ditandatangani: <span className="text-gray-800">{p.tte_at ? new Date(p.tte_at).toLocaleString('id-ID') : '-'}</span></p>
                        <p>Penandatangan: <span className="text-gray-800">{p.tte_signer_name || '-'}</span></p>
                        <p>NIK: <span className="text-gray-800">{p.tte_signer_nik ? p.tte_signer_nik.replace(/(.{4})/g, '$1 ').trim() : '-'}</span></p>
                    </div>
                </div>

                <a
                    href={route('admin.tte.download', p.id)}
                    className="flex items-center justify-center gap-2 w-full py-3 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 hover:shadow-xl hover:scale-[1.01]"
                >
                    <Download className="w-4 h-4" />
                    Unduh PDF Ber-TTE
                </a>
            </div>
        );
    }

    // — BSrE belum dikonfigurasi —
    if (!bsreConfigured) {
        return (
            <div className="bg-amber-50 rounded-3xl p-6 border border-amber-200 space-y-3">
                <div className="flex items-center gap-3">
                    <AlertTriangle className="w-5 h-5 text-amber-600" />
                    <h3 className="text-[10px] font-black text-amber-900 uppercase tracking-widest">TTE BSrE Belum Dikonfigurasi</h3>
                </div>
                <p className="text-[10px] font-bold text-amber-700 leading-relaxed">
                    Untuk menggunakan fitur Tanda Tangan Elektronik (TTE) resmi, lengkapi konfigurasi BSrE 
                    di <strong>Pengaturan Desa → Integrasi BSrE</strong> terlebih dahulu.
                </p>
            </div>
        );
    }

    // — Surat belum di-TTE: tampilkan form —
    return (
        <div className="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-3xl p-6 border border-indigo-200 space-y-5">
            {/* Header */}
            <div className="flex items-center gap-3">
                <div className="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center">
                    <Stamp className="w-5 h-5 text-white" />
                </div>
                <div>
                    <h3 className="text-[10px] font-black text-indigo-900 uppercase tracking-widest">
                        Tanda Tangan Elektronik (TTE)
                    </h3>
                    <p className="text-[9px] font-bold text-indigo-500 uppercase tracking-widest mt-0.5">
                        Tersertifikasi BSrE · BSSN
                    </p>
                </div>
            </div>

            <form onSubmit={handleSign} className="space-y-4">
                {/* NIK Input + Cek Status */}
                <div className="space-y-1.5">
                    <label className="text-[9px] font-black text-indigo-700 uppercase tracking-widest ml-1">
                        NIK Pejabat Penandatangan (16 Digit)
                    </label>
                    <div className="flex gap-2">
                        <div className="relative flex-1">
                            <Fingerprint className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-indigo-400" />
                            <input
                                type="text"
                                value={data.nik}
                                onChange={e => setData('nik', e.target.value.replace(/\D/g, '').slice(0, 16))}
                                className="w-full pl-9 pr-4 py-2.5 bg-white border border-indigo-100 rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-400 transition-all"
                                placeholder="3201XXXXXXXXXXXX"
                                maxLength={16}
                            />
                        </div>
                        <button
                            type="button"
                            onClick={checkCertStatus}
                            disabled={statusLoading || data.nik.length !== 16}
                            className="px-3 py-2.5 bg-indigo-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50 transition-all flex items-center gap-1 whitespace-nowrap"
                        >
                            {statusLoading ? <Loader2 className="w-3.5 h-3.5 animate-spin" /> : <ShieldCheck className="w-3.5 h-3.5" />}
                            Cek
                        </button>
                    </div>
                    {errors.nik && <p className="text-red-500 text-[9px] font-bold uppercase ml-1">{errors.nik}</p>}

                    {/* Status Sertifikat Badge */}
                    {certStatus && (
                        <div className={cn(
                            "flex items-center gap-2 px-3 py-2 rounded-xl border text-[9px] font-black uppercase tracking-widest mt-1",
                            certStatus.is_active
                                ? "bg-emerald-50 border-emerald-200 text-emerald-700"
                                : "bg-red-50 border-red-200 text-red-700"
                        )}>
                            {certStatus.is_active
                                ? <CheckCircle2 className="w-3.5 h-3.5 shrink-0" />
                                : <XCircle className="w-3.5 h-3.5 shrink-0" />}
                            <span>{certStatus.message}</span>
                        </div>
                    )}
                </div>

                {/* Passphrase Input */}
                <div className="space-y-1.5">
                    <label className="text-[9px] font-black text-indigo-700 uppercase tracking-widest ml-1">
                        Passphrase (PIN) BSrE
                    </label>
                    <div className="relative">
                        <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-indigo-400" />
                        <input
                            type={showPass ? 'text' : 'password'}
                            value={data.passphrase}
                            onChange={e => setData('passphrase', e.target.value)}
                            className="w-full pl-9 pr-10 py-2.5 bg-white border border-indigo-100 rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-400 transition-all"
                            placeholder="Masukkan PIN BSrE pejabat..."
                        />
                        <button
                            type="button"
                            onClick={() => setShowPass(!showPass)}
                            className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            {showPass ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                        </button>
                    </div>
                    {errors.passphrase && <p className="text-red-500 text-[9px] font-bold uppercase ml-1">{errors.passphrase}</p>}
                    <p className="text-[8px] font-bold text-indigo-400 uppercase tracking-widest ml-1 italic">
                        ⚠ PIN tidak disimpan di sistem. Hanya digunakan untuk sesi ini.
                    </p>
                </div>

                {/* Error TTE */}
                {errors.tte && (
                    <div className="flex items-start gap-2 px-3 py-2.5 bg-red-50 rounded-xl border border-red-200">
                        <XCircle className="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
                        <p className="text-[9px] font-bold text-red-700 leading-relaxed">{errors.tte}</p>
                    </div>
                )}

                {/* Submit Button */}
                <button
                    type="submit"
                    disabled={processing || !data.nik || !data.passphrase || (certStatus && !certStatus.is_active)}
                    className="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-800 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:shadow-lg hover:shadow-indigo-200 hover:scale-[1.01] active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2"
                >
                    {processing ? (
                        <>
                            <Loader2 className="w-4 h-4 animate-spin" />
                            Memproses TTE... (Bisa memakan waktu 1-2 menit)
                        </>
                    ) : (
                        <>
                            <Stamp className="w-4 h-4" />
                            Tanda Tangani Surat (TTE BSrE)
                        </>
                    )}
                </button>
            </form>

            {/* Info box */}
            <div className="bg-white/60 rounded-2xl p-3 border border-indigo-100 text-[8px] font-bold text-indigo-500 uppercase tracking-widest leading-relaxed space-y-1">
                <p>📄 Sistem akan: Generate Word → Konversi ke PDF → Kirim ke BSrE → Simpan PDF ber-TTE</p>
                <p>🔐 Sertifikat Elektronik diterbitkan oleh Balai Sertifikasi Elektronik (BSrE) — BSSN</p>
            </div>
        </div>
    );
}
