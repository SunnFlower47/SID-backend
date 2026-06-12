import React from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormField } from '@/Components/Shared';
import { BookOpen, Save } from 'lucide-react';

export default function Form({ peraturan, is_edit }) {
    const { data, setData, post, processing, errors } = useForm({
        jenis_peraturan: peraturan.jenis_peraturan || 'Peraturan Desa',
        tahun_anggaran: peraturan.tahun_anggaran || new Date().getFullYear(),
        judul: peraturan.judul || '',
        nomor_peraturan: peraturan.nomor_peraturan || '',
        tanggal_ditetapkan: peraturan.tanggal_ditetapkan ? new Date(peraturan.tanggal_ditetapkan).toISOString().split('T')[0] : '',
        status: peraturan.status || 'disetujui',
        keterangan_bpd: peraturan.keterangan_bpd || '',
        file_dokumen: null,
        _method: is_edit ? 'PUT' : 'POST'
    });

    const submit = (e) => {
        e.preventDefault();
        
        if (is_edit) {
            post(route('sekretariat.peraturan-desa.update', peraturan.id));
        } else {
            post(route('sekretariat.peraturan-desa.store'));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title={is_edit ? "Edit Peraturan Desa" : "Tambah Peraturan Desa"} />

            <div className="space-y-6 pb-20">
                <PageHeader
                    icon={BookOpen}
                    title={is_edit ? "Edit Peraturan Desa" : "Tambah Peraturan Desa"}
                    subtitle="Isi form di bawah ini untuk menyimpan dokumen peraturan tingkat desa"
                    backHref={route('sekretariat.peraturan-desa.index')}
                />

                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="p-6 sm:p-8">
                        <form onSubmit={submit} className="space-y-6">
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField
                                    label="Jenis Peraturan *"
                                    error={errors.jenis_peraturan}
                                >
                                    <select
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.jenis_peraturan}
                                        onChange={e => setData('jenis_peraturan', e.target.value)}
                                        required
                                    >
                                        <option value="Peraturan Desa">Peraturan Desa</option>
                                        <option value="Peraturan Kepala Desa">Peraturan Kepala Desa</option>
                                        <option value="Peraturan Bersama Kepala Desa">Peraturan Bersama Kepala Desa</option>
                                        <option value="APBDes">APBDes</option>
                                        <option value="Perubahan APBDes">Perubahan APBDes</option>
                                        <option value="Lpj APBDes">LPJ APBDes</option>
                                    </select>
                                </FormField>

                                <FormField
                                    label="Tahun *"
                                    error={errors.tahun_anggaran}
                                >
                                    <input
                                        type="number"
                                        min="2000"
                                        max="2099"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.tahun_anggaran}
                                        onChange={e => setData('tahun_anggaran', e.target.value)}
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Nomor Peraturan"
                                    error={errors.nomor_peraturan}
                                >
                                    <input
                                        type="text"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.nomor_peraturan}
                                        onChange={e => setData('nomor_peraturan', e.target.value)}
                                        placeholder="Contoh: 1 Tahun 2026"
                                    />
                                </FormField>

                                <FormField
                                    label="Tanggal Ditetapkan"
                                    error={errors.tanggal_ditetapkan}
                                >
                                    <input
                                        type="date"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.tanggal_ditetapkan}
                                        onChange={e => setData('tanggal_ditetapkan', e.target.value)}
                                    />
                                </FormField>
                            </div>

                            <FormField
                                label="Judul / Uraian Peraturan *"
                                error={errors.judul}
                            >
                                <textarea
                                    className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                    rows="2"
                                    value={data.judul}
                                    onChange={e => setData('judul', e.target.value)}
                                    placeholder="Contoh: Pembentukan BUMDesa..."
                                    required
                                ></textarea>
                            </FormField>
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField
                                    label="Status Penetapan *"
                                    error={errors.status}
                                >
                                    <select
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.status}
                                        onChange={e => setData('status', e.target.value)}
                                        required
                                    >
                                        <option value="draft">Draft</option>
                                        <option value="diajukan_bpd">Diajukan ke BPD</option>
                                        <option value="dibahas">Sedang Dibahas</option>
                                        <option value="disetujui">Disetujui / Ditetapkan</option>
                                        <option value="ditolak">Ditolak</option>
                                    </select>
                                </FormField>

                                <FormField
                                    label="Keterangan / Keputusan BPD"
                                    error={errors.keterangan_bpd}
                                >
                                    <input
                                        type="text"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.keterangan_bpd}
                                        onChange={e => setData('keterangan_bpd', e.target.value)}
                                        placeholder="No Keputusan BPD atau lainnya..."
                                    />
                                </FormField>
                            </div>

                            <FormField
                                label={is_edit ? "Ganti File Dokumen (PDF, max 5MB)" : "Unggah File Dokumen (PDF, max 5MB)"}
                                error={errors.file_dokumen}
                                help={is_edit && peraturan.file_dokumen ? "Biarkan kosong jika tidak ingin mengganti file saat ini." : "Opsional. Dokumen asli peraturan yang telah disahkan."}
                            >
                                <input
                                    type="file"
                                    accept=".pdf"
                                    className="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    onChange={e => setData('file_dokumen', e.target.files[0])}
                                />
                                {is_edit && peraturan.file_dokumen && (
                                    <div className="mt-2 text-sm text-gray-500">
                                        File saat ini: <a href={`/storage/${peraturan.file_dokumen}`} target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:underline">Lihat Dokumen</a>
                                    </div>
                                )}
                            </FormField>

                            <div className="pt-6 border-t border-gray-100 flex justify-end gap-3">
                                <Link
                                    href={route('sekretariat.peraturan-desa.index')}
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
