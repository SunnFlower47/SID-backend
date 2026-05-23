import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { MessageSquare, Save, User, MapPin, Star } from 'lucide-react';

export default function Edit({ auth, testimoni, masterRwOptions }) {
    const { data, setData, put, processing, errors } = useForm({
        nama: testimoni.nama || '',
        email: testimoni.email || '',
        telepon: testimoni.telepon || '',
        rw_id: testimoni.rw_id || '',
        rt_id: testimoni.rt_id || '',
        dusun_id: testimoni.dusun_id || '',
        testimoni: testimoni.testimoni || '',
        rating: testimoni.rating || 5,
        kategori: testimoni.kategori || 'umum',
        status: testimoni.status || 'pending',
        is_anonymous: testimoni.is_anonymous || false,
    });

    const [availableRts, setAvailableRts] = useState([]);

    useEffect(() => {
        if (data.rw_id) {
            const selectedRw = masterRwOptions.find(rw => rw.id === parseInt(data.rw_id));
            setAvailableRts(selectedRw?.rts || []);
        } else {
            setAvailableRts([]);
        }
    }, [data.rw_id]);

    const handleRtChange = (rtId) => {
        const selectedRt = availableRts.find(rt => rt.id === parseInt(rtId));
        setData(prev => ({ ...prev, rt_id: rtId, dusun_id: selectedRt ? selectedRt.dusun_id : '' }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('testimoni.update', testimoni.id));
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Edit Testimoni">
            <Head title={`Edit Testimoni: ${testimoni.nama}`} />

            <div className="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-10 text-left">
                <PageHeader
                    icon={MessageSquare}
                    title="Edit Testimoni"
                    subtitle="Memperbarui data testimoni warga"
                    backUrl={route('testimoni.index')}
                />

                <form onSubmit={handleSubmit} className="grid grid-cols-1 lg:grid-cols-3 gap-6 text-left">
                    <div className="lg:col-span-2 space-y-6 text-left">
                        <FormCard title="Detail Testimoni" icon={MessageSquare}>
                            <div className="space-y-6 text-left">
                                <FormField.Textarea
                                    label="Isi Testimoni"
                                    value={data.testimoni}
                                    onChange={e => setData('testimoni', e.target.value)}
                                    error={errors.testimoni}
                                    className="min-h-[200px]"
                                />
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                                    <div className="text-left">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 block text-left">Rating Kepuasan</label>
                                        <div className="flex items-center gap-2 p-4 bg-gray-50 rounded-2xl shadow-inner text-left">
                                            {[1, 2, 3, 4, 5].map(s => (
                                                <button key={s} type="button" onClick={() => setData('rating', s)} className="focus:outline-none transform hover:scale-110 transition-transform text-left">
                                                    <Star className={`w-8 h-8 ${data.rating >= s ? 'fill-orange-400 text-orange-400' : 'text-gray-200'}`} />
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                    <div className="text-left space-y-4">
                                        <FormField.Select
                                            label="Kategori"
                                            value={data.kategori}
                                            onChange={e => setData('kategori', e.target.value)}
                                            options={[
                                                { value: 'umum', label: 'Umum' },
                                                { value: 'pelayanan', label: 'Pelayanan' },
                                                { value: 'pembangunan', label: 'Pembangunan' }
                                            ]}
                                        />
                                        <FormField.Select
                                            label="Status"
                                            value={data.status}
                                            onChange={e => setData('status', e.target.value)}
                                            options={[
                                                { value: 'pending', label: 'Pending' },
                                                { value: 'approved', label: 'Approved' },
                                                { value: 'rejected', label: 'Rejected' }
                                            ]}
                                        />
                                    </div>
                                </div>
                            </div>
                        </FormCard>
                        
                        <div className="flex justify-end text-left">
                            <button type="submit" disabled={processing} className="px-12 py-5 bg-indigo-600 text-white rounded-[2rem] text-[10px] font-black uppercase tracking-[0.2em] shadow-2xl hover:scale-105 active:scale-95 transition-all flex items-center gap-3 text-left">
                                <Save className="w-5 h-5 text-left" /> SIMPAN PERUBAHAN
                            </button>
                        </div>
                    </div>

                    <div className="space-y-6 text-left">
                        <FormCard title="Informasi Pengirim" icon={User} compact>
                            <div className="space-y-4 text-left">
                                <FormField.Input
                                    placeholder="Nama Lengkap"
                                    value={data.nama}
                                    onChange={e => setData('nama', e.target.value)}
                                    error={errors.nama}
                                />
                                <FormField.Input
                                    type="email"
                                    placeholder="Email"
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    error={errors.email}
                                />
                                <FormField.Input
                                    placeholder="Telepon"
                                    value={data.telepon}
                                    onChange={e => setData('telepon', e.target.value)}
                                    error={errors.telepon}
                                />
                            </div>
                        </FormCard>

                        <FormCard title="Lokasi / Wilayah" icon={MapPin} compact>
                            <div className="space-y-4 text-left">
                                <FormField.Select
                                    value={data.rw_id}
                                    onChange={e => setData('rw_id', e.target.value)}
                                    options={[
                                        { value: '', label: 'PILIH RW' },
                                        ...masterRwOptions.map(rw => ({
                                            value: rw.id,
                                            label: `RW ${rw.kode} - ${rw.nama}`
                                        }))
                                    ]}
                                />
                                <FormField.Select
                                    value={data.rt_id}
                                    onChange={e => handleRtChange(e.target.value)}
                                    disabled={!data.rw_id}
                                    options={[
                                        { value: '', label: 'PILIH RT' },
                                        ...availableRts.map(rt => ({
                                            value: rt.id,
                                            label: `RT ${rt.kode}`
                                        }))
                                    ]}
                                />
                            </div>
                        </FormCard>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
