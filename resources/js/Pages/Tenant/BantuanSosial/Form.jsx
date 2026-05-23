import React from 'react';
import { useForm, Link } from '@inertiajs/react';
import { HandHeart, Save, ArrowLeft, FileText, Calendar, DollarSign, Users, Tag, Building2 } from 'lucide-react';
import { FormCard } from '@/Components/Shared';

const JENIS_OPTIONS = [
    { value: 'BLT', label: 'BLT (Bantuan Langsung Tunai)' },
    { value: 'PKH', label: 'PKH (Program Keluarga Harapan)' },
    { value: 'BPNT', label: 'BPNT (Bantuan Pangan Non Tunai)' },
    { value: 'Bansos Lainnya', label: 'Bansos Lainnya' },
];

const STATUS_OPTIONS = [
    { value: 'aktif', label: 'Aktif' },
    { value: 'selesai', label: 'Selesai' },
    { value: 'ditangguhkan', label: 'Ditangguhkan' },
];

const SUMBER_DANA_OPTIONS = [
    { value: 'APBN', label: 'APBN' },
    { value: 'APBD', label: 'APBD' },
    { value: 'Swasta', label: 'Swasta' },
    { value: 'Lainnya', label: 'Lainnya' },
];

// ── Reusable field wrapper ──────────────────────────────────────
function Field({ label, required, error, children }) {
    return (
        <div>
            <label className="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">
                {label} {required && <span className="text-red-500">*</span>}
            </label>
            {children}
            {error && (
                <p className="mt-1.5 text-xs font-bold text-red-500 flex items-center gap-1">
                    <span className="w-1 h-1 bg-red-500 rounded-full inline-block" />
                    {error}
                </p>
            )}
        </div>
    );
}

const inputClass = (hasError) =>
    `w-full px-4 py-3 border-2 rounded-xl text-sm font-medium text-gray-800 focus:outline-none focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all ${
        hasError ? 'border-red-400 bg-red-50/50' : 'border-gray-200 bg-white hover:border-gray-300'
    }`;

