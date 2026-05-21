import Swal from 'sweetalert2';

/**
 * useSwalDelete — Custom hook untuk menghilangkan ~20 baris konfigurasi SweetAlert
 * yang identik di setiap Index page.
 *
 * Usage:
 *   const confirmDelete = useSwalDelete();
 *
 *   // Dengan route name:
 *   confirmDelete({ name: 'Nama Item', onConfirm: () => router.delete(route('xxx.destroy', id)) });
 *
 *   // Atau format singkat:
 *   confirmDelete('Nama Item', () => router.delete(route('xxx.destroy', id)));
 *
 * @returns {Function} confirmDelete(nameOrOptions, onConfirmFn?)
 *
 * Konfigurasi default SweetAlert yang distandardkan dari audit semua Index pages:
 * - popup: rounded-3xl border-none shadow-2xl
 * - confirmButtonColor: '#ef4444' (red-500)
 * - cancelButtonColor: '#f3f4f6' (gray-100)
 * - text: warna red-600 untuk nama item yang dihapus
 */
export function useSwalDelete() {
    return function confirmDelete(nameOrOptions, onConfirmFn) {
        // Support 2 format pemanggilan
        let name, onConfirm, customHtml, title;

        if (typeof nameOrOptions === 'string') {
            name = nameOrOptions;
            onConfirm = onConfirmFn;
        } else {
            name = nameOrOptions?.name ?? nameOrOptions?.nama ?? '';
            onConfirm = nameOrOptions?.onConfirm ?? onConfirmFn;
            customHtml = nameOrOptions?.html;
            title = nameOrOptions?.title;
        }

        return Swal.fire({
            title: title ?? 'KONFIRMASI HAPUS',
            html: customHtml ?? (
                name
                    ? `Hapus <b class="text-red-600">${name}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`
                    : 'Yakin ingin menghapus data ini?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>'
            ),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATALKAN',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500',
            },
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    };
}

export default useSwalDelete;
