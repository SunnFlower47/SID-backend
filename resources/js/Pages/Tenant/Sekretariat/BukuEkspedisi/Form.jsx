import React from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormField } from '@/Components/Shared';
import { Send, Save } from 'lucide-react';

export default function Form({ bukuEkspedisi, is_edit }) {
    const { data, setData, post, put, processing, errors } = useForm({
        tanggal_pengiriman: bukuEkspedisi.tanggal_pengiriman ? new Date(bukuEkspedisi.tanggal_pengiriman).toISOString().split('T')[0] : '',
        tanggal_surat: bukuEkspedisi.tanggal_surat ? new Date(bukuEkspedisi.tanggal_surat).toISOString().split('T')[0] : '',
        nomor_surat: bukuEkspedisi.nomor_surat || '',
        isi_singkat: bukuEkspedisi.isi_singkat || '',
        tujuan: bukuEkspedisi.tujuan || '',
        penerima: bukuEkspedisi.penerima || '',
        keterangan: bukuEkspedisi.keterangan || ''
    });

    const submit = (e) => {
        e.preventDefault();
        
        if (is_edit) {
            put(route('sekretariat.buku-ekspedisi.update', bukuEkspedisi.id));
        } else {
            post(route('sekretariat.buku-ekspedisi.store'));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title={is_edit ? "Edit Ekspedisi" : "Tambah Ekspedisi"} />

            <div className="space-y-6 pb-20">
                <PageHeader
                    icon={Send}
                    title={is_edit ? "Edit Data Ekspedisi" : "Tambah Data Ekspedisi"}
                    subtitle="Isi form di bawah ini untuk mencatat pengiriman surat/barang"
                    backHref={route('sekretariat.buku-ekspedisi.index')}
                />

                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="p-6 sm:p-8">
                        <form onSubmit={submit} className="space-y-6">
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField
                                    label="Tanggal Pengiriman *"
                                    error={errors.tanggal_pengiriman}
                                >
                                    <input
                                        type="date"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.tanggal_pengiriman}
                                        onChange={e => setData('tanggal_pengiriman', e.target.value)}
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Tanggal Surat *"
                                    error={errors.tanggal_surat}
                                >
                                    <input
                                        type="date"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.tanggal_surat}
                                        onChange={e => setData('tanggal_surat', e.target.value)}
                                        required
                                    />
                                </FormField>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField
                                    label="Nomor Surat *"
                                    error={errors.nomor_surat}
                                >
                                    <input
                                        type="text"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.nomor_surat}
                                        onChange={e => setData('nomor_surat', e.target.value)}
                                        placeholder="Nomor surat yang dikirim"
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Tujuan Pengiriman *"
                                    error={errors.tujuan}
                                    help="Instansi atau orang yang dituju"
                                >
                                    <input
                                        type="text"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.tujuan}
                                        onChange={e => setData('tujuan', e.target.value)}
                                        placeholder="Contoh: Kecamatan Cibatu"
                                        required
                                    />
                                </FormField>
                            </div>

                            <FormField
                                label="Isi Singkat Surat *"
                                error={errors.isi_singkat}
                            >
                                <textarea
                                    className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                    rows="3"
                                    value={data.isi_singkat}
                                    onChange={e => setData('isi_singkat', e.target.value)}
                                    placeholder="Ringkasan isi surat..."
                                    required
                                ></textarea>
                            </FormField>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField
                                    label="Penerima (Opsional)"
                                    error={errors.penerima}
                                    help="Orang yang menerima dokumen"
                                >
                                    <input
                                        type="text"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.penerima}
                                        onChange={e => setData('penerima', e.target.value)}
                                        placeholder="Nama penerima..."
                                    />
                                </FormField>

                                <FormField
                                    label="Keterangan (Opsional)"
                                    error={errors.keterangan}
                                >
                                    <input
                                        type="text"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.keterangan}
                                        onChange={e => setData('keterangan', e.target.value)}
                                        placeholder="Catatan tambahan..."
                                    />
                                </FormField>
                            </div>

                            <div className="pt-6 border-t border-gray-100 flex justify-end gap-3">
                                <Link
                                    href={route('sekretariat.buku-ekspedisi.index')}
                                    className="px-6 py-3 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors"
                                >
                                    Batal
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 disabled:opacity-50"
                                >
                                    {processing ? (
                                        <span className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2" />
                                    ) : (
                                        <Save className="w-5 h-5 mr-2" />
                                    )}
                                    {is_edit ? 'Simpan Perubahan' : 'Simpan Data'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
