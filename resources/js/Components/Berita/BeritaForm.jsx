import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { 
    Newspaper, Save, X, Image as ImageIcon, 
    FileText, CheckCircle, Info, Star,
    Calendar, User, ShieldCheck, Tag,
    ChevronRight, ArrowRight, LayoutGrid
} from 'lucide-react';
import ReactQuill from 'react-quill-new';
import 'react-quill-new/dist/quill.snow.css';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';
import { FormCard, FormField } from '@/Components/Shared';

export default function BeritaForm({ berita = null, isEdit = false }) {
    const { data, setData, post, processing, errors } = useForm({
        _method: isEdit ? 'PUT' : 'POST',
        judul: berita?.judul ?? '',
        konten: berita?.konten ?? '',
        excerpt: berita?.excerpt ?? '',
        kategori: berita?.kategori ?? 'berita',
        status: berita?.status ?? 'draft',
        featured: berita ? !!berita.featured : false,
        gambar: null,
    });

    const [preview, setPreview] = useState(null);

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'FILE TERLALU BESAR',
                    text: 'Maksimal ukuran file adalah 5MB.',
                    customClass: { popup: 'rounded-3xl' }
                });
                return;
            }
            setData('gambar', file);
            setPreview(URL.createObjectURL(file));
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const routeName = isEdit ? route('berita.update', berita.slug) : route('berita.store');
        
        post(routeName, {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL!',
                    text: `Konten telah ${isEdit ? 'diperbarui' : 'diterbitkan'}.`,
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-3xl' }
                });
            },
        });
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6 text-left">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 text-left">
                {/* Main Content Area */}
                <div className="lg:col-span-9 space-y-6 text-left">
                    <FormCard 
                        title="Konten Utama" 
                        subtitle="Tulis informasi yang ingin dibagikan" 
                        icon={FileText} 
                        iconColor="text-green-600" 
                        iconBg="bg-green-50"
                    >
                        <div className="space-y-6 text-left">
                            <FormField.Input
                                label="Judul Konten"
                                icon={Newspaper}
                                value={data.judul}
                                onChange={e => setData('judul', e.target.value)}
                                error={errors.judul}
                                placeholder="Ketik judul yang menarik..."
                            />

                            <div className="space-y-2 text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Isi Konten / Berita</label>
                                <div className="relative group text-left rounded-[2rem] overflow-hidden bg-gray-50 p-2 min-h-[600px]">
                                    <ReactQuill 
                                        theme="snow"
                                        value={data.konten}
                                        onChange={(content) => setData('konten', content)}
                                        className="h-[500px] mb-14 border-none bg-white rounded-xl shadow-inner"
                                        modules={{
                                            toolbar: [
                                                [{ 'font': [] }, { 'size': ['small', false, 'large', 'huge'] }],
                                                ['bold', 'italic', 'underline', 'strike'],
                                                [{ 'color': [] }, { 'background': [] }],
                                                [{ 'script': 'sub' }, { 'script': 'super' }],
                                                [{ 'header': 1 }, { 'header': 2 }, 'blockquote', 'code-block'],
                                                [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
                                                [{ 'direction': 'rtl' }, { 'align': [] }],
                                                ['link', 'image', 'video'],
                                                ['clean']
                                            ],
                                        }}
                                        placeholder="Tulis detail informasi desa yang menarik di sini..."
                                    />
                                </div>
                                {errors.konten && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic text-left">{errors.konten}</p>}
                            </div>

                            <FormField.Textarea
                                label="Ringkasan (Excerpt) - Opsional"
                                value={data.excerpt}
                                onChange={e => setData('excerpt', e.target.value)}
                                error={errors.excerpt}
                                rows={2}
                                placeholder="Ringkasan singkat untuk tampilan depan..."
                            />
                        </div>
                    </FormCard>
                </div>

                {/* Sidebar Configuration */}
                <div className="lg:col-span-3 space-y-6 text-left">
                    <FormCard 
                        title="Pengaturan" 
                        icon={LayoutGrid} 
                        iconColor="text-blue-600" 
                        iconBg="bg-blue-50"
                        className="p-8"
                    >
                        <div className="space-y-4 text-left">
                            <FormField.Select
                                label="Kategori"
                                value={data.kategori}
                                onChange={e => setData('kategori', e.target.value)}
                                error={errors.kategori}
                                options={[
                                    { value: 'berita', label: 'Berita Desa' },
                                    { value: 'pengumuman', label: 'Pengumuman Resmi' },
                                    { value: 'agenda', label: 'Agenda Kegiatan' }
                                ]}
                            />

                            <FormField.Select
                                label="Status"
                                value={data.status}
                                onChange={e => setData('status', e.target.value)}
                                error={errors.status}
                                options={[
                                    { value: 'draft', label: 'Simpan Sebagai Draft' },
                                    { value: 'published', label: 'Langsung Terbitkan' }
                                ]}
                            />
                        </div>
                    </FormCard>

                    <FormCard 
                        title="Highlight" 
                        icon={Star} 
                        iconColor="text-orange-600" 
                        iconBg="bg-orange-50"
                        className="p-8"
                    >
                        <button
                            type="button"
                            onClick={() => setData('featured', !data.featured)}
                            className={cn(
                                "w-full flex items-center justify-between p-4 rounded-2xl border-2 transition-all text-left",
                                data.featured ? "bg-orange-50 border-orange-500 text-orange-700 shadow-lg shadow-orange-100 scale-[1.02]" : "bg-white border-gray-50 text-gray-400 hover:border-gray-200"
                            )}
                        >
                            <div className="flex items-center gap-3 text-left">
                                <Star className={cn("w-5 h-5", data.featured && "fill-current")} />
                                <span className="text-[10px] font-black uppercase tracking-widest text-left">Jadikan Unggulan</span>
                            </div>
                            <div className={cn("w-5 h-5 rounded-full border-2 flex items-center justify-center text-left", data.featured ? "border-orange-500 bg-orange-500" : "border-gray-200")}>
                                {data.featured && <CheckCircle className="w-3 h-3 text-white text-left" />}
                            </div>
                        </button>
                    </FormCard>

                    <FormCard 
                        title="Cover Visual" 
                        icon={ImageIcon} 
                        iconColor="text-purple-600" 
                        iconBg="bg-purple-50"
                        className="p-8"
                    >
                        <div className="aspect-video bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden relative group text-left">
                            {preview || (isEdit && berita.gambar) ? (
                                <img src={preview || berita.image_url || `/storage/${berita.gambar}`} className="w-full h-full object-cover text-left" />
                            ) : (
                                <div className="text-center p-6 text-left">
                                    <ImageIcon className="w-8 h-8 text-gray-300 mx-auto mb-2 text-left" />
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-tight text-left">Pilih Cover Konten</p>
                                </div>
                            )}
                            <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-left text-left">
                                <label className="px-4 py-2 bg-white rounded-xl text-[10px] font-black uppercase tracking-widest cursor-pointer hover:bg-gray-100 transition-colors text-left text-left text-left text-left text-left text-left text-left text-left text-left text-left">
                                    UBAH GAMBAR
                                    <input type="file" className="hidden text-left text-left text-left text-left text-left" onChange={handleImageChange} accept="image/*" />
                                </label>
                            </div>
                        </div>
                        {errors.gambar && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest mt-2 italic text-left">{errors.gambar}</p>}
                    </FormCard>

                    <div className="pt-4 text-left">
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full flex items-center justify-center gap-3 px-8 py-6 bg-green-600 text-white rounded-[2rem] font-black uppercase tracking-widest text-[11px] shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 text-left"
                        >
                            {processing ? "MEMPROSES..." : (isEdit ? "PERBARUI KONTEN" : "TERBITKAN KONTEN")}
                            <ArrowRight className="w-4 h-4 ml-2" />
                        </button>
                    </div>
                </div>
            </div>
        </form>
    );
}
