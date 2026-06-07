import React from 'react';
import { cn } from '@/lib/utils';

/**
 * DataTable Component
 * Komponen tabel dengan styling garis pembatas (border) vertikal antar kolom,
 * teks header rata tengah, dan font bold biru dongker (slate/blue).
 * 
 * @param {Array} columns - Array konfigurasi kolom [{ header: 'Nama', accessor: 'nama', render: (row, idx) => (...), headerClassName: '', className: '' }]
 * @param {Array} data - Array data baris
 * @param {React.ReactNode} emptyState - Komponen untuk state kosong
 * @param {string} className - Class tambahan untuk container luar tabel
 * @param {boolean} borderedBody - Jika true, memberikan garis batas vertikal juga di body (td), default true.
 */
export default function DataTable({
    columns = [],
    data = [],
    emptyState,
    className,
    borderedBody = true
}) {
    return (
        <div className={cn("overflow-x-auto", className)}>
            <table className="w-full border-collapse">
                <thead>
                    <tr className="bg-slate-50 border-b-2 border-slate-200/70">
                        {columns.map((col, idx) => (
                            <th
                                key={idx}
                                className={cn(
                                    "px-4 py-4 text-[10px] font-black text-slate-800 uppercase tracking-widest text-center whitespace-nowrap",
                                    idx > 0 && "border-l-2 border-slate-200/70", // Garis pembatas antar kolom di header (warna shade ~150)
                                    col.headerClassName
                                )}
                            >
                                {col.header}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody className="bg-white">
                    {data && data.length > 0 ? (
                        data.map((row, rowIndex) => (
                            <tr
                                key={row.id || rowIndex}
                                className="group hover:bg-slate-50/50 transition-colors border-b border-gray-50 last:border-0"
                            >
                                {columns.map((col, colIndex) => (
                                    <td
                                        key={colIndex}
                                        className={cn(
                                            "px-4 py-4 text-sm text-gray-700",
                                            borderedBody && colIndex > 0 && "border-l border-gray-50", // Garis pembatas antar kolom di body (lebih tipis/samar)
                                            col.className
                                        )}
                                    >
                                        {col.render ? col.render(row, rowIndex) : row[col.accessor]}
                                    </td>
                                ))}
                            </tr>
                        ))
                    ) : (
                        <tr>
                            <td colSpan={columns.length} className="p-0 border-0">
                                {emptyState}
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>
        </div>
    );
}
