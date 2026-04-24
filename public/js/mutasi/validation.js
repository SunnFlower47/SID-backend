// Form validation functions
function validateForm(jenisMutasi) {
    switch(jenisMutasi) {
        case 'kelahiran': return validateKelahiran();
        case 'kematian': return validateKematian();
        case 'pindah_masuk': return validatePindahMasuk();
        case 'pindah_keluar': return validatePindahKeluar();
        case 'pindah_rt_rw': return validatePindahRTRW();
        case 'pisah_kk': return validatePisahKK();
        default: return true;
    }
}

function validateRTFormat(rtValue) {
    if (!rtValue || rtValue.trim() === '') {
        return { valid: false, message: 'RT harus diisi' };
    }

    // Check if RT is 3 digits
    if (!/^[0-9]{3}$/.test(rtValue)) {
        return { valid: false, message: 'RT harus terdiri dari 3 digit angka (contoh: 001, 002, 003)' };
    }

    // Check if RT is not 000
    if (parseInt(rtValue) === 0) {
        return { valid: false, message: 'RT tidak boleh 000' };
    }

    // Check if RT is between 001-999
    if (parseInt(rtValue) < 1 || parseInt(rtValue) > 999) {
        return { valid: false, message: 'RT harus antara 001-999' };
    }

    return { valid: true, message: '' };
}

function validateKelahiran() {
    // Check if NKK is selected first
    const nkkField = document.getElementById('nkk_kelahiran');

    if (!nkkField || !nkkField.value.trim()) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'No KK harus dipilih!',
            confirmButtonColor: '#ef4444'
        });
        return false;
    }

    // Block submit jika NIK bayi sudah terdaftar (berdasarkan cek realtime)
    const nikExistingInfo = document.getElementById('nikExistingInfoKelahiran');
    if (nikExistingInfo && !nikExistingInfo.classList.contains('hidden')) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'NIK bayi sudah terdaftar. Gunakan NIK yang lain.',
            confirmButtonColor: '#ef4444'
        });
        return false;
    }

    // Validate required fields with correct IDs
    const requiredFields = [
        { id: 'nama_bayi', name: 'Nama Bayi' },
        { id: 'jenis_kelamin_bayi', name: 'Jenis Kelamin' },
        { id: 'tempat_lahir', name: 'Tempat Lahir' },
        { id: 'tanggal_lahir', name: 'Tanggal Lahir' },
        { id: 'nama_ayah', name: 'Nama Ayah' },
        { id: 'nama_ibu', name: 'Nama Ibu' },
        { id: 'tanggal_mutasi_kelahiran', name: 'Tanggal Mutasi' }
    ];

    for (const field of requiredFields) {
        const element = document.getElementById(field.id);
        if (!element || !element.value.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: `${field.name} harus diisi!`,
                confirmButtonColor: '#ef4444'
            });
            if (element) element.focus();
            return false;
        }
    }

    // Validate RT format if RT field exists
    const rtField = document.getElementById('rt_bayi');
    if (rtField && rtField.value.trim()) {
        const rtValidation = validateRTFormat(rtField.value.trim());
        if (!rtValidation.valid) {
            showError(rtValidation.message);
            rtField.focus();
            return false;
        }
    }

    return true;
}

function validateKematian() {
    // Check if penduduk is selected
    const pendudukIdField = document.getElementById('penduduk_id_kematian');

    if (!pendudukIdField || !pendudukIdField.value.trim()) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Penduduk yang meninggal harus dipilih!',
            confirmButtonColor: '#ef4444'
        });
        return false;
    }

    const required = ['tanggal_mutasi_kematian', 'hari_meninggal', 'jam_meninggal', 'bertempat_di', 'hari_pemakaman', 'tanggal_pemakaman', 'jam_pemakaman', 'lokasi_pemakaman', 'alasan'];

    for (const fieldName of required) {
        const field = document.getElementById(fieldName);

        if (!field || !field.value.trim()) {
            const fieldLabel = fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: `${fieldLabel} harus diisi!`,
                confirmButtonColor: '#ef4444'
            });
            if (field) field.focus();
            return false;
        }
    }

    return true;
}

function validatePindahMasuk() {
    // Check KK option first
    const kkOption = document.querySelector('input[name="kk_option_pindah_masuk"]:checked');
    if (!kkOption) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Pilih opsi KK terlebih dahulu!',
            confirmButtonColor: '#ef4444'
        });
        return false;
    }

    // Basic required fields
    const required = ['nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'kedudukan_keluarga', 'alamat', 'rt', 'rw', 'kategori_mutasi', 'asal_tujuan', 'tanggal_mutasi'];

    for (let field of required) {
        const element = document.getElementById(field + '_pindah_masuk') || document.getElementById(field);
        if (!element || !element.value.trim()) {
            showError(`Field ${field.replace('_', ' ')} harus diisi!`);
            if (element) element.focus();
            return false;
        }
    }

    // Validate RT format
    const rtField = document.getElementById('rt_pindah_masuk') || document.getElementById('rt');
    if (rtField && rtField.value.trim()) {
        const rtValidation = validateRTFormat(rtField.value.trim());
        if (!rtValidation.valid) {
            showError(rtValidation.message);
            rtField.focus();
            return false;
        }
    }

    // Check NKK based on option
    if (kkOption.value === 'existing') {
        const nkkExisting = document.getElementById('nkk_existing_pindah_masuk');
        if (!nkkExisting || !nkkExisting.value) {
            showError('Pilih KK yang sudah ada!');
            return false;
        }
    } else if (kkOption.value === 'new') {
        const nkkNew = document.getElementById('nkk_new_pindah_masuk');
        if (!nkkNew || !nkkNew.value.trim()) {
            showError('No KK baru harus diisi!');
            return false;
        }
    }

    return true;
}

