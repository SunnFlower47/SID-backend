import { useState } from 'react';
import { Head, useForm, Link, usePage } from '@inertiajs/react';

export default function TwoFactorSetup({ qrCodeImage, secretKey }) {
    const { flash } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        code: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('landlord.2fa.enable'));
    };

    return (
        <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-100 px-4">
            <Head title="Setup 2FA - Landlord Diskominfo" />

            <div className="w-full sm:max-w-lg mt-6 px-8 py-8 bg-white shadow-xl overflow-hidden rounded-2xl border border-slate-200">
                <div className="text-center mb-6">
                    <div className="inline-flex items-center justify-center w-14 h-14 rounded-full bg-indigo-100 text-indigo-600 mb-3 font-bold text-xl">
                        🔐
                    </div>
                    <h1 className="text-2xl font-bold text-slate-800">Setup Verifikasi 2 Langkah</h1>
                    <p className="text-slate-500 text-sm mt-1">Keamanan Tambahan Akun Super Admin Diskominfo</p>
                </div>

                {flash?.warning && (
                    <div className="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-xs font-semibold flex items-center gap-2">
                        <span>⚠️</span>
                        <span>{flash.warning}</span>
                    </div>
                )}

                <div className="space-y-6 text-sm text-slate-600">
                    <div className="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <span className="font-bold text-slate-800 block mb-1">Langkah 1: Scan QR Code</span>
                        Buka aplikasi autentikator (Google Authenticator, Authy, atau Bitwarden) di ponsel Anda dan scan QR code berikut:
                        
                        <div className="my-4 flex justify-center">
                            <img 
                                src={qrCodeImage} 
                                alt="QR Code 2FA" 
                                className="w-48 h-48 border border-slate-200 p-2 rounded-xl bg-white shadow-sm"
                            />
                        </div>

                        <div className="text-center">
                            <span className="text-xs text-slate-400 block mb-1">Atau masukkan secret key secara manual:</span>
                            <code className="inline-block font-mono font-bold tracking-wider text-indigo-600 bg-indigo-50 border border-indigo-100 px-3 py-1.5 rounded-lg select-all text-xs">
                                {secretKey}
                            </code>
                        </div>
                    </div>

                    <div className="border-t border-slate-100 pt-4">
                        <span className="font-bold text-slate-800 block mb-2">Langkah 2: Konfirmasi Kode OTP</span>
                        Masukkan 6 digit kode yang muncul di aplikasi autentikator untuk memverifikasi pemasangan:
                    </div>
                </div>

                <form onSubmit={submit} className="mt-4">
                    <div>
                        <input
                            type="text"
                            inputMode="numeric"
                            pattern="[0-9]*"
                            maxLength="6"
                            placeholder="000000"
                            value={data.code}
                            onChange={(e) => setData('code', e.target.value)}
                            className="block w-full text-center text-2xl font-mono tracking-[0.5em] font-bold py-3 border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm"
                            autoFocus
                            required
                        />
                        {errors.code && <p className="text-xs text-red-600 mt-2 text-center font-semibold">{errors.code}</p>}
                    </div>

                    <div className="mt-6 flex flex-col gap-3">
                        <button
                            type="submit"
                            disabled={processing || data.code.length < 6}
                            className="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed shadow-md shadow-indigo-200"
                        >
                            {processing ? 'Memverifikasi...' : 'Aktifkan 2FA & Masuk ke Dashboard'}
                        </button>

                        <Link
                            href={route('landlord.logout')}
                            method="post"
                            as="button"
                            className="w-full text-center text-xs font-semibold text-slate-400 hover:text-slate-600 transition"
                        >
                            Logout / Keluar
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    );
}
