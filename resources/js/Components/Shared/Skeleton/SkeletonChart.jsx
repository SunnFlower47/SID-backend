import React from 'react';

export default function SkeletonChart({ height = '300px' }) {
    return (
        <div 
            style={{ borderRadius: '28px', height }}
            className="w-full bg-white p-8 border border-gray-100 shadow-sm overflow-hidden animate-pulse flex flex-col gap-6"
        >
            <div className="h-6 w-48 bg-gray-200 rounded italic"></div>
            <div className="flex-1 w-full bg-gray-50 rounded-2xl relative">
                <div className="absolute bottom-0 left-0 w-full flex items-end justify-around px-4 h-full">
                    {[...Array(6)].map((_, i) => (
                        <div key={i} className="bg-gray-100 rounded-t-lg w-12" style={{ height: `${20 + (i * 15)}%` }}></div>
                    ))}
                </div>
            </div>
        </div>
    );
}
