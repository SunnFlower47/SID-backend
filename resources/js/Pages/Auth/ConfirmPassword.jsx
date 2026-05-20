import React, { useState, useEffect } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';

export default function ConfirmPassword() {
    const { flash } = usePage().props;
    const [isFadeIn, setIsFadeIn] = useState(false);
    const [showPassword, setShowPassword] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        password: '',
    });

    useEffect(() => {
        setIsFadeIn(true);
        return () => {
            reset('password');
        };
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('password.confirm'));
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <Head title="Konfirmasi Password - Desa Cibatu" />

            <div className={`max-w-md w-full space-y-8 transition-all duration-700 transform ${isFadeIn ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
                {/* Logo */}
                <div className="flex justify-center mb-4">
                    <div className="bg-white p-4 rounded-2xl shadow-lg border border-gray-100">
                        <img 
                            src="/assets/images/logo-desa-cibatu.png" 
                            alt="Logo Desa Cibatu" 
                            className="h-16 w-16 mx-auto"
                        />
                    </div>
                </div>

                {/* Title */}
                <div className="text-center mb-8">
                    <h2 className="text-3xl font-extrabold text-gray-900 mb-2">Konfirmasi Password</h2>
                    <p className="text-gray-600 text-sm sm:text-base">Masukkan password untuk melanjutkan</p>
                </div>

                {/* Main Card */}
                <div className="bg-white py-8 px-6 sm:px-8 shadow-2xl rounded-2xl sm:rounded-3xl border border-gray-100 transition-all duration-300 hover:shadow-2xl">
                    <form className="space-y-6" onSubmit={handleSubmit}>
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

                        {/* Submit & Logout Action Buttons */}
                        <div className="pt-2 space-y-4">
                            <button
                                type="submit"
                                disabled={processing}
                                className="group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-base font-bold rounded-xl text-white bg-gradient-to-r from-green-600 via-green-700 to-green-800 hover:from-green-700 hover:via-green-800 hover:to-green-900 focus:outline-none focus:ring-4 focus:ring-green-200 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            >
                                <span className="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <i className="fas fa-check text-green-100 group-hover:text-white transition-colors"></i>
                                </span>
                                <span>{processing ? 'Memproses...' : 'Konfirmasi'}</span>
                            </button>

                            <div className="text-center pt-2">
                                <a
                                    href="/logout"
                                    className="inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-semibold rounded-xl text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none transition-all duration-200"
                                >
                                    <i className="fas fa-sign-out-alt mr-2"></i>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {/* Footer Info Box */}
                <div className="text-center">
                    <div className="bg-white/80 backdrop-blur rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center justify-center space-x-3">
                        <i className="fas fa-shield-alt text-blue-500 text-lg animate-pulse"></i>
                        <p className="text-gray-600 text-sm text-left">
                            Konfirmasi password diperlukan demi keamanan akun Anda sebelum melanjutkan tindakan ini.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}
