import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, FormCard, FormField, TableCard, DataTable, Badge } from '@/Components/Shared';
import { Megaphone, Send, Info, AlertTriangle, AlertCircle, Calendar, Clock, Check, X, ChevronDown, Search } from 'lucide-react';
import { cn } from '@/lib/utils';

export default function Index({ announcements, tenants = [] }) {
    const { flash } = usePage().props;
    
    const { data, setData, post, processing, errors, reset } = useForm({
        title: '',
        message: '',
        type: 'info',
        expires_at: '',
        sender_name: 'Diskominfo',
        target_type: 'all',
        target_tenant_ids: [],
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('announcements.store'), {
            onSuccess: () => reset()
        });
    };

    const columns = [
        {
            header: 'Info Pengumuman',
            accessor: 'title',
            className: 'text-left min-w-[200px]',
            render: (row) => (
                <div className="space-y-1">
                    <div className="font-black text-slate-800 text-sm leading-snug">{row.title}</div>
                    <div className="text-[10px] text-indigo-600 font-extrabold uppercase tracking-wider">
                        Pengirim: {row.sender_name || 'Diskominfo'} • Target: {row.target_type === 'all' ? 'Semua Desa' : `Spesifik (${row.target_tenant_ids?.join(', ') || ''})`}
                    </div>
                    <div className="text-slate-600 text-xs line-clamp-2 leading-relaxed whitespace-pre-line">{row.message}</div>
                </div>
            )
        },
        {
            header: 'Tipe',
            className: 'text-center',
            render: (row) => {
                const colors = {
                    info: 'blue',
                    warning: 'yellow',
                    danger: 'red'
                };
                return (
                    <div className="flex justify-center">
                        <Badge 
                            color={colors[row.type] || 'blue'}
                            dot={colors[row.type] || 'blue'}
                        >
                            {row.type.toUpperCase()}
                        </Badge>
                    </div>
                );
            }
        },
        {
            header: 'Masa Berlaku',
            className: 'text-center text-xs font-bold text-slate-500',
            render: (row) => (
                <div className="flex flex-col items-center gap-0.5">
                    <span className="flex items-center gap-1">
                        <Calendar className="w-3 h-3 text-slate-400" />
                        {row.expires_at ? new Date(row.expires_at).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }) : 'Selamanya'}
                    </span>
                    {row.expires_at && new Date(row.expires_at) < new Date() && (
                        <span className="text-[10px] text-red-500 font-extrabold uppercase tracking-wider">Expired</span>
                    )}
                </div>
            )
        },
        {
            header: 'Disiarkan Oleh',
            className: 'text-center text-xs font-bold text-slate-700',
            render: (row) => row.creator?.name || 'Sistem'
        }
    ];

    return (
        <LandlordLayout>
            <Head title="Siaran Pengumuman" />

            <div className="space-y-8">
                <PageHeader 
                    icon={Megaphone}
                    title="Siaran Pengumuman"
                    subtitle="Kirim dan kelola pengumuman global atau spesifik ke dashboard desa di sistem SaaS."
                    gradient="from-indigo-600 via-indigo-700 to-indigo-800"
                />

                {flash?.success && (
                    <div className="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-bold shadow-sm" role="alert">
                        <span className="block sm:inline">{flash.success}</span>
                    </div>
                )}

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    {/* Form Kiri */}
                    <div className="lg:col-span-5">
                        <form onSubmit={submit}>
                            <FormCard 
                                icon={Megaphone}
                                title="Buat Siaran Baru"
                            >
                                <div className="space-y-4">
                                    <FormField.Input 
                                        label="Judul Siaran"
                                        placeholder="Tulis judul yang menarik & padat..."
                                        value={data.title}
                                        onChange={e => setData('title', e.target.value)}
                                        error={errors.title}
                                        required
                                    />

                                    <FormField.Input 
                                        label="Nama Pengirim / Instansi"
                                        placeholder="Contoh: Diskominfo, Kementerian, Kecamatan..."
                                        value={data.sender_name}
                                        onChange={e => setData('sender_name', e.target.value)}
                                        error={errors.sender_name}
                                        required
                                    />

                                    <FormField.Select
                                        label="Tingkat Urgensi (Type)"
                                        value={data.type}
                                        onChange={e => setData('type', e.target.value)}
                                        error={errors.type}
                                        required
                                        options={[
                                            { value: 'info', label: 'INFO (Biru - Pengumuman Biasa)' },
                                            { value: 'warning', label: 'WARNING (Kuning - Perhatian/Penting)' },
                                            { value: 'danger', label: 'DANGER (Merah - Darurat/Kritikal)' },
                                        ]}
                                    />

                                    <FormField.Input 
                                        label="Masa Berlaku (Expires At)"
                                        type="date"
                                        value={data.expires_at}
                                        onChange={e => setData('expires_at', e.target.value)}
                                        error={errors.expires_at}
                                        placeholder="Pilih tanggal kadaluarsa (kosongkan jika selamanya)"
                                    />

                                    <FormField.Select
                                        label="Target Penerima Desa"
                                        value={data.target_type}
                                        onChange={e => {
                                            const val = e.target.value;
                                            setData(prev => ({
                                                ...prev,
                                                target_type: val,
                                                target_tenant_ids: val === 'all' ? [] : prev.target_tenant_ids
                                            }));
                                        }}
                                        error={errors.target_type}
                                        required
                                        options={[
                                            { value: 'all', label: 'Semua Desa (Broadcast Global)' },
                                            { value: 'specific', label: 'Desa Spesifik (Targeted)' },
                                        ]}
                                    />

                                    {data.target_type === 'specific' && (
                                        <SearchableMultiSelect 
                                            label="Pilih Desa Target"
                                            options={tenants}
                                            selectedValues={data.target_tenant_ids}
                                            onChange={val => setData('target_tenant_ids', val)}
                                            error={errors.target_tenant_ids}
                                        />
                                    )}

                                    <FormField.Textarea 
                                        label="Isi Pengumuman / Pesan"
                                        placeholder="Masukkan isi pesan pengumuman secara rinci..."
                                        value={data.message}
                                        onChange={e => setData('message', e.target.value)}
                                        error={errors.message}
                                        rows={5}
                                        required
                                    />

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-300 text-white rounded-2xl text-sm font-bold shadow-lg shadow-indigo-600/10 hover:shadow-indigo-700/20 active:scale-[0.98] transition-all cursor-pointer"
                                    >
                                        <Send className="w-4 h-4" />
                                        {processing ? 'Menyiarkan...' : 'Siarkan Pengumuman'}
                                    </button>
                                </div>
                            </FormCard>
                        </form>
                    </div>

                    {/* List Kanan */}
                    <div className="lg:col-span-7">
                        <TableCard
                            title="Riwayat Pengumuman Global"
                            icon={Clock}
                            total={announcements.total}
                            totalLabel="Pengumuman"
                            pagination={announcements}
                            noPadding
                        >
                            <DataTable 
                                columns={columns}
                                data={announcements.data}
                                borderedBody={true}
                            />
                        </TableCard>
                    </div>
                </div>
            </div>
        </LandlordLayout>
    );
}

