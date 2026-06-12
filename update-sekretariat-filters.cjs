const fs = require('fs');
const path = require('path');

const dir = 'd:/SISTEM-DESA-CIBATU/sistem-desa-cibatu/resources/js/Pages/Tenant/Sekretariat';

function processFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    let changed = false;

    // Check if it has a form
    if (!content.includes('<form onSubmit={handleSearch')) {
        return;
    }

    // 1. Add cn import
    if (!content.includes('import { cn }')) {
        content = content.replace(/(import React.*?;)/, "$1\nimport { cn } from '@/lib/utils';");
        changed = true;
    }

    // 2. Add Filter to lucide-react
    if (!content.includes('Filter,') && !content.includes(', Filter')) {
        content = content.replace(/from 'lucide-react';/, ", Filter } from 'lucide-react';").replace(/} , Filter/, ', Filter }');
        changed = true;
    }

    // 3. Add showFilters state
    if (!content.includes('const [showFilters')) {
        content = content.replace(/(const \[search, setSearch\].*?;)/, "$1\n    const hasActiveFilters = filters?.search || filters?.status || filters?.jenis;\n    const [showFilters, setShowFilters] = useState(hasActiveFilters ? true : false);");
        changed = true;
    }

    // 4. Wrap the form
    const formRegex = /(<form onSubmit={handleSearch[^\n]*\n(?:.*?\n)+?[ \t]*<\/form>)/;
    const match = content.match(formRegex);
    if (match && !content.includes('BUKA FILTER')) {
        const formStr = match[1];
        
        // Remove mb-6 from form class
        const newFormStr = formStr.replace('mb-6', '').replace('animate-in', '').replace('slide-in-from-top-2', '').replace('duration-300', '').replace('className="', 'className="animate-in slide-in-from-top-2 duration-300 ');

        const wrapper = `
                <div className="mb-6 space-y-4">
                    <div className="flex justify-between items-center bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm transition-all">
                        <div className="flex items-center gap-2 sm:gap-4">
                            <div className="w-8 h-8 sm:w-12 sm:h-12 bg-green-50 rounded-xl flex items-center justify-center">
                                <Search className="w-4 h-4 sm:w-6 sm:h-6 text-green-600" />
                            </div>
                            <div>
                                <h3 className="text-[10px] sm:text-sm font-black text-gray-950 uppercase italic tracking-tighter leading-none mb-1 text-left">Konfigurasi Data</h3>
                                <p className="hidden sm:block text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">Pencarian & Filter Buku</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setShowFilters(!showFilters)}
                            className={cn(
                                "flex items-center px-4 py-2 sm:px-6 sm:py-3 rounded-xl text-[9px] sm:text-xs font-black transition-all border shadow-sm active:scale-95",
                                showFilters
                                    ? "bg-yellow-400 text-yellow-900 border-yellow-500 shadow-yellow-400/20"
                                    : "bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100"
                            )}
                        >
                            <Filter className="w-3 h-3 sm:w-4 sm:h-4 mr-2" />
                            {showFilters ? 'TUTUP PANEL' : 'BUKA FILTER'}
                        </button>
                    </div>

                    {showFilters && (
${newFormStr.split('\n').map(l => '    ' + l).join('\n')}
                    )}
                </div>
`;
        content = content.replace(formStr, wrapper.trim());
        changed = true;
    }

    if (changed) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log('Updated ' + filePath);
    }
}

function traverseDir(currentDir) {
    fs.readdirSync(currentDir).forEach(file => {
        let fullPath = path.join(currentDir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            traverseDir(fullPath);
        } else if (file === 'Index.jsx') {
            processFile(fullPath);
        }
    });
}

traverseDir(dir);
