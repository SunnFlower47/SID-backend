import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/Shared';
import { FileText, ArrowLeft, Info } from 'lucide-react';

export default function Panduan({ auth, suratTypes }) {
    return (
        <AuthenticatedLayout user={auth.user} title="Panduan Variabel Surat">
            <Head title="Panduan Variabel Surat" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader 
                    title="Panduan Variabel Surat"
                    subtitle="Daftar kode variabel yang dapat digunakan di file .docx Anda"
                    icon={FileText}
                    backHref={route('admin.surat-type.index')}
                />

                <div className="bg-blue-50 border border-blue-100 rounded-3xl p-6 flex items-start gap-4 shadow-sm">
                    <div className="p-2 bg-blue-100 text-blue-600 rounded-xl">
                        <Info className="w-6 h-6" />
                    </div>
                    <div>
                        <p className="text-sm font-black text-blue-900 uppercase tracking-widest mb-2 italic">
                            Cara Menggunakan Variabel
                        </p>
                        <p className="text-xs text-blue-800 font-medium leading-relaxed">
                            Variabel ini berfungsi untuk mengganti teks secara otomatis saat mencetak surat dari file Word (.docx).<br />
                            Ketikkan kode variabel persis seperti yang tertera (termasuk tanda <code>{`\${}`}</code>) ke dalam template Microsoft Word Anda. 
                            Misalnya, jika Anda ingin menampilkan Nama Warga, ketik <b>{`\${nama}`}</b> di Word, dan sistem akan otomatis menggantinya menjadi nama pemohon ketika surat dicetak.
                        </p>
                    </div>
                </div>

                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-8">
                    {/* Data Dasar */}
                    <section>
                        <h4 className="text-xs font-black text-blue-600 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                            <div className="w-2 h-2 bg-blue-600 rounded-full"></div>
                            Data Penduduk (Otomatis dari Database)
                        </h4>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            {[
                                { code: '${nama}', desc: 'Nama Lengkap Warga' },
                                { code: '${nik}', desc: 'NIK (16 Digit)' },
                                { code: '${nkk}', desc: 'Nomor Kartu Keluarga' },
                                { code: '${tempat_lahir}', desc: 'Tempat Lahir' },
                                { code: '${tanggal_lahir}', desc: 'Tanggal Lahir (Format Indo)' },
                                { code: '${jenis_kelamin}', desc: 'Laki-laki / Perempuan' },
                                { code: '${umur}', desc: 'Umur / Usia (Tahun)' },
                                { code: '${agama}', desc: 'Agama' },
                                { code: '${pekerjaan}', desc: 'Pekerjaan' },
                                { code: '${pendidikan}', desc: 'Pendidikan Terakhir' },
                                { code: '${status_perkawinan}', desc: 'Status Kawin' },
                                { code: '${nama_ayah}', desc: 'Nama Ayah' },
                                { code: '${nama_ibu}', desc: 'Nama Ibu' },
                                { code: '${alamat}', desc: 'Alamat (Tanpa RT/RW)' },
                                { code: '${rt}', desc: 'Nomor RT' },
                                { code: '${rw}', desc: 'Nomor RW' },
                                { code: '${dusun}', desc: 'Nama Dusun' },
                            ].map((item, i) => (
                                <div key={i} className="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <code className="text-blue-700 font-bold bg-blue-100/50 px-2 py-1 rounded-md text-xs">{item.code}</code>
                                    <span className="text-gray-500 text-[11px] font-bold text-right">{item.desc}</span>
                                </div>
                            ))}
                        </div>
                    </section>

                    <div className="h-px bg-gray-100 w-full my-6"></div>

                    {/* Wilayah */}
                    <section>
                        <h4 className="text-xs font-black text-green-600 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                            <div className="w-2 h-2 bg-green-600 rounded-full"></div>
                            Wilayah, Surat & Penandatangan
                        </h4>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            {[
                                { code: '${desa}', desc: 'Nama Desa' },
                                { code: '${kecamatan}', desc: 'Nama Kecamatan' },
                                { code: '${kabupaten}', desc: 'Nama Kabupaten' },
                                { code: '${provinsi}', desc: 'Nama Provinsi' },
                                { code: '${alamat_desa}', desc: 'Alamat Kantor Desa' },
                                { code: '${nomor_surat}', desc: 'Nomor Surat Lengkap' },
                                { code: '${tanggal_surat}', desc: 'Tanggal Cetak (Format Indo)' },
                                { code: '${keperluan}', desc: 'Keperluan Surat' },
                                { code: '${tujuan}', desc: 'Tujuan Surat' },
                                { code: '${ttd_atas}', desc: 'Jabatan Penandatangan' },
                                { code: '${ttd_bawah}', desc: 'Nama Penandatangan (Bold)' },
                            ].map((item, i) => (
                                <div key={i} className="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <code className="text-green-700 font-bold bg-green-100/50 px-2 py-1 rounded-md text-xs">{item.code}</code>
                                    <span className="text-gray-500 text-[11px] font-bold text-right">{item.desc}</span>
                                </div>
                            ))}
                        </div>
                    </section>

                    <div className="h-px bg-gray-100 w-full my-6"></div>

                    {/* Kematian */}
                    <section>
                        <h4 className="text-xs font-black text-red-600 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                            <div className="w-2 h-2 bg-red-600 rounded-full"></div>
                            Khusus Surat Kematian
                        </h4>
                        <div className="bg-red-50 border border-red-100 rounded-2xl p-3 mb-4">
                            <p className="text-[10px] font-bold text-red-700 uppercase tracking-widest leading-relaxed">
                                ℹ️ Untuk identitas warga yang meninggal (seperti Nama, NIK, Tempat/Tanggal Lahir, dll), Anda tetap bisa menggunakan variabel <span className="text-blue-600 bg-blue-100 px-1 rounded">Data Penduduk (Otomatis dari Database)</span> yang ada di atas.
                            </p>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {[
                                { code: '${kematian_hari}', desc: 'Hari Meninggal' },
                                { code: '${kematian_tanggal}', desc: 'Tanggal Meninggal' },
                                { code: '${kematian_jam}', desc: 'Jam Meninggal' },
                                { code: '${kematian_bertempat_di}', desc: 'Tempat Meninggal' },
                                { code: '${alasan}', desc: 'Penyebab Kematian' },
                                { code: '${pemakaman_hari}', desc: 'Hari Pemakaman' },
                                { code: '${pemakaman_tanggal}', desc: 'Tanggal Pemakaman' },
                                { code: '${pemakaman_jam}', desc: 'Jam Pemakaman' },
                                { code: '${pemakaman_lokasi}', desc: 'Tempat Pemakaman' },
                                { code: '${pelapor_nama}', desc: 'Nama Pelapor' },
                                { code: '${pelapor_umur}', desc: 'Umur Pelapor' },
                                { code: '${pelapor_pekerjaan}', desc: 'Pekerjaan Pelapor' },
                                { code: '${pelapor_alamat}', desc: 'Alamat Pelapor' },
                                { code: '${pelapor_hubungan}', desc: 'Hubungan Pelapor' },
                            ].map((item, i) => (
                                <div key={i} className="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <code className="text-red-700 font-bold bg-red-100/50 px-2 py-1 rounded-md text-xs">{item.code}</code>
                                    <span className="text-gray-500 text-[11px] font-bold text-right">{item.desc}</span>
                                </div>
                            ))}
                        </div>
                    </section>

                    <div className="h-px bg-gray-100 w-full my-6"></div>

                    {/* Domisili */}
                    <section>
                        <h4 className="text-xs font-black text-orange-600 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                            <div className="w-2 h-2 bg-orange-600 rounded-full"></div>
                            Khusus Keterangan Domisili
                        </h4>
                        <div className="bg-orange-50 border border-orange-100 rounded-2xl p-3 mb-4">
                            <p className="text-[10px] font-bold text-orange-700 uppercase tracking-widest leading-relaxed">
                                ⚠️ Data domisili diambil dari tabel domisili (BUKAN penduduk tetap). Wajib gunakan prefix <code className="bg-orange-100 px-1 rounded">dm_</code> agar tidak bentrok dengan data penduduk tetap.
                            </p>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {[
                                // Identitas
                                { code: '${dm_nama}', desc: 'Nama Lengkap' },
                                { code: '${dm_nik}', desc: 'NIK (16 Digit)' },
                                { code: '${dm_tempat_lahir}', desc: 'Tempat Lahir' },
                                { code: '${dm_tanggal_lahir}', desc: 'Tanggal Lahir (Format Indo)' },
                                { code: '${dm_jenis_kelamin}', desc: 'Laki-laki / Perempuan' },
                                { code: '${dm_agama}', desc: 'Agama' },
                                { code: '${dm_status_perkawinan}', desc: 'Status Perkawinan' },
                                { code: '${dm_kewarganegaraan}', desc: 'Kewarganegaraan' },
                                { code: '${dm_pekerjaan}', desc: 'Pekerjaan' },
                                // Alamat Asal (1 field teks penuh, tidak ada RT/RW)
                                { code: '${dm_asal_daerah}', desc: 'Kota / Kabupaten Asal' },
                                { code: '${dm_alamat_asal}', desc: 'Alamat Asal sesuai KTP (1 teks penuh)' },
                                // Alamat Domisili di Desa (ada RT/RW/Dusun)
                                { code: '${dm_alamat_tinggal}', desc: 'Alamat Tinggal di Desa (teks)' },
                                { code: '${dm_rt}', desc: 'RT Domisili di Desa' },
                                { code: '${dm_rw}', desc: 'RW Domisili di Desa' },
                                { code: '${dm_dusun}', desc: 'Dusun Domisili di Desa' },
                                // Masa berlaku
                                { code: '${dm_keperluan}', desc: 'Keperluan (kerja/sekolah/dll)' },
                                { code: '${dm_tanggal_masuk}', desc: 'Tanggal Masuk (Format Indo)' },
                                { code: '${dm_tanggal_berlaku}', desc: 'Berlaku Sampai (Format Indo)' },
                                { code: '${dm_perpanjangan_ke}', desc: 'Berapa Kali Diperpanjang' },
                            ].map((item, i) => (
                                <div key={i} className="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <code className="text-orange-700 font-bold bg-orange-100/50 px-2 py-1 rounded-md text-xs">{item.code}</code>
                                    <span className="text-gray-500 text-[11px] font-bold text-right">{item.desc}</span>
                                </div>
                            ))}
                        </div>
                    </section>

                    {/* Custom Variables */}
                    {suratTypes.length > 0 && (
                        <>
                            <div className="h-px bg-gray-100 w-full my-6"></div>
                            <section>
                                <h4 className="text-xs font-black text-purple-600 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                    <div className="w-2 h-2 bg-purple-600 rounded-full"></div>
                                    Variabel Custom (Berdasarkan Master Surat)
                                </h4>
                                <div className="space-y-6">
                                    {suratTypes.map((type) => {
                                        if (!type.form_json || type.form_json.length === 0) return null;
                                        return (
                                            <div key={type.id} className="bg-purple-50/30 p-5 rounded-3xl border border-purple-100">
                                                <h5 className="text-[11px] font-black text-purple-800 uppercase tracking-widest mb-3 italic">
                                                    {type.nama} ({type.id})
                                                </h5>
                                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                                    {type.form_json.map((field, index) => (
                                                        <div key={index} className="flex items-center justify-between p-3 bg-white rounded-xl shadow-sm border border-purple-50">
                                                            <code className="text-purple-700 font-bold bg-purple-50 px-2 py-1 rounded-md text-xs">
                                                                {`\${${field.name}}`}
                                                            </code>
                                                            <span className="text-gray-500 text-[10px] font-bold text-right truncate ml-2" title={field.label}>
                                                                {field.label}
                                                            </span>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </section>
                        </>
                    )}

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
