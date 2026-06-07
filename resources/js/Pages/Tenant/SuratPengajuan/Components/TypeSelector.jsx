import React, { useState } from 'react';
import * as LucideIcons from 'lucide-react';
import { FileText, ChevronRight, Search } from 'lucide-react';

const TypeSelector = ({ suratTypes, onSelectType }) => {
    const [search, setSearch] = useState('');

    const filteredTypes = suratTypes.filter(type => 
        type.nama.toLowerCase().includes(search.toLowerCase()) || 
        type.kode.toLowerCase().includes(search.toLowerCase())
    );

    // Icon mapping logic
    const getIcon = (iconName, className) => {
        if (!iconName) return <FileText className={className} />;
        
        // Render dinamik menggunakan LucideIcons
        const IconComponent = LucideIcons[iconName] || FileText;
        return <IconComponent className={className} />;
    };

    const getIconWrapperClass = (colorName) => {
        const colors = {
            blue: "bg-blue-50 group-hover:bg-blue-100",
            green: "bg-green-50 group-hover:bg-green-100",
            purple: "bg-purple-50 group-hover:bg-purple-100",
            orange: "bg-orange-50 group-hover:bg-orange-100",
            red: "bg-red-50 group-hover:bg-red-100",
            pink: "bg-pink-50 group-hover:bg-pink-100",
            yellow: "bg-yellow-50 group-hover:bg-yellow-100",
        };
        return colors[colorName] || "bg-green-50 group-hover:bg-green-100";
    };

    const getIconClass = (colorName) => {
        const colors = {
            blue: "text-gray-400 group-hover:text-blue-600",
            green: "text-gray-400 group-hover:text-green-600",
            purple: "text-gray-400 group-hover:text-purple-600",
            orange: "text-gray-400 group-hover:text-orange-600",
            red: "text-gray-400 group-hover:text-red-600",
            pink: "text-gray-400 group-hover:text-pink-600",
            yellow: "text-gray-400 group-hover:text-yellow-600",
        };
        return colors[colorName] || "text-gray-400 group-hover:text-green-600";
    };

    const getTitleClass = (colorName) => {
        const colors = {
            blue: "group-hover:text-blue-700",
            green: "group-hover:text-green-700",
            purple: "group-hover:text-purple-700",
            orange: "group-hover:text-orange-700",
            red: "group-hover:text-red-700",
            pink: "group-hover:text-pink-700",
            yellow: "group-hover:text-yellow-700",
        };
        return colors[colorName] || "group-hover:text-green-700";
    };

    const getPilihDokumenClass = (colorName) => {
        const colors = {
            blue: "text-blue-600",
            green: "text-green-600",
            purple: "text-purple-600",
            orange: "text-orange-600",
            red: "text-red-600",
            pink: "text-pink-600",
            yellow: "text-yellow-600",
        };
        return colors[colorName] || "text-green-600";
    };

    const getWatermarkClass = (colorName) => {
        const colors = {
            blue: "text-gray-100 group-hover:text-blue-200",
            green: "text-gray-100 group-hover:text-green-200",
            purple: "text-gray-100 group-hover:text-purple-200",
            orange: "text-gray-100 group-hover:text-orange-200",
            red: "text-gray-100 group-hover:text-red-200",
            pink: "text-gray-100 group-hover:text-pink-200",
            yellow: "text-gray-100 group-hover:text-yellow-200",
        };
        return colors[colorName] || "text-gray-100 group-hover:text-green-200";
    };

    const getHoverBgClass = (colorName) => {
        const colors = {
            blue: "group-hover:bg-blue-50",
            green: "group-hover:bg-green-50",
            purple: "group-hover:bg-purple-50",
            orange: "group-hover:bg-orange-50",
            red: "group-hover:bg-red-50",
            pink: "group-hover:bg-pink-50",
            yellow: "group-hover:bg-yellow-50",
        };
        return colors[colorName] || "group-hover:bg-green-50";
    };

    return (
        <div className="space-y-6">
            {/* Search Bar */}
            <div className="relative animate-in slide-in-from-top-4 duration-500">
                <div className="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <Search className="h-5 w-5 text-gray-400" />
                </div>
                <input
                    type="text"
                    className="block w-full pl-14 pr-5 py-4 bg-white border-2 border-gray-100 rounded-3xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition-all"
                    placeholder="CARI JENIS SURAT (CONTOH: SKU, DOMISILI)..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                />
            </div>

            {/* Grid Kartu Surat */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-in slide-in-from-bottom-6 duration-700">
                {filteredTypes.length > 0 ? (
                    filteredTypes.map((type) => (
                        <button 
                            key={type.id}
                            onClick={() => onSelectType(type)}
                            className="group relative bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden flex flex-col text-left h-full min-h-[220px]"
                        >
                            <div className="p-6 flex-1 flex flex-col relative z-10">
                                <div className="flex justify-between items-start mb-6">
                                    <div className={`p-4 rounded-2xl transition-colors duration-300 ${getIconWrapperClass(type.color)}`}>
                                        {getIcon(type.icon, `w-7 h-7 transition-colors duration-300 ${getIconClass(type.color)}`)}
                                    </div>
                                </div>
                                
                                <div className="flex-1 flex flex-col justify-end">
                                    <h3 className={`text-sm font-black text-gray-900 tracking-tight uppercase italic leading-tight transition-colors mb-2 ${getTitleClass(type.color)}`}>
                                        {type.nama}
                                    </h3>
                                    <div className="flex items-center gap-2 mt-auto">
                                        <span className="px-2.5 py-1 bg-gray-100 text-gray-500 text-[10px] font-black rounded-lg uppercase tracking-wider">{type.kode}</span>
                                    </div>
                                </div>

                                <div className="mt-4 pt-4 border-t border-gray-50 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                                    <p className={`text-[10px] font-black uppercase tracking-widest flex items-center gap-1 ${getPilihDokumenClass(type.color)}`}>
                                        BUAT SURAT INI
                                        <ChevronRight className="w-3 h-3" />
                                    </p>
                                </div>
                            </div>
                            
                            <div className={`absolute -bottom-6 -right-6 transition-all duration-500 transform group-hover:scale-110 group-hover:-rotate-12 ${getWatermarkClass(type.color)}`}>
                                {getIcon(type.icon, "w-32 h-32 opacity-10")}
                            </div>
                        </button>
                    ))
                ) : (
                    <div className="col-span-full py-16 text-center bg-white rounded-3xl border-2 border-dashed border-gray-200">
                        <div className="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <Search className="h-8 w-8 text-gray-300" />
                        </div>
                        <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">TIDAK ADA JENIS SURAT DITEMUKAN</h3>
                        <p className="text-[10px] font-bold text-gray-400 mt-2 uppercase tracking-widest">Coba gunakan kata kunci lain untuk pencarian</p>
                    </div>
                )}
            </div>
        </div>
    );
};

export default TypeSelector;
