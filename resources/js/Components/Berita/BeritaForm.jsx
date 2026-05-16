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
                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 text-left">
                        <div className="flex items-center gap-4 mb-8 text-left">
                            <div className="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-left">
                                <FileText className="w-6 h-6 text-green-600" />
                            </div>
                            <div className="text-left">
                                <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter text-left">Konten Utama</h3>
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none text-left">Tulis informasi yang ingin dibagikan</p>
                            </div>
                        </div>

                        <div className="space-y-6 text-left">
                            <div className="space-y-2 text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Judul Konten</label>
                                <div className="relative group text-left">
                                    <Newspaper className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                                    <input
                                        type="text"
                                        value={data.judul}
                                        onChange={e => setData('judul', e.target.value)}
                                        className="w-full pl-12 pr-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all text-left"
                                        placeholder="Ketik judul yang menarik..."
                                    />
                                </div>
                                {errors.judul && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.judul}</p>}
                            </div>

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

                            <div className="space-y-2 text-left">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left text-left text-left">Ringkasan (Excerpt) - Opsional</label>
                                <div className="relative group text-left">
                                    <textarea
                                        value={data.excerpt}
                                        onChange={e => setData('excerpt', e.target.value)}
                                        rows="2"
                                        className="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-xs font-bold text-gray-500 focus:ring-4 focus:ring-green-500/10 transition-all resize-none text-left text-left text-left"
                                        placeholder="Ringkasan singkat untuk tampilan depan..."
                                    ></textarea>
                                </div>
                                {errors.excerpt && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{errors.excerpt}</p>}
                            </div>
                        </div>
                    </div>
                </div>

                {/* Sidebar Configuration */}
                <div className="lg:col-span-3 space-y-6 text-left">
                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 text-left">
                        <div className="flex items-center gap-4 mb-6 text-left">
                            <div className="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-left">
                                <LayoutGrid className="w-5 h-5 text-blue-600" />
                            </div>
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter text-left">Pengaturan</h3>
                        </div>

                        <div className="space-y-4 text-left">
                            <div className="space-y-1.5 text-left text-left">
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Kategori</label>
                                <select 
                                    value={data.kategori} 
                                    onChange={e => setData('kategori', e.target.value)} 
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 focus:ring-4 focus:ring-blue-500/10 transition-all appearance-none text-left"
                                >
                                    <option value="berita">Berita Desa</option>
                                    <option value="pengumuman">Pengumuman Resmi</option>
                                    <option value="agenda">Agenda Kegiatan</option>
                                </select>
                            </div>

                            <div className="space-y-1.5 text-left text-left">
                                <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Status</label>
                                <select 
                                    value={data.status} 
                                    onChange={e => setData('status', e.target.value)} 
                                    className="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all appearance-none text-left"
                                >
                                    <option value="draft">Simpan Sebagai Draft</option>
                                    <option value="published">Langsung Terbitkan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 text-left">
                        <div className="flex items-center gap-4 mb-6 text-left">
                            <div className="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-left">
                                <Star className="w-5 h-5 text-orange-600" />
                            </div>
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter text-left">Highlight</h3>
                        </div>

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
                    </div>

                    <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 text-left">
                        <div className="flex items-center gap-4 mb-6 text-left">
                            <div className="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-left">
                                <ImageIcon className="w-5 h-5 text-purple-600" />
                            </div>
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter text-left">Cover Visual</h3>
                        </div>

                        <div className="aspect-video bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden relative group text-left">
                            {preview || (isEdit && berita.gambar) ? (
                                <img src={preview || `/storage/${berita.gambar}`} className="w-full h-full object-cover text-left" />
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
                    </div>

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
