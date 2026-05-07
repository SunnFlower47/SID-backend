import React from 'react';

export default function SkeletonStats({ count = 4 }) {
    return (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            {[...Array(count)].map((_, index) => (
                <div 
                    key={index} 
                    className="bg-white rounded-2xl p-3 sm:p-4 border border-gray-100 shadow-sm flex items-center gap-4 animate-pulse"
                >
                    <div className="w-12 h-12 rounded-xl bg-gray-100 flex-shrink-0"></div>
                    <div className="flex-1">
                        <div className="h-2 w-16 bg-gray-100 rounded mb-2"></div>
                        <div className="h-6 w-12 bg-gray-200 rounded"></div>
                    </div>
                </div>
            ))}
        </div>
    );
}
