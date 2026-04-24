/**
 * Auto-format input fields untuk meningkatkan UX
 */

document.addEventListener('DOMContentLoaded', function() {
    // Auto-format NIK input
    const nikInputs = document.querySelectorAll('input[name="nik"]');
    nikInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            // Hanya allow angka
            let value = e.target.value.replace(/[^0-9]/g, '');

            // Limit to 16 digits
            if (value.length > 16) {
                value = value.substring(0, 16);
            }

            e.target.value = value;

            // Visual feedback
            if (value.length === 16) {
                e.target.classList.remove('border-red-500');
                e.target.classList.add('border-green-500');
            } else {
                e.target.classList.remove('border-green-500');
                e.target.classList.add('border-red-500');
            }
        });

        input.addEventListener('blur', function(e) {
            if (e.target.value.length > 0 && e.target.value.length < 16) {
                showError(e.target, 'NIK harus 16 digit');
            } else {
                hideError(e.target);
            }
        });
    });

    // Auto-format RT/RW input
    const rtRwInputs = document.querySelectorAll('input[name="rt"], input[name="rw"]');
    rtRwInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            // Hanya allow angka
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    });

    // Auto-format nama input (title case)
    const namaInputs = document.querySelectorAll('input[name="nama"], input[name="tempat_lahir"]');
    namaInputs.forEach(input => {
        input.addEventListener('blur', function(e) {
            if (e.target.value) {
                e.target.value = toTitleCase(e.target.value);
            }
        });
    });

    // Auto-format alamat input (title case)
    const alamatInputs = document.querySelectorAll('input[name="alamat"], textarea[name="alamat"]');
    alamatInputs.forEach(input => {
        input.addEventListener('blur', function(e) {
            if (e.target.value) {
                e.target.value = toTitleCase(e.target.value);
            }
        });
    });

    // Real-time validation untuk tanggal lahir
    const tanggalLahirInputs = document.querySelectorAll('input[name="tanggal_lahir"]');
    tanggalLahirInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            validateBirthDate(e.target);
        });
    });

    // Real-time validation untuk status perkawinan vs umur
    const statusPerkawinanSelects = document.querySelectorAll('select[name="status_perkawinan"]');
    statusPerkawinanSelects.forEach(select => {
        select.addEventListener('change', function(e) {
            validateMaritalStatus(e.target);
        });
    });

    // Real-time validation untuk kedudukan keluarga vs jenis kelamin
    const kedudukanKeluargaSelects = document.querySelectorAll('select[name="kedudukan_keluarga"]');
    kedudukanKeluargaSelects.forEach(select => {
        select.addEventListener('change', function(e) {
            validateFamilyPosition(e.target);
        });
    });
});

/**
 * Convert string to title case
 */
function toTitleCase(str) {
    return str.toLowerCase().replace(/\b\w/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

/**
 * Validate birth date
 */
function validateBirthDate(input) {
    const birthDate = new Date(input.value);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();

    hideError(input);

    if (birthDate > today) {
        showError(input, 'Tanggal lahir tidak boleh di masa depan');
        return false;
    }

    if (age > 120) {
        showError(input, 'Umur terlalu tua (lebih dari 120 tahun)');
        return false;
    }

    if (age < 0) {
        showError(input, 'Tanggal lahir tidak valid');
        return false;
    }

    return true;
}

/**
 * Validate marital status based on age
 */
function validateMaritalStatus(select) {
    const form = select.closest('form');
    const tanggalLahirInput = form.querySelector('input[name="tanggal_lahir"]');

    if (!tanggalLahirInput || !tanggalLahirInput.value) {
        return;
    }

    const birthDate = new Date(tanggalLahirInput.value);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();

    hideError(select);

    if (age < 10 && ['Kawin', 'Cerai Hidup', 'Cerai Mati'].includes(select.value)) {
        showError(select, `Anak berusia ${age} tahun tidak boleh memiliki status perkawinan '${select.value}'`);
        return false;
    }

    if (age < 16 && ['Kawin', 'Cerai Hidup', 'Cerai Mati'].includes(select.value)) {
        showError(select, `Anak berusia ${age} tahun belum memenuhi syarat usia pernikahan (minimal 16 tahun)`);
        return false;
    }

    return true;
}

/**
 * Validate family position based on gender
 */
function validateFamilyPosition(select) {
    const form = select.closest('form');
    const jenisKelaminSelect = form.querySelector('select[name="jenis_kelamin"]');

    if (!jenisKelaminSelect) {
        return;
    }

    hideError(select);

    if (select.value === 'Istri' && !['P', 'PEREMPUAN', 'Perempuan'].includes(jenisKelaminSelect.value)) {
        showError(select, "Kedudukan 'Istri' hanya untuk jenis kelamin perempuan");
        return false;
    }

    return true;
}

/**
 * Show error message
 */
function showError(element, message) {
    hideError(element);

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-red-600 text-sm mt-1';
    errorDiv.textContent = message;

    element.classList.add('border-red-500');
    element.classList.remove('border-green-500');

    element.parentNode.appendChild(errorDiv);
}

/**
 * Hide error message
 */
function hideError(element) {
    const existingError = element.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }

    element.classList.remove('border-red-500');
}

/**
 * Format NIK with spaces for better readability (optional)
 */
function formatNikWithSpaces(nik) {
    if (nik.length === 16) {
        return nik.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5 $6 $7');
    }
    return nik;
}

/**
 * Validate NIK structure (basic validation)
 */
function validateNikStructure(nik) {
    if (nik.length !== 16) {
        return false;
    }

    // Check if all digits
    if (!/^\d{16}$/.test(nik)) {
        return false;
    }

    // Check province code (32 = Jawa Barat)
    const province = nik.substring(0, 2);
    if (province !== '32') {
        return false;
    }

    // Check date validity
    const day = parseInt(nik.substring(6, 8));
    const month = parseInt(nik.substring(8, 10));
    const year = parseInt(nik.substring(10, 12));

    if (day < 1 || day > 31 || month < 1 || month > 12) {
        return false;
    }

    return true;
}

