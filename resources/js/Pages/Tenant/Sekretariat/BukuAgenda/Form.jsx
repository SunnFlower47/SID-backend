import React from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormField } from '@/Components/Shared';
import { Mails, Save } from 'lucide-react';

export default function Form({ agenda, is_edit }) {
    const { data, setData, post, put, processing, errors } = useForm({
        tanggal: agenda.tanggal || '',
        jenis_surat: agenda.jenis_surat || 'Masuk',
        nomor_surat: agenda.nomor_surat || '',
        tanggal_surat: agenda.tanggal_surat || '',
        pengirim_penerima: agenda.pengirim_penerima || '',
        isi_singkat: agenda.isi_singkat || '',
        keterangan: agenda.keterangan || ''
    });

    const submit = (e) => {
        e.preventDefault();
        
        if (is_edit) {
            put(route('sekretariat.buku-agenda.update', agenda.id));
        } else {
            post(route('sekretariat.buku-agenda.store'));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title={is_edit ? "Edit Surat" : "Tambah Surat"} />

            <div className="space-y-6 pb-20">
                <PageHeader
                    icon={Mails}
                    title={is_edit ? "Edit Surat" : "Tambah Surat"}
                    subtitle={`Isi form di bawah ini untuk menyimpan data surat ${data.jenis_surat.toLowerCase()}`}
                    backHref={route('sekretariat.buku-agenda.index')}
                />

                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="p-6 sm:p-8">
                        <form onSubmit={submit} className="space-y-6">
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField
                                    label="Jenis Surat *"
                                    error={errors.jenis_surat}
                                >
                                    <select
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.jenis_surat}
                                        onChange={e => setData('jenis_surat', e.target.value)}
                                        required
                                    >
                                        <option value="Masuk">Surat Masuk</option>
                                        <option value="Keluar">Surat Keluar</option>
                                    </select>
                                </FormField>

                                <FormField
                                    label="Tanggal Catat / Terima *"
                                    error={errors.tanggal}
                                    help="Tanggal surat ini dicatat di buku agenda"
                                >
                                    <input
                                        type="date"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.tanggal}
                                        onChange={e => setData('tanggal', e.target.value)}
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Nomor Surat"
                                    error={errors.nomor_surat}
                                >
                                    <input
                                        type="text"
                                        className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                        value={data.nomor_surat}
                                        onChange={e => setData('nomor_surat', e.target.value)}
                                        placeholder="Kosongkan jika tidak ada nomor"
                                    />
                                </FormField>

                                <FormField
                                    label="Tanggal Surat *"
                                    error={errors.tanggal_surat}
                                    help="Tanggal yang tertera pada surat"
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

                            <FormField
                                label={data.jenis_surat === 'Masuk' ? "Pengirim Surat *" : "Ditujukan Kepada (Penerima) *"}
                                error={errors.pengirim_penerima}
                            >
                                <input
                                    type="text"
                                    className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50/50 focus:bg-white transition-colors"
                                    value={data.pengirim_penerima}
                                    onChange={e => setData('pengirim_penerima', e.target.value)}
                                    placeholder={data.jenis_surat === 'Masuk' ? "Contoh: Kecamatan Cibatu" : "Contoh: Bupati Garut"}
                                    required
                                />
                            </FormField>

                            <FormField
                                label="Isi Singkat / Perihal Surat *"
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

                            <div className="pt-6 border-t border-gray-100 flex justify-end gap-3">
                                <Link
                                    href={route('sekretariat.buku-agenda.index')}
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
