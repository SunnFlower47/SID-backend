import React, { useState } from 'react';
import { Head, useForm, usePage, router } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, TableCard, DataTable, Badge, FormField, Modal, FormCard, FilterContainer } from '@/Components/Shared';
import { UserCheck, Plus, Edit2, Trash2, ShieldCheck, Mail, Building2, Search } from 'lucide-react';

export default function Index({ users, tenants, filters }) {
    const { flash } = usePage().props;
    const [modalOpen, setModalOpen] = useState(false);
    const [editMode, setEditMode] = useState(false);
    const [selectedUser, setSelectedUser] = useState(null);

    const [searchVal, setSearchVal] = useState(filters.search || '');
    const [tenantFilter, setTenantFilter] = useState(filters.tenant_id || '');

    const { data, setData, post, put, processing, errors, reset, clearErrors } = useForm({
        tenant_id: '',
        name: '',
        email: '',
        password: '',
        role: 'Staf Desa',
    });

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get(route('tenant-users.index'), {
            search: searchVal,
            tenant_id: tenantFilter
        }, {
            preserveState: true,
            replace: true
        });
    };

    const handleClearFilter = () => {
        setSearchVal('');
        setTenantFilter('');
        router.get(route('tenant-users.index'), {}, {
            preserveState: true,
            replace: true
        });
    };

    const openAddModal = () => {
        reset();
        clearErrors();
        setEditMode(false);
        setSelectedUser(null);
        setModalOpen(true);
    };

    const openEditModal = (user) => {
        clearErrors();
        setEditMode(true);
        setSelectedUser(user);
        setData({
            tenant_id: user.tenant_id,
            name: user.name,
            email: user.email,
            password: '',
            role: user.role,
        });
        setModalOpen(true);
    };

    const submit = (e) => {
        e.preventDefault();
        if (editMode) {
            put(route('tenant-users.update', selectedUser.map_id), {
                onSuccess: () => {
                    setModalOpen(false);
                    reset();
                }
            });
        } else {
            post(route('tenant-users.store'), {
                onSuccess: () => {
                    setModalOpen(false);
                    reset();
                }
            });
        }
    };

    const columns = [
        {
            header: 'Pengguna Desa',
            accessor: 'name',
            className: 'text-left min-w-[200px]',
            render: (row) => (
                <div className="flex items-center gap-3">
                    <div className="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 font-bold text-sm">
                        {row.name.charAt(0).toUpperCase()}
                    </div>
                    <div className="flex flex-col">
                        <span className="font-black text-slate-800 text-sm">{row.name}</span>
                        <span className="text-gray-400 text-xs font-bold flex items-center gap-1">
                            <Mail className="w-3 h-3" />
                            {row.email}
                        </span>
                    </div>
                </div>
            )
        },
        {
            header: 'Desa (Tenant)',
            accessor: 'tenant_name',
            className: 'text-center',
            render: (row) => (
                <div className="flex flex-col items-center">
                    <span className="font-bold text-slate-800 text-xs flex items-center gap-1">
                        <Building2 className="w-3.5 h-3.5 text-indigo-500" />
                        {row.tenant_name}
                    </span>
                    <span className="text-[10px] text-gray-400 font-mono tracking-wider">{row.tenant_id.toUpperCase()}</span>
                </div>
            )
        },
        {
            header: 'Role Spatie',
            accessor: 'role',
            className: 'text-center',
            render: (row) => {
                const colors = {
                    'Super Admin': 'red',
                    'Admin Desa': 'blue',
                    'Staf Desa': 'green',
                    'Viewer': 'slate'
                };
                return (
                    <div className="flex justify-center">
                        <Badge 
                            color={colors[row.role] || 'slate'}
                            dot={colors[row.role] || 'slate'}
                        >
                            {row.role}
                        </Badge>
                    </div>
                );
            }
        },
        {
            header: 'Tanggal Dibuat',
            className: 'text-center text-xs font-bold text-slate-500',
            render: (row) => new Date(row.created_at).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            })
        },
        {
            header: 'Aksi',
            className: 'text-center w-[150px]',
            render: (row) => (
                <div className="flex items-center justify-center gap-2">
                    {row.id !== null && (
                        <button
                            onClick={() => openEditModal(row)}
                            className="inline-flex items-center justify-center p-2 bg-slate-50 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 rounded-xl transition-all border border-slate-100 cursor-pointer"
                            title="Edit User"
                        >
                            <Edit2 className="w-4 h-4" />
                        </button>
                    )}
                    <button
                        onClick={() => {
                            if (confirm('Apakah Anda yakin ingin menghapus pengguna tenant ini beserta pemetaannya?')) {
                                router.delete(route('tenant-users.destroy', row.map_id));
                            }
                        }}
                        className="inline-flex items-center justify-center p-2 bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-600 rounded-xl transition-all border border-slate-100 cursor-pointer"
                        title="Hapus User"
                    >
                        <Trash2 className="w-4 h-4" />
                    </button>
                </div>
            )
        }
    ];

    return (
        <LandlordLayout>
            <Head title="Manajemen User Tenant" />

            <div className="space-y-8">
                <PageHeader 
                    icon={UserCheck}
                    title="Manajemen User Tenant Desa"
                    subtitle="Pantau dan kelola seluruh akun pengguna di setiap tenant desa digital."
                    gradient="from-indigo-600 via-indigo-700 to-indigo-800"
                    actions={[
                        {
                            label: 'Tambah User Tenant Baru',
                            icon: Plus,
                            onClick: openAddModal,
                            variant: 'white'
                        }
                    ]}
                />

                {flash?.success && (
                    <div className="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-bold shadow-sm" role="alert">
                        <span>{flash.success}</span>
                    </div>
                )}

                {/* Filter Panel */}
                <FilterContainer 
                    hasActiveFilters={!!filters.search || !!filters.tenant_id}
                    title="Filter Pengguna Desa"
                    subtitle="Cari dan filter berdasarkan email atau desa"
                >
                    <form onSubmit={handleFilterSubmit} className="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <FormField.Input 
                                label="Cari Email"
                                placeholder="Tulis email user..."
                                value={searchVal}
                                onChange={e => setSearchVal(e.target.value)}
                            />

                            <FormField.Select
                                label="Berdasarkan Desa"
                                value={tenantFilter}
                                onChange={e => setTenantFilter(e.target.value)}
                                placeholder="Semua Desa"
                                options={tenants.map(t => ({ value: t.id, label: t.name }))}
                            />
                        </div>

                        <div className="flex justify-end gap-3">
                            <button
                                type="button"
                                onClick={handleClearFilter}
                                className="px-5 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-bold transition-all cursor-pointer"
                            >
                                Bersihkan
                            </button>
                            <button
                                type="submit"
                                className="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                            >
                                <Search className="w-3.5 h-3.5" />
                                Cari Data
                            </button>
                        </div>
                    </form>
                </FilterContainer>

                {/* Table Card */}
                <TableCard
                    title="Daftar Pengguna Desa Terdaftar"
                    icon={UserCheck}
                    total={users.total}
                    totalLabel="User Tenant"
                    pagination={users}
                    noPadding
                >
                    <DataTable 
                        columns={columns}
                        data={users.data}
                        borderedBody={true}
                    />
                </TableCard>

                {/* Modal Form */}
                <Modal show={modalOpen} onClose={() => setModalOpen(false)} maxWidth="md">
                    <FormCard 
                        icon={editMode ? Edit2 : Plus}
                        title={editMode ? 'Ubah Data User Tenant' : 'Daftarkan User Tenant Baru'}
                    >
                        <form onSubmit={submit} className="space-y-4 text-left">
                            {!editMode && (
                                <FormField.Select
                                    label="Pilih Desa (Tenant Target)"
                                    value={data.tenant_id}
                                    onChange={e => setData('tenant_id', e.target.value)}
                                    error={errors.tenant_id}
                                    required
                                    placeholder="-- Pilih Desa Target --"
                                    options={tenants.map(t => ({ value: t.id, label: t.name }))}
                                />
                            )}

                            <FormField.Input 
                                label="Nama Lengkap"
                                placeholder="Tulis nama lengkap..."
                                value={data.name}
                                onChange={e => setData('name', e.target.value)}
                                error={errors.name}
                                required
                            />

                            <FormField.Input 
                                label="Email Address"
                                type="email"
                                placeholder="nama@desa.id"
                                value={data.email}
                                onChange={e => setData('email', e.target.value)}
                                error={errors.email}
                                required
                            />

                            <FormField.Input 
                                label={editMode ? 'Kata Sandi Baru (Kosongkan jika tidak diubah)' : 'Kata Sandi'}
                                type="password"
                                placeholder="Minimal 8 karakter..."
                                value={data.password}
                                onChange={e => setData('password', e.target.value)}
                                error={errors.password}
                                required={!editMode}
                            />

                            <FormField.Select
                                label="Peran Wewenang (Spatie Role)"
                                value={data.role}
                                onChange={e => setData('role', e.target.value)}
                                error={errors.role}
                                required
                                options={[
                                    { value: 'Super Admin', label: 'Super Admin (Akses Penuh Desa)' },
                                    { value: 'Admin Desa', label: 'Admin Desa (Pengelola Utama)' },
                                    { value: 'Staf Desa', label: 'Staf Desa (Pelayanan & Aduan)' },
                                    { value: 'Viewer', label: 'Viewer (Read-Only)' },
                                ]}
                            />

                            <div className="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                <button
                                    type="button"
                                    onClick={() => setModalOpen(false)}
                                    className="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all cursor-pointer"
                                >
                                    {editMode ? 'Simpan Perubahan' : 'Buatkan Akun'}
                                </button>
                            </div>
                        </form>
                    </FormCard>
                </Modal>
            </div>
        </LandlordLayout>
    );
}
