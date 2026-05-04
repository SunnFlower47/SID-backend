import React from 'react';
import { FileText, ChevronRight } from 'lucide-react';

const TypeSelector = ({ suratTypes, onSelectType }) => {
    return (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-in slide-in-from-bottom-6 duration-700">
            {suratTypes.map((type) => (
                <button 
                    key={type.id}
                    onClick={() => onSelectType(type)}
                    className="group relative bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl hover:scale-[1.03] transition-all duration-500 text-left overflow-hidden"
                >
                    <div className="absolute top-0 right-0 w-24 h-24 bg-gray-50 rounded-full -mr-12 -mt-12 group-hover:bg-green-50 transition-colors"></div>
                    <div className="relative z-10">
                        <div className="w-12 h-12 bg-gray-50 group-hover:bg-green-100 rounded-2xl flex items-center justify-center mb-4 transition-colors">
                            <FileText className="w-6 h-6 text-gray-400 group-hover:text-green-600" />
                        </div>
                        <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter leading-tight group-hover:text-green-700">{type.nama}</h3>
                        <div className="flex items-center gap-2 mt-2">
                            <span className="px-2 py-0.5 bg-gray-100 text-gray-500 text-[9px] font-black rounded uppercase tracking-wider">{type.kode}</span>
                        </div>
                        <p className="text-[10px] text-gray-400 mt-4 line-clamp-2 font-bold uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                            Pilih untuk mulai mengisi form
                        </p>
                    </div>
                    <div className="absolute bottom-4 right-4 text-gray-200 group-hover:text-green-200 group-hover:translate-x-1 transition-all">
                        <ChevronRight className="w-8 h-8" />
                    </div>
                </button>
            ))}
        </div>
    );
};

export default TypeSelector;
