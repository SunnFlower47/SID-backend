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

export default function TermsOfService() {
    const lastUpdated = '20 Mei 2025';

    return (
        <div className="min-h-screen bg-gray-50 font-sans">
            <Head>
                <title>Ketentuan Layanan - Admin Panel Desa Cibatu</title>
                <meta name="description" content="Ketentuan dan syarat penggunaan sistem administrasi digital Admin Panel Desa Cibatu, Purwakarta." />
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
                style={{ background: 'linear-gradient(135deg, #111827 0%, #1f2937 50%, #111827 100%)' }}
                className="text-white py-16 sm:py-20"
            >
                <div className="max-w-4xl mx-auto px-4 sm:px-6 text-center">
                    <div className="inline-flex items-center px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full border border-white/20 text-xs font-black uppercase tracking-widest text-yellow-300 mb-6">
                        <i className="fas fa-file-contract mr-2"></i>
                        Perjanjian Penggunaan
                    </div>
                    <h1 className="text-3xl sm:text-5xl font-black uppercase italic tracking-tighter leading-tight">
                        Ketentuan Layanan
                    </h1>
                    <p className="mt-4 text-sm sm:text-base text-gray-300 font-semibold max-w-xl mx-auto leading-relaxed">
                        Syarat dan ketentuan yang mengatur penggunaan sistem administrasi digital Admin Panel Desa Cibatu.
                    </p>
                    <p className="mt-6 text-xs text-gray-500 font-bold uppercase tracking-widest">
                        Terakhir diperbarui: {lastUpdated}
                    </p>
                </div>
            </div>

            {/* Content */}
            <main className="max-w-4xl mx-auto px-4 sm:px-6 py-16">
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sm:p-12">

                    <div className="mb-10 p-5 bg-amber-50 border border-amber-100 rounded-2xl">
                        <p className="text-sm text-amber-800 font-semibold leading-relaxed">
                            <i className="fas fa-exclamation-triangle mr-2 text-amber-500"></i>
                            Dengan mengakses dan menggunakan <strong>Admin Panel Desa Cibatu</strong>, Anda menyatakan telah membaca, memahami, dan menyetujui seluruh ketentuan yang tercantum dalam dokumen ini. Jika tidak menyetujui, harap tidak menggunakan sistem ini.
                        </p>
                    </div>

                    <Section title="1. Definisi">
                        <p>Dalam ketentuan layanan ini, yang dimaksud dengan:</p>
                        <ul className="space-y-2 mt-3">
                            {[
                                ['"Sistem"', 'Admin Panel Desa Cibatu — platform administrasi digital yang dikelola Pemerintah Desa Cibatu'],
                                ['"Pengelola"', 'Pemerintah Desa Cibatu, Kecamatan Cibatu, Kabupaten Purwakarta, Jawa Barat'],
                                ['"Pengguna"', 'Petugas desa, admin, dan pihak yang mendapatkan akses resmi ke sistem'],
                                ['"Layanan"', 'Seluruh fitur yang tersedia di sistem, termasuk pengelolaan surat, pengaduan, data kependudukan, dan lainnya'],
                                ['"Data"', 'Informasi yang dimasukkan, diproses, atau dihasilkan melalui sistem'],
                            ].map(([term, def]) => (
                                <li key={term} className="flex items-start space-x-3 p-3 bg-gray-50 rounded-xl">
                                    <span className="font-black text-green-600 shrink-0">{term}</span>
                                    <span className="text-gray-600">{def}</span>
                                </li>
                            ))}
                        </ul>
                    </Section>

                    <Section title="2. Persyaratan Penggunaan">
                        <p>Untuk dapat menggunakan sistem ini, pengguna wajib memenuhi persyaratan berikut:</p>
                        <ul className="space-y-2 mt-3">
                            {[
                                'Merupakan petugas desa atau pihak yang mendapatkan otorisasi resmi dari Kepala Desa Cibatu',
                                'Memiliki akun yang dibuat dan diverifikasi oleh administrator sistem',
                                'Menjaga kerahasiaan kredensial akun (username dan password)',
                                'Menggunakan sistem hanya untuk keperluan administrasi desa yang sah',
                                'Berusia minimal 18 tahun atau memiliki kapasitas hukum yang diperlukan',
                            ].map((item) => (
                                <li key={item} className="flex items-start space-x-2">
                                    <i className="fas fa-check-circle text-green-500 mt-0.5 shrink-0"></i>
                                    <span>{item}</span>
                                </li>
                            ))}
                        </ul>
                    </Section>

                    <Section title="3. Hak dan Kewajiban Pengguna">
                        <p><strong className="text-gray-800">Hak Pengguna:</strong></p>
                        <ul className="space-y-2 mt-2 mb-4">
                            {[
                                'Mengakses fitur sistem sesuai dengan tingkat otorisasi yang diberikan',
                                'Mendapatkan dukungan teknis dari pengelola sistem',
                                'Mendapatkan notifikasi perubahan sistem yang signifikan',
                                'Mengajukan pertanyaan dan keluhan terkait penggunaan sistem',
                            ].map((item) => (
                                <li key={item} className="flex items-start space-x-2">
                                    <i className="fas fa-plus text-blue-400 mt-0.5 shrink-0 text-xs"></i>
                                    <span>{item}</span>
                                </li>
                            ))}
                        </ul>
                        <p><strong className="text-gray-800">Kewajiban Pengguna:</strong></p>
                        <ul className="space-y-2 mt-2">
                            {[
                                'Memasukkan data yang akurat, lengkap, dan dapat dipertanggungjawabkan',
                                'Tidak menyalahgunakan sistem untuk kepentingan pribadi atau pihak lain yang tidak berwenang',
                                'Segera melaporkan kepada administrator jika mengetahui adanya pelanggaran keamanan',
                                'Mematuhi seluruh peraturan perundang-undangan yang berlaku',
                                'Tidak mencoba mengakses area sistem yang di luar otorisasi',
                            ].map((item) => (
                                <li key={item} className="flex items-start space-x-2">
                                    <i className="fas fa-minus text-red-400 mt-0.5 shrink-0 text-xs"></i>
                                    <span>{item}</span>
                                </li>
                            ))}
                        </ul>
                    </Section>

                    <Section title="4. Larangan Penggunaan">
                        <p>Pengguna dilarang keras melakukan hal-hal berikut:</p>
                        <div className="mt-3 space-y-2">
                            {[
                                'Memasukkan data palsu, tidak akurat, atau menyesatkan ke dalam sistem',
                                'Mengakses akun atau data milik pengguna lain tanpa otorisasi',
                                'Melakukan percobaan peretasan, injeksi kode, atau serangan keamanan terhadap sistem',
                                'Mengekstrak, menyalin, atau mendistribusikan data kependudukan di luar tujuan yang sah',
                                'Menggunakan sistem untuk kegiatan yang melanggar hukum atau merugikan masyarakat',
                                'Berbagi kredensial akun kepada pihak yang tidak berwenang',
                                'Memodifikasi, mendekompilasi, atau melakukan rekayasa balik terhadap sistem',
                            ].map((item) => (
                                <div key={item} className="flex items-start space-x-3 p-3 bg-red-50 border border-red-100 rounded-xl">
                                    <i className="fas fa-times-circle text-red-500 mt-0.5 shrink-0"></i>
                                    <span className="text-red-800">{item}</span>
                                </div>
                            ))}
                        </div>
                    </Section>

                    <Section title="5. Keamanan Akun">
                        <p>
                            Pengguna bertanggung jawab penuh atas keamanan akun masing-masing. Pemerintah Desa Cibatu tidak bertanggung jawab atas kerugian yang timbul akibat:
                        </p>
                        <ul className="space-y-2 mt-3">
                            {[
                                'Penggunaan akun oleh pihak yang tidak berwenang karena kelalaian pengguna',
                                'Pengungkapan password atau informasi login kepada pihak lain',
                                'Penggunaan perangkat yang tidak aman atau jaringan publik untuk mengakses sistem',
                            ].map((item) => (
                                <li key={item} className="flex items-start space-x-2">
                                    <i className="fas fa-exclamation-triangle text-amber-500 mt-0.5 shrink-0"></i>
                                    <span>{item}</span>
                                </li>
                            ))}
                        </ul>
                        <p className="mt-3">
                            Segera hubungi administrator jika Anda mencurigai adanya akses tidak sah ke akun Anda.
                        </p>
                    </Section>

                    <Section title="6. Kepemilikan dan Hak Kekayaan Intelektual">
                        <p>
                            Sistem Admin Panel Desa Cibatu, termasuk seluruh kode, desain, konten, dan fiturnya, merupakan milik Pemerintah Desa Cibatu. Data kependudukan dan administrasi yang dimasukkan ke sistem tetap merupakan milik Pemerintah Desa Cibatu sesuai ketentuan hukum yang berlaku.
                        </p>
                        <p className="mt-3">
                            Pengguna tidak mendapatkan hak kepemilikan atas sistem atau komponennya dalam bentuk apapun.
                        </p>
                    </Section>

                    <Section title="7. Ketersediaan Layanan">
                        <p>
                            Pengelola berupaya menjaga sistem beroperasi 24/7, namun tidak dapat menjamin ketersediaan layanan tanpa gangguan. Pemeliharaan terjadwal atau tidak terduga dapat menyebabkan sistem tidak tersedia sementara. Pengelola akan berusaha memberikan pemberitahuan sebelumnya untuk pemeliharaan terjadwal.
                        </p>
                    </Section>

                    <Section title="8. Batasan Tanggung Jawab">
                        <p>Pemerintah Desa Cibatu tidak bertanggung jawab atas:</p>
                        <ul className="space-y-2 mt-3">
                            {[
                                'Kerugian tidak langsung yang timbul dari penggunaan atau ketidakmampuan menggunakan sistem',
                                'Kehilangan data akibat force majeure, bencana alam, atau kejadian di luar kendali',
                                'Kesalahan data yang dimasukkan oleh pengguna',
                                'Gangguan layanan yang disebabkan oleh faktor eksternal (pemadaman internet, bencana, dll)',
                            ].map((item) => (
                                <li key={item} className="flex items-start space-x-2">
                                    <i className="fas fa-minus-circle text-gray-400 mt-0.5 shrink-0"></i>
                                    <span>{item}</span>
                                </li>
                            ))}
                        </ul>
                    </Section>

                    <Section title="9. Penangguhan dan Penghentian Akses">
                        <p>
                            Pengelola berhak menangguhkan atau menghentikan akses pengguna yang:
                        </p>
                        <ul className="space-y-2 mt-3">
                            {[
                                'Melanggar ketentuan layanan ini',
                                'Tidak lagi menjabat sebagai petugas desa atau kehilangan otorisasi',
                                'Melakukan tindakan yang membahayakan keamanan sistem atau data',
                                'Diperintahkan oleh Kepala Desa atau pihak berwenang',
                            ].map((item) => (
                                <li key={item} className="flex items-start space-x-2">
                                    <i className="fas fa-user-times text-red-400 mt-0.5 shrink-0"></i>
                                    <span>{item}</span>
                                </li>
                            ))}
                        </ul>
                    </Section>

                    <Section title="10. Perubahan Ketentuan">
                        <p>
                            Pengelola berhak mengubah ketentuan layanan ini sewaktu-waktu. Perubahan akan efektif setelah dipublikasikan di sistem. Penggunaan sistem secara berkelanjutan setelah perubahan dianggap sebagai persetujuan terhadap ketentuan yang diperbarui.
                        </p>
                    </Section>

                    <Section title="11. Hukum yang Berlaku">
                        <p>
                            Ketentuan layanan ini tunduk pada dan ditafsirkan sesuai dengan hukum Negara Kesatuan Republik Indonesia. Setiap sengketa yang timbul dari ketentuan ini akan diselesaikan melalui musyawarah mufakat, dan jika tidak tercapai, melalui pengadilan yang berwenang di Indonesia.
                        </p>
                    </Section>

                    <Section title="12. Hubungi Kami">
                        <p>Untuk pertanyaan terkait ketentuan layanan ini, silakan hubungi:</p>
                        <div className="mt-4 p-5 bg-gray-50 border border-gray-200 rounded-2xl space-y-2">
                            <p className="font-black text-gray-800 text-sm uppercase tracking-widest">Pemerintah Desa Cibatu</p>
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
                    <Link href={route('privacy-policy')} className="flex items-center space-x-2 text-gray-500 hover:text-green-600 transition-colors group">
                        <span className="font-bold">Kebijakan Privasi</span>
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
