const fs = require('fs');
const path = require('path');

const dirs = [
    'd:/SISTEM-DESA-CIBATU/sistem-desa-cibatu/resources/js/Pages/Tenant/Sekretariat',
    'd:/SISTEM-DESA-CIBATU/sistem-desa-cibatu/resources/js/Components/Administrasi'
];

function processFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    let changed = false;

    // Check if it has the raw filter UI we want to replace
    if (!content.includes('TUTUP PANEL') || !content.includes('BUKA FILTER')) {
        return;
    }

    // Add FilterContainer import
    if (!content.includes('FilterContainer')) {
        content = content.replace(/(import .*?;)/, "$1\nimport { FilterContainer } from '@/Components/Shared';");
        changed = true;
    }

    // Replace the block
    const blockRegex = /<div className="mb-6 space-y-4">[\s\S]*?\{showFilters && \([\s\S]*?(<form[\s\S]*?<\/form>)[\s\S]*?\)\}?\s*<\/div>/;
    
    if (blockRegex.test(content)) {
        content = content.replace(blockRegex, (match, formContent) => {
            // Remove animate-in from the form if it was added, because FilterContainer handles it
            let cleanForm = formContent.replace(/animate-in slide-in-from-top-2 duration-300\s*/, '');
            return `<FilterContainer hasActiveFilters={hasActiveFilters}>\n                    ${cleanForm}\n                </FilterContainer>`;
        });
        changed = true;
    }

    // Remove old showFilters state if it exists, since FilterContainer handles it
    const stateRegex = /const \[showFilters, setShowFilters\] = useState\([^)]+\);/;
    if (stateRegex.test(content)) {
        content = content.replace(stateRegex, '');
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
        } else if (file.endsWith('.jsx')) {
            processFile(fullPath);
        }
    });
}

dirs.forEach(traverseDir);
