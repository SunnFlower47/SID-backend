import React, { useState } from 'react';
import { Head, useForm, usePage, Link, router } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, FormCard, FormField, TableCard, DataTable, Badge, Modal } from '@/Components/Shared';
import { Settings, Save, User, HardDrive, Phone, Lock, Globe, ShieldCheck, Plus, Edit2, Trash2, Database, History, RotateCcw, Download, Play, Files, PieChart, Archive, FileArchive, Clock, Weight, Inbox } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';

export default function Index({ settings, user, roles, backupFiles = [], diskSpace = {}, stats = {} }) {
    const { flash } = usePage().props;
    const [activeTab, setActiveTab] = useState('system');

    // System/Profile Settings Form
    const { data, setData, put, processing, errors, reset } = useForm({
        // System Settings
        default_max_users: settings.default_max_users || '10',
        default_storage_limit_mb: settings.default_storage_limit_mb || '1024',
        diskominfo_hotline: settings.diskominfo_hotline || '',
        diskominfo_email: settings.diskominfo_email || '',
        central_base_domain: settings.central_base_domain || 'sistem-desa-cibatu.test',
        central_admin_domain: settings.central_admin_domain || 'admin.sistem-desa-cibatu.test',

        // Profile Settings
        name: user.name || '',
        email: user.email || '',
        password: '',
        password_confirmation: '',
    });

    // Roles CRUD Form
    const roleForm = useForm({
        name: '',
        display_name: '',
        permissions: [],
    });

    const [roleModalOpen, setRoleModalOpen] = useState(false);
    const [editRoleMode, setEditRoleMode] = useState(false);
    const [selectedRole, setSelectedRole] = useState(null);

    const openAddRoleModal = () => {
        roleForm.reset();
        roleForm.clearErrors();
        setEditRoleMode(false);
        setSelectedRole(null);
        setRoleModalOpen(true);
    };

    const openEditRoleModal = (role) => {
        roleForm.clearErrors();
        setEditRoleMode(true);
        setSelectedRole(role);
        roleForm.setData({
            name: role.name,
            display_name: role.display_name,
            permissions: role.permissions || [],
        });
        setRoleModalOpen(true);
    };

    const togglePermission = (perm) => {
        const current = [...roleForm.data.permissions];
        const index = current.indexOf(perm);
        if (index > -1) {
            current.splice(index, 1);
        } else {
            current.push(perm);
        }
        roleForm.setData('permissions', current);
    };

    const submitRole = (e) => {
        e.preventDefault();
        if (editRoleMode) {
            roleForm.put(route('landlord.settings.roles.update', selectedRole.id), {
                onSuccess: () => {
                    setRoleModalOpen(false);
                    roleForm.reset();
                }
            });
        } else {
            roleForm.post(route('landlord.settings.roles.store'), {
                onSuccess: () => {
                    setRoleModalOpen(false);
                    roleForm.reset();
                }
            });
        }
    };

    const deleteRole = (role) => {
        if (role.name === 'superadmin') {
            Swal.fire({
                title: 'Galat!',
                text: 'Role Super Admin tidak dapat dihapus.',
                icon: 'error',
                confirmButtonColor: '#4f46e5',
            });
            return;
        }

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Anda akan menghapus role "${role.display_name}". Tindakan ini tidak dapat dibatalkan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                roleForm.delete(route('landlord.settings.roles.destroy', role.id), {
                    onSuccess: () => {
                        Swal.fire('Terhapus!', 'Role berhasil dihapus.', 'success');
                    },
                    onError: (err) => {
                        Swal.fire('Gagal!', err.error || 'Terjadi kesalahan saat menghapus role.', 'error');
                    }
                });
            }
        });
    };

    const handleCreateBackup = (type) => {
        Swal.fire({
            title: 'Mulai Backup SaaS?',
            text: type === 'full' 
                ? 'Mencadangkan database central + seluruh desa + seluruh file sistem. Proses ini memerlukan waktu beberapa saat.'
                : type === 'files'
                ? 'Hanya mencadangkan berkas sistem & dokumen upload (tanpa database).'
                : 'Mencadangkan database central dan seluruh database desa (tanpa file sistem).',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Mulai Backup',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    router.post(route('landlord.settings.backup.create'), { type }, {
                        onFinish: () => resolve(),
                        preserveScroll: true
                    });
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    };

    const handleDeleteBackup = (filename) => {
        Swal.fire({
            title: 'Hapus Berkas Backup?',
            text: `Berkas backup "${filename}" akan dihapus permanen dari server.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('landlord.settings.backup.destroy', filename), {
                    preserveScroll: true
                });
            }
        });
    };

    const formatBytes = (bytes, decimals = 2) => {
        if (!+bytes) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
    };

    const diskPercentage = diskSpace?.percentage || 0;
    const diskUsed = diskSpace?.used || 0;
    const diskFree = diskSpace?.free || 0;
    const totalFilesCount = stats?.total_files || 0;
    const totalFilesSize = stats?.total_size || 0;

    const diskSpaceColor = diskPercentage > 90 ? 'bg-red-500' 
                         : diskPercentage > 75 ? 'bg-amber-500' 
                         : 'bg-emerald-500';

    const submit = (e) => {
        e.preventDefault();
        put(route('landlord.settings.update'), {
            onSuccess: () => {
                reset('password', 'password_confirmation');
            }
        });
    };

    const handleDisable2FA = () => {
        Swal.fire({
            title: 'Nonaktifkan 2FA?',
            text: 'Masukkan password Anda untuk mengkonfirmasi penonaktifan verifikasi dua langkah:',
            input: 'password',
            inputPlaceholder: 'Password login Anda...',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Nonaktifkan 2FA',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ef4444',
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Password wajib diisi!');
                    return false;
                }
                return new Promise((resolve) => {
                    router.post(route('landlord.2fa.disable'), { password }, {
                        onSuccess: () => {
                            Swal.fire('Nonaktif', 'Verifikasi Dua Langkah berhasil dinonaktifkan.', 'success');
                            resolve();
                        },
                        onError: (err) => {
                            Swal.showValidationMessage(err.password || 'Gagal menonaktifkan 2FA.');
                            resolve();
                        }
                    });
                });
            }
        });
    };

    const tabs = [
        { id: 'system', label: 'Sistem & Alokasi', icon: HardDrive },
        { id: 'profile', label: 'Profil & Keamanan', icon: User },
        { id: 'roles', label: 'Manajemen Role', icon: ShieldCheck },
        { id: 'backup', label: 'Sistem Backup', icon: Database },
    ];

    const roleColumns = [
        {
            header: 'Nama Role',
            accessor: 'display_name',
            className: 'text-left min-w-[200px]',
            render: (row) => (
                <div className="flex flex-col">
                    <span className="font-black text-slate-800 text-sm">{row.display_name}</span>
                    <span className="text-gray-400 text-xs font-mono font-bold">{row.name}</span>
                </div>
            )
        },
        {
            header: 'Hak Akses / Izin',
            accessor: 'permissions',
            className: 'text-left',
            render: (row) => {
                const permLabels = {
                    'manage-central-users': 'Kelola User',
                    'manage-allocations': 'Kelola Alokasi',
                    'manage-tenants': 'Kelola Tenant',
                    'broadcast-announcements': 'Siaran Pengumuman',
                };
                
                // Jika superadmin, tampilkan badge khusus "Akses Penuh"
                if (row.name === 'superadmin') {
                    return (
                        <div className="flex flex-wrap gap-1">
                            <Badge color="red" dot="red">Semua Akses (Super)</Badge>
                        </div>
                    );
                }

                const perms = row.permissions || [];
                if (perms.length === 0) {
                    return <span className="text-gray-400 text-xs font-bold italic">Tidak ada akses (Read-only)</span>;
                }

                return (
                    <div className="flex flex-wrap gap-1.5">
                        {perms.map(p => (
                            <Badge key={p} color="indigo">{permLabels[p] || p}</Badge>
                        ))}
                    </div>
                );
            }
        },
        {
            header: 'Aksi',
            className: 'text-center w-[120px]',
            render: (row) => (
                <div className="flex items-center justify-center gap-2">
                    <button
                        type="button"
                        onClick={() => openEditRoleModal(row)}
                        className="inline-flex items-center justify-center p-2 bg-slate-50 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 rounded-xl transition-all border border-slate-100 cursor-pointer"
                        title="Edit Role"
                    >
                        <Edit2 className="w-4 h-4" />
                    </button>
                    {row.name !== 'superadmin' && (
                        <button
                            type="button"
                            onClick={() => deleteRole(row)}
                            className="inline-flex items-center justify-center p-2 bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-600 rounded-xl transition-all border border-slate-100 cursor-pointer"
                            title="Hapus Role"
                        >
                            <Trash2 className="w-4 h-4" />
                        </button>
                    )}
                </div>
            )
        }
    ];

    return (
        <LandlordLayout>
            <Head title="Pengaturan Sistem" />

            <div className="space-y-8 text-left">
                <PageHeader 
                    icon={Settings}
                    title="Pengaturan Sistem"
                    subtitle="Kelola konfigurasi parameter default SaaS dan detail akun personal Super Admin."
                    gradient="from-indigo-600 via-indigo-700 to-indigo-800"
                />

                {flash?.success && (
                    <div className="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-bold shadow-sm animate-in fade-in duration-300" role="alert">
                        <span className="block sm:inline">{flash.success}</span>
                    </div>
                )}

                {roleForm.errors.error && (
                    <div className="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl text-sm font-bold shadow-sm" role="alert">
                        <span>{roleForm.errors.error}</span>
                    </div>
                )}

                {/* Tab Selector */}
                <div className="flex border-b border-gray-200 gap-6">
                    {tabs.map((tab) => {
                        const Icon = tab.icon;
                        const isActive = activeTab === tab.id;
                        return (
                            <button
                                key={tab.id}
                                type="button"
                                onClick={() => setActiveTab(tab.id)}
                                className={cn(
                                    "pb-4 px-2 text-sm font-black uppercase tracking-wider flex items-center gap-2 border-b-2 transition-all cursor-pointer",
                                    isActive 
                                        ? "border-indigo-600 text-indigo-600 scale-[1.02]" 
                                        : "border-transparent text-gray-400 hover:text-gray-600"
                                )}
                            >
                                <Icon className="w-4.5 h-4.5" />
                                {tab.label}
                            </button>
                        );
                    })}
                </div>

                <form onSubmit={submit}>
                    <div className="space-y-6">
                        {/* Tab 1: System Settings */}
                        {activeTab === 'system' && (
                            <div className="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-300">
                                <FormCard 
                                    icon={HardDrive}
                                    title="Alokasi Default Desa Baru"
                                    subtitle="Parameter alokasi kuota resource awal saat desa baru pertama kali didaftarkan."
                                >
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <FormField.Input 
                                            label="Batas Maksimal Pengguna (User Limit)"
                                            type="number"
                                            placeholder="Contoh: 10"
                                            value={data.default_max_users}
                                            onChange={e => setData('default_max_users', e.target.value)}
                                            error={errors.default_max_users}
                                            required
                                        />
                                        <FormField.Input 
                                            label="Batas Kapasitas Storage (MB)"
                                            type="number"
                                            placeholder="Contoh: 1024"
                                            value={data.default_storage_limit_mb}
                                            onChange={e => setData('default_storage_limit_mb', e.target.value)}
                                            error={errors.default_storage_limit_mb}
                                            required
                                        />
                                    </div>
                                </FormCard>

                                <FormCard 
                                    icon={Phone}
                                    title="Detail Kontak Bantuan Pusat"
                                    subtitle="Hotline WhatsApp dan Email bantuan yang ditampilkan di dashboard desa."
                                >
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <FormField.Input 
                                            label="Hotline WhatsApp Admin Pusat"
                                            type="text"
                                            placeholder="Contoh: 081234567890"
                                            value={data.diskominfo_hotline}
                                            onChange={e => setData('diskominfo_hotline', e.target.value)}
                                            error={errors.diskominfo_hotline}
                                            required
                                        />
                                        <FormField.Input 
                                            label="Email Bantuan Admin Pusat"
                                            type="email"
                                            placeholder="Contoh: admin@central.go.id"
                                            value={data.diskominfo_email}
                                            onChange={e => setData('diskominfo_email', e.target.value)}
                                            error={errors.diskominfo_email}
                                            required
                                        />
                                    </div>
                                </FormCard>
                                <FormCard 
                                    icon={Globe}
                                    title="Konfigurasi Domain Sistem"
                                    subtitle="Parameter domain sistem SaaS untuk Website Warga dan Admin Panel Desa."
                                >
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <FormField.Input 
                                            label="Domain Utama Web Desa (Base Domain)"
                                            type="text"
                                            placeholder="Contoh: sistem-desa-cibatu.test"
                                            value={data.central_base_domain}
                                            onChange={e => setData('central_base_domain', e.target.value)}
                                            error={errors.central_base_domain}
                                            required
                                        />
                                        <FormField.Input 
                                            label="Domain Utama Admin Panel (Admin Domain)"
                                            type="text"
                                            placeholder="Contoh: admin.sistem-desa-cibatu.test"
                                            value={data.central_admin_domain}
                                            onChange={e => setData('central_admin_domain', e.target.value)}
                                            error={errors.central_admin_domain}
                                            required
                                        />
                                    </div>
                                </FormCard>
                            </div>
                        )}

                        {/* Tab 2: Profile Settings */}
                        {activeTab === 'profile' && (
                            <div className="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-300">
                                <FormCard 
                                    icon={User}
                                    title="Informasi Profil Admin"
                                    subtitle="Ubah informasi nama dan alamat email login Anda."
                                >
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <FormField.Input 
                                            label="Nama Lengkap"
                                            type="text"
                                            placeholder="Nama Anda..."
                                            value={data.name}
                                            onChange={e => setData('name', e.target.value)}
                                            error={errors.name}
                                            required
                                        />
                                        <FormField.Input 
                                            label="Alamat Email"
                                            type="email"
                                            placeholder="Email login..."
                                            value={data.email}
                                            onChange={e => setData('email', e.target.value)}
                                            error={errors.email}
                                            required
                                        />
                                    </div>
                                </FormCard>

                                <FormCard 
                                    icon={Lock}
                                    title="Ubah Password Keamanan"
                                    subtitle="Kosongkan jika Anda tidak ingin mengubah password masuk."
                                >
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <FormField.Input 
                                            label="Password Baru"
                                            type="password"
                                            placeholder="Masukkan password baru..."
                                            value={data.password}
                                            onChange={e => setData('password', e.target.value)}
                                            error={errors.password}
                                        />
                                        <FormField.Input 
                                            label="Konfirmasi Password Baru"
                                            type="password"
                                            placeholder="Ulangi password baru..."
                                            value={data.password_confirmation}
                                            onChange={e => setData('password_confirmation', e.target.value)}
                                            error={errors.password_confirmation}
                                        />
                                    </div>
                                </FormCard>

                                <FormCard 
                                    icon={ShieldCheck}
                                    title="Verifikasi Dua Langkah (2FA TOTP)"
                                    subtitle="Keamanan tambahan menggunakan aplikasi autentikator (Google Authenticator, Authy)."
                                >
                                    <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                        <div>
                                            <div className="flex items-center gap-2">
                                                <span className="font-bold text-slate-800 text-sm">Status 2FA:</span>
                                                {user.two_factor_enabled ? (
                                                    <Badge color="emerald" dot="emerald">Aktif (Terverifikasi)</Badge>
                                                ) : (
                                                    <Badge color="red" dot="red">Belum Aktif</Badge>
                                                )}
                                            </div>
                                            <p className="text-xs text-slate-500 mt-1">
                                                {user.two_factor_enabled 
                                                    ? 'Akun Anda dilindungi dengan TOTP. Kode OTP dibutuhkan setiap kali Anda login.'
                                                    : 'Anda wajib mengaktifkan 2FA untuk menjaga keamanan akses ke Admin Panel Central.'}
                                            </p>
                                        </div>

                                        <div>
                                            {user.two_factor_enabled ? (
                                                <button
                                                    type="button"
                                                    onClick={handleDisable2FA}
                                                    className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-sm transition"
                                                >
                                                    Nonaktifkan 2FA
                                                </button>
                                            ) : (
                                                <Link
                                                    href={route('landlord.2fa.setup')}
                                                    className="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition"
                                                >
                                                    Setup 2FA Sekarang
                                                </Link>
                                            )}
                                        </div>
                                    </div>

                                    {flash?.recoveryCodes && (
                                        <div className="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                                            <div className="font-bold text-amber-900 text-xs mb-1 flex items-center gap-1">
                                                <span>⚠️ Simpan Kode Pemulihan Darurat Anda!</span>
                                            </div>
                                            <p className="text-xs text-amber-700 mb-3">
                                                Jika Anda kehilangan ponsel atau tidak dapat mengakses aplikasi autentikator, gunakan kode berikut untuk masuk. Setiap kode hanya dapat digunakan <b>satu kali</b>:
                                            </p>
                                            <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 font-mono text-xs font-bold bg-white p-3 rounded border border-amber-200 select-all text-center">
                                                {flash.recoveryCodes.map((c, idx) => (
                                                    <div key={idx} className="p-1 bg-slate-50 rounded border border-slate-100 text-indigo-600">
                                                        {c}
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </FormCard>
                            </div>
                        )}

                        {/* Tab 3: Role Management */}
                        {activeTab === 'roles' && (
                            <div className="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-300">
                                <TableCard
                                    title="Daftar Hak Akses (Role) Central"
                                    icon={ShieldCheck}
                                    total={roles.length}
                                    totalLabel="Role"
                                    actions={[
                                        {
                                            label: 'Tambah Role Baru',
                                            icon: Plus,
                                            onClick: openAddRoleModal,
                                            variant: 'white'
                                        }
                                    ]}
                                    noPadding
                                >
                                    <DataTable 
                                        columns={roleColumns}
                                        data={roles}
                                        borderedBody={true}
                                    />
                                </TableCard>
                            </div>
                        )}

                        {/* Tab 4: Backup System */}
                        {activeTab === 'backup' && (
                            <div className="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-300">
                                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    {/* Storage & Stats */}
                                    <div className="lg:col-span-1 space-y-6">
                                        {/* Disk Capacity */}
                                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                                            <h3 className="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center mb-4">
                                                <HardDrive className="w-4 h-4 mr-2 text-indigo-600" />
                                                Disk Server SaaS
                                            </h3>
                                            <div className="mb-2 flex justify-between items-end">
                                                <span className="text-3xl font-black text-gray-900 leading-none">
                                                    {diskPercentage}%
                                                </span>
                                                <span className="text-xs font-bold text-gray-500 uppercase">Terpakai</span>
                                            </div>
                                            <div className="w-full bg-gray-100 rounded-full h-3 mb-4 overflow-hidden">
                                                <div className={`h-3 rounded-full ${diskSpaceColor} transition-all duration-1000`} style={{ width: `${diskPercentage}%` }}></div>
                                            </div>
                                            <div className="grid grid-cols-2 gap-4 text-xs">
                                                <div>
                                                    <p className="text-gray-500 font-medium uppercase tracking-wider mb-0.5">Digunakan</p>
                                                    <p className="font-bold text-gray-900">{formatBytes(diskUsed)}</p>
                                                </div>
                                                <div>
                                                    <p className="text-gray-500 font-medium uppercase tracking-wider mb-0.5">Sisa Ruang</p>
                                                    <p className="font-bold text-gray-900">{formatBytes(diskFree)}</p>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Quick Stats */}
                                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                                            <h3 className="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center">
                                                <PieChart className="w-4 h-4 mr-2 text-indigo-600" />
                                                Statistik Backup SaaS
                                            </h3>
                                            <div className="space-y-4">
                                                <div className="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                                                    <div className="flex items-center">
                                                        <div className="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center mr-3">
                                                            <Files className="w-4 h-4 text-indigo-600" />
                                                        </div>
                                                        <span className="text-xs font-bold text-gray-600 uppercase">Total Berkas</span>
                                                    </div>
                                                    <span className="font-black text-gray-900">{totalFilesCount}</span>
                                                </div>
                                                <div className="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                                                    <div className="flex items-center">
                                                        <div className="w-8 h-8 rounded-xl bg-purple-100 flex items-center justify-center mr-3">
                                                            <Weight className="w-4 h-4 text-purple-600" />
                                                        </div>
                                                        <span className="text-xs font-bold text-gray-600 uppercase">Total Ukuran</span>
                                                    </div>
                                                    <span className="font-black text-gray-900">{formatBytes(totalFilesSize)}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Action & Table List */}
                                    <div className="lg:col-span-2 space-y-6">
                                        {/* Actions */}
                                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <button
                                                type="button"
                                                onClick={() => handleCreateBackup('database')}
                                                className="group relative overflow-hidden bg-white rounded-3xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-all text-left"
                                            >
                                                <div className="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-3">
                                                    <Database className="w-5 h-5" />
                                                </div>
                                                <h4 className="font-black text-gray-950 uppercase italic tracking-tight text-xs mb-1">Backup Database</h4>
                                                <p className="text-[10px] text-gray-500 leading-relaxed">Backup database pusat & seluruh database desa-desa.</p>
                                            </button>

                                            <button
                                                type="button"
                                                onClick={() => handleCreateBackup('files')}
                                                className="group relative overflow-hidden bg-white rounded-3xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-all text-left"
                                            >
                                                <div className="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-3">
                                                    <Files className="w-5 h-5" />
                                                </div>
                                                <h4 className="font-black text-gray-950 uppercase italic tracking-tight text-xs mb-1">Backup Storage</h4>
                                                <p className="text-[10px] text-gray-500 leading-relaxed">Mencadangkan seluruh berkas upload & dokumen warga.</p>
                                            </button>

                                            <button
                                                type="button"
                                                onClick={() => handleCreateBackup('full')}
                                                className="group relative overflow-hidden bg-white rounded-3xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-all text-left"
                                            >
                                                <div className="w-10 h-10 bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center mb-3">
                                                    <Archive className="w-5 h-5" />
                                                </div>
                                                <h4 className="font-black text-gray-950 uppercase italic tracking-tight text-xs mb-1">Backup Full SaaS</h4>
                                                <p className="text-[10px] text-gray-500 leading-relaxed">Gabungan database dan seluruh file sistem aplikasi.</p>
                                            </button>
                                        </div>

                                        {/* Riwayat Backup */}
                                        <TableCard
                                            title="Riwayat Backup SaaS"
                                            icon={History}
                                            total={backupFiles.length}
                                            totalLabel="Berkas"
                                            noPadding
                                        >
                                            <div className="overflow-x-auto">
                                                <table className="w-full text-sm text-left">
                                                    <thead className="bg-gray-50/50 text-gray-500 text-xs uppercase font-bold tracking-wider">
                                                        <tr>
                                                            <th className="px-6 py-4 whitespace-nowrap">Info Berkas</th>
                                                            <th className="px-6 py-4">Tipe & Ukuran</th>
                                                            <th className="px-6 py-4 text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="divide-y divide-gray-100">
                                                        {backupFiles.length > 0 ? (
                                                            backupFiles.map((backup, index) => {
                                                                const isFull = backup.name.includes('full');
                                                                const isDb = backup.name.includes('database') || backup.name.includes('only-db');
                                                                const isFiles = backup.name.includes('files') || backup.name.includes('only-files');
                                                                const typeLabel = isFull ? 'Full Backup' : (isDb ? 'Database' : (isFiles ? 'Storage' : 'Backup'));
                                                                const badgeColor = isFull ? 'bg-pink-100 text-pink-700 border-pink-200' 
                                                                                 : isDb ? 'bg-indigo-100 text-indigo-700 border-indigo-200'
                                                                                 : 'bg-purple-100 text-purple-700 border-purple-200';

                                                                return (
                                                                    <tr key={index} className="hover:bg-gray-50/50 transition-colors">
                                                                        <td className="px-6 py-4">
                                                                            <div className="flex items-start">
                                                                                <FileArchive className="w-5 h-5 text-gray-400 mr-3 mt-0.5 shrink-0" />
                                                                                <div>
                                                                                    <div className="font-bold text-gray-900 truncate max-w-[150px] sm:max-w-xs" title={backup.name}>
                                                                                        {backup.name}
                                                                                    </div>
                                                                                    <div className="text-[10px] text-gray-400 font-bold mt-1 flex items-center">
                                                                                        <Clock className="w-3.5 h-3.5 mr-1 text-gray-300" />
                                                                                        {backup.created_at ? new Date(backup.created_at).toLocaleString('id-ID', {
                                                                                            day: 'numeric', month: 'short', year: 'numeric',
                                                                                            hour: '2-digit', minute: '2-digit'
                                                                                        }) : 'Unknown'}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td className="px-6 py-4">
                                                                            <div className="flex flex-col items-start gap-1">
                                                                                <span className={`inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider border ${badgeColor}`}>
                                                                                    {typeLabel}
                                                                                </span>
                                                                                <span className="text-[11px] font-bold text-gray-500 font-mono">
                                                                                    {formatBytes(backup.size)}
                                                                                </span>
                                                                            </div>
                                                                        </td>
                                                                        <td className="px-6 py-4 text-center">
                                                                            <div className="flex items-center justify-center space-x-2">
                                                                                <a
                                                                                    href={route('landlord.settings.backup.download', backup.name)}
                                                                                    className="inline-flex items-center justify-center p-2 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-xl transition-all border border-blue-100 hover:border-transparent cursor-pointer"
                                                                                    title="Unduh File"
                                                                                >
                                                                                    <Download className="w-4 h-4" />
                                                                                </a>
                                                                                <button
                                                                                    type="button"
                                                                                    onClick={() => handleDeleteBackup(backup.name)}
                                                                                    className="inline-flex items-center justify-center p-2 bg-slate-50 hover:bg-slate-800 text-slate-500 hover:text-white rounded-xl transition-all border border-slate-100 hover:border-transparent cursor-pointer"
                                                                                    title="Hapus Backup"
                                                                                >
                                                                                    <Trash2 className="w-4 h-4" />
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                );
                                                            })
                                                        ) : (
                                                            <tr>
                                                                <td colSpan="3" className="px-6 py-12 text-center">
                                                                    <div className="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                                                        <Inbox className="w-8 h-8 text-gray-300" />
                                                                    </div>
                                                                    <p className="text-sm font-bold text-gray-900">Belum Ada Backup SaaS</p>
                                                                    <p className="text-xs text-gray-500 mt-1">Gunakan tombol di atas untuk memulai pencadangan.</p>
                                                                </td>
                                                            </tr>
                                                        )}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </TableCard>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Submit Button (Only for Settings Form) */}
                        {activeTab !== 'roles' && activeTab !== 'backup' && (
                            <div className="flex justify-end pt-4">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-300 text-white rounded-2xl text-sm font-bold shadow-lg shadow-indigo-600/10 hover:shadow-indigo-700/20 active:scale-[0.98] transition-all cursor-pointer"
                                >
                                    <Save className="w-4 h-4" />
                                    {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                                </button>
                            </div>
                        )}
                    </div>
                </form>

                {/* Modal Form Role */}
                <Modal show={roleModalOpen} onClose={() => setRoleModalOpen(false)} maxWidth="md">
                    <FormCard 
                        icon={editRoleMode ? Edit2 : Plus}
                        title={editRoleMode ? 'Ubah Data Role' : 'Tambah Role Baru'}
                    >
                        <form onSubmit={submitRole} className="space-y-4 text-left">
                            <FormField.Input 
                                label="Slug Role (ID)"
                                placeholder="Contoh: operator_keuangan (hanya huruf, angka, -, _)"
                                value={roleForm.data.name}
                                onChange={e => roleForm.setData('name', e.target.value.toLowerCase())}
                                error={roleForm.errors.name}
                                required
                                disabled={editRoleMode}
                            />

                            <FormField.Input 
                                label="Nama Role"
                                placeholder="Contoh: Operator Keuangan"
                                value={roleForm.data.display_name}
                                onChange={e => roleForm.setData('display_name', e.target.value)}
                                error={roleForm.errors.display_name}
                                required
                            />

                            {/* Checkboxes for permissions */}
                            <div className="space-y-2.5">
                                <label className="block text-xs font-black text-slate-800 uppercase tracking-wider">Daftar Hak Akses (Izin)</label>
                                {roleForm.errors.permissions && (
                                    <div className="text-red-500 text-xs font-bold">{roleForm.errors.permissions}</div>
                                )}
                                <div className="space-y-2 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    {[
                                        { key: 'manage-central-users', label: 'Kelola User Central & Pengaturan', desc: 'Mengatur admin pusat dan hak akses role.' },
                                        { key: 'manage-allocations', label: 'Kelola Alokasi Resource', desc: 'Mengatur kuota user dan kapasitas storage desa.' },
                                        { key: 'manage-tenants', label: 'Kelola Tenant / Desa', desc: 'Mendaftarkan, mengedit, menonaktifkan, atau menghapus desa.' },
                                        { key: 'broadcast-announcements', label: 'Siaran Pengumuman', desc: 'Menyiarkan pengumuman massal ke dashboard desa.' },
                                    ].map((perm) => {
                                        const isChecked = roleForm.data.permissions.includes(perm.key);
                                        const isDisabled = selectedRole?.name === 'superadmin';

                                        return (
                                            <div 
                                                key={perm.key} 
                                                onClick={() => !isDisabled && togglePermission(perm.key)}
                                                className={cn(
                                                    "flex items-start gap-3 p-2.5 rounded-xl cursor-pointer transition-all border",
                                                    isChecked 
                                                        ? "bg-white border-indigo-100 shadow-sm" 
                                                        : "border-transparent hover:bg-slate-100/50"
                                                )}
                                            >
                                                <input 
                                                    type="checkbox"
                                                    checked={isChecked}
                                                    onChange={() => {}} // handled by click on container
                                                    disabled={isDisabled}
                                                    className="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 cursor-pointer"
                                                />
                                                <div className="flex flex-col">
                                                    <span className="text-xs font-bold text-slate-850">{perm.label}</span>
                                                    <span className="text-[10px] text-gray-400 font-medium">{perm.desc}</span>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>

                            <div className="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                <button
                                    type="button"
                                    onClick={() => setRoleModalOpen(false)}
                                    className="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={roleForm.processing}
                                    className="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all cursor-pointer"
                                >
                                    {editRoleMode ? 'Simpan Perubahan' : 'Tambah Role'}
                                </button>
                            </div>
                        </form>
                    </FormCard>
                </Modal>
            </div>
        </LandlordLayout>
    );
}
