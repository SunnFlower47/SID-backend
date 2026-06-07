import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Home, Plus, Crown, Save, AlertTriangle, CheckCircle } from 'lucide-react';
import Swal from 'sweetalert2';

// Shared Components
import { PageHeader, FormCard, FormField } from '@/Components/Shared';

export default function Create({ auth, masterRwOptions }) {
    const { data, setData, post, processing, errors } = useForm({
        nkk: '',
        nama_kepala_keluarga: '',
        nik_kepala_keluarga: '',
        alamat: '',
        rt_id: '',
        rw_id: '',
        status_perkawinan: 'KAWIN TERCATAT',
        pekerjaan: '',
        pendidikan: 'SLTA / SEDERAJAT',
        tempat_dikeluarkan: '',
        tanggal_dikeluarkan: '',
        golongan_darah: 'TIDAK TAHU',
        warganegara: 'WNI',
        nama_ayah: '',
        nama_ibu: '',
    });

    const OPTIONS = {
        agama: ['ISLAM', 'KRISTEN', 'KATOLIK', 'HINDU', 'BUDDHA', 'KONGHUCU'],
        status_perkawinan: ['BELUM KAWIN', 'KAWIN TERCATAT', 'KAWIN BELUM TERCATAT', 'CERAI HIDUP TERCATAT', 'CERAI HIDUP BELUM TERCATAT', 'CERAI MATI'],
        golongan_darah: ['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'TIDAK TAHU'],
        warganegara: ['WNI', 'WNA'],
    };

    const [availableRts, setAvailableRts] = useState([]);
    const [nkkStatus, setNkkStatus] = useState({ checking: false, exists: false });
    const [nikStatus, setNikStatus] = useState({ checking: false, exists: false, data: null });

    const checkNkk = async (nkk) => {
        if (!nkk || nkk.length !== 16) {
            setNkkStatus({ checking: false, exists: false });
            return;
        }
        setNkkStatus({ checking: true, exists: false });
        try {
            const response = await fetch(route('mutasi.check-nkk') + `?nkk=${nkk}`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            setNkkStatus({ checking: false, exists: result.exists });
        } catch (e) {
            setNkkStatus({ checking: false, exists: false });
        }
    };

    const checkNik = async (nik) => {
        if (!nik || nik.length !== 16) {
            setNikStatus({ checking: false, exists: false, data: null });
            return;
        }
        setNikStatus({ checking: true, exists: false, data: null });
        try {
            const response = await fetch(route('penduduk.check-nik') + `?nik=${nik}`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            setNikStatus({ checking: false, exists: result.exists, data: result.data });
        } catch (e) {
            setNikStatus({ checking: false, exists: false, data: null });
        }
    };

    useEffect(() => {
        const timer = setTimeout(() => checkNkk(data.nkk), 500);
        return () => clearTimeout(timer);
    }, [data.nkk]);

    useEffect(() => {
        const timer = setTimeout(() => checkNik(data.nik_kepala_keluarga), 500);
        return () => clearTimeout(timer);
    }, [data.nik_kepala_keluarga]);

    useEffect(() => {
        if (data.rw_id) {
            const selectedRw = masterRwOptions.find(rw => String(rw.id) === String(data.rw_id));
            setAvailableRts(selectedRw ? selectedRw.rts : []);
        } else {
            setAvailableRts([]);
        }
    }, [data.rw_id, masterRwOptions]);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('kk.store'), {
            onSuccess: () => {
                // Flash success handled globally
            },
            onError: (errs) => {
                if (Object.keys(errs).length > 0) {
                    Swal.fire({
                        title: 'Validasi Gagal!',
                        text: 'Silakan periksa kembali form Anda.',
                        icon: 'error'
                    });
                }
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Registrasi Kartu Keluarga Baru">
            <Head title="Buat Kartu Keluarga" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader
                    title="Registrasi Kartu Keluarga"
                    titleSize="sm"
                    subtitle="Pendaftaran Rumah Tangga Baru Desa Cibatu"
                    icon={Plus}
                    backHref={route('kk.index')}
                />

                <div className="w-full">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Household Data Section */}
                        <FormCard icon={Home} title="Informasi Rumah Tangga (KK)">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="md:col-span-2">
                                    <FormField label="Nomor Kartu Keluarga (NKK)" required error={errors.nkk}>
                                        <div className="relative">
                                            <input
                                                maxLength="16"
                                                value={data.nkk}
                                                onChange={e => setData('nkk', e.target.value.replace(/\D/g, ''))}
                                                placeholder="MASUKKAN 16 DIGIT NKK"
                                                className={`w-full px-4 py-3.5 bg-gray-50 border rounded-2xl text-sm font-bold font-mono tracking-widest outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all ${errors.nkk ? 'border-red-500' : 'border-gray-100'}`}
                                                required
                                            />
                                            {nkkStatus.checking && <div className="absolute right-4 top-3.5 animate-spin w-5 h-5 border-2 border-gray-400 border-t-transparent rounded-full"></div>}
                                        </div>
                                    </FormField>

                                    {data.nkk.length === 16 && !nkkStatus.checking && (
                                        <div className={`mt-2 p-2 rounded-xl border text-[10px] font-bold uppercase tracking-tight flex items-start ${nkkStatus.exists ? 'bg-red-50 border-red-100 text-red-700' : 'bg-green-50 border-green-100 text-green-700'}`}>
                                            {nkkStatus.exists ? (
                                                <><AlertTriangle className="w-3.5 h-3.5 mr-1.5 shrink-0 mt-0.5" /> <span>NKK Sudah Terdaftar!</span></>
                                            ) : (
                                                <><CheckCircle className="w-3.5 h-3.5 mr-1.5 shrink-0 mt-0.5" /> NKK Tersedia</>
                                            )}
                                        </div>
                                    )}
                                </div>

                                <div className="md:col-span-2">
                                    <FormField.Textarea
                                        label="Alamat Domisili"
                                        required
                                        rows="2"
                                        value={data.alamat}
                                        onChange={e => setData('alamat', e.target.value.toUpperCase())}
                                        error={errors.alamat}
                                        placeholder="KP / JALAN / NO RUMAH"
                                        inputClassName="uppercase"
                                    />
                                </div>

                                <FormField label="RW" required error={errors.rw_id}>
                                    <select
                                        value={data.rw_id}
                                        onChange={e => setData('rw_id', e.target.value)}
                                        className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-sm uppercase"
                                        required
                                    >
                                        <option value="">PILIH RW</option>
                                        {masterRwOptions.map(rw => (
                                            <option key={rw.id} value={rw.id}>RW {rw.kode} </option>
                                        ))}
                                    </select>
                                </FormField>

                                <FormField label="RT" required error={errors.rt_id}>
                                    <select
                                        value={data.rt_id}
                                        onChange={e => setData('rt_id', e.target.value)}
                                        disabled={!data.rw_id}
                                        className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-sm uppercase disabled:opacity-50"
                                        required
                                    >
                                        <option value="">PILIH RT</option>
                                        {availableRts.map(rt => (
                                            <option key={rt.id} value={rt.id}>RT {rt.kode} {rt.dusun ? `- ${rt.dusun.toUpperCase()}` : ''}</option>
                                        ))}
                                    </select>
                                </FormField>

                                <FormField.Input
                                    label="Tempat Dikeluarkan KK"
                                    value={data.tempat_dikeluarkan}
                                    onChange={e => setData('tempat_dikeluarkan', e.target.value.toUpperCase())}
                                    error={errors.tempat_dikeluarkan}
                                    placeholder="CONTOH: GARUT"
                                    inputClassName="uppercase"
                                />

                                <FormField.Input
                                    label="Tanggal Dikeluarkan KK"
                                    type="date"
                                    value={data.tanggal_dikeluarkan}
                                    onChange={e => setData('tanggal_dikeluarkan', e.target.value)}
                                    error={errors.tanggal_dikeluarkan}
                                />
                            </div>
                        </FormCard>

                        {/* Head of Family Section */}
                        <FormCard icon={Crown} title="Profil Kepala Keluarga Utama">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormField.Input
                                    label="Nama Kepala Keluarga"
                                    required
                                    value={data.nama_kepala_keluarga}
                                    onChange={e => setData('nama_kepala_keluarga', e.target.value.toUpperCase())}
                                    error={errors.nama_kepala_keluarga}
                                    placeholder="CONTOH: BUDI SANTOSO"
                                    inputClassName="uppercase italic"
                                />

                                <div className="space-y-0">
                                    <FormField label="NIK Kepala Keluarga" required error={errors.nik_kepala_keluarga}>
                                        <div className="relative">
                                            <input
                                                maxLength="16"
                                                value={data.nik_kepala_keluarga}
                                                onChange={e => setData('nik_kepala_keluarga', e.target.value.replace(/\D/g, ''))}
                                                placeholder="NIK 16 DIGIT"
                                                className={`w-full px-4 py-3.5 bg-gray-50 border rounded-2xl text-sm font-bold font-mono tracking-widest outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all ${errors.nik_kepala_keluarga ? 'border-red-500' : 'border-gray-100'}`}
                                                required
                                            />
                                            {nikStatus.checking && <div className="absolute right-4 top-3.5 animate-spin w-5 h-5 border-2 border-gray-400 border-t-transparent rounded-full"></div>}
                                        </div>
                                    </FormField>

                                    {data.nik_kepala_keluarga.length === 16 && !nikStatus.checking && (
                                        <div className={`mt-2 p-2 rounded-xl border text-[10px] font-bold uppercase tracking-tight flex items-start ${nikStatus.exists ? 'bg-red-50 border-red-100 text-red-700' : 'bg-green-50 border-green-100 text-green-700'}`}>
                                            {nikStatus.exists ? (
                                                <><AlertTriangle className="w-3.5 h-3.5 mr-1.5 shrink-0 mt-0.5" /> <span>NIK Terdaftar: {nikStatus.data?.nama}</span></>
                                            ) : (
                                                <><CheckCircle className="w-3.5 h-3.5 mr-1.5 shrink-0 mt-0.5" /> NIK Tersedia</>
                                            )}
                                        </div>
                                    )}
                                </div>

                                <FormField.Input
                                    label="Tempat Lahir"
                                    required
                                    value={data.tempat_lahir}
                                    onChange={e => setData('tempat_lahir', e.target.value.toUpperCase())}
                                    error={errors.tempat_lahir}
                                    placeholder="CONTOH: BANDUNG"
                                    inputClassName="uppercase"
                                />

                                <FormField.Input
                                    label="Tanggal Lahir"
                                    type="date"
                                    required
                                    value={data.tanggal_lahir}
                                    onChange={e => setData('tanggal_lahir', e.target.value)}
                                    error={errors.tanggal_lahir}
                                />

                                <FormField label="Jenis Kelamin">
                                    <div className="grid grid-cols-2 gap-3">
                                        {['LAKI-LAKI', 'PEREMPUAN'].map(gender => (
                                            <button
                                                key={gender}
                                                type="button"
                                                onClick={() => setData('jenis_kelamin', gender)}
                                                className={`py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest border transition-all ${data.jenis_kelamin === gender ? 'bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-white border-gray-100 text-gray-400 hover:bg-gray-50'}`}
                                            >
                                                {gender}
                                            </button>
                                        ))}
                                    </div>
                                </FormField>

                                <FormField label="Agama" required error={errors.agama}>
                                    <select
                                        value={data.agama}
                                        onChange={e => setData('agama', e.target.value)}
                                        className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-sm uppercase"
                                        required
                                    >
                                        <option value="">PILIH AGAMA</option>
                                        {OPTIONS.agama.map(ag => (
                                            <option key={ag} value={ag}>{ag}</option>
                                        ))}
                                    </select>
                                </FormField>

                                <FormField label="Golongan Darah" required error={errors.golongan_darah}>
                                    <select
                                        value={data.golongan_darah}
                                        onChange={e => setData('golongan_darah', e.target.value)}
                                        className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-sm uppercase"
                                        required
                                    >
                                        {OPTIONS.golongan_darah.map(gd => (
                                            <option key={gd} value={gd}>{gd}</option>
                                        ))}
                                    </select>
                                </FormField>

                                <FormField label="Status Perkawinan" required error={errors.status_perkawinan}>
                                    <select
                                        value={data.status_perkawinan}
                                        onChange={e => setData('status_perkawinan', e.target.value)}
                                        className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-sm uppercase"
                                        required
                                    >
                                        {OPTIONS.status_perkawinan.map(sp => (
                                            <option key={sp} value={sp}>{sp}</option>
                                        ))}
                                    </select>
                                </FormField>

                                <FormField label="Kewarganegaraan" required error={errors.warganegara}>
                                    <select
                                        value={data.warganegara}
                                        onChange={e => setData('warganegara', e.target.value)}
                                        className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-sm uppercase"
                                        required
                                    >
                                        {OPTIONS.warganegara.map(wg => (
                                            <option key={wg} value={wg}>{wg}</option>
                                        ))}
                                    </select>
                                </FormField>

                                <FormField.Input
                                    label="Pekerjaan"
                                    required
                                    value={data.pekerjaan}
                                    onChange={e => setData('pekerjaan', e.target.value.toUpperCase())}
                                    error={errors.pekerjaan}
                                    placeholder="CONTOH: WIRASWASTA"
                                    inputClassName="uppercase"
                                />

                                <FormField.Input
                                    label="Pendidikan Terakhir"
                                    required
                                    value={data.pendidikan}
                                    onChange={e => setData('pendidikan', e.target.value.toUpperCase())}
                                    error={errors.pendidikan}
                                    placeholder="CONTOH: SLTA / SEDERAJAT"
                                    inputClassName="uppercase"
                                />

                                <FormField.Input
                                    label="Nama Ayah Kandung"
                                    required
                                    value={data.nama_ayah}
                                    onChange={e => setData('nama_ayah', e.target.value.toUpperCase())}
                                    error={errors.nama_ayah}
                                    placeholder="CONTOH: AGUS"
                                    inputClassName="uppercase"
                                />

                                <FormField.Input
                                    label="Nama Ibu Kandung"
                                    required
                                    value={data.nama_ibu}
                                    onChange={e => setData('nama_ibu', e.target.value.toUpperCase())}
                                    error={errors.nama_ibu}
                                    placeholder="CONTOH: SITI"
                                    inputClassName="uppercase"
                                />
                            </div>
                        </FormCard>

                        {/* Submit Section */}
                        <div className="flex justify-end pt-4 pb-12">
                            <button
                                type="submit"
                                disabled={processing || nkkStatus.exists || nikStatus.exists}
                                className="w-full sm:w-auto px-10 py-3.5 bg-green-600 hover:bg-green-700 text-white font-black rounded-xl shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-95 transition-all text-[10px] uppercase tracking-[0.2em] flex items-center justify-center disabled:opacity-50"
                            >
                                {processing ? (
                                    <><div className="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-3"></div> MEMPROSES...</>
                                ) : (
                                    <><Save className="w-4 h-4 mr-2" /> DAFTARKAN KARTU KELUARGA</>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
