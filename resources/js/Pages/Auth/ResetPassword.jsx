import React, { useState, useEffect, useRef } from 'react';
import { Head, useForm, Link, usePage } from '@inertiajs/react';
import RecaptchaV2 from '@/Components/Auth/RecaptchaV2';
import PasswordStrengthMeter from '@/Components/Auth/PasswordStrengthMeter';

export default function ResetPassword({ token, email }) {
    const { recaptcha, flash } = usePage().props;
    const [isFadeIn, setIsFadeIn] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const recaptchaRef = useRef(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        token: token || '',
        email: email || '',
        password: '',
        password_confirmation: '',
        'g-recaptcha-response': '',
    });

    useEffect(() => {
        setIsFadeIn(true);
        return () => {
            reset('password', 'password_confirmation');
        };
    }, []);

    const handleVerify = (token) => {
        setData('g-recaptcha-response', token);
    };

    const handleExpire = () => {
        setData('g-recaptcha-response', '');
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        post(route('password.store'), {
            onSuccess: () => {
                reset('password', 'password_confirmation');
                if (recaptchaRef.current) recaptchaRef.current.reset();
            },
            onError: () => {
                if (recaptchaRef.current) recaptchaRef.current.reset();
            }
        });
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-indigo-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <Head title="Reset Password - Desa Cibatu" />

            <div className={`sm:mx-auto sm:w-full sm:max-w-md transition-all duration-700 transform ${isFadeIn ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
                {/* Logo */}
                <div className="flex justify-center mb-6">
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
                    <h2 className="text-3xl font-extrabold text-gray-900 mb-2">Reset Password</h2>
                    <p className="text-gray-600 text-sm sm:text-base">Masukkan password baru untuk akun Anda</p>
                    <div className="mt-3 bg-amber-50 border border-amber-200 rounded-xl p-3 inline-flex items-center text-amber-800 text-xs sm:text-sm">
                        <i className="fas fa-clock mr-2 text-amber-600"></i>
                        <span>Link ini berlaku selama 5 menit</span>
                    </div>
                </div>
            </div>

            <div className={`sm:mx-auto sm:w-full sm:max-w-md transition-all duration-700 delay-100 transform ${isFadeIn ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
                {/* Success Alert */}
                {flash?.status && (
                    <div className="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6 shadow-sm flex items-center">
                        <i className="fas fa-check-circle text-green-600 mr-3 text-lg"></i>
                        <div>
                            <h3 className="font-bold text-green-800">Password Berhasil Direset!</h3>
                            <p className="text-green-700 text-sm mt-1">{flash.status}</p>
                        </div>
                    </div>
                )}

                {/* Main Card */}
                <div className="bg-white py-6 sm:py-8 lg:py-10 px-6 sm:px-8 lg:px-10 shadow-2xl rounded-2xl sm:rounded-3xl border border-gray-100 transition-all duration-300 hover:shadow-2xl">
                    <form className="space-y-6 sm:space-y-8" onSubmit={handleSubmit}>
                        {/* Hidden input for Token */}
                        <input type="hidden" name="token" value={data.token} />

                        {/* Readonly Email Input */}
                        <div>
                            <label htmlFor="email" className="block text-sm font-semibold text-gray-800 mb-2">
                                <i className="fas fa-envelope mr-2 text-gray-400"></i>Email
                            </label>
                            <div className="relative group">
                                <div className="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                    <i className="fas fa-envelope text-gray-400 text-sm sm:text-base"></i>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value={data.email}
                                    readOnly
                                    required
                                    className="appearance-none block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 border-2 border-gray-100 bg-gray-50 rounded-xl sm:rounded-2xl text-gray-500 text-sm sm:text-base focus:outline-none"
                                />
                            </div>
                            {errors.email && (
                                <p className="mt-2 text-sm text-red-600 flex items-center">
                                    <i className="fas fa-exclamation-circle mr-2"></i>
                                    {errors.email}
                                </p>
                            )}
                        </div>

                        {/* Password Input */}
                        <div>
                            <label htmlFor="password" className="block text-sm font-semibold text-gray-800 mb-2">
                                <i className="fas fa-lock mr-2 text-gray-400"></i>Password Baru
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
                                    autoComplete="new-password"
                                    placeholder="Masukkan password baru"
                                    className={`appearance-none block w-full pl-10 sm:pl-12 pr-10 sm:pr-12 py-3 sm:py-4 border-2 border-gray-200 rounded-xl sm:rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 text-sm sm:text-base transition-all duration-300 ${
                                        errors.password ? 'border-red-300 focus:ring-red-100 focus:border-red-500' : ''
                                    }`}
                                />
                                <div className="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center">
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(!showPassword)}
                                        className="text-gray-400 hover:text-gray-600 transition-colors"
                                        aria-label={showPassword ? 'Sembunyikan password' : 'Tampilkan password'}
                                    >
                                        <i className={`fas ${showPassword ? 'fa-eye-slash' : 'fa-eye'}`}></i>
                                    </button>
                                </div>
                            </div>

                            {/* Password Strength Meter Component */}
                            <PasswordStrengthMeter password={data.password} />

                            {errors.password && (
                                <p className="mt-2 text-sm text-red-600 flex items-center">
                                    <i className="fas fa-exclamation-circle mr-2"></i>
                                    {errors.password}
                                </p>
                            )}
                        </div>

                        {/* Password Confirmation Input */}
                        <div>
                            <label htmlFor="password_confirmation" className="block text-sm font-semibold text-gray-800 mb-2">
                                <i className="fas fa-lock mr-2 text-gray-400"></i>Konfirmasi Password
                            </label>
                            <div className="relative group">
                                <div className="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                    <i className="fas fa-lock text-gray-400 group-focus-within:text-green-500 transition-colors text-sm sm:text-base"></i>
                                </div>
                                <input
                                    id="password_confirmation"
                                    type={showConfirmPassword ? 'text' : 'password'}
                                    name="password_confirmation"
                                    value={data.password_confirmation}
                                    onChange={(e) => setData('password_confirmation', e.target.value)}
                                    required
                                    autoComplete="new-password"
                                    placeholder="Ulangi password baru"
                                    className={`appearance-none block w-full pl-10 sm:pl-12 pr-10 sm:pr-12 py-3 sm:py-4 border-2 border-gray-200 rounded-xl sm:rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 text-sm sm:text-base transition-all duration-300 ${
                                        errors.password_confirmation ? 'border-red-300 focus:ring-red-100 focus:border-red-500' : ''
                                    }`}
                                />
                                <div className="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center">
                                    <button
                                        type="button"
                                        onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                        className="text-gray-400 hover:text-gray-600 transition-colors"
                                        aria-label={showConfirmPassword ? 'Sembunyikan password' : 'Tampilkan password'}
                                    >
                                        <i className={`fas ${showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'}`}></i>
                                    </button>
                                </div>
                            </div>
                            {errors.password_confirmation && (
                                <p className="mt-2 text-sm text-red-600 flex items-center">
                                    <i className="fas fa-exclamation-circle mr-2"></i>
                                    {errors.password_confirmation}
                                </p>
                            )}
                        </div>

                        {/* reCAPTCHA v2 Checkbox widget */}
                        {recaptcha?.enabled && recaptcha?.v2_site_key && (
                            <div>
                                <RecaptchaV2
                                    ref={recaptchaRef}
                                    siteKey={recaptcha.v2_site_key}
                                    enabled={recaptcha.enabled}
                                    onVerify={handleVerify}
                                    onExpire={handleExpire}
                                />
                                {errors['g-recaptcha-response'] && (
                                    <p className="mt-2 text-sm text-red-600 flex items-center justify-center">
                                        <i className="fas fa-exclamation-circle mr-2"></i>
                                        {errors['g-recaptcha-response']}
                                    </p>
                                )}
                            </div>
                        )}

                        {/* Action Buttons */}
                        <div className="pt-2 space-y-4">
                            <button
                                type="submit"
                                disabled={processing || (recaptcha?.enabled && !data['g-recaptcha-response'])}
                                className="group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-sm sm:text-base font-bold rounded-xl sm:rounded-2xl text-white bg-gradient-to-r from-green-600 via-green-700 to-green-800 hover:from-green-700 hover:via-green-800 hover:to-green-900 focus:outline-none focus:ring-4 focus:ring-green-200 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            >
                                <span className="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <i className="fas fa-save text-green-100 group-hover:text-white transition-colors"></i>
                                </span>
                                <span>{processing ? 'Menyimpan...' : 'Reset Password'}</span>
                            </button>

                            <div className="text-center pt-2">
                                <Link
                                    href={route('login')}
                                    className="inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-semibold rounded-xl text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200"
                                >
                                    <i className="fas fa-arrow-left mr-2"></i>
                                    Kembali ke Login
                                </Link>
                            </div>
                        </div>
                    </form>
                </div>

                {/* Password Criteria box */}
                <div className="mt-6">
                    <div className="bg-white/80 backdrop-blur rounded-2xl p-4 border border-gray-100 shadow-sm">
                        <h3 className="text-gray-800 font-bold mb-2 flex items-center">
                            <i className="fas fa-shield-alt mr-2 text-blue-500"></i>Persyaratan Password Aman:
                        </h3>
                        <ul className="text-gray-600 text-xs sm:text-sm space-y-1">
                            <li className="flex items-center"><i className="fas fa-check mr-2 text-gray-400"></i> Minimal 8 karakter</li>
                            <li className="flex items-center"><i className="fas fa-check mr-2 text-gray-400"></i> Mengandung huruf besar (A-Z) dan kecil (a-z)</li>
                            <li className="flex items-center"><i className="fas fa-check mr-2 text-gray-400"></i> Mengandung angka (0-9)</li>
                            <li className="flex items-center"><i className="fas fa-check mr-2 text-gray-400"></i> Mengandung simbol / karakter khusus (e.g. @, #, $, !)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    );
}
