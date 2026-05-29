import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/Shared';
import { 
    Settings, User, Shield, Server, Plus, Edit2, 
    Trash2, Save, X, CheckCircle, Info, Database, Map,
    FileSpreadsheet, ShieldAlert, Key, UserMinus, Eye, EyeOff
} from 'lucide-react';
import Swal from 'sweetalert2';

export default function Index({ auth, users, roles, permissions, permissions_structure = {}, stats, desa_settings = {} }) {
    // Get query params for tab switching
    const { url } = usePage();
    const searchParams = new URLSearchParams(window.location.search);
    const [activeTab, setActiveTab] = useState(searchParams.get('tab') || 'profile');

    useEffect(() => {
        const tab = new URLSearchParams(window.location.search).get('tab');
        if (tab) {
            setActiveTab(tab);
        }
    }, [url]);

    const handleTabChange = (tabName) => {
        setActiveTab(tabName);
        const newUrl = `${window.location.pathname}?tab=${tabName}`;
        window.history.pushState({ path: newUrl }, '', newUrl);
    };

    // ==========================================
    // 1. MY PROFILE FORMS
    // ==========================================
    const profileForm = useForm({
        name: auth.user.name || '',
        email: auth.user.email || '',
    });

    const handleProfileSubmit = (e) => {
        e.preventDefault();
        profileForm.patch(route('profile.update'), {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL!',
                    text: 'Profil Anda telah diperbarui.',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-3xl' }
                });
            }
        });
    };

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const [showPassword, setShowPassword] = useState({
        current: false,
        new: false,
        confirm: false
    });

    const handlePasswordSubmit = (e) => {
        e.preventDefault();
        passwordForm.patch(route('profile.password.update'), {
            onSuccess: () => {
                passwordForm.reset();
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL!',
                    text: 'Kata sandi Anda telah diperbarui.',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-3xl' }
                });
            }
        });
    };

    const deleteAccountForm = useForm({
        password: '',
    });

    const handleDeleteAccount = (e) => {
        e.preventDefault();
        Swal.fire({
            title: 'HAPUS AKUN ANDA?',
            html: `<p class="text-gray-600 text-sm">Apakah Anda yakin ingin menghapus akun Anda secara permanen? Semua data Anda akan dihapus selamanya.</p>
                   <input type="password" id="swal-password" class="w-full mt-4 px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-red-500/10" placeholder="Masukkan kata sandi Anda untuk konfirmasi">`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS AKUN!',
            cancelButtonText: 'BATAL',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            },
            preConfirm: () => {
                const password = document.getElementById('swal-password').value;
                if (!password) {
                    Swal.showValidationMessage('Kata sandi harus diisi!');
                }
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                deleteAccountForm.setData('password', result.value);
                // Trigger destruction via router to ensure proper execution
                router.delete(route('profile.destroy'), {
                    data: { password: result.value },
                    onError: (err) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'GAGAL!',
                            text: err.password || 'Terjadi kesalahan atau sandi tidak cocok.',
                            customClass: { popup: 'rounded-3xl' }
                        });
                    }
                });
            }
        });
    };

    // ==========================================
    // 2. USERS MANAGEMENT
    // ==========================================
    const [isUserModalOpen, setIsUserModalOpen] = useState(false);
    const [editUserId, setEditUserId] = useState(null);
    const [showUserPassword, setShowUserPassword] = useState(false);
    const [showUserConfirmPassword, setShowUserConfirmPassword] = useState(false);

    const userForm = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: roles[0]?.id || '',
        permissions: [],
    });

    const openCreateUserModal = () => {
        userForm.reset();
        userForm.clearErrors();
        setEditUserId(null);
        setShowUserPassword(false);
        setShowUserConfirmPassword(false);
        setIsUserModalOpen(true);
    };

    const openEditUserModal = (user) => {
        userForm.clearErrors();
        userForm.setData({
            name: user.name,
            email: user.email,
            password: '',
            password_confirmation: '',
            role: user.roles[0]?.id || '',
            permissions: user.permissions ? user.permissions.map(p => p.id) : [],
        });
        setEditUserId(user.id);
        setShowUserPassword(false);
        setShowUserConfirmPassword(false);
        setIsUserModalOpen(true);
    };

    const handleUserPermissionToggle = (permissionId) => {
        const isChecked = userForm.data.permissions.includes(permissionId);
        if (isChecked) {
            userForm.setData('permissions', userForm.data.permissions.filter(id => id !== permissionId));
        } else {
            userForm.setData('permissions', [...userForm.data.permissions, permissionId]);
        }
    };

    const handleUserSubmit = (e) => {
        e.preventDefault();
        if (editUserId) {
            userForm.put(route('settings.users.update', editUserId), {
                onSuccess: () => {
                    setIsUserModalOpen(false);
                    Swal.fire({
                        icon: 'success',
                        title: 'BERHASIL!',
                        text: 'Informasi pengguna berhasil diperbarui.',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-3xl' }
                    });
                }
            });
        } else {
            userForm.post(route('settings.users.create'), {
                onSuccess: () => {
                    setIsUserModalOpen(false);
                    Swal.fire({
                        icon: 'success',
                        title: 'BERHASIL!',
                        text: 'Pengguna baru telah ditambahkan.',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-3xl' }
                    });
                }
            });
        }
    };

    const handleUserDelete = (user) => {
        Swal.fire({
            title: 'HAPUS PENGGUNA?',
            html: `Apakah Anda yakin ingin menghapus <b class="text-red-600">${user.name}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Akses pengguna ini ke sistem akan dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATAL',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('settings.users.delete', user.id), {
                    onSuccess: () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'TERHAPUS!',
                            text: 'Pengguna berhasil dihapus.',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-3xl' }
                        });
                    }
                });
            }
        });
    };

    // ==========================================
    // 3. ROLES & PERMISSIONS MANAGEMENT
    // ==========================================
    const [isRoleModalOpen, setIsRoleModalOpen] = useState(false);
    const [editRoleId, setEditRoleId] = useState(null);

    const roleForm = useForm({
        name: '',
        permissions: [],
    });

    const openCreateRoleModal = () => {
        roleForm.reset();
        roleForm.clearErrors();
        setEditRoleId(null);
        setIsRoleModalOpen(true);
    };

    const openEditRoleModal = (role) => {
        roleForm.clearErrors();
        roleForm.setData({
            name: role.name,
            permissions: role.permissions.map(p => p.id),
        });
        setEditRoleId(role.id);
        setIsRoleModalOpen(true);
    };

    const getCategoryMatrix = (categoryNames) => {
        const rows = {};
        categoryNames.forEach(name => {
            const dbPermission = permissions.find(p => p.name === name);
            if (!dbPermission) return;
            
            let resource = name;
            let action = name;
            
            if (name.includes('.')) {
                const parts = name.split('.');
                resource = parts[0];
                action = parts[1];
            }
            
            if (!rows[resource]) {
                rows[resource] = {
                    resourceName: resource,
                    permissions: []
                };
            }
            
            rows[resource].permissions.push({
                id: dbPermission.id,
                name: name,
                action: action
            });
        });
        return Object.values(rows);
    };

    const formatResourceName = (name) => {
        return name
            .replace(/[_-]/g, ' ')
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    };

    const isRowAllChecked = (rowPermissions) => {
        return rowPermissions.every(p => roleForm.data.permissions.includes(p.id));
    };

    const toggleRowPermissions = (rowPermissions) => {
        const allChecked = isRowAllChecked(rowPermissions);
        const pIds = rowPermissions.map(p => p.id);
        if (allChecked) {
            roleForm.setData('permissions', roleForm.data.permissions.filter(id => !pIds.includes(id)));
        } else {
            const newPermissions = [...new Set([...roleForm.data.permissions, ...pIds])];
            roleForm.setData('permissions', newPermissions);
        }
    };

    const handleRoleSubmit = (e) => {
        e.preventDefault();
        if (editRoleId) {
            roleForm.put(route('settings.roles.update', editRoleId), {
                onSuccess: () => {
                    setIsRoleModalOpen(false);
                    Swal.fire({
                        icon: 'success',
                        title: 'BERHASIL!',
                        text: 'Role berhasil diperbarui.',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-3xl' }
                    });
                }
            });
        } else {
            roleForm.post(route('settings.roles.create'), {
                onSuccess: () => {
                    setIsRoleModalOpen(false);
                    Swal.fire({
                        icon: 'success',
                        title: 'BERHASIL!',
                        text: 'Role baru berhasil ditambahkan.',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-3xl' }
                    });
                }
            });
        }
    };

    const handleRoleDelete = (role) => {
        Swal.fire({
            title: 'HAPUS ROLE?',
            html: `Apakah Anda yakin ingin menghapus role <b class="text-red-600">${role.name}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATAL',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('settings.roles.delete', role.id), {
                    onSuccess: () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'TERHAPUS!',
                            text: 'Role berhasil dihapus.',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-3xl' }
                        });
                    },
                    onError: (err) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'GAGAL!',
                            text: err.error || 'Role masih digunakan oleh beberapa pengguna.',
                            customClass: { popup: 'rounded-3xl' }
                        });
                    }
                });
            }
        });
    };

    const handlePermissionToggle = (permissionId) => {
        const isChecked = roleForm.data.permissions.includes(permissionId);
        if (isChecked) {
            roleForm.setData('permissions', roleForm.data.permissions.filter(id => id !== permissionId));
        } else {
            roleForm.setData('permissions', [...roleForm.data.permissions, permissionId]);
        }
    };

    // ==========================================
    // 4. SYSTEM ACTIONS
    // ==========================================
    const handleClearCache = () => {
        Swal.fire({
            title: 'BERSIHKAN CACHE?',
            text: 'Ini akan membersihkan seluruh cache aplikasi Laravel.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'YA, BERSIHKAN!',
            cancelButtonText: 'BATAL',
            customClass: {
                popup: 'rounded-3xl',
                title: 'font-black uppercase italic text-green-600',
                confirmButton: 'rounded-2xl px-5 py-3 text-[10px] uppercase font-black',
                cancelButton: 'rounded-2xl px-5 py-3 text-[10px] uppercase font-black'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                router.post(route('settings.clear-cache'), {}, {
                    onSuccess: (page) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'BERHASIL!',
                            text: 'Cache sistem berhasil dibersihkan.',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-3xl' }
                        });
                    },
                    onError: () => {
                        Swal.fire({
                            icon: 'error',
                            title: 'GAGAL!',
                            text: 'Gagal membersihkan cache.',
                            customClass: { popup: 'rounded-3xl' }
                        });
                    }
                });
            }
        });
    };

    // ==========================================
    // 5. DESA SETTINGS (GEOJSON UPLOAD)
    // ==========================================
    const desaForm = useForm({
        _method: 'put',
        settings: [
            { key: 'batas_wilayah_geojson', type: 'json', group: 'geography' }
        ],
        files: {
            batas_wilayah_geojson: null
        }
    });

    const handleDesaSubmit = (e) => {
        e.preventDefault();
        desaForm.post(route('settings.desa.update'), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL!',
                    text: 'Pengaturan Geografi berhasil diperbarui.',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-3xl' }
                });
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Pengaturan Sistem">
            <Head title="Pengaturan - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                {/* Header Card */}
                <PageHeader
                    title="Pengaturan & Keamanan"
                    subtitle="Kelola profil, pengguna, peran, dan pemeliharaan sistem"
                    icon={Settings}
                />

                {/* Tab Controls */}
                <div className="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 flex flex-wrap gap-1">
                    <button
                        onClick={() => handleTabChange('profile')}
                        className={`flex items-center gap-2 px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest italic transition-all ${
                            activeTab === 'profile' 
                                ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-md' 
                                : 'text-gray-600 hover:bg-gray-50'
                        }`}
                    >
                        <User className="w-4 h-4" />
                        Profil Saya
                    </button>
                    <button
                        onClick={() => handleTabChange('users')}
                        className={`flex items-center gap-2 px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest italic transition-all ${
                            activeTab === 'users' 
                                ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-md' 
                                : 'text-gray-600 hover:bg-gray-50'
                        }`}
                    >
                        <Settings className="w-4 h-4" />
                        Manajemen Pengguna
                    </button>
                    <button
                        onClick={() => handleTabChange('roles')}
                        className={`flex items-center gap-2 px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest italic transition-all ${
                            activeTab === 'roles' 
                                ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-md' 
                                : 'text-gray-600 hover:bg-gray-50'
                        }`}
                    >
                        <Shield className="w-4 h-4" />
                        Role & Izin
                    </button>
                    <button
                        onClick={() => handleTabChange('system')}
                        className={`flex items-center gap-2 px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest italic transition-all ${
                            activeTab === 'system' 
                                ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-md' 
                                : 'text-gray-600 hover:bg-gray-50'
                        }`}
                    >
                        <Server className="w-4 h-4" />
                        Sistem & Stats
                    </button>
                    <button
                        onClick={() => handleTabChange('desa')}
                        className={`flex items-center gap-2 px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest italic transition-all ${
                            activeTab === 'desa' 
                                ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-md' 
                                : 'text-gray-600 hover:bg-gray-50'
                        }`}
                    >
                        <Map className="w-4 h-4" />
                        Peta & Geografi
                    </button>
                </div>

                {/* Tab Contents */}
                <div className="space-y-6">

                    {/* ========================================================================= */}
                    {/* TAB 1: PROFIL SAYA */}
                    {/* ========================================================================= */}
                    {activeTab === 'profile' && (
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-in fade-in duration-300">
                            
                            {/* Profile Info Card */}
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
                                <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter border-b border-gray-50 pb-4">
                                    <User className="w-6 h-6 text-green-600" />
                                    Informasi Profil
                                </h3>
                                
                                <form onSubmit={handleProfileSubmit} className="space-y-5">
                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                                        <input
                                            type="text"
                                            value={profileForm.data.name}
                                            onChange={e => profileForm.setData('name', e.target.value)}
                                            className="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all"
                                            required
                                        />
                                        {profileForm.errors.name && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{profileForm.errors.name}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Email</label>
                                        <input
                                            type="email"
                                            value={profileForm.data.email}
                                            onChange={e => profileForm.setData('email', e.target.value)}
                                            className="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all"
                                            required
                                        />
                                        {profileForm.errors.email && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{profileForm.errors.email}</p>}
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={profileForm.processing}
                                        className="flex items-center justify-center gap-2 px-6 py-4 bg-gray-900 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lg shadow-gray-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50"
                                    >
                                        <Save className="w-4 h-4" />
                                        Simpan Perubahan
                                    </button>
                                </form>
                            </div>

                            {/* Password and Account deletion Card */}
                            <div className="space-y-6">
                                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
                                    <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter border-b border-gray-50 pb-4">
                                        <Key className="w-6 h-6 text-green-600" />
                                        Perbarui Kata Sandi
                                    </h3>
                                    
                                    <form onSubmit={handlePasswordSubmit} className="space-y-5">
                                        <div className="space-y-2 relative">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kata Sandi Saat Ini</label>
                                            <div className="relative">
                                                <input
                                                    type={showPassword.current ? "text" : "password"}
                                                    value={passwordForm.data.current_password}
                                                    onChange={e => passwordForm.setData('current_password', e.target.value)}
                                                    className="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all pr-12"
                                                    required
                                                />
                                                <button 
                                                    type="button"
                                                    onClick={() => setShowPassword(p => ({ ...p, current: !p.current }))}
                                                    className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                                >
                                                    {showPassword.current ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                                </button>
                                            </div>
                                            {passwordForm.errors.current_password && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{passwordForm.errors.current_password}</p>}
                                        </div>

                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kata Sandi Baru</label>
                                            <div className="relative">
                                                <input
                                                    type={showPassword.new ? "text" : "password"}
                                                    value={passwordForm.data.password}
                                                    onChange={e => passwordForm.setData('password', e.target.value)}
                                                    className="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all pr-12"
                                                    required
                                                />
                                                <button 
                                                    type="button"
                                                    onClick={() => setShowPassword(p => ({ ...p, new: !p.new }))}
                                                    className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                                >
                                                    {showPassword.new ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                                </button>
                                            </div>
                                            {passwordForm.errors.password && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{passwordForm.errors.password}</p>}
                                        </div>

                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Konfirmasi Kata Sandi</label>
                                            <div className="relative">
                                                <input
                                                    type={showPassword.confirm ? "text" : "password"}
                                                    value={passwordForm.data.password_confirmation}
                                                    onChange={e => passwordForm.setData('password_confirmation', e.target.value)}
                                                    className="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all pr-12"
                                                    required
                                                />
                                                <button 
                                                    type="button"
                                                    onClick={() => setShowPassword(p => ({ ...p, confirm: !p.confirm }))}
                                                    className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                                >
                                                    {showPassword.confirm ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                                </button>
                                            </div>
                                            {passwordForm.errors.password_confirmation && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{passwordForm.errors.password_confirmation}</p>}
                                        </div>

                                        <button
                                            type="submit"
                                            disabled={passwordForm.processing}
                                            className="flex items-center justify-center gap-2 px-6 py-4 bg-gray-900 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lg shadow-gray-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50"
                                        >
                                            <Save className="w-4 h-4" />
                                            Perbarui Sandi
                                        </button>
                                    </form>
                                </div>

                                {/* Danger Zone: Delete Account */}
                                <div className="bg-red-50/50 rounded-3xl shadow-sm border border-red-100 p-6 sm:p-8 space-y-6">
                                    <h3 className="text-lg font-black text-red-700 flex items-center gap-3 uppercase italic tracking-tighter border-b border-red-100/50 pb-4">
                                        <ShieldAlert className="w-6 h-6 text-red-600" />
                                        Zona Bahaya
                                    </h3>
                                    <p className="text-xs text-red-600 font-semibold leading-relaxed">
                                        Sekali Anda menghapus akun Anda, semua data dan akses kependudukan terkait akun ini akan dihapus permanen dan tidak dapat dipulihkan.
                                    </p>
                                    <button
                                        type="button"
                                        onClick={handleDeleteAccount}
                                        className="flex items-center gap-2 px-6 py-4 bg-red-600 text-white hover:bg-red-700 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200 transition-all hover:scale-[1.02] active:scale-[0.98]"
                                    >
                                        <UserMinus className="w-4 h-4" />
                                        HAPUS AKUN PERMANEN
                                    </button>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* ========================================================================= */}
                    {/* TAB 2: MANAJEMEN PENGGUNA */}
                    {/* ========================================================================= */}
                    {activeTab === 'users' && (
                        <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-in fade-in duration-300">
                            <div className="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-gray-50 to-white">
                                <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter">
                                    <Info className="w-6 h-6 text-gray-600" />
                                    Daftar Pengguna Sistem
                                </h3>
                                <button
                                    onClick={openCreateUserModal}
                                    className="flex items-center justify-center px-5 py-3 bg-gray-900 text-white rounded-xl text-[10px] font-black transition-all hover:scale-105 uppercase tracking-widest shadow-lg shadow-black/10"
                                >
                                    <Plus className="w-3.5 h-3.5 mr-2" />
                                    TAMBAH PENGGUNA
                                </button>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-sm text-gray-600">
                                    <thead>
                                        <tr className="bg-gray-50/50 border-b border-gray-100">
                                            <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pengguna</th>
                                            <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</th>
                                            <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Role</th>
                                            <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Dibuat</th>
                                            <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-50">
                                        {users.map((user) => (
                                            <tr key={user.id} className="hover:bg-gray-50/50 transition-colors group">
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-10 h-10 bg-green-50 text-green-700 rounded-full flex items-center justify-center font-black text-sm uppercase">
                                                            {user.name.charAt(0)}
                                                        </div>
                                                        <div>
                                                            <div className="font-black text-gray-950 uppercase italic tracking-tight">{user.name}</div>
                                                            {user.id === auth.user.id && (
                                                                <span className="text-[9px] bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded-full font-black uppercase italic tracking-widest">Akun Saya</span>
                                                            )}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <span className="font-bold text-gray-600 text-xs">{user.email}</span>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="flex flex-wrap gap-1">
                                                        {user.roles.map((role) => (
                                                            <span key={role.id} className="px-2.5 py-1 bg-green-50 text-green-700 border border-green-100 text-[9px] font-black uppercase tracking-widest italic rounded-full">
                                                                {role.name}
                                                            </span>
                                                        ))}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <span className="text-xs text-gray-400 font-bold">{new Date(user.created_at).toLocaleDateString('id-ID')}</span>
                                                </td>
                                                <td className="px-6 py-4 text-right">
                                                    <div className="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <button 
                                                            onClick={() => openEditUserModal(user)}
                                                            className="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-all active:scale-95"
                                                        >
                                                            <Edit2 className="w-4 h-4" />
                                                        </button>
                                                        {user.id !== auth.user.id && (
                                                            <button 
                                                                onClick={() => handleUserDelete(user)}
                                                                className="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-all active:scale-95"
                                                            >
                                                                <Trash2 className="w-4 h-4" />
                                                            </button>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    )}

                    {/* ========================================================================= */}
                    {/* TAB 3: ROLE & PERMISSION */}
                    {/* ========================================================================= */}
                    {activeTab === 'roles' && (
                        <div className="grid grid-cols-1 xl:grid-cols-3 gap-6 animate-in fade-in duration-300">
                            
                            {/* Role Cards List */}
                            <div className="xl:col-span-2 space-y-4">
                                <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                                    <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter">
                                        <ShieldAlert className="w-6 h-6 text-gray-600" />
                                        Daftar Role Keamanan
                                    </h3>
                                    <button
                                        onClick={openCreateRoleModal}
                                        className="flex items-center justify-center px-4 py-2.5 bg-gray-900 text-white rounded-xl text-[10px] font-black transition-all hover:scale-105 uppercase tracking-widest shadow-lg shadow-black/10"
                                    >
                                        <Plus className="w-3.5 h-3.5 mr-2" />
                                        TAMBAH ROLE
                                    </button>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {roles.map((role) => (
                                        <div key={role.id} className="bg-white rounded-3xl p-6 border border-gray-100 flex flex-col justify-between group hover:shadow-lg transition-all duration-300">
                                            <div>
                                                <div className="flex items-start justify-between">
                                                    <div className="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center">
                                                        <Shield className="w-5 h-5" />
                                                    </div>
                                                    <div className="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <button 
                                                            onClick={() => openEditRoleModal(role)}
                                                            className="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                                        >
                                                            <Edit2 className="w-3.5 h-3.5" />
                                                        </button>
                                                        <button 
                                                            onClick={() => handleRoleDelete(role)}
                                                            className="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                        >
                                                            <Trash2 className="w-3.5 h-3.5" />
                                                        </button>
                                                    </div>
                                                </div>
                                                <h4 className="text-base font-black text-gray-900 uppercase italic tracking-tight mt-4">{role.name}</h4>
                                                <p className="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Spatie Guard: {role.guard_name}</p>
                                            </div>

                                            <div className="border-t border-gray-50 mt-6 pt-4 flex items-center justify-between text-left">
                                                <div>
                                                    <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Izin Akses</span>
                                                    <span className="text-xs font-black text-gray-900 italic uppercase">{role.permissions?.length || 0} Izin</span>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Permissions Summary Panel */}
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
                                <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter border-b border-gray-50 pb-4">
                                    <Key className="w-6 h-6 text-green-600" />
                                    Daftar Permission Sistem
                                </h3>
                                <p className="text-xs text-gray-500 font-semibold leading-relaxed">
                                    Berikut adalah modul permission yang aktif untuk mendefinisikan hak akses pengguna dalam aplikasi.
                                </p>
                                <div className="space-y-2 max-h-[400px] overflow-y-auto pr-2">
                                    {permissions.map((p) => (
                                        <div key={p.id} className="p-3 bg-gray-50 rounded-xl flex items-center justify-between">
                                            <span className="text-xs font-bold text-gray-700">{p.name}</span>
                                            <span className="px-2 py-0.5 bg-gray-200 text-gray-500 text-[8px] font-black uppercase tracking-widest rounded-full">
                                                {p.guard_name}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    )}

                    {/* ========================================================================= */}
                    {/* TAB 4: SISTEM & STATS */}
                    {/* ========================================================================= */}
                    {activeTab === 'system' && (
                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-in fade-in duration-300">
                            
                            {/* Database Stats Card */}
                            <div className="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
                                <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter border-b border-gray-50 pb-4">
                                    <Database className="w-6 h-6 text-green-600" />
                                    Metrik Database
                                </h3>

                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div className="bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                                        <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Total Penduduk</span>
                                        <span className="text-3xl font-black text-gray-900 block mt-2 tabular-nums">{stats.totalPenduduk}</span>
                                    </div>
                                    <div className="bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                                        <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Total Kartu Keluarga</span>
                                        <span className="text-3xl font-black text-gray-900 block mt-2 tabular-nums">{stats.totalKK}</span>
                                    </div>
                                    <div className="bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                                        <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Total Mutasi</span>
                                        <span className="text-3xl font-black text-gray-900 block mt-2 tabular-nums">{stats.totalMutasi}</span>
                                    </div>
                                </div>
                            </div>

                            {/* System Actions Panel */}
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
                                <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter border-b border-gray-50 pb-4">
                                    <Server className="w-6 h-6 text-green-600" />
                                    Tindakan Sistem
                                </h3>
                                
                                <div className="space-y-4">
                                    <div className="flex flex-col gap-2">
                                        <button
                                            onClick={handleClearCache}
                                            className="w-full flex items-center justify-center gap-3 px-6 py-4 bg-gray-900 hover:bg-gray-800 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lg shadow-gray-200 transition-all hover:scale-[1.02] active:scale-[0.98]"
                                        >
                                            <Server className="w-4 h-4" />
                                            Bersihkan Cache Aplikasi
                                        </button>
                                        <p className="text-[10px] text-gray-400 font-bold ml-1">Membersihkan template terkompilasi, route cache, config cache, dll.</p>
                                    </div>

                                    <div className="flex flex-col gap-2 pt-4 border-t border-gray-100">
                                        <Link
                                            href={route('export.index')}
                                            className="w-full flex items-center justify-center gap-3 px-6 py-4 bg-green-700 hover:bg-green-800 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lg shadow-green-200 transition-all hover:scale-[1.02] active:scale-[0.98]"
                                        >
                                            <FileSpreadsheet className="w-4 h-4" />
                                            Export / Backup Data Excel
                                        </Link>
                                        <p className="text-[10px] text-gray-400 font-bold ml-1">Ekspor seluruh data warga dan mutasi kependudukan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            {/* ========================================================================= */}
            {/* USER FORM MODAL */}
            {/* ========================================================================= */}
            {isUserModalOpen && (
                <div className="fixed inset-0 z-[100] overflow-y-auto">
                    <div className="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0 text-left">
                        <div className="fixed inset-0 transition-opacity" aria-hidden="true" onClick={() => setIsUserModalOpen(false)}>
                            <div className="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                        </div>

                        <span className="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <div className="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-in zoom-in-95 duration-300">
                            <form onSubmit={handleUserSubmit}>
                                <div className="bg-white px-8 pt-8 pb-6">
                                    <div className="flex items-center justify-between mb-8">
                                        <div className="flex items-center gap-4">
                                            <div className="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center">
                                                <Plus className="w-6 h-6 text-gray-900" />
                                            </div>
                                            <div>
                                                <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter">
                                                    {editUserId ? 'Perbarui Pengguna' : 'Tambah Pengguna'}
                                                </h3>
                                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Manajemen Akun Administrator</p>
                                            </div>
                                        </div>
                                        <button type="button" onClick={() => setIsUserModalOpen(false)} className="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                                            <X className="w-5 h-5 text-gray-400" />
                                        </button>
                                    </div>

                                    <div className="space-y-5">
                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                                            <input
                                                type="text"
                                                value={userForm.data.name}
                                                onChange={e => userForm.setData('name', e.target.value)}
                                                className={`w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 ${userForm.errors.name ? 'focus:ring-red-500/10' : 'focus:ring-green-500/10'} transition-all`}
                                                required
                                            />
                                            {userForm.errors.name && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{userForm.errors.name}</p>}
                                        </div>

                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Email</label>
                                            <input
                                                type="email"
                                                value={userForm.data.email}
                                                onChange={e => userForm.setData('email', e.target.value)}
                                                className={`w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 ${userForm.errors.email ? 'focus:ring-red-500/10' : 'focus:ring-green-500/10'} transition-all`}
                                                required
                                            />
                                            {userForm.errors.email && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{userForm.errors.email}</p>}
                                        </div>

                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Peran / Role Pengguna</label>
                                            <select
                                                value={userForm.data.role}
                                                onChange={e => userForm.setData('role', e.target.value)}
                                                className="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all appearance-none"
                                                required
                                            >
                                                {roles.map(role => (
                                                    <option key={role.id} value={role.id}>{role.name}</option>
                                                ))}
                                            </select>
                                            {userForm.errors.role && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{userForm.errors.role}</p>}
                                        </div>

                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                                {editUserId ? 'Kata Sandi Baru (Kosongkan jika tidak diganti)' : 'Kata Sandi'}
                                            </label>
                                            <div className="relative">
                                                <input
                                                    type={showUserPassword ? "text" : "password"}
                                                    value={userForm.data.password}
                                                    onChange={e => userForm.setData('password', e.target.value)}
                                                    className={`w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 ${userForm.errors.password ? 'focus:ring-red-500/10' : 'focus:ring-green-500/10'} transition-all pr-12`}
                                                    required={!editUserId}
                                                />
                                                <button
                                                    type="button"
                                                    onClick={() => setShowUserPassword(!showUserPassword)}
                                                    className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                                                >
                                                    {showUserPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                                </button>
                                            </div>
                                            {userForm.errors.password && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{userForm.errors.password}</p>}
                                        </div>

                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Konfirmasi Kata Sandi</label>
                                            <div className="relative">
                                                <input
                                                    type={showUserConfirmPassword ? "text" : "password"}
                                                    value={userForm.data.password_confirmation}
                                                    onChange={e => userForm.setData('password_confirmation', e.target.value)}
                                                    className={`w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 ${userForm.errors.password_confirmation ? 'focus:ring-red-500/10' : 'focus:ring-green-500/10'} transition-all pr-12`}
                                                    required={!editUserId}
                                                />
                                                <button
                                                    type="button"
                                                    onClick={() => setShowUserConfirmPassword(!showUserConfirmPassword)}
                                                    className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                                                >
                                                    {showUserConfirmPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                                </button>
                                            </div>
                                            {userForm.errors.password_confirmation && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{userForm.errors.password_confirmation}</p>}
                                        </div>

                                        {/* Direct Permissions section */}
                                        <div className="space-y-3 pt-4 border-t border-gray-100">
                                            <div className="flex items-center justify-between">
                                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Direct Permissions (Izin Ekstra Tambahan)</label>
                                                <button
                                                    type="button"
                                                    onClick={() => userForm.setData('permissions', [])}
                                                    className="text-[9px] font-bold text-red-600 hover:text-red-800 uppercase tracking-widest italic"
                                                >
                                                    Reset Izin Ekstra
                                                </button>
                                            </div>
                                            <p className="text-[10px] text-gray-400 font-medium ml-1 leading-relaxed">
                                                Gunakan ini jika ingin memberi akses tambahan khusus ke user ini di luar permission bawaan Role-nya.
                                            </p>
                                            
                                            <div className="border border-gray-100 rounded-2xl overflow-hidden max-h-[220px] overflow-y-auto bg-gray-50/30 p-3 space-y-4">
                                                {Object.entries(permissions_structure).map(([category, categoryPerms]) => {
                                                    const categoryDbPerms = permissions.filter(p => categoryPerms.includes(p.name));
                                                    if (categoryDbPerms.length === 0) return null;
                                                    
                                                    return (
                                                        <div key={category} className="space-y-1.5">
                                                            <span className="text-[9px] font-black text-gray-400 uppercase tracking-widest italic border-b border-gray-100 pb-0.5 block">{category}</span>
                                                            <div className="grid grid-cols-2 gap-1.5">
                                                                {categoryDbPerms.map(p => {
                                                                    const isChecked = userForm.data.permissions.includes(p.id);
                                                                    return (
                                                                        <label key={p.id} className={`flex items-center gap-2 p-2 rounded-xl border text-left cursor-pointer transition-all ${
                                                                            isChecked 
                                                                                ? 'border-green-500 bg-green-50/50 text-green-950 font-bold' 
                                                                                : 'border-gray-100 bg-white hover:bg-gray-50 text-gray-600'
                                                                        }`}>
                                                                            <input
                                                                                type="checkbox"
                                                                                checked={isChecked}
                                                                                onChange={() => handleUserPermissionToggle(p.id)}
                                                                                className="rounded border-gray-300 text-green-600 focus:ring-green-500/20 w-3.5 h-3.5"
                                                                            />
                                                                            <span className="text-[9px] font-bold truncate uppercase tracking-tight">{p.name}</span>
                                                                        </label>
                                                                    );
                                                                })}
                                                            </div>
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="bg-gray-50 px-8 py-6 rounded-b-[2.5rem]">
                                    <button
                                        type="submit"
                                        disabled={userForm.processing}
                                        className="w-full flex items-center justify-center gap-3 px-6 py-5 bg-gray-900 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-xl shadow-gray-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50"
                                    >
                                        <Save className="w-4 h-4" />
                                        {editUserId ? 'PERBARUI PENGGUNA' : 'TAMBAH PENGGUNA'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {/* ========================================================================= */}
            {/* TAB 5: PETA & GEOGRAFI */}
            {/* ========================================================================= */}
            {activeTab === 'desa' && (
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6 animate-in fade-in duration-300">
                    <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter border-b border-gray-50 pb-4">
                        <Map className="w-6 h-6 text-green-600" />
                        Pengaturan Geografi & Peta Desa
                    </h3>
                    
                    <form onSubmit={handleDesaSubmit} className="space-y-6">
                        <div className="bg-gray-50/50 p-6 rounded-2xl border border-gray-100 space-y-4">
                            <h4 className="text-xs font-black text-gray-600 uppercase tracking-widest">Batas Wilayah Desa (GeoJSON)</h4>
                            <p className="text-xs text-gray-500 font-medium">Unggah file .geojson yang berisi data poligon batas administrasi desa Anda. File ini akan dirender secara otomatis pada Peta Interaktif.</p>
                            
                            <div className="space-y-2">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">File GeoJSON</label>
                                <input
                                    type="file"
                                    accept=".geojson,application/geo+json"
                                    onChange={e => desaForm.setData('files', { ...desaForm.data.files, batas_wilayah_geojson: e.target.files[0] })}
                                    className="w-full px-5 py-4 bg-white border border-gray-200 rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 focus:ring-green-500/10 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                                />
                                {desaForm.errors['files.batas_wilayah_geojson'] && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{desaForm.errors['files.batas_wilayah_geojson']}</p>}
                                
                                {desa_settings['batas_wilayah_geojson']?.value && (
                                    <div className="mt-3 inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 border border-green-100 rounded-full text-xs font-black uppercase tracking-widest italic">
                                        <CheckCircle className="w-4 h-4" />
                                        File GeoJSON saat ini telah terunggah
                                    </div>
                                )}
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={desaForm.processing}
                            className="flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lg shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50"
                        >
                            <Save className="w-4 h-4" />
                            Simpan Geografi
                        </button>
                    </form>
                </div>
            )}

            {/* ========================================================================= */}
            {/* ROLE FORM MODAL */}
            {/* ========================================================================= */}
            {isRoleModalOpen && (
                <div className="fixed inset-0 z-[100] overflow-y-auto">
                    <div className="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0 text-left">
                        <div className="fixed inset-0 transition-opacity" aria-hidden="true" onClick={() => setIsRoleModalOpen(false)}>
                            <div className="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                        </div>

                        <span className="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <div className="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full animate-in zoom-in-95 duration-300">
                            <form onSubmit={handleRoleSubmit}>
                                <div className="bg-white px-8 pt-8 pb-6">
                                    <div className="flex items-center justify-between mb-8">
                                        <div className="flex items-center gap-4">
                                            <div className="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center">
                                                <Shield className="w-6 h-6 text-gray-900" />
                                            </div>
                                            <div>
                                                <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter">
                                                    {editRoleId ? 'Perbarui Role Keamanan' : 'Tambah Role Keamanan'}
                                                </h3>
                                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Manajemen Peran & Hak Akses</p>
                                            </div>
                                        </div>
                                        <button type="button" onClick={() => setIsRoleModalOpen(false)} className="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                                            <X className="w-5 h-5 text-gray-400" />
                                        </button>
                                    </div>

                                    <div className="space-y-6">
                                        <div className="space-y-2">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Role</label>
                                            <input
                                                type="text"
                                                value={roleForm.data.name}
                                                onChange={e => roleForm.setData('name', e.target.value)}
                                                className={`w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-700 focus:ring-4 ${roleForm.errors.name ? 'focus:ring-red-500/10' : 'focus:ring-green-500/10'} transition-all`}
                                                placeholder="Contoh: Operator Surat"
                                                required
                                            />
                                            {roleForm.errors.name && <p className="text-[10px] font-bold text-red-500 uppercase tracking-widest ml-1 italic">{roleForm.errors.name}</p>}
                                        </div>

                                        <div className="space-y-4">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 block">Atur Izin Modul (Matrix Permission)</label>
                                            <div className="border border-gray-100 rounded-3xl overflow-hidden max-h-[450px] overflow-y-auto pr-1">
                                                {Object.entries(permissions_structure).map(([category, categoryPerms]) => {
                                                    const rows = getCategoryMatrix(categoryPerms);
                                                    return (
                                                        <div key={category} className="mb-6 last:mb-0">
                                                            <div className="bg-gray-50 px-5 py-3 border-b border-gray-100">
                                                                <span className="text-[10px] font-black text-gray-500 uppercase tracking-widest italic">{category}</span>
                                                            </div>
                                                            <div className="overflow-x-auto">
                                                                <table className="w-full text-left text-xs text-gray-600 min-w-[650px]">
                                                                    <thead>
                                                                        <tr className="border-b border-gray-100">
                                                                            <th className="px-5 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider w-1/3">Modul / Fitur</th>
                                                                            <th className="px-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Semua</th>
                                                                            <th className="px-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">View</th>
                                                                            <th className="px-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Create</th>
                                                                            <th className="px-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Edit</th>
                                                                            <th className="px-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Delete</th>
                                                                            <th className="px-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Export</th>
                                                                            <th className="px-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Import</th>
                                                                            <th className="px-3 py-3 text-[9px] font-black text-gray-400 uppercase tracking-wider text-center">Lainnya</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody className="divide-y divide-gray-50">
                                                                        {rows.map((row) => {
                                                                            const isAllChecked = isRowAllChecked(row.permissions);
                                                                            
                                                                            // Find standard actions
                                                                            const viewPerm = row.permissions.find(p => p.action === 'view');
                                                                            const createPerm = row.permissions.find(p => p.action === 'create');
                                                                            const editPerm = row.permissions.find(p => p.action === 'edit' || p.action === 'update');
                                                                            const deletePerm = row.permissions.find(p => p.action === 'delete' || p.action === 'destroy');
                                                                            const exportPerm = row.permissions.find(p => p.action === 'export');
                                                                            const importPerm = row.permissions.find(p => p.action === 'import');
                                                                            
                                                                            // Others: anything else
                                                                            const otherPerms = row.permissions.filter(p => 
                                                                                !['view', 'create', 'edit', 'update', 'delete', 'destroy', 'export', 'import'].includes(p.action)
                                                                            );

                                                                            const renderCheckbox = (perm) => {
                                                                                if (!perm) return <span className="text-gray-300">-</span>;
                                                                                const isChecked = roleForm.data.permissions.includes(perm.id);
                                                                                return (
                                                                                    <label className="flex items-center justify-center cursor-pointer p-1">
                                                                                        <input
                                                                                            type="checkbox"
                                                                                            checked={isChecked}
                                                                                            onChange={() => handlePermissionToggle(perm.id)}
                                                                                            className="rounded border-gray-300 text-green-600 focus:ring-green-500/20 w-4 h-4"
                                                                                        />
                                                                                    </label>
                                                                                );
                                                                            };

                                                                            return (
                                                                                <tr key={row.resourceName} className="hover:bg-gray-50/30 transition-colors">
                                                                                    <td className="px-5 py-3">
                                                                                        <div className="font-bold text-gray-800 uppercase tracking-tight">
                                                                                            {formatResourceName(row.resourceName)}
                                                                                        </div>
                                                                                        <div className="text-[9px] text-gray-400 font-medium font-mono lowercase">
                                                                                            {row.resourceName}
                                                                                        </div>
                                                                                    </td>
                                                                                    <td className="px-3 py-3 text-center">
                                                                                        <label className="flex items-center justify-center cursor-pointer">
                                                                                            <input
                                                                                                type="checkbox"
                                                                                                checked={isAllChecked}
                                                                                                onChange={() => toggleRowPermissions(row.permissions)}
                                                                                                className="rounded border-gray-300 text-blue-600 focus:ring-blue-500/20 w-4 h-4"
                                                                                            />
                                                                                        </label>
                                                                                    </td>
                                                                                    <td className="px-3 py-3 text-center">{renderCheckbox(viewPerm)}</td>
                                                                                    <td className="px-3 py-3 text-center">{renderCheckbox(createPerm)}</td>
                                                                                    <td className="px-3 py-3 text-center">{renderCheckbox(editPerm)}</td>
                                                                                    <td className="px-3 py-3 text-center">{renderCheckbox(deletePerm)}</td>
                                                                                    <td className="px-3 py-3 text-center">{renderCheckbox(exportPerm)}</td>
                                                                                    <td className="px-3 py-3 text-center">{renderCheckbox(importPerm)}</td>
                                                                                    <td className="px-3 py-3 text-center">
                                                                                        {otherPerms.length > 0 ? (
                                                                                            <div className="flex flex-col gap-1 items-center justify-center">
                                                                                                {otherPerms.map(p => {
                                                                                                    const isChecked = roleForm.data.permissions.includes(p.id);
                                                                                                    return (
                                                                                                        <label key={p.id} className="flex items-center gap-1 cursor-pointer text-[9px] bg-gray-50 border border-gray-100 rounded-md px-1.5 py-0.5 hover:bg-gray-100 transition-colors">
                                                                                                            <input
                                                                                                                type="checkbox"
                                                                                                                checked={isChecked}
                                                                                                                onChange={() => handlePermissionToggle(p.id)}
                                                                                                                className="rounded border-gray-300 text-green-600 focus:ring-green-500/20 w-3 h-3"
                                                                                                            />
                                                                                                            <span className="font-mono text-gray-500 font-bold uppercase tracking-widest text-[8px]">{p.action}</span>
                                                                                                        </label>
                                                                                                    );
                                                                                                })}
                                                                                            </div>
                                                                                        ) : (
                                                                                            <span className="text-gray-300">-</span>
                                                                                        )}
                                                                                    </td>
                                                                                </tr>
                                                                            );
                                                                        })}
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="bg-gray-50 px-8 py-6 rounded-b-[2.5rem]">
                                    <button
                                        type="submit"
                                        disabled={roleForm.processing}
                                        className="w-full flex items-center justify-center gap-3 px-6 py-5 bg-gray-900 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-xl shadow-gray-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50"
                                    >
                                        <Save className="w-4 h-4" />
                                        {editRoleId ? 'PERBARUI ROLE' : 'SIMPAN ROLE'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