function validatePindahKeluar() {
    const pendudukId = document.getElementById('penduduk_id_pindah_keluar');
    const kategoriMutasi = document.getElementById('kategori_mutasi_pindah_keluar');
    const asalTujuan = document.getElementById('asal_tujuan_pindah_keluar');
    const tanggalMutasi = document.getElementById('tanggal_mutasi_pindah_keluar');
    const alasan = document.getElementById('alasan_pindah_keluar');

    if (!pendudukId || !pendudukId.value) {
        showError('Penduduk harus dipilih!');
        return false;
    }

    if (!kategoriMutasi || !kategoriMutasi.value) {
        showError('Kategori mutasi harus dipilih!');
        return false;
    }

    if (!asalTujuan || !asalTujuan.value.trim()) {
        showError('Tujuan pindah harus diisi!');
        return false;
    }

    if (!tanggalMutasi || !tanggalMutasi.value) {
        showError('Tanggal pindah harus diisi!');
        return false;
    }

    if (!alasan || !alasan.value.trim()) {
        showError('Alasan pindah harus diisi!');
        return false;
    }

    return true;
}

function validatePindahRTRW() {
    const nkk = document.getElementById('nkk_pindah_rt_rw');
    const rtTujuan = document.getElementById('rt_tujuan');
    const rwTujuan = document.getElementById('rw_tujuan');
    const tanggalMutasi = document.querySelector('input[name="tanggal_mutasi"]');

    if (!nkk || !nkk.value) {
        showError('No KK harus dipilih!');
        return false;
    }

    if (!rtTujuan || !rtTujuan.value) {
        showError('RT tujuan harus dipilih!');
        return false;
    }

    if (!rwTujuan || !rwTujuan.value) {
        showError('RW tujuan harus dipilih!');
        return false;
    }

    if (!tanggalMutasi || !tanggalMutasi.value) {
        showError('Tanggal pindah harus diisi!');
        return false;
    }

    // Validate RT format
    if (rtTujuan && rtTujuan.value.trim()) {
        const rtValidation = validateRTFormat(rtTujuan.value.trim());
        if (!rtValidation.valid) {
            showError(rtValidation.message);
            rtTujuan.focus();
            return false;
        }
    }

    return true;
}

function validatePisahKK() {
    const pendudukId = document.getElementById('penduduk_id_pisah');
    const kategoriMutasi = document.getElementById('kategori_mutasi_pisah');
    const tanggalMutasi = document.getElementById('tanggal_mutasi_pisah');

    if (!pendudukId || !pendudukId.value) {
        showError('Penduduk harus dipilih!');
        return false;
    }

    if (!kategoriMutasi || !kategoriMutasi.value) {
        showError('Kategori mutasi harus dipilih!');
        return false;
    }

    if (!tanggalMutasi || !tanggalMutasi.value) {
        showError('Tanggal pisah KK harus diisi!');
        return false;
    }

    // If not dalam_desa, check additional fields
    if (kategoriMutasi.value !== 'dalam_desa') {
        const alamat = document.getElementById('alamat_pisah');

        if (!alamat || !alamat.value.trim()) {
            showError('Alamat tujuan harus diisi!');
            return false;
        }
    } else {
        // Untuk dalam_desa, validasi berdasarkan opsi KK
        const kkOption = document.querySelector('input[name="kk_option"]:checked');

        if (!kkOption) {
            showError('Pilih opsi KK terlebih dahulu!');
            return false;
        }

        if (kkOption.value === 'new') {
            // Validasi untuk KK baru
            const rtField = document.getElementById('rt_baru_pisah');
            const rwField = document.getElementById('rw_baru_pisah');
            const alamatField = document.getElementById('alamat_baru_pisah');

            if (!rtField || !rtField.value) {
                showError('RT harus diisi untuk KK baru!');
                return false;
            }

            if (!rwField || !rwField.value) {
                showError('RW harus diisi untuk KK baru!');
                return false;
            }

            if (!alamatField || !alamatField.value.trim()) {
                showError('Alamat lengkap harus diisi untuk KK baru!');
                return false;
            }
        } else if (kkOption.value === 'existing') {
            // Validasi untuk gabung ke KK yang sudah ada
            const nkkExisting = document.getElementById('nkk_existing_pisah');

            if (!nkkExisting || !nkkExisting.value.trim()) {
                showError('Pilih KK yang sudah ada!');
                return false;
            }
        }
    }

    return true;
}

function validateRequiredFields(fields) {
    for (const fieldName of fields) {
        const field = document.getElementById(fieldName);
        if (!field || !field.value.trim()) {
            const fieldLabel = fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            showError(`${fieldLabel} harus diisi!`);
            if (field) field.focus();
            return false;
        }
    }
    return true;
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: message,
        confirmButtonColor: '#ef4444'
    });
}
