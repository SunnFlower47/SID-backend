import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Home, ArrowLeft, Plus, MapPin, Crown, Info, UserCircle, Calendar, Briefcase, GraduationCap, Heart, User, Save } from 'lucide-react';
import Swal from 'sweetalert2';

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
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 text-white relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Plus className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black tracking-tight uppercase italic leading-none text-left">Registrasi Kartu Keluarga</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic text-left">Pendaftaran Rumah Tangga Baru Desa Cibatu</p>
                            </div>
                        </div>
                        <Link 
                            href={route('kk.index')}
                            className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all w-fit"
                        >
                            <ArrowLeft className="w-4 h-4 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>

                <div className="w-full">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Household Data Section */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center gap-3">
                                <div className="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                                    <Home className="w-4 h-4 text-blue-600" />
                                </div>
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Informasi Rumah Tangga (KK)</h3>
                            </div>
                            
                            <div className="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="md:col-span-2">
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Nomor Kartu Keluarga (NKK) *</label>
                                    <input 
                                        type="text"
                                        maxLength="16"
                                        value={data.nkk}
                                        onChange={e => setData('nkk', e.target.value)}
                                        className={`w-full px-4 py-3.5 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-mono font-bold text-sm tracking-widest ${errors.nkk ? 'border-red-500' : 'border-gray-100'}`}
                                        placeholder="MASUKKAN 16 DIGIT NKK"
                                        required
                                    />
                                    {errors.nkk && <p className="mt-2 text-[10px] font-bold text-red-600 uppercase italic px-1">{errors.nkk}</p>}
                                </div>

                                <div className="md:col-span-2">
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Alamat Domisili *</label>
                                    <textarea 
                                        value={data.alamat}
                                        onChange={e => setData('alamat', e.target.value.toUpperCase())}
                                        rows="2"
                                        className={`w-full px-4 py-3.5 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-sm uppercase ${errors.alamat ? 'border-red-500' : 'border-gray-100'}`}
                                        placeholder="KP / JALAN / NO RUMAH"
                                        required
                                    />
                                </div>

                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">RW *</label>
                                    <select 
                                        value={data.rw_id}
                                        onChange={e => setData('rw_id', e.target.value)}
                                        className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-black text-xs uppercase"
                                        required
                                    >
                                        <option value="">PILIH RW</option>
                                        {masterRwOptions.map(rw => (
                                            <option key={rw.id} value={rw.id}>RW {rw.kode} - {rw.nama}</option>
                                        ))}
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">RT *</label>
                                    <select 
                                        value={data.rt_id}
                                        onChange={e => setData('rt_id', e.target.value)}
                                        disabled={!data.rw_id}
                                        className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-black text-xs uppercase disabled:opacity-50"
                                        required
                                    >
                                    <option value="">PILIH RT</option>
                                    {availableRts.map(rt => (
                                        <option key={rt.id} value={rt.id}>RT {rt.kode} {rt.dusun ? `- DUSUN ${rt.dusun.toUpperCase()}` : ''}</option>
                                    ))}
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Head of Family Section */}
                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center gap-3">
                            <div className="w-8 h-8 bg-yellow-50 rounded-lg flex items-center justify-center">
                                <Crown className="w-4 h-4 text-yellow-600" />
                            </div>
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Profil Kepala Keluarga Utama</h3>
                        </div>
                        
                        <div className="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Nama Kepala Keluarga *</label>
                                <input 
                                    type="text"
                                    value={data.nama_kepala_keluarga}
                                    onChange={e => setData('nama_kepala_keluarga', e.target.value.toUpperCase())}
                                    className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-black text-xs uppercase italic"
                                    placeholder="NAMA LENGKAP"
                                    required
                                />
                            </div>

                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">NIK Kepala Keluarga *</label>
                                <input 
                                    type="text"
                                    maxLength="16"
                                    value={data.nik_kepala_keluarga}
                                    onChange={e => setData('nik_kepala_keluarga', e.target.value)}
                                    className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-mono font-bold text-xs"
                                    placeholder="NIK 16 DIGIT"
                                    required
                                />
                            </div>

                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Tempat Lahir</label>
                                <input 
                                    type="text"
                                    value={data.tempat_lahir}
                                    onChange={e => setData('tempat_lahir', e.target.value.toUpperCase())}
                                    className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-xs uppercase"
                                    required
                                />
                            </div>

                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Tanggal Lahir</label>
                                <input 
                                    type="date"
                                    value={data.tanggal_lahir}
                                    onChange={e => setData('tanggal_lahir', e.target.value)}
                                    className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-xs"
                                    required
                                />
                            </div>

                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Jenis Kelamin</label>
                                <div className="grid grid-cols-2 gap-3">
                                    {['LAKI-LAKI', 'PEREMPUAN'].map(gender => (
                                        <button
                                            key={gender}
                                            type="button"
                                            onClick={() => setData('jenis_kelamin', gender)}
                                            className={`py-3 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all ${data.jenis_kelamin === gender ? 'bg-blue-600 border-blue-600 text-white shadow-lg' : 'bg-white border-gray-100 text-gray-400 hover:bg-gray-50'}`}
                                        >
                                            {gender}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Pekerjaan</label>
                                <input 
                                    type="text"
                                    value={data.pekerjaan}
                                    onChange={e => setData('pekerjaan', e.target.value.toUpperCase())}
                                    className="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-xs uppercase"
                                    placeholder="CONTOH: WIRASWASTA"
                                    required
                                />
                            </div>
                        </div>
                    </div>

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
