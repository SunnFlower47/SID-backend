import React from 'react';

export default function SkeletonActivity({ count = 5 }) {
    return (
        <div 
            style={{ borderRadius: '28px' }}
            className="bg-white p-8 border border-gray-100 shadow-sm flex flex-col h-full animate-pulse"
        >
            <div className="flex items-center justify-between mb-8">
                <div className="h-6 w-32 bg-gray-200 rounded"></div>
                <div className="h-5 w-5 bg-gray-100 rounded"></div>
            </div>
            <div className="space-y-6">
                {[...Array(count)].map((_, i) => (
                    <div key={i} className="flex items-center gap-4">
                        <div className="w-10 h-10 rounded-xl bg-gray-100 shrink-0"></div>
                        <div className="flex-1 space-y-2">
                            <div className="h-3 w-32 bg-gray-100 rounded"></div>
                            <div className="h-2 w-20 bg-gray-50 rounded"></div>
                        </div>
                        <div className="h-2 w-10 bg-gray-50 rounded shrink-0"></div>
                    </div>
                ))}
            </div>
            <div className="mt-8 h-10 w-full bg-gray-50 rounded-2xl"></div>
        </div>
    );
}
