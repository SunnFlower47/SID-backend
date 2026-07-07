import React, { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, TableCard, DataTable, Badge, FormField, Modal, FormCard } from '@/Components/Shared';
import { Users, Plus, Edit2, Trash2, Key, ShieldCheck, Mail, User, X } from 'lucide-react';

export default function Index({ users, roles }) {
    const { flash } = usePage().props;
    const [modalOpen, setModalOpen] = useState(false);
    const [editMode, setEditMode] = useState(false);
    const [selectedUser, setSelectedUser] = useState(null);

    const { data, setData, post, put, processing, errors, reset, clearErrors } = useForm({
        name: '',
        email: '',
        password: '',
        role: roles && roles.length > 0 ? roles[0].name : 'operator_monitoring',
    });

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
            put(route('users.update', selectedUser.id), {
                onSuccess: () => {
                    setModalOpen(false);
                    reset();
                }
            });
        } else {
            post(route('users.store'), {
                onSuccess: () => {
                    setModalOpen(false);
                    reset();
                }
            });
        }
    };

    const columns = [
        {
            header: 'Nama Pengguna',
            accessor: 'name',
            className: 'text-left min-w-[200px]',
            render: (row) => (
                <div className="flex items-center gap-3">
                    <div className="w-9 h-9 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
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
            header: 'Hak Akses (Role)',
            accessor: 'role',
            className: 'text-center',
            render: (row) => {
                const foundRole = roles?.find(r => r.name === row.role);
                const displayName = foundRole ? foundRole.display_name : row.role;

                let color = 'slate';
                if (row.role === 'superadmin') color = 'red';
                else if (row.role.includes('onboarding')) color = 'blue';
                else if (row.role.includes('monitoring')) color = 'green';
                else if (row.role.includes('admin')) color = 'indigo';

                return (
                    <div className="flex justify-center">
                        <Badge 
                            color={color}
                            dot={color}
                        >
                            {displayName}
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
                    <button
                        onClick={() => openEditModal(row)}
                        className="inline-flex items-center justify-center p-2 bg-slate-50 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 rounded-xl transition-all border border-slate-100 cursor-pointer"
                        title="Edit User"
                    >
                        <Edit2 className="w-4 h-4" />
                    </button>
                    <button
                        onClick={() => {
                            if (confirm('Apakah Anda yakin ingin menghapus user central ini?')) {
                                useForm().delete(route('users.destroy', row.id));
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
            <Head title="Manajemen User Central" />

            <div className="space-y-8">
                <PageHeader 
                    icon={Users}
                    title="User Central Diskominfo"
                    subtitle="Kelola akun admin pusat, staf, dan hak akses operasional Diskominfo."
                    gradient="from-indigo-600 via-indigo-700 to-indigo-800"
                    actions={[
                        {
                            label: 'Tambah User Baru',
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

                {/* Table Card */}
                <TableCard
                    title="Daftar Administrator Central"
                    icon={ShieldCheck}
                    total={users.total}
                    totalLabel="User"
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
                        title={editMode ? 'Ubah Data User Central' : 'Daftarkan User Central'}
                    >
                        <form onSubmit={submit} className="space-y-4 text-left">
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
                                placeholder="nama@diskominfo.go.id"
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
                                label="Hak Akses (Role)"
                                value={data.role}
                                onChange={e => setData('role', e.target.value)}
                                error={errors.role}
                                required
                                options={roles?.map(r => ({
                                    value: r.name,
                                    label: r.display_name + (r.name === 'superadmin' ? ' (Akses Penuh)' : '')
                                })) || []}
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
                                    {editMode ? 'Simpan Perubahan' : 'Daftarkan Admin'}
                                </button>
                            </div>
                        </form>
                    </FormCard>
                </Modal>
            </div>
        </LandlordLayout>
    );
}
