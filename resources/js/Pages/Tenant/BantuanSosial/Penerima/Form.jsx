import React, { useState, useCallback } from 'react';
import { useForm, Link } from '@inertiajs/react';
import ResidentAutocomplete from '@/Components/Shared/ResidentAutocomplete';
import MultiResidentAutocomplete from '@/Components/Shared/MultiResidentAutocomplete';
import { Users, Save, ArrowLeft, DollarSign, Calendar, Info } from 'lucide-react';

// ── Field wrapper ───────────────────────────────────────────────
function Field({ label, required, error, hint, children }) {
    return (
        <div>
            <label className="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">
                {label} {required && <span className="text-red-500">*</span>}
            </label>
            {children}
            {hint && <p className="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-wider">{hint}</p>}
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

// ── Berkala breakdown preview ────────────────────────────────
function BerkalaBreakdown({ total }) {
    if (!total || isNaN(total) || total <= 0) return null;
    const t = parseFloat(total);
    const per = Math.floor(t / 4);
    const rem = Math.round(t % 4);
    return (
        <div className="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-xl">
            <p className="text-[10px] font-black text-blue-700 uppercase tracking-widest mb-2 flex items-center gap-2">
                <Info className="w-3 h-3" />
                Perkiraan pembagian tahapan
            </p>
            <div className="space-y-1 text-xs text-blue-800 font-bold">
                <div className="flex justify-between"><span>Tahap 1</span><span>Rp {(per + (rem >= 1 ? 1 : 0)).toLocaleString('id-ID')}</span></div>
                <div className="flex justify-between"><span>Tahap 2</span><span>Rp {(per + (rem >= 2 ? 1 : 0)).toLocaleString('id-ID')}</span></div>
                <div className="flex justify-between"><span>Tahap 3</span><span>Rp {(per + (rem >= 3 ? 1 : 0)).toLocaleString('id-ID')}</span></div>
                <div className="flex justify-between"><span>Tahap 4</span><span>Rp {per.toLocaleString('id-ID')}</span></div>
            </div>
        </div>
    );
}

// ────────────────────────────────────────────────────────────────
// Shared Form Component
// ────────────────────────────────────────────────────────────────
export default function PenerimaForm({ mode = 'create', bantuanSosial, penerima = null }) {
    const isEdit = mode === 'edit';
    const isLocked = bantuanSosial.status === 'selesai' || bantuanSosial.is_expired;

    // Parse data_tambahan dari penerima existing
    const dataTambahan = (() => {
        if (!penerima?.data_tambahan) return {};
        try { return typeof penerima.data_tambahan === 'string' ? JSON.parse(penerima.data_tambahan) : penerima.data_tambahan; }
        catch { return {}; }
    })();

    const defaultSistem = dataTambahan?.sistem_pembayaran ?? (penerima ? 'sekali' : 'sekali');
    const isBerkalaInit = defaultSistem === 'berkala' || defaultSistem === 'triwulanan'; // backward compat

    const { data, setData, post, put, processing, errors } = useForm({
        // For Edit: we use single penduduk_id. For Create: we use penduduk_ids array.
        penduduk_id:            isEdit ? (penerima?.penduduk_id ?? '') : '',
        penduduk_ids:           isEdit ? [] : [],
        sistem_pembayaran:      isBerkalaInit ? 'berkala' : 'sekali',
        nilai_diterima:         isEdit && !isBerkalaInit ? (penerima?.nilai_diterima ?? '') : '',
        nilai_total_berkala:    isEdit && isBerkalaInit ? (dataTambahan?.total_amount ?? penerima?.nilai_diterima ?? '') : '',
        tanggal_penerimaan:     isEdit && !isBerkalaInit ? (penerima?.tanggal_penerimaan?.substring(0, 10) ?? '') : '',
        tanggal_tahap_1:        dataTambahan?.tahap_1?.tanggal ?? dataTambahan?.triwulan_1?.tanggal ?? '',
        tanggal_tahap_2:        dataTambahan?.tahap_2?.tanggal ?? dataTambahan?.triwulan_2?.tanggal ?? '',
        tanggal_tahap_3:        dataTambahan?.tahap_3?.tanggal ?? dataTambahan?.triwulan_3?.tanggal ?? '',
        tanggal_tahap_4:        dataTambahan?.tahap_4?.tanggal ?? '',
        status_penerimaan:      penerima?.status_penerimaan ?? 'aktif',
        keterangan:             penerima?.keterangan ?? '',
    });

    const isBerkala = data.sistem_pembayaran === 'berkala';

    // initialSelected logic
    const initialSelected = penerima?.penduduk
        ? { id: penerima.penduduk.id, nama: penerima.penduduk.nama, nik: penerima.penduduk.nik, alamat: penerima.penduduk.alamat }
        : null;

    const handleSelectSinglePenduduk = useCallback((resident) => {
        setData('penduduk_id', resident?.id ?? '');
    }, []);

    const handleSelectMultiPenduduk = useCallback((residents) => {
        setData('penduduk_ids', residents.map(r => r.id));
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(route('bantuan-sosial.penerima.update', [bantuanSosial.id, penerima.id]));
        } else {
            post(route('bantuan-sosial.penerima.store', bantuanSosial.id));
        }
    };

    return (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {/* ── Form ── */}
            <div className="lg:col-span-2">
                {isLocked ? (
                    <div className="bg-white rounded-2xl border border-amber-100 shadow-sm p-12 text-center">
                         <div className="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <Clock className="w-8 h-8 text-amber-600" />
                         </div>
                         <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Akses Terkunci</h3>
                         <p className="text-sm text-gray-500 mt-2 max-w-sm mx-auto font-bold uppercase tracking-widest text-[10px]">
                            Program bantuan ini telah selesai atau kadaluarsa. Data tidak dapat ditambah atau diubah lagi.
                         </p>
                         <Link
                            href={route('bantuan-sosial.penerima.index', bantuanSosial.id)}
                            className="inline-flex items-center mt-8 px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-black uppercase tracking-widest transition-all"
                         >
                            <ArrowLeft className="w-4 h-4 mr-2" />
                            KEMBALI KE DAFTAR
                         </Link>
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} className="space-y-5">
                    {/* Pilih Penduduk */}
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm relative z-50">
                        <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center gap-3 rounded-t-2xl">
                            <div className="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center">
                                <Users className="w-4 h-4 text-green-600" />
                            </div>
                            <div>
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">
                                    {isEdit ? 'Ubah Penerima' : 'Pilih Warga Penerima'}
                                </h3>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    {isEdit ? 'Edit data warga penerima bantuan' : 'Cari & pilih banyak warga sekaligus'}
                                </p>
                            </div>
                        </div>
                        <div className="p-6">
                            {isEdit ? (
                                <ResidentAutocomplete
                                    label="Cari Penduduk"
                                    placeholder="Ketik nama atau NIK warga..."
                                    onSelect={handleSelectSinglePenduduk}
                                    initialSelected={initialSelected}
                                />
                            ) : (
                                <MultiResidentAutocomplete
                                    label="Pilih Banyak Warga"
                                    placeholder="Ketik nama atau NIK warga..."
                                    onSelect={handleSelectMultiPenduduk}
                                />
                            )}
                            {errors.penduduk_id && <p className="mt-2 text-xs font-bold text-red-500">{errors.penduduk_id}</p>}
                            {errors.penduduk_ids && <p className="mt-2 text-xs font-bold text-red-500">{errors.penduduk_ids}</p>}
                        </div>
                    </div>

                    {/* Sistem Pembayaran */}
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center gap-3">
                            <div className="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center">
                                <DollarSign className="w-4 h-4 text-green-600" />
                            </div>
                            <div>
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Sistem Pembayaran</h3>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tentukan pola penerimaan bantuan</p>
                            </div>
                        </div>
                        <div className="p-6 space-y-5">
                            {/* Radio Sistem */}
                            <div className="flex flex-col sm:flex-row gap-3">
                                {[
                                    { value: 'sekali', label: 'Sekali Bayar', desc: '1x penerimaan' },
                                    { value: 'berkala', label: 'Berkala (Tahapan)', desc: '4x tahap (per 3 bulan)' },
                                ].map((opt) => (
                                    <label
                                        key={opt.value}
                                        className={`flex-1 flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all ${
                                            data.sistem_pembayaran === opt.value
                                                ? 'border-green-500 bg-green-50'
                                                : 'border-gray-200 hover:border-gray-300'
                                        }`}
                                    >
                                        <input
                                            type="radio"
                                            name="sistem_pembayaran"
                                            value={opt.value}
                                            checked={data.sistem_pembayaran === opt.value}
                                            onChange={() => setData('sistem_pembayaran', opt.value)}
                                            className="accent-green-600"
                                        />
                                        <div>
                                            <p className="text-sm font-black text-gray-900">{opt.label}</p>
                                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{opt.desc}</p>
                                        </div>
                                    </label>
                                ))}
                            </div>

                            {/* Sekali */}
                            {!isBerkala && (
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5 animate-in slide-in-from-top duration-200">
                                    <Field label="Nilai Diterima (Rp)" required error={errors.nilai_diterima}>
                                        <input
                                            type="number"
                                            value={data.nilai_diterima}
                                            onChange={(e) => setData('nilai_diterima', e.target.value)}
                                            placeholder={bantuanSosial.nilai_bantuan ?? '0'}
                                            min="0"
                                            className={inputClass(errors.nilai_diterima)}
                                        />
                                    </Field>
                                    <Field label="Tanggal Penerimaan" required error={errors.tanggal_penerimaan}>
                                        <input
                                            type="date"
                                            value={data.tanggal_penerimaan}
                                            onChange={(e) => setData('tanggal_penerimaan', e.target.value)}
                                            className={inputClass(errors.tanggal_penerimaan)}
                                        />
                                    </Field>
                                </div>
                            )}

                            {/* Berkala (4 Tahap) */}
                            {isBerkala && (
                                <div className="space-y-5 animate-in slide-in-from-top duration-200">
                                    <Field label="Nilai Total Bantuan Setahun (Rp)" required error={errors.nilai_total_berkala}>
                                        <input
                                            type="number"
                                            value={data.nilai_total_berkala}
                                            onChange={(e) => setData('nilai_total_berkala', e.target.value)}
                                            placeholder={bantuanSosial.nilai_bantuan ?? '0'}
                                            min="0"
                                            className={inputClass(errors.nilai_total_berkala)}
                                        />
                                        <BerkalaBreakdown total={data.nilai_total_berkala} />
                                    </Field>
                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                        {[
                                            { key: 'tanggal_tahap_1', label: 'Tahap 1', err: errors.tanggal_tahap_1 },
                                            { key: 'tanggal_tahap_2', label: 'Tahap 2', err: errors.tanggal_tahap_2 },
                                            { key: 'tanggal_tahap_3', label: 'Tahap 3', err: errors.tanggal_tahap_3 },
                                            { key: 'tanggal_tahap_4', label: 'Tahap 4', err: errors.tanggal_tahap_4 },
                                        ].map(({ key, label, err }) => (
                                            <Field key={key} label={label} required error={err}>
                                                <input
                                                    type="date"
                                                    value={data[key]}
                                                    onChange={(e) => setData(key, e.target.value)}
                                                    className={inputClass(err)}
                                                />
                                            </Field>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Status & Keterangan */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2 border-t border-gray-100">
                                <Field label="Status Penerimaan" required error={errors.status_penerimaan}>
                                    <select
                                        value={data.status_penerimaan}
                                        onChange={(e) => setData('status_penerimaan', e.target.value)}
                                        className={inputClass(errors.status_penerimaan)}
                                    >
                                        <option value="aktif">Aktif</option>
                                        <option value="ditangguhkan">Ditangguhkan</option>
                                        <option value="berhenti">Berhenti</option>
                                    </select>
                                </Field>
                                <Field label="Keterangan" error={errors.keterangan}>
                                    <input
                                        type="text"
                                        value={data.keterangan}
                                        onChange={(e) => setData('keterangan', e.target.value)}
                                        placeholder="Opsional..."
                                        className={inputClass(errors.keterangan)}
                                    />
                                </Field>
                            </div>
                        </div>
                    </div>

                    {/* Action */}
                    <div className="flex flex-col sm:flex-row gap-3 justify-end">
                        <Link
                            href={route('bantuan-sosial.penerima.index', bantuanSosial.id)}
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
                            {processing ? 'MENYIMPAN...' : (isEdit ? 'SIMPAN PERUBAHAN' : 'SIMPAN PENERIMA')}
                        </button>
                    </div>
                </form>
                )}
            </div>

            {/* ── Info Program Sidebar ── */}
            <div>
                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden sticky top-6">
                    <div className="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Info Program</h3>
                    </div>
                    <div className="p-5 space-y-3">
                        <div>
                            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Program</p>
                            <p className="text-sm font-bold text-gray-900 mt-1">{bantuanSosial.nama_program}</p>
                        </div>
                        <div>
                            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jenis Bantuan</p>
                            <p className="text-sm font-bold text-gray-900 mt-1">{bantuanSosial.jenis_bantuan}</p>
                        </div>
                        {bantuanSosial.nilai_bantuan && (
                            <div>
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nilai Bantuan</p>
                                <p className="text-sm font-bold text-green-700 mt-1">
                                    Rp {Number(bantuanSosial.nilai_bantuan).toLocaleString('id-ID')}
                                </p>
                            </div>
                        )}
                        <div>
                            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Periode</p>
                            <p className="text-sm font-bold text-gray-900 mt-1">{bantuanSosial.periode}</p>
                        </div>
                        {bantuanSosial.kuota_penerima && (
                            <div>
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kuota</p>
                                <p className="text-sm font-bold text-gray-900 mt-1">
                                    {Number(bantuanSosial.kuota_penerima).toLocaleString('id-ID')} orang
                                </p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
