import React, { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, FormCard, FormField, TableCard, DataTable, Badge, Modal } from '@/Components/Shared';
import { Settings, Save, User, HardDrive, Phone, Lock, Globe, ShieldCheck, Plus, Edit2, Trash2 } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';

export default function Index({ settings, user, roles }) {
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

    const submit = (e) => {
        e.preventDefault();
        put(route('landlord.settings.update'), {
            onSuccess: () => {
                reset('password', 'password_confirmation');
            }
        });
    };

    const tabs = [
        { id: 'system', label: 'Sistem & Alokasi', icon: HardDrive },
        { id: 'profile', label: 'Profil & Keamanan', icon: User },
        { id: 'roles', label: 'Manajemen Role', icon: ShieldCheck },
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
                                            label="Hotline WhatsApp Diskominfo"
                                            type="text"
                                            placeholder="Contoh: 081234567890"
                                            value={data.diskominfo_hotline}
                                            onChange={e => setData('diskominfo_hotline', e.target.value)}
                                            error={errors.diskominfo_hotline}
                                            required
                                        />
                                        <FormField.Input 
                                            label="Email Bantuan Diskominfo"
                                            type="email"
                                            placeholder="Contoh: diskominfo@purwakartakab.go.id"
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

                        {/* Submit Button (Only for Settings Form) */}
                        {activeTab !== 'roles' && (
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
                                        { key: 'manage-central-users', label: 'Kelola User Central & Pengaturan', desc: 'Mengatur admin pusat diskominfo dan hak akses role.' },
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
