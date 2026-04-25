// Global functions that can be accessed from onclick handlers
// Function to auto-set hari based on tanggal
function setHariFromTanggal() {
    const tanggalInput = document.getElementById('tanggal_mutasi_kematian');
    const hariSelect = document.getElementById('hari_meninggal');

    if (tanggalInput && hariSelect && tanggalInput.value) {
        const date = new Date(tanggalInput.value);
        const hariNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const hariName = hariNames[date.getDay()];

        // Set the selected option
        for (let option of hariSelect.options) {
            if (option.value === hariName) {
                option.selected = true;
                break;
            }
        }
    }
}

// Function to auto-set hari pemakaman based on tanggal pemakaman
function setHariPemakamanFromTanggal() {
    const tanggalInput = document.getElementById('tanggal_pemakaman');
    const hariSelect = document.getElementById('hari_pemakaman');

    if (tanggalInput && hariSelect && tanggalInput.value) {
        const date = new Date(tanggalInput.value);
        const hariNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const hariName = hariNames[date.getDay()];

        // Set the selected option
        for (let option of hariSelect.options) {
            if (option.value === hariName) {
                option.selected = true;
                break;
            }
        }
    }
}

// Function to clear NKK selection
function clearNKKSelection(formType) {
    const nkkInput = document.getElementById(`nkk_${formType}`);
    const selectedNKKDiv = document.getElementById(`selected_nkk_${formType}`);
    const searchInput = document.getElementById(`nkk_search_${formType}`);

    if (nkkInput) nkkInput.value = '';
    if (selectedNKKDiv) selectedNKKDiv.classList.add('hidden');
    if (searchInput) searchInput.value = '';
}

// Function to clear penduduk selection
function clearPendudukSelection(formType) {
    // Handle special case for pisah_kk form
    const actualFormType = formType === 'pisah_kk' ? 'pisah' : formType;

    const pendudukIdInput = document.getElementById(`penduduk_id_${actualFormType}`);
    const selectedPendudukDiv = document.getElementById(`selected_penduduk_${actualFormType}`);
    const searchInput = document.getElementById(`penduduk_search_${actualFormType}`);

    if (pendudukIdInput) pendudukIdInput.value = '';
    if (selectedPendudukDiv) selectedPendudukDiv.classList.add('hidden');
    if (searchInput) searchInput.value = '';
    
    if (actualFormType === 'pindah_keluar') {
        const container = document.getElementById('anggota_keluarga_container_pindah_keluar');
        if (container) container.classList.add('hidden');
    }
}
