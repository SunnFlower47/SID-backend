import { useState } from 'react';
import { Head, useForm, Link } from '@inertiajs/react';

export default function TwoFactorVerify() {
    const [useRecoveryCode, setUseRecoveryCode] = useState(false);
    const { data, setData, post, processing, errors } = useForm({
        code: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('landlord.2fa.verify.post'));
    };

    return (
        <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-100 px-4">
            <Head title="Verifikasi 2FA - Admin Panel Central" />

            <div className="w-full sm:max-w-md mt-6 px-8 py-8 bg-white shadow-xl overflow-hidden rounded-2xl border border-slate-200">
                <div className="text-center mb-6">
                    <div className="inline-flex items-center justify-center w-14 h-14 rounded-full bg-indigo-100 text-indigo-600 mb-3 font-bold text-xl">
                        🛡️
                    </div>
                    <h1 className="text-2xl font-bold text-slate-800">Verifikasi 2 Langkah</h1>
                    <p className="text-slate-500 text-sm mt-1">
                        {useRecoveryCode 
                            ? 'Masukkan salah satu kode pemulihan darurat Anda'
                            : 'Masukkan 6 digit kode dari aplikasi autentikator Anda'
                        }
                    </p>
                </div>

                <form onSubmit={submit} className="mt-6">
                    <div>
                        {useRecoveryCode ? (
                            <input
                                type="text"
                                placeholder="XXXXX-XXXXX"
                                value={data.code}
                                onChange={(e) => setData('code', e.target.value.toUpperCase())}
                                className="block w-full text-center text-lg font-mono font-bold py-3 border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm uppercase tracking-wider"
                                autoFocus
                                required
                            />
                        ) : (
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
                        )}
                        {errors.code && <p className="text-xs text-red-600 mt-2 text-center font-semibold">{errors.code}</p>}
                    </div>

                    <div className="mt-4 text-center">
                        <button
                            type="button"
                            onClick={() => {
                                setUseRecoveryCode(!useRecoveryCode);
                                setData('code', '');
                            }}
                            className="text-xs text-indigo-600 hover:text-indigo-800 font-semibold underline"
                        >
                            {useRecoveryCode 
                                ? 'Gunakan kode dari aplikasi autentikator' 
                                : 'Gunakan kode pemulihan darurat (Recovery Code)'
                            }
                        </button>
                    </div>

                    <div className="mt-6 flex flex-col gap-3">
                        <button
                            type="submit"
                            disabled={processing || (!useRecoveryCode && data.code.length < 6)}
                            className="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed shadow-md shadow-indigo-200"
                        >
                            {processing ? 'Memverifikasi...' : 'Verifikasi & Masuk'}
                        </button>

                        <Link
                            href={route('landlord.logout')}
                            method="post"
                            as="button"
                            className="w-full text-center text-xs font-semibold text-slate-400 hover:text-slate-600 transition"
                        >
                            Logout / Ganti Akun
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    );
}
