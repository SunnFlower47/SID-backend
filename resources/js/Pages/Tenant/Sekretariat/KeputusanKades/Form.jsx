import React from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormField } from '@/Components/Shared';
import { Scale, Save } from 'lucide-react';

export default function Form({ keputusan, is_edit }) {
    const { data, setData, post, put, processing, errors } = useForm({
        nomor_keputusan: keputusan.nomor_keputusan || '',
        judul_keputusan: keputusan.judul_keputusan || '',
        tanggal_ditetapkan: keputusan.tanggal_ditetapkan ? new Date(keputusan.tanggal_ditetapkan).toISOString().split('T')[0] : '',
        keterangan: keputusan.keterangan || '',
        file_dokumen: null,
        _method: is_edit ? 'PUT' : 'POST' // using spoofing for file uploads on PUT
    });

    const submit = (e) => {
        e.preventDefault();
        
        if (is_edit) {
            // Laravel needs POST with _method=PUT to handle multipart/form-data properly
            post(route('sekretariat.keputusan-kades.update', keputusan.id));
        } else {
            post(route('sekretariat.keputusan-kades.store'));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title={is_edit ? "Edit Keputusan Kades" : "Tambah Keputusan Kades"} />

            <div className="space-y-6 pb-20">
                <PageHeader
                    icon={Scale}
                    title={is_edit ? "Edit Keputusan Kades" : "Tambah Keputusan Kades"}
                    subtitle="Isi form di bawah ini untuk menyimpan dokumen keputusan Kepala Desa"
                    backHref={route('sekretariat.keputusan-kades.index')}
                />

                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="p-6 sm:p-8">
                        <form onSubmit={submit} className="space-y-6">
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField
                                    label="Nomor Keputusan *"
                                    error={errors.nomor_keputusan}
                                >
                                    <input
                                        type="text"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.nomor_keputusan}
                                        onChange={e => setData('nomor_keputusan', e.target.value)}
                                        placeholder="Contoh: 141/01/Kep-Desa/2026"
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Tanggal Ditetapkan *"
                                    error={errors.tanggal_ditetapkan}
                                >
                                    <input
                                        type="date"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.tanggal_ditetapkan}
                                        onChange={e => setData('tanggal_ditetapkan', e.target.value)}
                                        required
                                    />
                                </FormField>
                            </div>

                            <FormField
                                label="Judul / Uraian Singkat Keputusan *"
                                error={errors.judul_keputusan}
                            >
                                <textarea
                                    className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                    rows="2"
                                    value={data.judul_keputusan}
                                    onChange={e => setData('judul_keputusan', e.target.value)}
                                    placeholder="Contoh: Pembentukan Panitia Pemilihan Kepala Desa..."
                                    required
                                ></textarea>
                            </FormField>

                            <FormField
                                label="Keterangan (Opsional)"
                                error={errors.keterangan}
                            >
                                <textarea
                                    className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                    rows="3"
                                    value={data.keterangan}
                                    onChange={e => setData('keterangan', e.target.value)}
                                    placeholder="Catatan tambahan..."
                                ></textarea>
                            </FormField>

                            <FormField
                                label={is_edit ? "Ganti File Dokumen (PDF, max 5MB)" : "Unggah File Dokumen (PDF, max 5MB)"}
                                error={errors.file_dokumen}
                                help={is_edit && keputusan.file_dokumen ? "Biarkan kosong jika tidak ingin mengganti file saat ini." : "Opsional. Dokumen asli yang telah di scan."}
                            >
                                <input
                                    type="file"
                                    accept=".pdf"
                                    className="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    onChange={e => setData('file_dokumen', e.target.files[0])}
                                />
                                {is_edit && keputusan.file_dokumen && (
                                    <div className="mt-2 text-sm text-gray-500">
                                        File saat ini: <a href={`/storage/${keputusan.file_dokumen}`} target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:underline">Lihat Dokumen</a>
                                    </div>
                                )}
                            </FormField>

                            <div className="pt-6 border-t border-gray-100 flex justify-end gap-3">
                                <Link
                                    href={route('sekretariat.keputusan-kades.index')}
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
