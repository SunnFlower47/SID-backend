import React, { useState, useEffect } from 'react';
import { Head, useForm, Link, usePage } from '@inertiajs/react';

export default function Login() {
    const { recaptcha, flash } = usePage().props;
    const [showPassword, setShowPassword] = useState(false);
    const [clientError, setClientError] = useState('');
    const [isFadeIn, setIsFadeIn] = useState(false);

    const { data, setData, post, processing, errors, reset, transform } = useForm({
        email: '',
        password: '',
        remember: false,
        recaptcha_token: '',
    });

    useEffect(() => {
        setIsFadeIn(true);

        // Dynamically load reCAPTCHA v3 script if enabled
        if (recaptcha?.enabled && recaptcha?.v3_site_key) {
            const scriptId = 'recaptcha-v3-script';
            let script = document.getElementById(scriptId);
            if (!script) {
                script = document.createElement('script');
                script.id = scriptId;
                script.src = `https://www.recaptcha.net/recaptcha/api.js?render=${recaptcha.v3_site_key}`;
                script.async = true;
                script.defer = true;
                document.body.appendChild(script);
            }
        }

        return () => {
            reset('password');
        };
    }, []);

    const executeRecaptcha = () => {
        return new Promise((resolve, reject) => {
            if (!recaptcha?.enabled || !recaptcha?.v3_site_key) {
                resolve('');
                return;
            }

            if (!window.grecaptcha || !window.grecaptcha.execute) {
                reject(new Error('reCAPTCHA tidak termuat dengan benar. Silakan coba lagi.'));
                return;
            }

            window.grecaptcha.ready(() => {
                window.grecaptcha.execute(recaptcha.v3_site_key, { action: 'login' })
                    .then(resolve)
                    .catch(reject);
            });
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setClientError('');

        try {
            let token = '';
            if (recaptcha?.enabled) {
                token = await executeRecaptcha();
                // Attempt retry once if empty
                if (!token) {
                    token = await executeRecaptcha();
                }
            }

            // Gunakan transform agar data terupdate seketika sebelum disubmit ke backend
            transform((data) => ({
                ...data,
                recaptcha_token: token
            }));

            // Submit menggunakan Inertia post
            post(route('login'), {
                onError: () => {
                    // Flash errors handle
                }
            });
        } catch (err) {
            console.error('reCAPTCHA error:', err);
            setClientError('Verifikasi keamanan gagal. Silakan klik tombol Masuk lagi.');
        }
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-indigo-100 flex items-center justify-center py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
            <Head title="Login Admin - Desa Cibatu" />

            <div className={`max-w-md w-full space-y-6 sm:space-y-8 transition-all duration-700 transform ${isFadeIn ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
                {/* Header */}
                <div className="text-center">
                    <div className="flex items-center justify-center space-x-2 sm:space-x-3 mb-4 sm:mb-6">
                        <img 
                            src="/assets/images/logo-desa-cibatu.png" 
                            alt="Logo Desa Cibatu" 
                            className="h-10 w-10 sm:h-12 sm:w-12 rounded-lg shadow-md"
                        />
                        <div className="text-left">
                            <h1 className="text-xl sm:text-2xl font-bold text-gray-900 leading-tight">Admin Panel</h1>
                            <p className="text-xs sm:text-sm text-gray-600">Desa Cibatu, Purwakarta</p>
                        </div>
                    </div>
                    <h2 className="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2">Masuk ke Dashboard</h2>
                    <p className="text-sm sm:text-base text-gray-600">Kelola sistem administrasi desa dengan mudah</p>
                </div>

                {/* Login Card */}
                <div className="bg-white py-6 sm:py-8 lg:py-10 px-6 sm:px-8 lg:px-10 shadow-2xl rounded-2xl sm:rounded-3xl border border-gray-100 transition-all duration-300 hover:shadow-2xl">
                    <form className="space-y-6" onSubmit={handleSubmit}>
                        {/* Flash message */}
                        {flash?.success && (
                            <div className="p-4 mb-4 text-sm text-green-800 bg-green-50 border border-green-200 rounded-xl flex items-center animate-fade-in">
                                <i className="fas fa-check-circle mr-2 text-green-600"></i>
                                <span>{flash.success}</span>
                            </div>
                        )}
                        {flash?.error && (
                            <div className="p-4 mb-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded-xl flex items-center animate-fade-in">
                                <i className="fas fa-exclamation-circle mr-2 text-red-600"></i>
                                <span>{flash.error}</span>
                            </div>
                        )}

                        {/* Email Field */}
                        <div>
                            <label htmlFor="email" className="block text-sm font-semibold text-gray-800 mb-2">
                                Email Admin
                            </label>
                            <div className="relative group">
                                <div className="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                    <i className="fas fa-envelope text-gray-400 group-focus-within:text-green-500 transition-colors text-sm sm:text-base"></i>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    required
                                    autoComplete="email"
                                    placeholder="admin@desacibatu.com"
                                    className={`appearance-none block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 border-2 border-gray-200 rounded-xl sm:rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 text-sm sm:text-base transition-all duration-300 ${
                                        errors.email ? 'border-red-300 focus:ring-red-100 focus:border-red-500' : ''
                                    }`}
                                />
                            </div>
                            {errors.email && (
                                <p className="mt-2 text-sm text-red-600 flex items-center">
                                    <i className="fas fa-exclamation-circle mr-2"></i>
                                    {errors.email}
                                </p>
                            )}
                        </div>

                        {/* Password Field */}
                        <div>
                            <label htmlFor="password" className="block text-sm font-semibold text-gray-800 mb-2">
                                Password
                            </label>
                            <div className="relative group">
                                <div className="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                    <i className="fas fa-lock text-gray-400 group-focus-within:text-green-500 transition-colors text-sm sm:text-base"></i>
                                </div>
                                <input
                                    id="password"
                                    type={showPassword ? 'text' : 'password'}
                                    name="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    required
                                    autoComplete="current-password"
                                    placeholder="Masukkan password"
                                    className={`appearance-none block w-full pl-10 sm:pl-12 pr-12 sm:pr-14 py-3 sm:py-4 border-2 border-gray-200 rounded-xl sm:rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 text-sm sm:text-base transition-all duration-300 ${
                                        errors.password ? 'border-red-300 focus:ring-red-100 focus:border-red-500' : ''
                                    }`}
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center text-gray-400 hover:text-green-600 transition-colors"
                                    aria-label={showPassword ? 'Sembunyikan password' : 'Tampilkan password'}
                                >
                                    <i className={`fas ${showPassword ? 'fa-eye-slash' : 'fa-eye'} text-sm sm:text-base`}></i>
                                </button>
                            </div>
                            {errors.password && (
                                <p className="mt-2 text-sm text-red-600 flex items-center">
                                    <i className="fas fa-exclamation-circle mr-2"></i>
                                    {errors.password}
                                </p>
                            )}
                        </div>

                        {/* Remember Me & Forgot Password */}
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <div className="flex items-center">
                                <input
                                    id="remember"
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={(e) => setData('remember', e.target.checked)}
                                    className="h-4 w-4 sm:h-5 sm:w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded-lg transition-colors cursor-pointer"
                                />
                                <label htmlFor="remember" className="ml-2 sm:ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                                    Ingat saya
                                </label>
                            </div>

                            <div className="text-sm">
                                <Link
                                    href={route('password.request')}
                                    className="font-semibold text-green-600 hover:text-green-700 transition-colors"
                                >
                                    Lupa password?
                                </Link>
                            </div>
                        </div>

                        {/* Client/Server reCAPTCHA errors */}
                        {errors.recaptcha_token && (
                            <p className="text-sm text-red-600 flex items-center">
                                <i className="fas fa-exclamation-circle mr-2"></i>
                                {errors.recaptcha_token}
                            </p>
                        )}
                        {clientError && (
                            <p className="text-sm text-red-600 flex items-center">
                                <i className="fas fa-exclamation-circle mr-2"></i>
                                {clientError}
                            </p>
                        )}

                        {/* Submit Button */}
                        <div className="pt-2">
                            <button
                                type="submit"
                                disabled={processing}
                                className="group relative w-full flex justify-center py-3 sm:py-4 px-4 sm:px-6 border border-transparent text-sm sm:text-base font-bold rounded-xl sm:rounded-2xl text-white bg-gradient-to-r from-green-600 via-green-700 to-green-800 hover:from-green-700 hover:via-green-800 hover:to-green-900 focus:outline-none focus:ring-4 focus:ring-green-200 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-0.5 disabled:opacity-75 disabled:cursor-not-allowed disabled:transform-none"
                            >
                                <span className="absolute left-0 inset-y-0 flex items-center pl-4 sm:pl-6">
                                    <i className="fas fa-sign-in-alt text-green-100 group-hover:text-white transition-colors text-sm sm:text-base"></i>
                                </span>
                                <span className="flex items-center text-base sm:text-lg">
                                    <i className="fas fa-arrow-right mr-2 sm:mr-3"></i>
                                    {processing ? 'Memproses...' : (
                                        <>
                                            <span className="hidden sm:inline">Masuk ke Dashboard Admin</span>
                                            <span className="sm:hidden">Masuk Admin</span>
                                        </>
                                    )}
                                </span>
                            </button>
                        </div>
                    </form>

                    {/* Back to welcome */}
                    <div className="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-100">
                        <div className="text-center">
                            <a
                                href="/"
                                className="inline-flex items-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg sm:rounded-xl transition-all duration-200"
                            >
                                <i className="fas fa-arrow-left mr-1 sm:mr-2"></i>
                                <span className="hidden sm:inline">Kembali ke Halaman Utama</span>
                                <span className="sm:hidden">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
