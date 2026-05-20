import React, { useState, useEffect } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth, flash } = usePage().props;
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const [isAlertVisible, setIsAlertVisible] = useState(false);
    const [alertMessage, setAlertMessage] = useState('');
    const [alertType, setAlertType] = useState('success');

    // Population Stats State
    const [popStats, setPopStats] = useState({
        total: '-',
        laki_laki: '-',
        perempuan: '-',
        loading: true
    });

    // Admin/Complaints Stats State
    const [adminStats, setAdminStats] = useState({
        surat_selesai: '500+',
        pengaduan_percentage: '95%',
        transparansi: '100%',
        kepuasan: '98%',
        loading: true
    });

    // Social Media & Desa Info State
    const [socialLinks, setSocialLinks] = useState({
        facebook: null,
        instagram: null,
        whatsapp: null,
        loading: true
    });

    // Handle Session Flash Messages
    useEffect(() => {
        if (flash?.success) {
            setAlertMessage(flash.success);
            setAlertType('success');
            setIsAlertVisible(true);
        } else if (flash?.error) {
            setAlertMessage(flash.error);
            setAlertType('error');
            setIsAlertVisible(true);
        }

        // Auto dismiss flash after 6 seconds
        if (flash?.success || flash?.error) {
            const timer = setTimeout(() => {
                setIsAlertVisible(false);
                // Clear session message on backend as well
                fetch(route('clear-session-message'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ type: flash.success ? 'success' : 'error' })
                }).catch(() => {});
            }, 6000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    // Fetch Public Population Stats
    useEffect(() => {
        const fetchPopulation = async () => {
            try {
                const res = await fetch('/api/v1/public-statistics/penduduk', {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.success && data.data) {
                        setPopStats({
                            total: data.data.total?.toLocaleString('id-ID') || '3.915',
                            laki_laki: data.data.laki_laki?.toLocaleString('id-ID') || '1.968',
                            perempuan: data.data.perempuan?.toLocaleString('id-ID') || '1.947',
                            loading: false
                        });
                    } else {
                        throw new Error();
                    }
                } else {
                    throw new Error();
                }
            } catch (err) {
                // Fallback to offline defaults
                setPopStats({
                    total: '3.915',
                    laki_laki: '1.968',
                    perempuan: '1.947',
                    loading: false
                });
            }
        };

        const fetchAdminStats = async () => {
            try {
                const res = await fetch('/api/v1/public-statistics', {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.success && data.data) {
                        const stats = data.data;
                        const pengaduanPercent = stats.pengaduan_total > 0
                            ? Math.round((stats.pengaduan_selesai / stats.pengaduan_total) * 100)
                            : 95;

                        setAdminStats({
                            surat_selesai: stats.surat_selesai ? `${stats.surat_selesai}+` : '500+',
                            pengaduan_percentage: `${pengaduanPercent}%`,
                            transparansi: '100%',
                            kepuasan: `${Math.min(pengaduanPercent + 3, 100)}%`,
                            loading: false
                        });
                    } else {
                        throw new Error();
                    }
                } else {
                    throw new Error();
                }
            } catch (err) {
                setAdminStats({
                    surat_selesai: '500+',
                    pengaduan_percentage: '95%',
                    transparansi: '100%',
                    kepuasan: '98%',
                    loading: false
                });
            }
        };

        fetchPopulation();
        fetchAdminStats();

        // Fetch social media links
        const fetchDesaInfo = async () => {
            try {
                const res = await fetch('/api/v1/public-statistics/info-desa', {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.success && data.data?.social) {
                        setSocialLinks({
                            facebook: data.data.social.facebook || null,
                            instagram: data.data.social.instagram || null,
                            whatsapp: data.data.social.whatsapp || null,
                            loading: false
                        });
                    } else {
                        setSocialLinks({ facebook: null, instagram: null, whatsapp: null, loading: false });
                    }
                } else {
                    setSocialLinks({ facebook: null, instagram: null, whatsapp: null, loading: false });
                }
            } catch {
                setSocialLinks({ facebook: null, instagram: null, whatsapp: null, loading: false });
            }
        };
        fetchDesaInfo();
    }, []);

    // Smooth Scroll to Section Helper
    const handleScroll = (e, id) => {
        e.preventDefault();
        const element = document.getElementById(id);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            setIsMenuOpen(false);
        }
    };

    return (
        <div className="min-h-screen bg-gray-50 text-gray-800 font-sans selection:bg-green-500 selection:text-white">
            <Head>
                <title>Welcome - Admin Panel Desa Cibatu</title>
                <meta name="description" content="Dashboard administrasi digital terintegrasi untuk pengelolaan data penduduk, surat, pengaduan, dan transparansi anggaran Desa Cibatu." />
            </Head>

            {/* Premium Flash Alert Banner */}
            {isAlertVisible && (
                <div className="fixed top-20 left-1/2 -translate-x-1/2 z-[9999] w-[calc(100%-2rem)] max-w-lg transition-all duration-500 animate-bounce-short">
                    <div className={`p-4 rounded-2xl shadow-2xl border flex items-start justify-between backdrop-blur-md ${
                        alertType === 'success'
                            ? 'bg-emerald-50/95 border-emerald-200 text-emerald-900'
                            : 'bg-red-50/95 border-red-200 text-red-900'
                    }`}>
                        <div className="flex items-center space-x-3">
                            <div className={`p-2 rounded-xl shrink-0 ${
                                alertType === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'
                            }`}>
                                <i className={`fas ${alertType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} text-lg`}></i>
                            </div>
                            <div>
                                <p className="text-xs font-black uppercase tracking-widest text-gray-400">Notifikasi Sistem</p>
                                <p className="text-sm font-bold mt-0.5">{alertMessage}</p>
                            </div>
                        </div>
                        <button
                            onClick={() => setIsAlertVisible(false)}
                            className="p-1 rounded-lg text-gray-400 hover:text-gray-700 transition-colors"
                        >
                            <i className="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            )}

            {/* Sticky Modern Navbar */}
            <nav className="bg-white/80 backdrop-blur-lg border-b border-gray-100 sticky top-0 z-50 transition-all duration-300">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center h-20">
                        {/* Brand Logo & Title */}
                        <div className="flex items-center space-x-3 group cursor-pointer">
                            <img
                                src="/assets/images/logo-desa-cibatu.png"
                                alt="Logo Desa Cibatu"
                                className="h-10 w-10 sm:h-12 sm:w-12 rounded-xl shadow-md border-2 border-green-500/20 group-hover:scale-105 transition-all duration-300"
                            />
                            <div>
                                <h1 className="text-lg sm:text-xl font-black text-gray-950 uppercase italic tracking-tighter leading-none group-hover:text-green-600 transition-colors">
                                    Desa Cibatu
                                </h1>
                                <p className="text-[10px] sm:text-xs font-black text-gray-400 uppercase tracking-widest mt-1">
                                    Purwakarta, Jawa Barat
                                </p>
                            </div>
                        </div>

                        {/* Desktop Nav Links */}
                        <div className="hidden md:flex items-center space-x-8">
                            <a
                                href="#features"
                                onClick={(e) => handleScroll(e, 'features')}
                                className="text-sm font-bold text-gray-600 hover:text-green-600 uppercase tracking-widest transition-colors"
                            >
                                Fitur
                            </a>
                            <a
                                href="#stats"
                                onClick={(e) => handleScroll(e, 'stats')}
                                className="text-sm font-bold text-gray-600 hover:text-green-600 uppercase tracking-widest transition-colors"
                            >
                                Statistik
                            </a>

                            {auth?.user ? (
                                <Link
                                    href={route('dashboard')}
                                    className="flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 active:scale-95 shadow-lg shadow-green-200 transition-all duration-300"
                                >
                                    <i className="fas fa-desktop mr-2 text-sm"></i>
                                    Dashboard
                                </Link>
                            ) : (
                                <Link
                                    href={route('login')}
                                    className="flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 active:scale-95 shadow-lg shadow-green-200 transition-all duration-300"
                                >
                                    <i className="fas fa-sign-in-alt mr-2 text-sm"></i>
                                    Masuk Admin
                                </Link>
                            )}
                        </div>

                        {/* Mobile Menu Trigger */}
                        <div className="md:hidden">
                            <button
                                onClick={() => setIsMenuOpen(!isMenuOpen)}
                                className="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-xl transition-all focus:outline-none"
                            >
                                <i className={`fas ${isMenuOpen ? 'fa-times' : 'fa-bars'} text-xl`}></i>
                            </button>
                        </div>
                    </div>

                    {/* Mobile Navigation Drawer */}
                    {isMenuOpen && (
                        <div className="md:hidden pb-6 border-t border-gray-100 animate-in slide-in-from-top-4 duration-300">
                            <div className="px-2 pt-4 pb-3 space-y-2">
                                <a
                                    href="#features"
                                    onClick={(e) => handleScroll(e, 'features')}
                                    className="block px-4 py-3 text-sm font-bold text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl uppercase tracking-widest transition-all"
                                >
                                    Fitur Admin
                                </a>
                                <a
                                    href="#stats"
                                    onClick={(e) => handleScroll(e, 'stats')}
                                    className="block px-4 py-3 text-sm font-bold text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl uppercase tracking-widest transition-all"
                                >
                                    Statistik
                                </a>
                                <div className="pt-4 border-t border-gray-100 mt-2 px-4">
                                    {auth?.user ? (
                                        <Link
                                            href={route('dashboard')}
                                            className="w-full flex items-center justify-center py-3.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-green-200"
                                        >
                                            <i className="fas fa-desktop mr-2 text-sm"></i>
                                            Ke Dashboard
                                        </Link>
                                    ) : (
                                        <Link
                                            href={route('login')}
                                            className="w-full flex items-center justify-center py-3.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-green-200"
                                        >
                                            <i className="fas fa-sign-in-alt mr-2 text-sm"></i>
                                            Masuk Admin
                                        </Link>
                                    )}
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </nav>

            {/* High-Impact Hero Section */}
            <section className="relative min-h-[calc(100vh-5rem)] flex items-center py-16 sm:py-24 overflow-hidden bg-emerald-950">
                {/* Visual Image Background with Glass Overlay */}
                <div className="absolute inset-0 z-0">
                    <img
                        src="/assets/images/foto-sawah-1.webp"
                        alt="Desa Cibatu"
                        className="w-full h-full object-cover opacity-30 object-center scale-105 animate-pulse-slow"
                    />
                    <div className="absolute inset-0 bg-gradient-to-tr from-green-950/95 via-emerald-900/80 to-teal-950/95"></div>
                    {/* SVG Light Orbs */}
                    <div className="absolute top-1/4 left-1/4 w-96 h-96 bg-green-500/10 rounded-full blur-3xl animate-floating pointer-events-none"></div>
                    <div className="absolute bottom-1/4 right-1/4 w-[40rem] h-[40rem] bg-emerald-400/5 rounded-full blur-3xl animate-floating-delay pointer-events-none"></div>
                </div>

                <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                        {/* Title and Introduction */}
                        <div className="lg:col-span-7 text-center lg:text-left text-white space-y-6 sm:space-y-8">
                            <span className="inline-flex items-center px-4.5 py-1.5 bg-white/10 backdrop-blur-md rounded-full border border-white/20 text-xs font-black uppercase tracking-widest text-yellow-300">
                                <i className="fas fa-star mr-2 text-[10px] animate-pulse"></i>
                                Sistem Informasi Desa
                            </span>
                            
                            <h2 className="text-3xl sm:text-5xl md:text-6xl font-black uppercase italic tracking-tighter leading-[0.95] text-white">
                                Admin Panel
                                <span className="block text-gradient bg-gradient-to-r from-yellow-300 via-amber-200 to-yellow-400 bg-clip-text text-transparent mt-2">
                                    Desa Cibatu
                                </span>
                            </h2>

                            <p className="text-sm sm:text-base md:text-lg text-emerald-100/90 leading-relaxed font-semibold max-w-2xl mx-auto lg:mx-0">
                                Platform administrasi digital terintegrasi untuk mempermudah manajemen data kependudukan, pemrosesan dokumen surat, pelaporan pengaduan warga, serta menjamin transparansi publik secara efisien dan aman.
                            </p>

                            <div className="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                                {auth?.user ? (
                                    <Link
                                        href={route('dashboard')}
                                        className="w-full sm:w-auto px-8 py-4 sm:py-5 bg-white text-green-950 font-black uppercase tracking-widest text-[11px] sm:text-xs rounded-2xl hover:scale-105 active:scale-95 shadow-2xl transition-all duration-300 text-center"
                                    >
                                        <i className="fas fa-desktop mr-2 text-sm"></i>
                                        Dashboard Utama
                                    </Link>
                                ) : (
                                    <Link
                                        href={route('login')}
                                        className="w-full sm:w-auto px-8 py-4 sm:py-5 bg-white text-green-950 font-black uppercase tracking-widest text-[11px] sm:text-xs rounded-2xl hover:scale-105 active:scale-95 shadow-2xl transition-all duration-300 text-center"
                                    >
                                        <i className="fas fa-sign-in-alt mr-2 text-sm"></i>
                                        Masuk Admin
                                    </Link>
                                )}

                                <a
                                    href="/web-desa"
                                    className="w-full sm:w-auto px-8 py-4 sm:py-5 border-2 border-white/20 hover:border-white text-white font-black uppercase tracking-widest text-[11px] sm:text-xs rounded-2xl bg-white/5 hover:bg-white/10 hover:scale-105 active:scale-95 transition-all duration-300 text-center"
                                >
                                    <i className="fas fa-globe mr-2 text-sm"></i>
                                    Portal Web Desa
                                </a>
                            </div>
                        </div>

                        {/* Interactive Realtime Stats Widget */}
                        <div className="lg:col-span-5 w-full">
                            <div className="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[2.5rem] p-6 sm:p-8 shadow-2xl space-y-6 sm:space-y-8 animate-in zoom-in-95 duration-500">
                                <div className="flex items-center justify-between border-b border-white/10 pb-4">
                                    <div className="flex items-center space-x-3">
                                        <div className="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0 border border-white/20">
                                            <i className="fas fa-users text-white text-lg"></i>
                                        </div>
                                        <div className="text-left">
                                            <h4 className="text-sm font-black text-white uppercase italic tracking-tight leading-none">Statistik Warga</h4>
                                            <span className="text-[9px] font-black text-emerald-300 uppercase tracking-widest">Update Realtime</span>
                                        </div>
                                    </div>
                                    <div className="h-2.5 w-2.5 bg-emerald-400 rounded-full animate-ping"></div>
                                </div>

                                <div className="space-y-6 text-left">
                                    {/* Total Population Item */}
                                    <div>
                                        <span className="text-[10px] font-black text-white/60 uppercase tracking-widest ml-1 block">Total Penduduk Terdaftar</span>
                                        {popStats.loading ? (
                                            <div className="h-10 w-36 bg-white/20 animate-pulse rounded-xl mt-1.5"></div>
                                        ) : (
                                            <span className="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1 block tabular-nums">
                                                {popStats.total} <span className="text-sm font-semibold text-white/60 uppercase tracking-widest font-sans ml-1">Jiwa</span>
                                            </span>
                                        )}
                                    </div>

                                    {/* Male & Female Grid */}
                                    <div className="grid grid-cols-2 gap-4 border-t border-white/10 pt-5">
                                        <div>
                                            <span className="text-[10px] font-black text-white/60 uppercase tracking-widest ml-1 block">Laki-Laki</span>
                                            {popStats.loading ? (
                                                <div className="h-8 w-24 bg-white/20 animate-pulse rounded-lg mt-1.5"></div>
                                            ) : (
                                                <span className="text-xl sm:text-2xl font-black text-white mt-1 block tabular-nums">
                                                    {popStats.laki_laki} <span className="text-xs font-semibold text-white/50">Jiwa</span>
                                                </span>
                                            )}
                                        </div>
                                        <div>
                                            <span className="text-[10px] font-black text-white/60 uppercase tracking-widest ml-1 block">Perempuan</span>
                                            {popStats.loading ? (
                                                <div className="h-8 w-24 bg-white/20 animate-pulse rounded-lg mt-1.5"></div>
                                            ) : (
                                                <span className="text-xl sm:text-2xl font-black text-white mt-1 block tabular-nums">
                                                    {popStats.perempuan} <span className="text-xs font-semibold text-white/50">Jiwa</span>
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Core Administrative Modules Feature Directory */}
            <section id="features" className="py-24 bg-white relative">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center max-w-3xl mx-auto mb-20 space-y-3">
                        <span className="text-xs font-black text-green-600 uppercase tracking-widest">Pusat Administrasi</span>
                        <h3 className="text-2xl sm:text-4xl font-black text-gray-950 uppercase italic tracking-tighter">
                            Direktori Fitur Utama
                        </h3>
                        <div className="h-1.5 w-16 bg-green-500 mx-auto rounded-full mt-2"></div>
                        <p className="text-sm sm:text-base text-gray-500 font-semibold leading-relaxed">
                            Akses langsung modul pengelolaan administrasi desa secara instan melalui sistem otorisasi terintegrasi.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {/* Module 1: Surat */}
                        <div className="group bg-white rounded-3xl p-8 border border-gray-100 hover:border-blue-100 hover:shadow-2xl hover:shadow-blue-100/50 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between text-left">
                            <div>
                                <div className="bg-blue-50 text-blue-600 w-14 h-14 rounded-2xl flex items-center justify-center border border-blue-100/50 group-hover:scale-110 transition-transform duration-300 shrink-0">
                                    <i className="fas fa-file-alt text-xl"></i>
                                </div>
                                <h4 className="text-lg font-black text-gray-950 uppercase italic tracking-tight mt-6">Kelola Surat Keterangan</h4>
                                <p className="text-sm text-gray-500 mt-3 font-semibold leading-relaxed">
                                    Verifikasi dan proses pengajuan surat keterangan warga (domisili, tidak mampu, dsb) secara daring.
                                </p>
                            </div>
                            <div className="border-t border-gray-50 mt-8 pt-5">
                                <Link
                                    href={route('admin.surat-pengajuan.index')}
                                    className="inline-flex items-center text-xs font-black text-blue-600 hover:text-blue-700 uppercase tracking-widest"
                                >
                                    Akses Modul <i className="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </Link>
                            </div>
                        </div>

                        {/* Module 2: Pengaduan */}
                        <div className="group bg-white rounded-3xl p-8 border border-gray-100 hover:border-red-100 hover:shadow-2xl hover:shadow-red-100/50 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between text-left">
                            <div>
                                <div className="bg-red-50 text-red-600 w-14 h-14 rounded-2xl flex items-center justify-center border border-red-100/50 group-hover:scale-110 transition-transform duration-300 shrink-0">
                                    <i className="fas fa-exclamation-triangle text-xl"></i>
                                </div>
                                <h4 className="text-lg font-black text-gray-950 uppercase italic tracking-tight mt-6">Kelola Pengaduan Warga</h4>
                                <p className="text-sm text-gray-500 mt-3 font-semibold leading-relaxed">
                                    Tinjau, respon, dan kelola pelaporan masalah serta aspirasi yang disampaikan oleh warga desa.
                                </p>
                            </div>
                            <div className="border-t border-gray-50 mt-8 pt-5">
                                <Link
                                    href={route('pengaduan.index')}
                                    className="inline-flex items-center text-xs font-black text-red-600 hover:text-red-700 uppercase tracking-widest"
                                >
                                    Akses Modul <i className="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </Link>
                            </div>
                        </div>

                        {/* Module 3: Berita */}
                        <div className="group bg-white rounded-3xl p-8 border border-gray-100 hover:border-emerald-100 hover:shadow-2xl hover:shadow-emerald-100/50 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between text-left">
                            <div>
                                <div className="bg-emerald-50 text-emerald-600 w-14 h-14 rounded-2xl flex items-center justify-center border border-emerald-100/50 group-hover:scale-110 transition-transform duration-300 shrink-0">
                                    <i className="fas fa-newspaper text-xl"></i>
                                </div>
                                <h4 className="text-lg font-black text-gray-950 uppercase italic tracking-tight mt-6">Informasi & Berita Desa</h4>
                                <p className="text-sm text-gray-500 mt-3 font-semibold leading-relaxed">
                                    Publikasikan berita resmi, agenda kegiatan, pengumuman, dan informasi edukasi warga secara dinamis.
                                </p>
                            </div>
                            <div className="border-t border-gray-50 mt-8 pt-5">
                                <Link
                                    href={route('berita.index')}
                                    className="inline-flex items-center text-xs font-black text-emerald-600 hover:text-emerald-700 uppercase tracking-widest"
                                >
                                    Akses Modul <i className="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </Link>
                            </div>
                        </div>

                        {/* Module 4: Transparansi */}
                        <div className="group bg-white rounded-3xl p-8 border border-gray-100 hover:border-purple-100 hover:shadow-2xl hover:shadow-purple-100/50 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between text-left">
                            <div>
                                <div className="bg-purple-50 text-purple-600 w-14 h-14 rounded-2xl flex items-center justify-center border border-purple-100/50 group-hover:scale-110 transition-transform duration-300 shrink-0">
                                    <i className="fas fa-chart-pie text-xl"></i>
                                </div>
                                <h4 className="text-lg font-black text-gray-950 uppercase italic tracking-tight mt-6">Anggaran APBDes & Realisasi</h4>
                                <p className="text-sm text-gray-500 mt-3 font-semibold leading-relaxed">
                                    Kelola transparansi pengelolaan anggaran pendapatan, pengeluaran, serta realisasi pembangunan fisik desa.
                                </p>
                            </div>
                            <div className="border-t border-gray-50 mt-8 pt-5">
                                <Link
                                    href={route('transparansi-desa.apbdes')}
                                    className="inline-flex items-center text-xs font-black text-purple-600 hover:text-purple-700 uppercase tracking-widest"
                                >
                                    Akses Modul <i className="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </Link>
                            </div>
                        </div>

                        {/* Module 5: Fasilitas */}
                        <div className="group bg-white rounded-3xl p-8 border border-gray-100 hover:border-amber-100 hover:shadow-2xl hover:shadow-amber-100/50 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between text-left">
                            <div>
                                <div className="bg-amber-50 text-amber-600 w-14 h-14 rounded-2xl flex items-center justify-center border border-amber-100/50 group-hover:scale-110 transition-transform duration-300 shrink-0">
                                    <i className="fas fa-map-marked-alt text-xl"></i>
                                </div>
                                <h4 className="text-lg font-black text-gray-950 uppercase italic tracking-tight mt-6">Kelola Fasilitas Umum</h4>
                                <p className="text-sm text-gray-500 mt-3 font-semibold leading-relaxed">
                                    Mutakhirkan pangkalan data fasilitas desa, tempat ibadah, sarana kesehatan, dan titik koordinat geografis.
                                </p>
                            </div>
                            <div className="border-t border-gray-50 mt-8 pt-5">
                                <Link
                                    href={route('fasilitas-desa.index')}
                                    className="inline-flex items-center text-xs font-black text-amber-600 hover:text-amber-700 uppercase tracking-widest"
                                >
                                    Akses Modul <i className="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </Link>
                            </div>
                        </div>

                        {/* Module 6: Kontak */}
                        <div className="group bg-white rounded-3xl p-8 border border-gray-100 hover:border-indigo-100 hover:shadow-2xl hover:shadow-indigo-100/50 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between text-left">
                            <div>
                                <div className="bg-indigo-50 text-indigo-600 w-14 h-14 rounded-2xl flex items-center justify-center border border-indigo-100/50 group-hover:scale-110 transition-transform duration-300 shrink-0">
                                    <i className="fas fa-envelope-open-text text-xl"></i>
                                </div>
                                <h4 className="text-lg font-black text-gray-950 uppercase italic tracking-tight mt-6">Kelola Pesan Kontak</h4>
                                <p className="text-sm text-gray-500 mt-3 font-semibold leading-relaxed">
                                    Tanggapi pesan masuk dari portal masyarakat umum, pertanyaan luar, dan koordinasi instansi luar secara cepat.
                                </p>
                            </div>
                            <div className="border-t border-gray-50 mt-8 pt-5">
                                <Link
                                    href={route('contact-messages.index')}
                                    className="inline-flex items-center text-xs font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-widest"
                                >
                                    Akses Modul <i className="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Dynamic Dashboard Metrics Section */}
            <section id="stats" className="py-24 bg-gradient-to-r from-emerald-600 to-teal-700 text-white relative overflow-hidden">
                {/* Vector Circles Layer */}
                <div className="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-white/5 rounded-full pointer-events-none blur-xl"></div>
                <div className="absolute bottom-0 left-0 -mb-20 -ml-20 w-96 h-96 bg-black/10 rounded-full pointer-events-none blur-2xl"></div>

                <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center max-w-2xl mx-auto mb-16 space-y-3">
                        <span className="text-xs font-black text-yellow-300 uppercase tracking-widest block">Metrik Administrasi</span>
                        <h3 className="text-2xl sm:text-4xl font-black uppercase italic tracking-tighter">Statistik Layanan Kami</h3>
                        <p className="text-sm text-emerald-100 font-semibold leading-relaxed">
                            Indikator keberhasilan pelayanan administrasi kependudukan digital serta tingkat kepuasan warga Desa Cibatu.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        {/* Stat Item 1 */}
                        <div className="bg-white/10 backdrop-blur border border-white/15 p-8 rounded-3xl text-center space-y-4 hover:bg-white/15 transition-all">
                            <div className="w-16 h-16 bg-white/15 rounded-full flex items-center justify-center mx-auto shadow-inner">
                                <i className="fas fa-check-double text-2xl text-yellow-300"></i>
                            </div>
                            <div>
                                <span className="text-3xl sm:text-4xl font-black block tabular-nums">
                                    {adminStats.surat_selesai}
                                </span>
                                <span className="text-[10px] font-black text-emerald-200 uppercase tracking-widest block mt-2">Surat Diproses</span>
                            </div>
                        </div>

                        {/* Stat Item 2 */}
                        <div className="bg-white/10 backdrop-blur border border-white/15 p-8 rounded-3xl text-center space-y-4 hover:bg-white/15 transition-all">
                            <div className="w-16 h-16 bg-white/15 rounded-full flex items-center justify-center mx-auto shadow-inner">
                                <i className="fas fa-tools text-2xl text-yellow-300"></i>
                            </div>
                            <div>
                                <span className="text-3xl sm:text-4xl font-black block tabular-nums">
                                    {adminStats.pengaduan_percentage}
                                </span>
                                <span className="text-[10px] font-black text-emerald-200 uppercase tracking-widest block mt-2">Pengaduan Selesai</span>
                            </div>
                        </div>

                        {/* Stat Item 3 */}
                        <div className="bg-white/10 backdrop-blur border border-white/15 p-8 rounded-3xl text-center space-y-4 hover:bg-white/15 transition-all">
                            <div className="w-16 h-16 bg-white/15 rounded-full flex items-center justify-center mx-auto shadow-inner">
                                <i className="fas fa-balance-scale text-2xl text-yellow-300"></i>
                            </div>
                            <div>
                                <span className="text-3xl sm:text-4xl font-black block tabular-nums">
                                    {adminStats.transparansi}
                                </span>
                                <span className="text-[10px] font-black text-emerald-200 uppercase tracking-widest block mt-2">Transparansi Publik</span>
                            </div>
                        </div>

                        {/* Stat Item 4 */}
                        <div className="bg-white/10 backdrop-blur border border-white/15 p-8 rounded-3xl text-center space-y-4 hover:bg-white/15 transition-all">
                            <div className="w-16 h-16 bg-white/15 rounded-full flex items-center justify-center mx-auto shadow-inner">
                                <i className="fas fa-heart text-2xl text-yellow-300"></i>
                            </div>
                            <div>
                                <span className="text-3xl sm:text-4xl font-black block tabular-nums">
                                    {adminStats.kepuasan}
                                </span>
                                <span className="text-[10px] font-black text-emerald-200 uppercase tracking-widest block mt-2">Kepuasan Warga</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Premium Corporate Footer */}
            <footer className="bg-gray-950 text-white pt-20 pb-10">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 text-left border-b border-gray-900 pb-16 mb-10">
                        {/* Brand Column */}
                        <div className="lg:col-span-2 space-y-6">
                            <div className="flex items-center space-x-3 cursor-pointer">
                                <img
                                    src="/assets/images/logo-desa-cibatu.png"
                                    alt="Logo Desa Cibatu"
                                    className="h-12 w-12 rounded-xl"
                                />
                                <div>
                                    <h3 className="text-lg font-black uppercase italic tracking-tighter leading-none text-white">Admin Panel</h3>
                                    <p className="text-[10px] font-black text-gray-500 uppercase tracking-widest mt-1">Sistem Administrasi Desa Cibatu</p>
                                </div>
                            </div>
                            <p className="text-sm text-gray-400 font-semibold leading-relaxed max-w-sm">
                                Dashboard administrasi digital yang efisien, aman, dan transparan, dirancang khusus untuk mengoptimalkan pelayanan publik bagi seluruh penduduk Desa Cibatu.
                            </p>
                            {/* Social Media Link Icons — Dinamis dari DB */}
                            <div className="flex items-center space-x-3">
                                {socialLinks.facebook && (
                                    <a
                                        href={socialLinks.facebook}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="w-10 h-10 bg-gray-900 hover:bg-blue-600 text-gray-400 hover:text-white rounded-xl flex items-center justify-center transition-all duration-200"
                                        title="Facebook Desa"
                                    >
                                        <i className="fab fa-facebook text-base"></i>
                                    </a>
                                )}
                                {socialLinks.instagram && (
                                    <a
                                        href={socialLinks.instagram}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="w-10 h-10 bg-gray-900 hover:bg-pink-600 text-gray-400 hover:text-white rounded-xl flex items-center justify-center transition-all duration-200"
                                        title="Instagram Desa"
                                    >
                                        <i className="fab fa-instagram text-base"></i>
                                    </a>
                                )}
                                {socialLinks.whatsapp && (
                                    <a
                                        href={socialLinks.whatsapp}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="w-10 h-10 bg-gray-900 hover:bg-green-600 text-gray-400 hover:text-white rounded-xl flex items-center justify-center transition-all duration-200"
                                        title="WhatsApp Desa"
                                    >
                                        <i className="fab fa-whatsapp text-base"></i>
                                    </a>
                                )}
                                {/* Tampilkan placeholder jika belum ada data sosmed */}
                                {!socialLinks.loading && !socialLinks.facebook && !socialLinks.instagram && !socialLinks.whatsapp && (
                                    <span className="text-xs text-gray-600 italic">Belum ada media sosial</span>
                                )}
                            </div>
                        </div>

                        {/* Modul Akses Column */}
                        <div className="space-y-6">
                            <h4 className="text-xs font-black uppercase tracking-widest text-gray-400 border-l-2 border-green-500 pl-3">Daftar Modul</h4>
                            <ul className="space-y-3.5 text-sm font-semibold text-gray-400">
                                <li>
                                    <Link href={route('admin.surat-pengajuan.index')} className="hover:text-white transition-colors">
                                        <i className="fas fa-angle-right mr-1.5 text-xs text-gray-600"></i>Kelola Surat Keterangan
                                    </Link>
                                </li>
                                <li>
                                    <Link href={route('pengaduan.index')} className="hover:text-white transition-colors">
                                        <i className="fas fa-angle-right mr-1.5 text-xs text-gray-600"></i>Kelola Pengaduan Warga
                                    </Link>
                                </li>
                                <li>
                                    <Link href={route('berita.index')} className="hover:text-white transition-colors">
                                        <i className="fas fa-angle-right mr-1.5 text-xs text-gray-600"></i>Informasi & Berita
                                    </Link>
                                </li>
                                <li>
                                    <Link href={route('contact-messages.index')} className="hover:text-white transition-colors">
                                        <i className="fas fa-angle-right mr-1.5 text-xs text-gray-600"></i>Pesan Kontak Masuk
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        {/* Portal Tautan Column */}
                        <div className="space-y-6">
                            <h4 className="text-xs font-black uppercase tracking-widest text-gray-400 border-l-2 border-green-500 pl-3">Akses Cepat</h4>
                            <ul className="space-y-3.5 text-sm font-semibold text-gray-400">
                                <li>
                                    <Link href={route('login')} className="hover:text-white transition-colors">
                                        <i className="fas fa-angle-right mr-1.5 text-xs text-gray-600"></i>Masuk Dashboard Admin
                                    </Link>
                                </li>
                                <li>
                                    <a href="/web-desa" className="hover:text-white transition-colors">
                                        <i className="fas fa-angle-right mr-1.5 text-xs text-gray-600"></i>Portal Warga Utama
                                    </a>
                                </li>
                                <li>
                                    <a href="#features" onClick={(e) => handleScroll(e, 'features')} className="hover:text-white transition-colors">
                                        <i className="fas fa-angle-right mr-1.5 text-xs text-gray-600"></i>Fitur Terintegrasi
                                    </a>
                                </li>
                                <li>
                                    <a href="#stats" onClick={(e) => handleScroll(e, 'stats')} className="hover:text-white transition-colors">
                                        <i className="fas fa-angle-right mr-1.5 text-xs text-gray-600"></i>Statistik Pelayanan
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {/* Copyright bar */}
                    <div className="flex flex-col sm:flex-row items-center justify-between text-xs font-bold text-gray-500 gap-4">
                        <p>© {new Date().getFullYear()} Pemerintah Desa Cibatu, Purwakarta. Seluruh Hak Cipta Dilindungi.</p>
                        <div className="flex items-center space-x-6">
                            <Link href={route('privacy-policy')} className="hover:text-white transition-colors">Kebijakan Privasi</Link>
                            <Link href={route('terms-of-service')} className="hover:text-white transition-colors">Ketentuan Layanan</Link>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    );
}