function SearchableMultiSelect({ label, options, selectedValues, onChange, error, placeholder = "Pilih desa target..." }) {
    const [isOpen, setIsOpen] = React.useState(false);
    const [search, setSearch] = React.useState('');
    const containerRef = React.useRef(null);

    React.useEffect(() => {
        function handleClickOutside(event) {
            if (containerRef.current && !containerRef.current.contains(event.target)) {
                setIsOpen(false);
                setSearch('');
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const filteredOptions = options.filter(opt => 
        opt.name.toLowerCase().includes(search.toLowerCase()) || 
        opt.id.toLowerCase().includes(search.toLowerCase())
    );

    const toggleOption = (val) => {
        if (selectedValues.includes(val)) {
            onChange(selectedValues.filter(v => v !== val));
        } else {
            onChange([...selectedValues, val]);
        }
    };

    return (
        <div className="space-y-1.5 relative text-left" ref={containerRef}>
            {label && (
                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                    {label} <span className="text-red-500">*</span>
                </label>
            )}
            
            {/* Display Box */}
            <div 
                onClick={() => setIsOpen(!isOpen)}
                className={cn(
                    "w-full px-4 py-3 bg-gray-50 border rounded-2xl text-sm font-bold flex items-center justify-between cursor-pointer transition-all",
                    isOpen ? "border-indigo-500 ring-4 ring-indigo-500/10 bg-white" : "border-gray-100 hover:border-gray-200",
                    error ? "border-red-400" : ""
                )}
            >
                <div className="flex flex-wrap gap-1.5 flex-1 min-w-0 mr-2">
                    {selectedValues.length > 0 ? (
                        selectedValues.map(val => {
                            const option = options.find(o => o.id === val);
                            return (
                                <span 
                                    key={val} 
                                    className="bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-xl flex items-center gap-1 group/tag"
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        toggleOption(val);
                                    }}
                                >
                                    {option ? option.name : val}
                                    <X className="w-3 h-3 text-indigo-400 hover:text-indigo-600 transition-colors shrink-0" />
                                </span>
                            );
                        })
                    ) : (
                        <span className="text-gray-400 font-bold">{placeholder}</span>
                    )}
                </div>
                <ChevronDown className={cn("w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0", isOpen && "transform rotate-180")} />
            </div>

            {/* Dropdown Panel */}
            {isOpen && (
                <div className="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 p-3 space-y-2.5 max-h-64 overflow-hidden flex flex-col">
                    {/* Search Field */}
                    <div className="relative shrink-0">
                        <Search className="w-3.5 h-3.5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
                        <input 
                            type="text"
                            placeholder="Cari nama atau ID desa..."
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            onClick={e => e.stopPropagation()}
                            className="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold outline-none focus:border-indigo-500 focus:bg-white transition-all"
                        />
                    </div>

                    {/* Options List */}
                    <div className="overflow-y-auto flex-1 divide-y divide-gray-50 custom-scrollbar pr-1">
                        {filteredOptions.length > 0 ? (
                            filteredOptions.map(opt => {
                                const isChecked = selectedValues.includes(opt.id);
                                return (
                                    <div 
                                        key={opt.id}
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            toggleOption(opt.id);
                                        }}
                                        className={cn(
                                            "flex items-center justify-between p-2.5 hover:bg-slate-50 rounded-xl cursor-pointer text-xs font-bold transition-all mt-0.5 first:mt-0",
                                            isChecked ? "text-indigo-600 bg-indigo-50/30 hover:bg-indigo-50/50" : "text-slate-700"
                                        )}
                                    >
                                        <span>{opt.name} ({opt.id})</span>
                                        {isChecked && (
                                            <Check className="w-3.5 h-3.5 text-indigo-600 shrink-0" />
                                        )}
                                    </div>
                                );
                            })
                        ) : (
                            <p className="text-[10px] text-gray-400 font-bold uppercase p-3 text-center">Desa tidak ditemukan</p>
                        )}
                    </div>
                </div>
            )}

            {error && (
                <p className="text-[10px] font-bold text-red-600 uppercase tracking-tight ml-1">
                    {error}
                </p>
            )}
        </div>
    );
}