// ────────────────────────────────────────────────────────────────
// Shared Form Component
// ────────────────────────────────────────────────────────────────
export default function BantuanSosialForm({ mode = 'create', bantuanSosial = null }) {
    const isEdit = mode === 'edit';

    const { data, setData, post, put, processing, errors } = useForm({
        nama_program:      bantuanSosial?.nama_program ?? '',
        jenis_bantuan:     bantuanSosial?.jenis_bantuan ?? '',
        deskripsi:         bantuanSosial?.deskripsi ?? '',
        nilai_bantuan:     bantuanSosial?.nilai_bantuan ?? '',
        periode:           bantuanSosial?.periode ?? '',
        tanggal_mulai:     bantuanSosial?.tanggal_mulai?.substring(0, 10) ?? '',
        tanggal_selesai:   bantuanSosial?.tanggal_selesai?.substring(0, 10) ?? '',
        status:            bantuanSosial?.status ?? 'aktif',
        kriteria_penerima: Array.isArray(bantuanSosial?.kriteria_penerima)
            ? bantuanSosial.kriteria_penerima.join(', ')
            : (bantuanSosial?.kriteria_penerima ?? ''),
        sumber_dana:       bantuanSosial?.sumber_dana ?? '',
        kuota_penerima:    bantuanSosial?.kuota_penerima ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(route('bantuan-sosial.update', bantuanSosial.id));
        } else {
            post(route('bantuan-sosial.store'));
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            {/* ── Section: Info Program ── */}
            <FormCard icon={FileText} title="Informasi Program">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div className="md:col-span-2">
                        <Field label="Nama Program" required error={errors.nama_program}>
                            <input
                                type="text"
                                value={data.nama_program}
                                onChange={(e) => setData('nama_program', e.target.value)}
                                placeholder="Contoh: Bantuan Langsung Tunai 2025"
                                className={inputClass(errors.nama_program)}
                            />
                        </Field>
                    </div>

                    <Field label="Jenis Bantuan" required error={errors.jenis_bantuan}>
                        <select
                            value={data.jenis_bantuan}
                            onChange={(e) => setData('jenis_bantuan', e.target.value)}
                            className={inputClass(errors.jenis_bantuan)}
                        >
                            <option value="">Pilih Jenis Bantuan</option>
                            {JENIS_OPTIONS.map((o) => (
                                <option key={o.value} value={o.value}>{o.label}</option>
                            ))}
                        </select>
                    </Field>

                    <Field label="Status Program" required error={errors.status}>
                        <select
                            value={data.status}
                            onChange={(e) => setData('status', e.target.value)}
                            className={inputClass(errors.status)}
                        >
                            {STATUS_OPTIONS.map((o) => (
                                <option key={o.value} value={o.value}>{o.label}</option>
                            ))}
                        </select>
                    </Field>

                    <div className="md:col-span-2">
                        <Field label="Deskripsi Program" required error={errors.deskripsi}>
                            <textarea
                                value={data.deskripsi}
                                onChange={(e) => setData('deskripsi', e.target.value)}
                                placeholder="Deskripsikan program bantuan sosial ini..."
                                rows={3}
                                className={inputClass(errors.deskripsi)}
                            />
                        </Field>
                    </div>

                    <div className="md:col-span-2">
                        <Field label="Kriteria Penerima" required error={errors.kriteria_penerima}>
                            <textarea
                                value={data.kriteria_penerima}
                                onChange={(e) => setData('kriteria_penerima', e.target.value)}
                                placeholder="Contoh: Keluarga tidak mampu, KIS aktif, usia di atas 60 tahun"
                                rows={2}
                                className={inputClass(errors.kriteria_penerima)}
                            />
                            <p className="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-wider">
                                Pisahkan kriteria dengan koma
                            </p>
                        </Field>
                    </div>
                </div>
            </FormCard>

            {/* ── Section: Dana & Periode ── */}
            <FormCard icon={DollarSign} title="Dana & Periode">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <Field label="Nilai Bantuan (Rp)" error={errors.nilai_bantuan}>
                        <input
                            type="number"
                            value={data.nilai_bantuan}
                            onChange={(e) => setData('nilai_bantuan', e.target.value)}
                            placeholder="0"
                            min="0"
                            step="1000"
                            className={inputClass(errors.nilai_bantuan)}
                        />
                    </Field>

                    <Field label="Sumber Dana" required error={errors.sumber_dana}>
                        <select
                            value={data.sumber_dana}
                            onChange={(e) => setData('sumber_dana', e.target.value)}
                            className={inputClass(errors.sumber_dana)}
                        >
                            <option value="">Pilih Sumber Dana</option>
                            {SUMBER_DANA_OPTIONS.map((o) => (
                                <option key={o.value} value={o.value}>{o.label}</option>
                            ))}
                        </select>
                    </Field>

                    <Field label="Periode" required error={errors.periode}>
                        <input
                            type="text"
                            value={data.periode}
                            onChange={(e) => setData('periode', e.target.value)}
                            placeholder="Contoh: 2025 atau 2025-2026"
                            className={inputClass(errors.periode)}
                        />
                    </Field>

                    <Field label="Kuota Penerima" error={errors.kuota_penerima}>
                        <input
                            type="number"
                            value={data.kuota_penerima}
                            onChange={(e) => setData('kuota_penerima', e.target.value)}
                            placeholder="0 (kosongkan jika tidak terbatas)"
                            min="0"
                            className={inputClass(errors.kuota_penerima)}
                        />
                    </Field>

                    <Field label="Tanggal Mulai" required error={errors.tanggal_mulai}>
                        <input
                            type="date"
                            value={data.tanggal_mulai}
                            onChange={(e) => setData('tanggal_mulai', e.target.value)}
                            className={inputClass(errors.tanggal_mulai)}
                        />
                    </Field>

                    <Field label="Tanggal Selesai" required error={errors.tanggal_selesai}>
                        <input
                            type="date"
                            value={data.tanggal_selesai}
                            onChange={(e) => setData('tanggal_selesai', e.target.value)}
                            className={inputClass(errors.tanggal_selesai)}
                        />
                    </Field>
                </div>
            </FormCard>

            {/* ── Action Buttons ── */}
            <div className="flex flex-col sm:flex-row gap-3 justify-end">
                <Link
                    href={route('bantuan-sosial.index')}
                    className="flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-black uppercase tracking-widest transition-all"
                >
                    <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                    BATAL
                </Link>
                <button
                    type="submit"
                    disabled={processing}
                    className="flex items-center justify-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 disabled:opacity-60 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-green-200 transition-all"
                >
                    <Save className="w-3.5 h-3.5 mr-2" />
                    {processing ? 'MENYIMPAN...' : (isEdit ? 'SIMPAN PERUBAHAN' : 'SIMPAN PROGRAM')}
                </button>
            </div>
        </form>
    );
}
