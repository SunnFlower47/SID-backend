import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Home, Plus, Crown, Save } from 'lucide-react';
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
        jenis_kelamin: 'LAKI-LAKI',
        tempat_lahir: '',
        tanggal_lahir: '',
        agama: 'ISLAM',
        status_perkawinan: 'KAWIN',
        pekerjaan: '',
        pendidikan: 'SLTA / SEDERAJAT',
    });

    const [availableRts, setAvailableRts] = useState([]);

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
                                    <FormField.Input 
                                        label="Nomor Kartu Keluarga (NKK)"
                                        required
                                        maxLength="16"
                                        value={data.nkk}
                                        onChange={e => setData('nkk', e.target.value)}
                                        error={errors.nkk}
                                        placeholder="MASUKKAN 16 DIGIT NKK"
                                        inputClassName="font-mono font-bold tracking-widest"
                                    />
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
                                            <option key={rw.id} value={rw.id}>RW {rw.kode} - {rw.nama}</option>
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
                                            <option key={rt.id} value={rt.id}>RT {rt.kode} {rt.dusun ? `- DUSUN ${rt.dusun.toUpperCase()}` : ''}</option>
                                        ))}
                                    </select>
                                </FormField>
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
                                    placeholder="NAMA LENGKAP"
                                    inputClassName="uppercase italic"
                                />

                                <FormField.Input 
                                    label="NIK Kepala Keluarga"
                                    required
                                    maxLength="16"
                                    value={data.nik_kepala_keluarga}
                                    onChange={e => setData('nik_kepala_keluarga', e.target.value)}
                                    error={errors.nik_kepala_keluarga}
                                    placeholder="NIK 16 DIGIT"
                                    inputClassName="font-mono font-bold"
                                />

                                <FormField.Input 
                                    label="Tempat Lahir"
                                    required
                                    value={data.tempat_lahir}
                                    onChange={e => setData('tempat_lahir', e.target.value.toUpperCase())}
                                    error={errors.tempat_lahir}
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

                                <FormField.Input 
                                    label="Pekerjaan"
                                    required
                                    value={data.pekerjaan}
                                    onChange={e => setData('pekerjaan', e.target.value.toUpperCase())}
                                    error={errors.pekerjaan}
                                    placeholder="CONTOH: WIRASWASTA"
                                    inputClassName="uppercase"
                                />
                            </div>
                        </FormCard>

                        {/* Submit Section */}
                        <div className="flex justify-end pt-4 pb-12">
                            <button
                                type="submit"
                                disabled={processing}
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
