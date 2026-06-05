import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { Users, Save, ArrowLeft, Layers, MapPin, GraduationCap, Search, Loader2, Phone, CreditCard, CheckCircle, XCircle, AlertCircle, Calendar, FileText } from 'lucide-react';
import Swal from 'sweetalert2';
import axios from 'axios';

export default function Form({ auth, anggota, is_edit }) {
    const [searchQuery, setSearchQuery] = useState('');
    const [searchResults, setSearchResults] = useState([]);
    const [showDropdown, setShowDropdown] = useState(false);
    const [isSearching, setIsSearching] = useState(false);
    const [selectedPerson, setSelectedPerson] = useState(
        is_edit && anggota?.nik ? { nama: anggota.nama, nik: anggota.nik } : null
    );
    const [nikStatus, setNikStatus] = useState(null);

    const { data, setData, post, put, processing, errors } = useForm({
        nik: anggota?.nik || '',
        nama: anggota?.nama || '',
        jenis_kelamin: anggota?.jenis_kelamin || 'L',
        tempat_lahir: anggota?.tempat_lahir || '',
        tanggal_lahir: anggota?.tanggal_lahir || '',
        agama: anggota?.agama || '',
        pendidikan_terakhir: anggota?.pendidikan_terakhir || '',
        jabatan: anggota?.jabatan || '',
        no_keputusan_pengangkatan: anggota?.no_keputusan_pengangkatan || '',
        tanggal_keputusan_pengangkatan: anggota?.tanggal_keputusan_pengangkatan || '',
        no_keputusan_pemberhentian: anggota?.no_keputusan_pemberhentian || '',
        tanggal_keputusan_pemberhentian: anggota?.tanggal_keputusan_pemberhentian || '',
        alamat: anggota?.alamat || '',
        rt: anggota?.rt || '',
        rw: anggota?.rw || '',
        dusun: anggota?.dusun || '',
        no_hp: anggota?.no_hp || '',
        keterangan: anggota?.keterangan || '',
        status: anggota?.status || 'aktif',
    });

    React.useEffect(() => {
        if (searchQuery.length < 3) {
            setSearchResults([]);
            setShowDropdown(false);
            return;
        }

        const delayDebounceFn = setTimeout(() => {
            setIsSearching(true);
            axios.get(route('penduduk.search'), { params: { q: searchQuery } })
                .then(res => {
                    setSearchResults(res.data);
                    setShowDropdown(true);
                })
                .catch(err => console.error(err))
                .finally(() => setIsSearching(false));
        }, 500);

        return () => clearTimeout(delayDebounceFn);
    }, [searchQuery]);

    React.useEffect(() => {
        if (!data.nik || data.nik.length < 16) {
            setNikStatus(null);
            return;
        }
        setNikStatus('checking');
        const timer = setTimeout(() => {
            axios.get(route('sekretariat.anggota-bpd.check-nik'), {
                params: { nik: data.nik, ignore_id: is_edit ? anggota.id : undefined }
            }).then(res => {
                setNikStatus(res.data.exists ? 'duplicate' : 'ok');
                if (res.data.exists) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'NIK Sudah Terdaftar',
                        html: `Penduduk dengan NIK ini sudah terdaftar sebagai anggota BPD dengan jabatan <b>${res.data.anggota?.jabatan}</b>.`,
                        confirmButtonText: 'Mengerti'
                    });
                }
            }).catch(() => setNikStatus(null));
        }, 600);
        return () => clearTimeout(timer);
    }, [data.nik]);

    const handleSelectPerson = async (nik) => {
        setShowDropdown(false);
        setIsSearching(true);
        try {
            const res = await axios.get(route('penduduk.check-nik'), { params: { nik: nik } });
            if (res.data.exists && res.data.data) {
                const p = res.data.data;
                setSelectedPerson({ nama: p.nama, nik: p.nik });
                setSearchQuery('');
                setData(prev => ({
                    ...prev,
                    nik: p.nik || prev.nik,
                    nama: p.nama || prev.nama,
                    jenis_kelamin: p.jenis_kelamin === 'PEREMPUAN' || p.jenis_kelamin === 'P' ? 'P' : 'L',
                    tempat_lahir: p.tempat_lahir || prev.tempat_lahir,
                    tanggal_lahir: p.tanggal_lahir || prev.tanggal_lahir,
                    agama: p.agama || prev.agama,
                    pendidikan_terakhir: p.pendidikan || prev.pendidikan_terakhir,
                    alamat: p.alamat || prev.alamat,
                    rt: p.rt_label || prev.rt,
                    rw: p.rw_label || prev.rw,
                    dusun: p.dusun_label || prev.dusun
                }));
                Swal.fire({
                    icon: 'success',
                    title: 'Data Ditemukan',
                    text: 'Form telah diisi otomatis berdasarkan data penduduk',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } catch (error) {
            Swal.fire('Error', 'Gagal mengambil detail penduduk', 'error');
        } finally {
            setIsSearching(false);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        
        const action = is_edit 
            ? put(route('sekretariat.anggota-bpd.update', anggota.id))
            : post(route('sekretariat.anggota-bpd.store'));
            
        action.then(() => {
            if (!Object.keys(errors).length) {
                // Success is handled by server redirect
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title={is_edit ? 'Edit Anggota BPD' : 'Tambah Anggota BPD'}>
            <Head title={is_edit ? 'Edit Anggota BPD' : 'Tambah Anggota BPD'} />
            
            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader
                    icon={Users}
                    title={is_edit ? 'Edit Anggota BPD' : 'Tambah Anggota BPD'}
                    subtitle={is_edit ? 'Ubah data anggota Badan Permusyawaratan Desa' : 'Tambahkan data anggota BPD baru'}
                    actions={[
                        { label: 'Kembali', icon: ArrowLeft, href: route('sekretariat.anggota-bpd.index'), variant: 'ghost' },
                    ]}
                />

                <form onSubmit={handleSubmit} className="space-y-6">
                    <FormCard icon={Users} title="Identitas Anggota" description="Informasi dasar mengenai anggota BPD.">
                        
                        <div className="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-2xl animate-in fade-in">
                            <label className="block text-sm font-bold text-blue-900 mb-2">
                                Isi Otomatis dari Data Penduduk (Opsional)
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    {isSearching ? <Loader2 className="h-5 w-5 text-blue-400 animate-spin" /> : <Search className="h-5 w-5 text-blue-400" />}
                                </div>
                                <input
                                    type="text"
                                    className="pl-10 block w-full bg-white border border-blue-200 text-blue-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 p-3 transition-colors"
                                    placeholder="Ketik NIK atau Nama Penduduk..."
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    onFocus={() => { if (searchResults.length > 0) setShowDropdown(true); }}
                                    onBlur={() => setTimeout(() => setShowDropdown(false), 200)}
                                />
                                {showDropdown && searchResults.length > 0 && (
                                    <div className="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-auto">
                                        {searchResults.map((item) => (
                                            <button
                                                key={item.id}
                                                type="button"
                                                className="w-full text-left px-4 py-3 hover:bg-blue-50 focus:bg-blue-50 transition-colors border-b border-gray-100 last:border-0"
                                                onClick={() => handleSelectPerson(item.nik)}
                                            >
                                                <div className="font-semibold text-gray-900">{item.nama}</div>
                                                <div className="text-xs text-gray-500 font-mono mt-0.5">NIK: {item.nik}</div>
                                            </button>
                                        ))}
                                    </div>
                                )}
                            </div>

                            {selectedPerson && (
                                <div className="mt-4 bg-white p-4 border border-blue-200 rounded-xl flex items-center justify-between animate-in fade-in slide-in-from-top-2">
                                    <div className="flex items-center gap-3">
                                        <div className="bg-blue-50 p-2.5 rounded-lg border border-blue-100">
                                            <Users className="w-5 h-5 text-blue-600" />
                                        </div>
                                        <div>
                                            <p className="text-sm font-bold text-gray-900 leading-none mb-1">{selectedPerson.nama}</p>
                                            <p className="text-xs text-gray-500 font-mono">NIK: {selectedPerson.nik}</p>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setSelectedPerson(null)}
                                        className="text-red-500 hover:text-red-700 text-sm font-bold px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            )}
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-1.5">
                                <label className="block text-sm font-bold text-gray-700">
                                    NIK <span className="text-gray-400 font-normal text-xs">(opsional)</span>
                                </label>
                                <div className="relative">
                                    <CreditCard className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <input
                                        type="text"
                                        maxLength={16}
                                        value={data.nik}
                                        onChange={e => setData('nik', e.target.value)}
                                        placeholder="16 digit NIK"
                                        className={`pl-10 pr-10 block w-full bg-gray-50 text-gray-900 text-sm rounded-xl p-3 transition-colors border ${
                                            nikStatus === 'duplicate' ? 'border-red-400 focus:ring-red-400 focus:border-red-400' :
                                            nikStatus === 'ok'        ? 'border-emerald-400 focus:ring-emerald-400 focus:border-emerald-400' :
                                            'border-gray-200 focus:ring-blue-500 focus:border-blue-500'
                                        }`}
                                    />
                                    <div className="absolute right-3 top-1/2 -translate-y-1/2">
                                        {nikStatus === 'checking'  && <Loader2 className="w-4 h-4 text-blue-400 animate-spin" />}
                                        {nikStatus === 'ok'        && <CheckCircle className="w-4 h-4 text-emerald-500" />}
                                        {nikStatus === 'duplicate' && <XCircle className="w-4 h-4 text-red-500" />}
                                    </div>
                                </div>
                                {nikStatus === 'duplicate' && <p className="text-xs text-red-500 flex items-center gap-1"><AlertCircle className="w-3 h-3"/>NIK sudah terdaftar</p>}
                                {errors.nik && <p className="text-xs text-red-500">{errors.nik}</p>}
                            </div>

                            <FormField.Input
                                label="Nama Lengkap"
                                value={data.nama}
                                onChange={e => setData('nama', e.target.value)}
                                error={errors.nama}
                                required
                            />
                            
                            <div className="grid grid-cols-2 gap-4">
                                <FormField.Input
                                    label="Tempat Lahir"
                                    value={data.tempat_lahir}
                                    onChange={e => setData('tempat_lahir', e.target.value)}
                                    error={errors.tempat_lahir}
                                />
                                <FormField.Input
                                    type="date"
                                    label="Tanggal Lahir"
                                    value={data.tanggal_lahir}
                                    onChange={e => setData('tanggal_lahir', e.target.value)}
                                    error={errors.tanggal_lahir}
                                />
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <label className="block text-sm font-bold text-gray-700">Jenis Kelamin</label>
                                    <select
                                        value={data.jenis_kelamin}
                                        onChange={e => setData('jenis_kelamin', e.target.value)}
                                        className="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 transition-colors"
                                        required
                                    >
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    {errors.jenis_kelamin && <p className="text-sm text-red-500 mt-1">{errors.jenis_kelamin}</p>}
                                </div>
                                <div className="space-y-2">
                                    <label className="block text-sm font-bold text-gray-700">Agama</label>
                                    <select
                                        value={data.agama}
                                        onChange={e => setData('agama', e.target.value)}
                                        className="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 transition-colors"
                                    >
                                        <option value="">Pilih Agama...</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                    {errors.agama && <p className="text-sm text-red-500 mt-1">{errors.agama}</p>}
                                </div>
                            </div>

                            <FormField.Input
                                label="Pendidikan Terakhir"
                                placeholder="Contoh: SMA, S1 Hukum, dll"
                                value={data.pendidikan_terakhir}
                                onChange={e => setData('pendidikan_terakhir', e.target.value)}
                                error={errors.pendidikan_terakhir}
                                required
                            />

                            <FormField.Input
                                label="No. HP / WhatsApp"
                                placeholder="08xxxxxxxxxx"
                                value={data.no_hp}
                                onChange={e => setData('no_hp', e.target.value)}
                                error={errors.no_hp}
                                icon={<Phone className="w-4 h-4" />}
                            />
                        </div>
                    </FormCard>

                    <FormCard icon={FileText} title="Data Jabatan & SK" description="Informasi tentang masa jabatan anggota BPD.">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <label className="block text-sm font-bold text-gray-700">Jabatan di BPD</label>
                                <select
                                    value={data.jabatan}
                                    onChange={e => setData('jabatan', e.target.value)}
                                    className="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 transition-colors"
                                    required
                                >
                                    <option value="" disabled>Pilih Jabatan...</option>
                                    <option value="Ketua">Ketua</option>
                                    <option value="Wakil Ketua">Wakil Ketua</option>
                                    <option value="Sekretaris">Sekretaris</option>
                                    <option value="Anggota">Anggota</option>
                                </select>
                                {errors.jabatan && <p className="text-sm text-red-500 mt-1">{errors.jabatan}</p>}
                            </div>

                            <div className="space-y-2">
                                <label className="block text-sm font-bold text-gray-700">Status Aktif</label>
                                <select
                                    value={data.status}
                                    onChange={e => setData('status', e.target.value)}
                                    className="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 transition-colors"
                                >
                                    <option value="aktif">Aktif</option>
                                    <option value="tidak_aktif">Purna Tugas / Tidak Aktif</option>
                                </select>
                            </div>

                            <FormField.Input
                                label="Nomor SK Pengangkatan"
                                value={data.no_keputusan_pengangkatan}
                                onChange={e => setData('no_keputusan_pengangkatan', e.target.value)}
                                error={errors.no_keputusan_pengangkatan}
                            />

                            <FormField.Input
                                type="date"
                                label="Tanggal SK Pengangkatan"
                                value={data.tanggal_keputusan_pengangkatan}
                                onChange={e => setData('tanggal_keputusan_pengangkatan', e.target.value)}
                                error={errors.tanggal_keputusan_pengangkatan}
                            />

                            <FormField.Input
                                label="Nomor SK Pemberhentian"
                                value={data.no_keputusan_pemberhentian}
                                onChange={e => setData('no_keputusan_pemberhentian', e.target.value)}
                                error={errors.no_keputusan_pemberhentian}
                                placeholder="Diisi jika sudah diberhentikan"
                            />

                            <FormField.Input
                                type="date"
                                label="Tanggal SK Pemberhentian"
                                value={data.tanggal_keputusan_pemberhentian}
                                onChange={e => setData('tanggal_keputusan_pemberhentian', e.target.value)}
                                error={errors.tanggal_keputusan_pemberhentian}
                            />
                        </div>
                    </FormCard>

                    <FormCard icon={MapPin} title="Detail & Alamat" description="Informasi lokasi dan keterangan tambahan.">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <FormField.Textarea
                                label="Alamat Jalan / Kampung"
                                value={data.alamat}
                                onChange={e => setData('alamat', e.target.value)}
                                error={errors.alamat}
                                rows={4}
                                required
                            />
                            <div className="space-y-6">
                                <div className="grid grid-cols-2 gap-4">
                                    <FormField.Input
                                        label="RT"
                                        value={data.rt}
                                        onChange={e => setData('rt', e.target.value)}
                                        error={errors.rt}
                                    />
                                    <FormField.Input
                                        label="RW"
                                        value={data.rw}
                                        onChange={e => setData('rw', e.target.value)}
                                        error={errors.rw}
                                    />
                                </div>
                                <FormField.Input
                                    label="Dusun"
                                    value={data.dusun}
                                    onChange={e => setData('dusun', e.target.value)}
                                    error={errors.dusun}
                                />
                            </div>
                        </div>

                        <div className="mt-6">
                            <FormField.Textarea
                                label="Keterangan (Opsional)"
                                value={data.keterangan}
                                onChange={e => setData('keterangan', e.target.value)}
                                error={errors.keterangan}
                                rows={2}
                            />
                        </div>
                    </FormCard>

                    <div className="flex items-center justify-end gap-3 pt-6">
                        <Link
                            href={route('sekretariat.anggota-bpd.index')}
                            className="px-6 py-3 text-gray-600 bg-white border border-gray-200 font-bold rounded-2xl hover:bg-gray-50 transition-colors"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            disabled={processing || nikStatus === 'duplicate'}
                            className="flex items-center gap-2 px-8 py-3 bg-gray-900 text-white font-bold rounded-2xl hover:bg-gray-800 focus:ring-4 focus:ring-gray-900/10 transition-all disabled:opacity-50"
                        >
                            <Save className="w-5 h-5" />
                            {processing ? 'Menyimpan...' : 'Simpan Data Anggota'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
