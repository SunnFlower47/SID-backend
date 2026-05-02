import React from 'react';

export default function SkeletonTable({ rows = 5, columns = 5 }) {
    return (
        <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-pulse">
            <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div className="h-6 w-48 bg-gray-200 rounded"></div>
                <div className="h-6 w-20 bg-gray-100 rounded-full"></div>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full text-left text-sm">
                    <thead className="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            {[...Array(columns)].map((_, i) => (
                                <th key={i} className="px-6 py-4">
                                    <div className="h-3 w-20 bg-gray-200 rounded"></div>
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-50">
                        {[...Array(rows)].map((_, rowIndex) => (
                            <tr key={rowIndex}>
                                {[...Array(columns)].map((_, colIndex) => (
                                    <td key={colIndex} className="px-6 py-6">
                                        <div className="flex flex-col gap-2">
                                            <div className={`h-3 bg-gray-100 rounded ${colIndex === 0 ? 'w-32' : 'w-24'}`}></div>
                                            {colIndex === 0 && <div className="h-2 w-20 bg-gray-50 rounded"></div>}
                                        </div>
                                    </td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            <div className="p-6 border-t border-gray-50 bg-gray-50/30 flex items-center justify-between">
                <div className="h-3 w-32 bg-gray-100 rounded"></div>
                <div className="flex gap-1">
                    {[...Array(3)].map((_, i) => (
                        <div key={i} className="h-8 w-8 bg-gray-100 rounded-lg"></div>
                    ))}
                </div>
            </div>
        </div>
    );
}
