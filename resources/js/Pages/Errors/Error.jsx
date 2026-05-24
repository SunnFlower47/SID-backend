import React from 'react';
import { Head, Link } from '@inertiajs/react';

export default function ErrorPage({ status }) {
    const title = {
        503: '503: Layanan Tidak Tersedia',
        500: '500: Terjadi Kesalahan Server',
        404: '404: Halaman Tidak Ditemukan',
        403: '403: Akses Ditolak',
        401: '401: Tidak Diizinkan',
        429: '429: Terlalu Banyak Permintaan',
    }[status] || 'Error';

    const description = {
        503: 'Maaf, kami sedang melakukan pemeliharaan pada server. Silakan coba beberapa saat lagi.',
        500: 'Oops, sesuatu yang salah terjadi pada server kami. Administrator telah diberitahu.',
        404: 'Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin sudah dihapus atau URL-nya salah.',
        403: 'Maaf, Anda dilarang mengakses halaman ini. Anda tidak memiliki izin yang cukup.',
        401: 'Anda harus login terlebih dahulu untuk mengakses halaman ini.',
        429: 'Anda telah mengirimkan terlalu banyak permintaan ke server kami. Harap tunggu beberapa saat.',
    }[status] || 'Terjadi kesalahan yang tidak diketahui.';

    return (
        <div className="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">
            <Head title={title} />
            <div className="sm:mx-auto sm:w-full sm:max-w-md text-center">
                <div className="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-6">
                    <svg
                        className="h-12 w-12 text-red-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        {status === 404 ? (
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        ) : status === 403 ? (
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        ) : (
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        )}
                    </svg>
                </div>
                <h1 className="text-4xl font-extrabold text-gray-900 tracking-tight mb-2">
                    {status}
                </h1>
                <h2 className="text-2xl font-bold text-gray-900 mb-4">{title.split(': ')[1]}</h2>
                <p className="text-base text-gray-500 mb-8 max-w-sm mx-auto">
                    {description}
                </p>
                <Link
                    href={route('dashboard')}
                    className="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                >
                    Kembali ke Beranda
                </Link>
            </div>
        </div>
    );
}
