import React from 'react';
import { Head, Link } from '@inertiajs/react';

const Section = ({ title, children }) => (
    <div className="mb-10">
        <h2 className="text-lg font-black text-gray-900 uppercase tracking-tight italic border-l-4 border-green-500 pl-4 mb-4">
            {title}
        </h2>
        <div className="text-sm text-gray-600 leading-relaxed space-y-3 pl-4">
            {children}
        </div>
    </div>
);

export default function PrivacyPolicy() {
    const lastUpdated = '20 Mei 2025';

    return (
        <div className="min-h-screen bg-gray-50 font-sans">
            <Head>
                <title>Kebijakan Privasi - Admin Panel Desa Cibatu</title>
                <meta name="description" content="Kebijakan privasi penggunaan sistem administrasi digital Admin Panel Desa Cibatu, Purwakarta." />
            </Head>

            {/* Sticky Header */}
            <header className="bg-white/90 backdrop-blur-lg border-b border-gray-100 sticky top-0 z-50">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                    <Link
                        href={route('welcome')}
                        className="flex items-center space-x-2 text-gray-500 hover:text-green-600 transition-colors group"
                    >
                        <i className="fas fa-arrow-left text-sm group-hover:-translate-x-1 transition-transform"></i>
                        <span className="text-xs font-black uppercase tracking-widest">Kembali</span>
                    </Link>
                    <div className="flex items-center space-x-3">
                        <img
                            src="/assets/images/logo-desa-cibatu.png"
                            alt="Logo Desa Cibatu"
                            className="h-8 w-8 rounded-lg"
                        />
                        <span className="text-xs font-black text-gray-500 uppercase tracking-widest hidden sm:block">Desa Cibatu</span>
                    </div>
                </div>
            </header>

            {/* Hero */}
            <div
                style={{ background: 'linear-gradient(135deg, #15803d 0%, #059669 50%, #0f766e 100%)' }}
                className="text-white py-16 sm:py-20"
            >
                <div className="max-w-4xl mx-auto px-4 sm:px-6 text-center">
                    <div className="inline-flex items-center px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full border border-white/20 text-xs font-black uppercase tracking-widest text-yellow-300 mb-6">
                        <i className="fas fa-shield-alt mr-2"></i>
                        Perlindungan Data
                    </div>
                    <h1 className="text-3xl sm:text-5xl font-black uppercase italic tracking-tighter leading-tight">
                        Kebijakan Privasi
                    </h1>
                    <p className="mt-4 text-sm sm:text-base text-emerald-100 font-semibold max-w-xl mx-auto leading-relaxed">
                        Komitmen kami dalam melindungi dan mengelola data pribadi pengguna sistem administrasi digital Desa Cibatu.
                    </p>
                    <p className="mt-6 text-xs text-emerald-200/70 font-bold uppercase tracking-widest">
                        Terakhir diperbarui: {lastUpdated}
                    </p>
                </div>
            </div>

            {/* Content */}
            <main className="max-w-4xl mx-auto px-4 sm:px-6 py-16">
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sm:p-12">

                    <div className="mb-10 p-5 bg-green-50 border border-green-100 rounded-2xl">
                        <p className="text-sm text-green-800 font-semibold leading-relaxed">
                            <i className="fas fa-info-circle mr-2 text-green-600"></i>
                            Kebijakan privasi ini berlaku untuk penggunaan sistem <strong>Admin Panel Desa Cibatu</strong> (selanjutnya disebut "Sistem") yang dikelola oleh Pemerintah Desa Cibatu, Kecamatan Cibatu, Kabupaten Purwakarta, Jawa Barat.
                        </p>
                    </div>

                    <Section title="1. Data yang Kami Kumpulkan">
                        <p>Sistem kami mengumpulkan beberapa kategori data dalam rangka penyelenggaraan layanan administrasi publik, meliputi:</p>
                        <ul className="list-none space-y-2 mt-3">
                            {[
                                ['fas fa-user', 'Data Identitas', 'Nama lengkap, Nomor Induk Kependudukan (NIK), nomor Kartu Keluarga (KK), dan tanggal lahir yang diperlukan untuk pemrosesan dokumen administrasi.'],
                                ['fas fa-map-marker-alt', 'Data Domisili', 'Alamat tempat tinggal, RT, RW, Dusun untuk keperluan surat keterangan domisili dan registrasi kependudukan.'],
                                ['fas fa-envelope', 'Data Kontak', 'Nomor telepon dan alamat email yang diberikan secara sukarela untuk keperluan notifikasi status layanan.'],
                                ['fas fa-file-alt', 'Data Pengajuan', 'Riwayat pengajuan surat, status layanan, dan dokumen pendukung yang diunggah ke sistem.'],
                                ['fas fa-desktop', 'Data Teknis', 'Alamat IP, jenis browser, dan log akses sistem untuk keamanan dan audit.'],
                            ].map(([icon, title, desc]) => (
                                <li key={title} className="flex items-start space-x-3 p-3 bg-gray-50 rounded-xl">
                                    <i className={`${icon} text-green-500 mt-0.5 shrink-0 w-4`}></i>
                                    <span><strong className="text-gray-800">{title}:</strong> {desc}</span>
                                </li>
                            ))}
                        </ul>
                    </Section>

                    <Section title="2. Tujuan Pengumpulan Data">
                        <p>Data yang dikumpulkan digunakan semata-mata untuk:</p>
                        <ul className="space-y-2 mt-3">
                            {[
                                'Memproses pengajuan surat keterangan dan dokumen administrasi kependudukan',
                                'Memverifikasi identitas warga yang mengajukan layanan secara digital',
                                'Memberikan notifikasi status pengajuan kepada pemohon',
                                'Menghasilkan laporan statistik kependudukan yang bersifat agregat (tidak mengidentifikasi individu)',
                                'Meningkatkan kualitas layanan administrasi publik desa',
                                'Memenuhi kewajiban hukum dan peraturan pemerintah yang berlaku',
                            ].map((item) => (
                                <li key={item} className="flex items-start space-x-2">
                                    <i className="fas fa-check-circle text-green-500 mt-0.5 shrink-0"></i>
                                    <span>{item}</span>
                                </li>
                            ))}
                        </ul>
                    </Section>

                    <Section title="3. Dasar Hukum Pemrosesan Data">
                        <p>Pemrosesan data pribadi dalam sistem ini didasarkan pada:</p>
                        <ul className="space-y-2 mt-3">
                            {[
                                'Undang-Undang No. 27 Tahun 2022 tentang Perlindungan Data Pribadi',
                                'Undang-Undang No. 23 Tahun 2014 tentang Pemerintahan Daerah',
                                'Undang-Undang No. 24 Tahun 2013 tentang Administrasi Kependudukan',
                                'Peraturan Menteri Dalam Negeri terkait penyelenggaraan administrasi desa',
                                'Persetujuan eksplisit dari pemilik data saat menggunakan layanan',
                            ].map((item) => (
                                <li key={item} className="flex items-start space-x-2">
                                    <i className="fas fa-gavel text-green-500 mt-0.5 shrink-0"></i>
                                    <span>{item}</span>
                                </li>
                            ))}
                        </ul>
                    </Section>

                    <Section title="4. Penyimpanan dan Keamanan Data">
                        <p>
                            Kami berkomitmen untuk melindungi data pribadi Anda dengan standar keamanan yang ketat:
                        </p>
                        <ul className="space-y-2 mt-3">
                            {[
                                'Data disimpan di server yang berlokasi di dalam wilayah Indonesia',
                                'Seluruh transmisi data dienkripsi menggunakan protokol HTTPS/TLS',
                                'Akses ke data dibatasi hanya kepada petugas yang berwenang dan berkepentingan',
                                'Sistem dilindungi dengan mekanisme autentikasi berlapis dan audit log',
                                'Data sensitif seperti NIK diproses dengan standar enkripsi yang kuat',
                                'Backup data dilakukan secara berkala untuk mencegah kehilangan data',
                            ].map((item) => (
                                <li key={item} className="flex items-start space-x-2">
                                    <i className="fas fa-lock text-green-500 mt-0.5 shrink-0"></i>
                                    <span>{item}</span>
                                </li>
                            ))}
                        </ul>
                    </Section>

                    <Section title="5. Berbagi Data dengan Pihak Ketiga">
                        <p>
                            Kami <strong className="text-gray-900">tidak menjual, menyewakan, atau memperjualbelikan</strong> data pribadi Anda kepada pihak ketiga manapun.
                        </p>
                        <p className="mt-3">Data hanya dapat dibagikan dalam kondisi terbatas berikut:</p>
                        <ul className="space-y-2 mt-3">
                            {[
                                'Instansi pemerintah yang memiliki kewenangan hukum (Dukcapil, Kecamatan, dll)',
                                'Penegak hukum berdasarkan perintah pengadilan atau kewajiban hukum',
                                'Penyedia layanan teknis sistem yang terikat perjanjian kerahasiaan',
                            ].map((item) => (
                                <li key={item} className="flex items-start space-x-2">
                                    <i className="fas fa-share-alt text-gray-400 mt-0.5 shrink-0"></i>
                                    <span>{item}</span>
                                </li>
                            ))}
                        </ul>
                    </Section>

                    <Section title="6. Hak-Hak Anda">
                        <p>Sebagai pemilik data, Anda memiliki hak-hak berikut yang dapat diexercise sesuai ketentuan UU PDP:</p>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                            {[
                                ['fa-eye', 'Akses Data', 'Mengakses data pribadi yang kami simpan tentang Anda'],
                                ['fa-edit', 'Koreksi Data', 'Meminta perbaikan data yang tidak akurat atau tidak lengkap'],
                                ['fa-trash', 'Penghapusan Data', 'Meminta penghapusan data dalam kondisi tertentu sesuai hukum'],
                                ['fa-ban', 'Pembatasan Pemrosesan', 'Membatasi cara kami memproses data Anda'],
                            ].map(([icon, title, desc]) => (
                                <div key={title} className="p-4 bg-gray-50 rounded-2xl">
                                    <div className="flex items-center space-x-2 mb-2">
                                        <i className={`fas ${icon} text-green-500`}></i>
                                        <span className="font-black text-gray-800 text-xs uppercase tracking-widest">{title}</span>
                                    </div>
                                    <p className="text-xs text-gray-500">{desc}</p>
                                </div>
                            ))}
                        </div>
                        <p className="mt-4">Untuk mengexercise hak-hak tersebut, hubungi kami melalui kontak di bawah ini.</p>
                    </Section>

                    <Section title="7. Retensi Data">
                        <p>
                            Data pribadi disimpan selama diperlukan untuk tujuan pemrosesan atau sebagaimana diwajibkan oleh peraturan perundang-undangan. Data kependudukan mengikuti ketentuan retensi arsip pemerintahan yang berlaku.
                        </p>
                    </Section>

                    <Section title="8. Cookie dan Teknologi Pelacakan">
                        <p>
                            Sistem ini menggunakan cookie sesi untuk keperluan autentikasi dan keamanan. Cookie ini bersifat esensial dan tidak digunakan untuk pelacakan lintas situs atau tujuan pemasaran. Cookie akan terhapus secara otomatis saat sesi berakhir.
                        </p>
                    </Section>

                    <Section title="9. Perubahan Kebijakan Privasi">
                        <p>
                            Kebijakan privasi ini dapat diperbarui sewaktu-waktu untuk mencerminkan perubahan praktik kami atau ketentuan hukum yang berlaku. Perubahan signifikan akan diberitahukan kepada pengguna sistem yang terdaftar.
                        </p>
                    </Section>

                    <Section title="10. Hubungi Kami">
                        <p>Untuk pertanyaan, permintaan, atau keluhan terkait privasi data, silakan hubungi:</p>
                        <div className="mt-4 p-5 bg-green-50 border border-green-100 rounded-2xl space-y-2">
                            <p className="font-black text-green-800 text-sm uppercase tracking-widest">Pemerintah Desa Cibatu</p>
                            <p className="flex items-center space-x-2 text-sm text-gray-600">
                                <i className="fas fa-map-marker-alt text-green-500 w-4"></i>
                                <span>Jl. Cibatu, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta, Jawa Barat</span>
                            </p>
                            <p className="flex items-center space-x-2 text-sm text-gray-600">
                                <i className="fas fa-envelope text-green-500 w-4"></i>
                                <span>desacibatu.2001@gmail.com</span>
                            </p>
                        </div>
                    </Section>

                </div>

                {/* Footer Navigation */}
                <div className="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
                    <Link href={route('welcome')} className="flex items-center space-x-2 text-gray-500 hover:text-green-600 transition-colors group">
                        <i className="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                        <span className="font-bold">Kembali ke Beranda</span>
                    </Link>
                    <Link href={route('terms-of-service')} className="flex items-center space-x-2 text-gray-500 hover:text-green-600 transition-colors group">
                        <span className="font-bold">Ketentuan Layanan</span>
                        <i className="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </Link>
                </div>
            </main>

            {/* Footer */}
            <footer className="bg-gray-950 text-gray-500 py-8 text-center text-xs font-bold">
                <p>© {new Date().getFullYear()} Pemerintah Desa Cibatu, Purwakarta. Seluruh Hak Cipta Dilindungi.</p>
            </footer>
        </div>
    );
}
