// Form handlers and event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Form submission handler
    const mutasiForm = document.getElementById('mutasiForm');
    if (mutasiForm) {
        mutasiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const formData = new FormData(this);
            const jenisMutasi = formData.get('jenis_mutasi');

            if (!jenisMutasi) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Pilih jenis mutasi terlebih dahulu!',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            // Debug: Check penduduk_id before validation
            const pendudukIdField = document.getElementById('penduduk_id_kematian');
            if (pendudukIdField) {
                // Validation check
            }

            // Validate form before submission
            const validationResult = validateForm(jenisMutasi);

            if (!validationResult) {
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Sedang menyimpan data mutasi',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    // Handle error response
                    return response.text().then(errorText => {
                        throw new Error(`HTTP error! status: ${response.status}, body: ${errorText}`);
                    });
                }

                // Handle success response
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If it's HTML (redirect), return success
                    return { success: true, redirect: response.url };
                }
            })
            .then(data => {
                Swal.close();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Data mutasi berhasil disimpan!',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = '/mutasi';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Terjadi kesalahan saat menyimpan data!',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                Swal.close();

                // Handle different types of errors
                let errorMessage = 'Terjadi kesalahan saat menyimpan data!';

                if (error.message) {
                    errorMessage = error.message;
                } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
                    errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                } else if (error.name === 'AbortError') {
                    errorMessage = 'Permintaan dibatalkan.';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    confirmButtonColor: '#ef4444',
                    showCancelButton: true,
                    cancelButtonText: 'Tutup',
                    confirmButtonText: 'Coba Lagi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Retry form submission
                        mutasiForm.dispatchEvent(new Event('submit'));
                    }
                });
            });
        });
    }

    // Mutation type change handler
    const jenisMutasiSelect = document.getElementById('jenis_mutasi');
    if (jenisMutasiSelect) {
        jenisMutasiSelect.addEventListener('change', function() {
            showMutasiForm(this.value);
        });
    }

    // Function to show/hide forms based on mutation type
    function showMutasiForm(jenisMutasi) {
        // Hide all forms
        const forms = ['formKelahiran', 'formKematian', 'formPindahMasuk', 'formPindahKeluar', 'formPindahRTRW', 'formPisahKK'];
        forms.forEach(formId => {
            const form = document.getElementById(formId);
            if (form) form.style.display = 'none';
        });

        // Hide default message
        const defaultMessage = document.getElementById('defaultMessage');
        if (defaultMessage) defaultMessage.style.display = 'none';

        // Show selected form
        if (jenisMutasi) {
            let formId = '';
            switch(jenisMutasi) {
                case 'kelahiran':
                    formId = 'formKelahiran';
                    break;
                case 'kematian':
                    formId = 'formKematian';
                    break;
                case 'pindah_masuk':
                    formId = 'formPindahMasuk';
                    break;
                case 'pindah_keluar':
                    formId = 'formPindahKeluar';
                    break;
                case 'pindah_rt_rw':
                    formId = 'formPindahRTRW';
                    break;
                case 'pisah_kk':
                    formId = 'formPisahKK';
                    break;
            }

            const selectedForm = document.getElementById(formId);
            if (selectedForm) {
                selectedForm.style.display = 'block';
                enableFormFields(selectedForm.id);
                setupFormSpecificEvents(formId);
            } else {
                // Form not found - log error
            }
        } else {
            if (defaultMessage) defaultMessage.style.display = 'block';
        }
    }

    // Function to enable form fields
    function enableFormFields(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        // First, disable all inputs in all forms
        const allForms = ['formKelahiran', 'formKematian', 'formPindahMasuk', 'formPindahKeluar', 'formPindahRTRW', 'formPisahKK'];
        allForms.forEach(id => {
            const formElement = document.getElementById(id);
            if (formElement) {
                const inputs = formElement.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
            }
        });

        // Then, enable inputs in the active form
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            // Always enable inputs in active form (including hidden fields)
            input.disabled = false;

            // Only add required if the input is visible and not in a hidden container
            if (isElementVisible(input)) {
                // Skip search inputs but allow hidden inputs that are required (like penduduk_id)
                if (input.id.includes('_search_') || input.id.includes('search_')) {
                    return;
                }

                // Special handling for different forms
                if (formId === 'formKematian') {
                    // For kematian form, check specific required fields
                    if (['penduduk_id', 'tanggal_mutasi', 'hari_meninggal', 'jam_meninggal', 'bertempat_di', 'hari_pemakaman', 'tanggal_pemakaman', 'jam_pemakaman', 'lokasi_pemakaman', 'alasan'].includes(input.name)) {
                        input.required = true;
                    }
                } else if (formId === 'formPisahKK') {
                    const kategoriSelect = document.getElementById('kategori_mutasi_pisah');
                    const isDalamDesa = kategoriSelect && kategoriSelect.value === 'dalam_desa';

                    // Alamat required jika bukan dalam_desa (untuk tahu pindah ke mana)
                    if (input.id === 'alamat_pisah') {
                        input.required = !isDalamDesa;
                    }
                    // Field lainnya required
                    else {
                        input.required = true;
                    }
                } else {
                    input.required = true;
                }
            }
        });
    }

    // Helper function to check if element is visible
    function isElementVisible(element) {
        if (!element) return false;

        // Check if element itself is hidden
        if (element.style.display === 'none' || element.hidden) return false;

        // Check if any parent is hidden
        let parent = element.parentElement;
        while (parent && parent !== document.body) {
            if (parent.style.display === 'none' || parent.hidden) return false;
            parent = parent.parentElement;
        }

        return true;
    }

    // Function to disable all form fields
    function disableAllFormFields() {
        const forms = ['formKelahiran', 'formKematian', 'formPindahMasuk', 'formPindahKeluar', 'formPindahRTRW', 'formPisahKK'];
        forms.forEach(formId => {
            const form = document.getElementById(formId);
            if (form) {
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = true;
                    input.required = false;
                });
            }
        });
    }

    // Function to setup form-specific event listeners
    function setupFormSpecificEvents(formId) {
        if (formId === 'formPindahMasuk') {
            setupPindahMasukEvents();
        } else if (formId === 'formPisahKK') {
            setupPisahKKEvents();
        } else if (formId === 'formKematian') {
            setupKematianEvents();
        } else if (formId === 'formPindahKeluar') {
            setupPindahKeluarEvents();
        } else if (formId === 'formPindahRTRW') {
            setupPindahRTRWEvents();
        } else if (formId === 'formKelahiran') {
            setupKelahiranEvents();
        }
    }

    // Setup events for Kelahiran form
    function setupKelahiranEvents() {
        // NIK Check (realtime)
        const nikInput = document.getElementById('nik_bayi');
        if (nikInput) {
            let nikCheckTimeout;
            nikInput.addEventListener('input', function() {
                clearTimeout(nikCheckTimeout);
                const nikValue = this.value.trim();

                if (nikValue.length === 16) {
                    nikCheckTimeout = setTimeout(() => {
                        checkNIKExistsKelahiran(nikValue);
                    }, 500);
                } else {
                    hideNIKStatusInfoKelahiran();
                }
            });
        }

        // NKK Search
        const nkkSearchInput = document.getElementById('nkk_search_kelahiran');
        if (nkkSearchInput) {
            let searchTimeout;
            nkkSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length >= 3) {
                    searchTimeout = setTimeout(() => {
                        searchKKKelahiran(query);
                    }, 300);
                } else {
                    hideKKSearchResultsKelahiran();
                }
            });
        }

        // RT Auto-fill Dusun
        const rtInput = document.getElementById('rt_bayi');
        if (rtInput) {
            rtInput.addEventListener('input', function() {
                const rtValue = this.value.trim();
                const dusunInput = document.getElementById('dusun_bayi');

                if (dusunInput && rtValue.length === 3) {
                    const dusunSatu = ['001', '002', '003', '004', '007', '008'];
                    const dusun = dusunSatu.includes(rtValue) ? 'Dusun 1' : 'Dusun 2';
                    dusunInput.value = dusun;
                }
            });
        }
    }

    // Setup events for Kematian form
    function setupKematianEvents() {
        // Penduduk Search
        const pendudukSearchInput = document.getElementById('penduduk_search_kematian');
        if (pendudukSearchInput) {
            let searchTimeout;
            pendudukSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length >= 3) {
                    searchTimeout = setTimeout(() => {
                        searchPendudukKematian(query);
                    }, 300);
                } else {
                    hidePendudukSearchResultsKematian();
                }
            });
        }

        // Add event listeners for kematian form (like in backup)
        setTimeout(() => {
            const tanggalInputKematian = document.getElementById('tanggal_mutasi_kematian');
            if (tanggalInputKematian) {
                tanggalInputKematian.addEventListener('change', setHariFromTanggal);
            }

            const tanggalPemakamanInput = document.getElementById('tanggal_pemakaman');
            if (tanggalPemakamanInput) {
                tanggalPemakamanInput.addEventListener('change', setHariPemakamanFromTanggal);
            }
        }, 100);
    }

    // Setup events for Pindah Masuk form
    function setupPindahMasukEvents() {
        // KK Options toggle
        const kkExistingRadio = document.getElementById('kk_existing_pindah_masuk');
        const kkNewRadio = document.getElementById('kk_new_pindah_masuk');
        const existingKKContainer = document.getElementById('existingKKContainerPindahMasuk');
        const newKKContainer = document.getElementById('newKKContainerPindahMasuk');

        if (kkExistingRadio && kkNewRadio && existingKKContainer && newKKContainer) {
            kkExistingRadio.addEventListener('change', function() {
                if (this.checked) {
                    existingKKContainer.style.display = 'block';
                    newKKContainer.style.display = 'none';
                    // Clear new KK input
                    const newKKInput = document.getElementById('nkk_new_pindah_masuk');
                    if (newKKInput) newKKInput.value = '';
                    hideNKKStatusInfoPindahMasuk();
                }
            });

            kkNewRadio.addEventListener('change', function() {
                if (this.checked) {
                    existingKKContainer.style.display = 'none';
                    newKKContainer.style.display = 'block';
                    // Clear existing KK selection
                    clearNKKSelection('pindah_masuk');
                }
            });
        }

        // NIK Check
        const nikInput = document.getElementById('nik_pindah_masuk');
        if (nikInput) {
            let nikCheckTimeout;
            nikInput.addEventListener('input', function() {
                clearTimeout(nikCheckTimeout);
                const nikValue = this.value.trim();

                if (nikValue.length === 16) {
                    nikCheckTimeout = setTimeout(() => {
                        checkNIKExistsPindahMasuk(nikValue);
                    }, 500); // 500ms debounce
                } else {
                    hideNIKStatusInfoPindahMasuk();
                }
            });
        }

        // NKK Search for existing KK
        const nkkSearchInput = document.getElementById('nkk_search_pindah_masuk');
        if (nkkSearchInput) {
            let searchTimeout;
            nkkSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length >= 3) {
                    searchTimeout = setTimeout(() => {
                        searchKKPindahMasuk(query);
                    }, 300);
                } else {
                    hideKKSearchResultsPindahMasuk();
                }
            });
        }

        // NKK Check for new KK
        const nkkNewInput = document.getElementById('nkk_new_pindah_masuk');
        if (nkkNewInput) {
            let nkkCheckTimeout;
            nkkNewInput.addEventListener('input', function() {
                clearTimeout(nkkCheckTimeout);
                const nkkValue = this.value.trim();

                if (nkkValue.length === 16) {
                    nkkCheckTimeout = setTimeout(() => {
                        checkNKKExistsPindahMasuk(nkkValue);
                    }, 500);
                } else {
                    hideNKKStatusInfoPindahMasuk();
                }
            });
        }

        // Auto-fill dusun based on RT
        const rtSelect = document.getElementById('rt_pindah_masuk');
        const dusunField = document.getElementById('dusun_pindah_masuk');

        if (rtSelect && dusunField) {
            rtSelect.addEventListener('change', function() {
                const rtValue = this.value;
                if (rtValue) {
                    const dusunSatu = ['001', '002', '003', '004', '007', '008'];
                    const dusunValue = dusunSatu.includes(rtValue) ? 'Dusun Satu' : 'Dusun Dua';
                    dusunField.value = dusunValue;
                }
            });
        }
    }

    // Setup events for Pindah Keluar form
    function setupPindahKeluarEvents() {
        // Penduduk Search
        const pendudukSearchInput = document.getElementById('penduduk_search_pindah_keluar');
        if (pendudukSearchInput) {
            let searchTimeout;
            pendudukSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length >= 3) {
                    searchTimeout = setTimeout(() => {
                        searchPendudukPindahKeluar(query);
                    }, 300);
                } else {
                    hidePendudukSearchResultsPindahKeluar();
                }
            });
        }
    }

    // Setup events for Pindah RT/RW form
    function setupPindahRTRWEvents() {
        // NKK Search
        const nkkSearchInput = document.getElementById('nkk_search_pindah_rt_rw');
        if (nkkSearchInput) {
            let searchTimeout;
            nkkSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length >= 3) {
                    searchTimeout = setTimeout(() => {
                        searchNKKPindahRTRW(query);
                    }, 300);
                } else {
                    hideNKKSearchResultsPindahRTRW();
                }
            });
        }

        // Auto-fill dusun based on RT selection
        const rtSelect = document.getElementById('rt_tujuan');
        const dusunSelect = document.getElementById('dusun_tujuan');

        if (rtSelect && dusunSelect) {
            rtSelect.addEventListener('change', function() {
                const rtValue = this.value;
                if (rtValue) {
                    // RT 001, 002, 003, 004, 007, 008 = Dusun 1
                    // RT 005, 006, 009, 010 = Dusun 2
                    const dusunSatu = ['001', '002', '003', '004', '007', '008'];
                    const dusunValue = dusunSatu.includes(rtValue) ? 'Dusun 1' : 'Dusun 2';

                    // Set the dusun select value
                    for (let option of dusunSelect.options) {
                        if (option.value === dusunValue) {
                            option.selected = true;
                            break;
                        }
                    }
                }
            });
        }
    }

    // Setup events for Pisah KK form
    function setupPisahKKEvents() {
        const kategoriSelect = document.getElementById('kategori_mutasi_pisah');
        if (kategoriSelect) {
            kategoriSelect.addEventListener('change', function() {
                const isDalamDesa = this.value === 'dalam_desa';
                const kkOptionContainer = document.getElementById('kkOptionContainer');
                const addressContainer = document.getElementById('addressContainer');

                // Tampilkan opsi KK hanya untuk dalam_desa
                if (kkOptionContainer) kkOptionContainer.style.display = isDalamDesa ? 'block' : 'none';

                // Tampilkan alamat hanya untuk luar desa/kota/negeri
                if (addressContainer) addressContainer.style.display = isDalamDesa ? 'none' : 'block';

                // Set required attributes based on visibility
                const alamatField = document.getElementById('alamat_pisah');

                // Alamat required jika bukan dalam_desa (untuk tahu pindah ke mana)
                if (alamatField) alamatField.required = !isDalamDesa;
            });
        }

        // Handle KK option radio buttons
        const kkOptionRadios = document.querySelectorAll('input[name="kk_option"]');
        kkOptionRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const newKKContainer = document.getElementById('newKKContainer');
                const existingKKContainer = document.getElementById('existingKKContainer');

                if (this.value === 'new') {
                    if (newKKContainer) newKKContainer.style.display = 'block';
                    if (existingKKContainer) existingKKContainer.style.display = 'none';

                    // Set required untuk field KK baru
                    const rtField = document.getElementById('rt_baru_pisah');
                    const rwField = document.getElementById('rw_baru_pisah');
                    const alamatField = document.getElementById('alamat_baru_pisah');
                    if (rtField) rtField.required = true;
                    if (rwField) rwField.required = true;
                    if (alamatField) alamatField.required = true;

                } else if (this.value === 'existing') {
                    if (newKKContainer) newKKContainer.style.display = 'none';
                    if (existingKKContainer) existingKKContainer.style.display = 'block';

                    // Remove required untuk field KK baru
                    const rtField = document.getElementById('rt_baru_pisah');
                    const rwField = document.getElementById('rw_baru_pisah');
                    const alamatField = document.getElementById('alamat_baru_pisah');
                    if (rtField) rtField.required = false;
                    if (rwField) rwField.required = false;
                    if (alamatField) alamatField.required = false;
                }
            });
        });

        // Penduduk Search
        const pendudukSearchInput = document.getElementById('penduduk_search_pisah');
        if (pendudukSearchInput) {
            let searchTimeout;
            pendudukSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length >= 3) {
                    searchTimeout = setTimeout(() => {
                        searchPendudukPisah(query);
                    }, 300);
                } else {
                    hidePendudukSearchResultsPisah();
                }
            });
        }

        // KK Options Toggle
        const kkNewRadio = document.getElementById('kk_new');
        const kkExistingRadio = document.getElementById('kk_existing');
        const newKKContainer = document.getElementById('newKKContainer');
        const existingKKContainer = document.getElementById('existingKKContainer');

        if (kkNewRadio && kkExistingRadio && newKKContainer && existingKKContainer) {
            kkNewRadio.addEventListener('change', function() {
                if (this.checked) {
                    newKKContainer.style.display = 'block';
                    existingKKContainer.style.display = 'none';
                    hideKKSearchResultsPisah();
                }
            });

            kkExistingRadio.addEventListener('change', function() {
                if (this.checked) {
                    newKKContainer.style.display = 'none';
                    existingKKContainer.style.display = 'block';
                }
            });
        }

        // NKK Existing Search
        const nkkExistingInput = document.getElementById('nkk_existing_pisah');
        if (nkkExistingInput) {
            let searchTimeout;
            nkkExistingInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length >= 3) {
                    searchTimeout = setTimeout(() => {
                        searchKKPisah(query);
                    }, 300);
                } else {
                    hideKKSearchResultsPisah();
                }
            });
        }
    }

    // Event listeners for tanggal inputs to auto-set hari (moved to setupKematianEvents to avoid duplication)

    // Auto-format NIK input (including dynamic family members)
    document.addEventListener('input', function(e) {
        if (e.target.id === 'nik' || e.target.id === 'nik_kepala_keluarga' || e.target.classList.contains('nik-input')) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            if (value.length > 16) value = value.substring(0, 16);
            e.target.value = value;
        }

        // Auto-format RT input
        if (e.target.id.includes('rt') || e.target.name === 'rt' || e.target.name === 'rt_bayi' || e.target.name === 'rt_pindah_masuk' || e.target.name === 'rt_pisah') {
            // Skip NKK search inputs
            if (e.target.id.includes('nkk_search') || e.target.id.includes('nkk_')) {
                return;
            }

            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            if (value.length > 3) value = value.substring(0, 3);
            e.target.value = value;

            // Real-time RT validation
            if (value.length === 3) {
                const rtValidation = validateRTFormat(value);
                if (!rtValidation.valid) {
                    e.target.classList.add('border-red-500');
                    e.target.classList.remove('border-gray-300');

                    // Show error message
                    let errorDiv = e.target.parentNode.querySelector('.rt-error-message');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.className = 'rt-error-message text-red-500 text-sm mt-1';
                        e.target.parentNode.appendChild(errorDiv);
                    }
                    errorDiv.textContent = rtValidation.message;
                } else {
                    e.target.classList.remove('border-red-500');
                    e.target.classList.add('border-gray-300');

                    // Remove error message
                    const errorDiv = e.target.parentNode.querySelector('.rt-error-message');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }
            } else {
                e.target.classList.remove('border-red-500');
                e.target.classList.add('border-gray-300');

                // Remove error message
                const errorDiv = e.target.parentNode.querySelector('.rt-error-message');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        }
    });

    // Toggle tujuan field for Pisah KK based on kategori
    const kategoriPisah = document.getElementById('kategori_mutasi_pisah');
    const tujuanContainer = document.getElementById('tujuanPisahContainer');
    const tujuanInput = document.getElementById('asal_tujuan_pisah');
    const kkOptionsContainer = document.getElementById('kkOptionsContainer');
    const existingKKContainer = document.getElementById('existingKKContainer');
    const newKKContainer = document.getElementById('newKKContainer');
    const addressContainer = document.getElementById('addressContainer');

    if (kategoriPisah && tujuanContainer && tujuanInput && kkOptionsContainer && existingKKContainer) {
        kategoriPisah.addEventListener('change', function() {
            if (this.value === 'dalam_desa') {
                // Dalam desa - show KK options, hide tujuan
                tujuanContainer.style.display = 'none';
                kkOptionsContainer.style.display = 'block';
                addressContainer.style.display = 'none';
                tujuanInput.removeAttribute('required');

                // Update required attributes
                enableFormFields('formPisahKK');
            } else {
                // Keluar desa - hide KK options, show tujuan
                tujuanContainer.style.display = 'block';
                kkOptionsContainer.style.display = 'none';
                existingKKContainer.style.display = 'none';
                newKKContainer.style.display = 'none';
                addressContainer.style.display = 'none';
                tujuanInput.setAttribute('required', 'required');

                // Update required attributes
                enableFormFields('formPisahKK');
            }
        });
    }

    // KK Options toggle for Pisah KK
    const kkNewRadio = document.getElementById('kk_new');
    const kkExistingRadio = document.getElementById('kk_existing');
    const statusPerkawinanPisah = document.getElementById('status_perkawinan_pisah');
    const kedudukanInfo = document.getElementById('kedudukanInfo');

    if (kkNewRadio && kkExistingRadio && existingKKContainer && newKKContainer) {
        kkNewRadio.addEventListener('change', function() {
            if (this.checked) {
                existingKKContainer.style.display = 'none';
                newKKContainer.style.display = 'block';
                addressContainer.style.display = 'block';
                kedudukanInfo.textContent = 'Kepala Keluarga untuk KK baru';

                // Update required attributes
                enableFormFields('formPisahKK');
            }
        });

        kkExistingRadio.addEventListener('change', function() {
            if (this.checked) {
                existingKKContainer.style.display = 'block';
                newKKContainer.style.display = 'none';
                addressContainer.style.display = 'none';
                kedudukanInfo.textContent = 'Pilih KK terlebih dahulu untuk melihat info kedudukan';

                // Update required attributes
                enableFormFields('formPisahKK');
            }
        });
    }

    // NKK check for Pisah KK
    const nkkBaruInput = document.getElementById('nkk_baru_pisah');
    const nkkStatusInfo = document.getElementById('nkkStatusInfo');
    const nkkNewInfo = document.getElementById('nkkNewInfo');
    const nkkExistingInfo = document.getElementById('nkkExistingInfo');

    if (nkkBaruInput && nkkStatusInfo && nkkNewInfo && nkkExistingInfo) {
        let nkkCheckTimeout;
        nkkBaruInput.addEventListener('input', function() {
            clearTimeout(nkkCheckTimeout);
            const nkkValue = this.value.trim();

            if (nkkValue.length >= 16) {
                nkkCheckTimeout = setTimeout(() => {
                    checkNKKExistsPisah(nkkValue);
                }, 500);
            } else {
                nkkStatusInfo.style.display = 'none';
            }
        });
    }

    // Join existing KK button
    const joinExistingKKBtn = document.getElementById('joinExistingKK');
    if (joinExistingKKBtn) {
        joinExistingKKBtn.addEventListener('click', function() {
            // Switch to existing KK option
            const existingRadio = document.getElementById('kk_existing');
            if (existingRadio) {
                existingRadio.checked = true;
                existingRadio.dispatchEvent(new Event('change'));
            }
        });
    }
});

// NKK Check function for Pisah KK
function checkNKKExistsPisah(nkk) {
    const loading = document.getElementById('nkk_check_loading');
    const statusInfo = document.getElementById('nkkStatusInfo');
    const newInfo = document.getElementById('nkkNewInfo');
    const existingInfo = document.getElementById('nkkExistingInfo');

    if (loading) loading.style.display = 'block';
    if (statusInfo) statusInfo.style.display = 'none';

    fetch(`/mutasi/check-nkk?nkk=${nkk}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.style.display = 'none';
            if (statusInfo) statusInfo.style.display = 'block';

            if (data.exists) {
                if (newInfo) newInfo.style.display = 'none';
                if (existingInfo) {
                    existingInfo.style.display = 'block';
                    const detailsSpan = document.getElementById('existingKKDetails');
                    if (detailsSpan) {
                        detailsSpan.textContent = `Kepala Keluarga: ${data.kepala_keluarga}, RT: ${data.rt}, RW: ${data.rw}`;
                    }
                }
            } else {
                if (existingInfo) existingInfo.style.display = 'none';
                if (newInfo) newInfo.style.display = 'block';
            }
        })
        .catch(error => {
            if (loading) loading.style.display = 'none';
            if (statusInfo) {
                statusInfo.style.display = 'block';
                statusInfo.innerHTML = '<div class="text-red-500">Error: ' + error.message + '</div>';
            }

            // Show user-friendly error message
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal memeriksa NKK. Silakan coba lagi.',
                confirmButtonColor: '#ef4444',
                timer: 3000,
                showConfirmButton: false
            });
        });
}

// NIK Check functions for Kelahiran
function checkNIKExistsKelahiran(nik) {
    const loading = document.getElementById('nik_check_loading_kelahiran');
    const statusInfo = document.getElementById('nikStatusInfoKelahiran');
    const newInfo = document.getElementById('nikNewInfoKelahiran');
    const existingInfo = document.getElementById('nikExistingInfoKelahiran');

    if (loading) loading.classList.remove('hidden');
    if (statusInfo) statusInfo.classList.add('hidden');

    fetch(`/penduduk/check-nik?nik=${nik}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (statusInfo) statusInfo.classList.remove('hidden');

            if (data.exists) {
                if (newInfo) newInfo.classList.add('hidden');
                if (existingInfo) {
                    existingInfo.classList.remove('hidden');
                    const detailsSpan = document.getElementById('existingNIKDetailsKelahiran');
                    if (detailsSpan && data.data) {
                        detailsSpan.textContent = `${data.data.nama || 'N/A'} (${data.data.nik || 'N/A'})`;
                    }
                }
            } else {
                if (existingInfo) existingInfo.classList.add('hidden');
                if (newInfo) newInfo.classList.remove('hidden');
            }
        })
        .catch(error => {
            if (loading) loading.classList.add('hidden');
            if (statusInfo) {
                statusInfo.classList.remove('hidden');
                statusInfo.innerHTML = '<div class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">Error: ' + error.message + '</div>';
            }
        });
}

function hideNIKStatusInfoKelahiran() {
    const statusInfo = document.getElementById('nikStatusInfoKelahiran');
    if (statusInfo) statusInfo.classList.add('hidden');
}

// NIK Check functions for Pindah Masuk
function checkNIKExistsPindahMasuk(nik) {
    const loading = document.getElementById('nik_check_loading_pindah_masuk');
    const statusInfo = document.getElementById('nikStatusInfoPindahMasuk');
    const newInfo = document.getElementById('nikNewInfoPindahMasuk');
    const existingInfo = document.getElementById('nikExistingInfoPindahMasuk');

    if (loading) loading.classList.remove('hidden');
    if (statusInfo) statusInfo.style.display = 'none';

    fetch(`/penduduk/check-nik?nik=${nik}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (statusInfo) statusInfo.style.display = 'block';

            if (data.exists) {
                if (newInfo) newInfo.style.display = 'none';
                if (existingInfo) {
                    existingInfo.style.display = 'block';
                    const detailsSpan = document.getElementById('existingNIKDetailsPindahMasuk');
                    if (detailsSpan && data.data) {
                        detailsSpan.textContent = `${data.data.nama || 'N/A'} (${data.data.nik || 'N/A'})`;
                    }
                }
            } else {
                if (existingInfo) existingInfo.style.display = 'none';
                if (newInfo) newInfo.style.display = 'block';
            }
        })
        .catch(error => {
            if (loading) loading.classList.add('hidden');
            if (statusInfo) {
                statusInfo.style.display = 'block';
                statusInfo.innerHTML = '<div class="text-red-500">Error: ' + error.message + '</div>';
            }

            // Use enhanced error handler
            if (window.ErrorHandler) {
                window.ErrorHandler.handleSearchError(error, 'kk');
            }
        });
}

function hideNIKStatusInfoPindahMasuk() {
    const statusInfo = document.getElementById('nikStatusInfoPindahMasuk');
    if (statusInfo) statusInfo.style.display = 'none';
}

// NKK Check functions for Pindah Masuk
function checkNKKExistsPindahMasuk(nkk) {
    const loading = document.getElementById('nkk_check_loading_pindah_masuk');
    const statusInfo = document.getElementById('nkkStatusInfoPindahMasuk');
    const newInfo = document.getElementById('nkkNewInfoPindahMasuk');
    const existingInfo = document.getElementById('nkkExistingInfoPindahMasuk');

    if (loading) loading.classList.remove('hidden');
    if (statusInfo) statusInfo.style.display = 'none';

    fetch(`/mutasi/check-nkk?nkk=${nkk}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (statusInfo) statusInfo.style.display = 'block';

            if (data.exists) {
                if (newInfo) newInfo.style.display = 'none';
                if (existingInfo) {
                    existingInfo.style.display = 'block';
                    const detailsSpan = document.getElementById('existingKKDetailsPindahMasuk');
                    if (detailsSpan && data.kk) {
                        detailsSpan.textContent = `${data.kk.kepala_keluarga || 'N/A'} (${data.kk.nkk || 'N/A'})`;
                    }
                }
            } else {
                if (existingInfo) existingInfo.style.display = 'none';
                if (newInfo) newInfo.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error checking NKK:', error);
            if (loading) loading.classList.add('hidden');
            if (statusInfo) {
                statusInfo.style.display = 'block';
                statusInfo.innerHTML = '<div class="text-red-500">Error: ' + error.message + '</div>';
            }
        });
}

function hideNKKStatusInfoPindahMasuk() {
    const statusInfo = document.getElementById('nkkStatusInfoPindahMasuk');
    if (statusInfo) statusInfo.style.display = 'none';
}

// KK Search functions for Kelahiran
function searchKKKelahiran(query) {
    const loading = document.getElementById('nkk_search_loading_kelahiran');
    const results = document.getElementById('nkk_search_results_kelahiran');

    if (loading) loading.classList.remove('hidden');
    if (results) results.classList.add('hidden');

    fetch(`/mutasi/search-kk?query=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(kk => {
                        const option = document.createElement('div');
                        option.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100';
                        option.innerHTML = `
                            <div class="font-medium text-gray-900">${kk.nkk}</div>
                            <div class="text-sm text-gray-600">${kk.kepala_keluarga}</div>
                        `;
                        option.addEventListener('click', () => selectKKKelahiran(kk));
                        results.appendChild(option);
                    });
                    results.classList.remove('hidden');
                } else {
                    results.innerHTML = '<div class="p-3 text-gray-500 text-center">Tidak ada KK ditemukan</div>';
                    results.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error searching KK:', error);
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '<div class="p-3 text-red-500 text-center">Error: ' + error.message + '</div>';
                results.classList.remove('hidden');
            }
        });
}

function selectKKKelahiran(kk) {
    const nkkInput = document.getElementById('nkk_kelahiran');
    const selectedDiv = document.getElementById('selected_nkk_kelahiran');
    const searchInput = document.getElementById('nkk_search_kelahiran');
    const results = document.getElementById('nkk_search_results_kelahiran');

    if (nkkInput) {
        nkkInput.value = kk.nkk;
    }

    // Auto-fill alamat bayi berdasarkan data KK
    if (kk.alamat) {
        const alamatBayi = document.getElementById('alamat_bayi');
        if (alamatBayi) {
            alamatBayi.value = kk.alamat;
        }
    }

    // Auto-fill RT/RW/Dusun bayi berdasarkan data KK
    if (kk.rt) {
        const rtBayi = document.getElementById('rt_bayi');
        if (rtBayi) {
            rtBayi.value = kk.rt;
        }
    }

    if (kk.rw) {
        const rwBayi = document.getElementById('rw_bayi');
        if (rwBayi) {
            rwBayi.value = kk.rw;
        }
    }

    if (kk.dusun) {
        const dusunBayi = document.getElementById('dusun_bayi');
        if (dusunBayi) {
            dusunBayi.value = kk.dusun;
        }
    }
    if (selectedDiv) {
        const nameSpan = document.getElementById('selected_nkk_name_kelahiran');
        const infoSpan = document.getElementById('selected_nkk_info_kelahiran');
        if (nameSpan) nameSpan.textContent = kk.nkk;
        if (infoSpan) infoSpan.textContent = kk.kepala_keluarga;
        selectedDiv.classList.remove('hidden');
    }
    if (searchInput) searchInput.value = '';
    if (results) results.classList.add('hidden');
}

function hideKKSearchResultsKelahiran() {
    const results = document.getElementById('nkk_search_results_kelahiran');
    if (results) results.classList.add('hidden');
}

// NKK Search functions for Pindah RT/RW
function searchNKKPindahRTRW(query) {
    const loading = document.getElementById('nkk_search_loading_pindah_rt_rw');
    const results = document.getElementById('nkk_search_results_pindah_rt_rw');

    if (loading) loading.classList.remove('hidden');
    if (results) results.classList.add('hidden');

    fetch(`/mutasi/search-kk?query=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(kk => {
                        const option = document.createElement('div');
                        option.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100';
                        option.innerHTML = `
                            <div class="font-medium text-gray-900">${kk.nkk}</div>
                            <div class="text-sm text-gray-600">${kk.kepala_keluarga}</div>
                        `;
                        option.addEventListener('click', () => selectNKKPindahRTRW(kk));
                        results.appendChild(option);
                    });
                    results.classList.remove('hidden');
                } else {
                    results.innerHTML = '<div class="p-3 text-gray-500 text-center">Tidak ada KK ditemukan</div>';
                    results.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error searching NKK:', error);
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '<div class="p-3 text-red-500 text-center">Error: ' + error.message + '</div>';
                results.classList.remove('hidden');
            }
        });
}

function selectNKKPindahRTRW(kk) {
    const nkkInput = document.getElementById('nkk_pindah_rt_rw');
    const selectedDiv = document.getElementById('selected_nkk_pindah_rt_rw');
    const searchInput = document.getElementById('nkk_search_pindah_rt_rw');
    const results = document.getElementById('nkk_search_results_pindah_rt_rw');

    if (nkkInput) nkkInput.value = kk.nkk;
    if (selectedDiv) {
        const nameSpan = document.getElementById('selected_nkk_name_pindah_rt_rw');
        const infoSpan = document.getElementById('selected_nkk_info_pindah_rt_rw');
        if (nameSpan) nameSpan.textContent = kk.nkk;
        if (infoSpan) infoSpan.textContent = kk.kepala_keluarga;
        selectedDiv.classList.remove('hidden');
    }
    if (searchInput) searchInput.value = '';
    if (results) results.classList.add('hidden');
}

function hideNKKSearchResultsPindahRTRW() {
    const results = document.getElementById('nkk_search_results_pindah_rt_rw');
    if (results) results.classList.add('hidden');
}

// KK Search functions for Pindah Masuk
function searchKKPindahMasuk(query) {
    const loading = document.getElementById('nkk_search_loading_pindah_masuk');
    const results = document.getElementById('nkk_search_results_pindah_masuk');

    if (loading) loading.classList.remove('hidden');
    if (results) results.classList.add('hidden');

    fetch(`/mutasi/search-kk?query=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(kk => {
                        const option = document.createElement('div');
                        option.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100';
                        option.innerHTML = `
                            <div class="font-medium text-gray-900">${kk.nkk}</div>
                            <div class="text-sm text-gray-600">${kk.kepala_keluarga}</div>
                        `;
                        option.addEventListener('click', () => selectKKPindahMasuk(kk));
                        results.appendChild(option);
                    });
                    results.classList.remove('hidden');
                } else {
                    results.innerHTML = '<div class="p-3 text-gray-500 text-center">Tidak ada KK ditemukan</div>';
                    results.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error searching KK:', error);
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '<div class="p-3 text-red-500 text-center">Error: ' + error.message + '</div>';
                results.classList.remove('hidden');
            }
        });
}

function selectKKPindahMasuk(kk) {
    const nkkInput = document.getElementById('nkk_existing_pindah_masuk');
    const selectedDiv = document.getElementById('selected_nkk_pindah_masuk');
    const searchInput = document.getElementById('nkk_search_pindah_masuk');
    const results = document.getElementById('nkk_search_results_pindah_masuk');

    if (nkkInput) nkkInput.value = kk.nkk;
    if (selectedDiv) {
        const nameSpan = document.getElementById('selected_nkk_name_pindah_masuk');
        const infoSpan = document.getElementById('selected_nkk_info_pindah_masuk');
        if (nameSpan) nameSpan.textContent = kk.nkk;
        if (infoSpan) infoSpan.textContent = kk.kepala_keluarga;
        selectedDiv.classList.remove('hidden');
    }
    if (searchInput) searchInput.value = '';
    if (results) results.classList.add('hidden');

    // Auto-fill alamat fields based on selected KK
    const alamatField = document.getElementById('alamat_pindah_masuk');
    const rtField = document.getElementById('rt_pindah_masuk');
    const rwField = document.getElementById('rw_pindah_masuk');
    const dusunField = document.getElementById('dusun_pindah_masuk');

    if (alamatField && kk.alamat) alamatField.value = kk.alamat;
    if (rtField && kk.rt) {
        // Set RT select value
        for (let option of rtField.options) {
            if (option.value === kk.rt) {
                option.selected = true;
                break;
            }
        }
    }
    if (rwField && kk.rw) {
        // Set RW select value
        for (let option of rwField.options) {
            if (option.value === kk.rw) {
                option.selected = true;
                break;
            }
        }
    }
    if (dusunField && kk.dusun) dusunField.value = kk.dusun;
}

function hideKKSearchResultsPindahMasuk() {
    const results = document.getElementById('nkk_search_results_pindah_masuk');
    if (results) results.classList.add('hidden');
}

// Penduduk Search functions for Kematian
function searchPendudukKematian(query) {
    const loading = document.getElementById('penduduk_search_loading_kematian');
    const results = document.getElementById('penduduk_search_results_kematian');

    if (loading) loading.classList.remove('hidden');
    if (results) results.classList.add('hidden');

    fetch(`/mutasi/search-penduduk?query=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(penduduk => {
                        const option = document.createElement('div');
                        option.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100';
                        option.innerHTML = `
                            <div class="font-medium text-gray-900">${penduduk.nama}</div>
                            <div class="text-sm text-gray-600">
                                <div class="flex items-center space-x-4">
                                    <span><i class="fas fa-id-card text-blue-500 mr-1"></i>NIK: ${penduduk.nik}</span>
                                    <span><i class="fas fa-users text-green-500 mr-1"></i>KK: ${penduduk.nkk}</span>
                                </div>
                            </div>
                        `;
                        option.addEventListener('click', () => selectPendudukKematian(penduduk));
                        results.appendChild(option);
                    });
                    results.classList.remove('hidden');
                } else {
                    results.innerHTML = '<div class="p-3 text-gray-500 text-center">Tidak ada penduduk ditemukan</div>';
                    results.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error searching penduduk:', error);
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '<div class="p-3 text-red-500 text-center">Error: ' + error.message + '</div>';
                results.classList.remove('hidden');
            }
        });
}

function selectPendudukKematian(penduduk) {
    const pendudukIdInput = document.getElementById('penduduk_id_kematian');
    const selectedDiv = document.getElementById('selected_penduduk_kematian');
    const searchInput = document.getElementById('penduduk_search_kematian');
    const results = document.getElementById('penduduk_search_results_kematian');

    if (pendudukIdInput) {
        pendudukIdInput.value = penduduk.id;
    }

    if (selectedDiv) {
        const nameSpan = document.getElementById('selected_penduduk_name_kematian');
        const infoSpan = document.getElementById('selected_penduduk_info_kematian');
        if (nameSpan) nameSpan.textContent = penduduk.nama;
        if (infoSpan) {
            infoSpan.innerHTML = `
                <div class="flex items-center space-x-4 text-sm">
                    <span><i class="fas fa-id-card text-blue-500 mr-1"></i>NIK: ${penduduk.nik}</span>
                    <span><i class="fas fa-users text-green-500 mr-1"></i>NKK: ${penduduk.nkk}</span>
                </div>
            `;
        }
        selectedDiv.classList.remove('hidden');
    }
    if (searchInput) searchInput.value = '';
    if (results) results.classList.add('hidden');

    // Fill penduduk data display
    fillPendudukDataKematian(penduduk);
}

function fillPendudukDataKematian(penduduk) {
    const namaSpan = document.getElementById('display_nama_kematian');
    const jenisKelaminSpan = document.getElementById('display_jenis_kelamin_kematian');
    const umurSpan = document.getElementById('display_umur_kematian');
    const agamaSpan = document.getElementById('display_agama_kematian');
    const alamatSpan = document.getElementById('display_alamat_kematian');

    if (namaSpan) namaSpan.textContent = penduduk.nama || 'Tidak diketahui';
    if (jenisKelaminSpan) jenisKelaminSpan.textContent = penduduk.jenis_kelamin || 'Tidak diketahui';
    if (umurSpan) umurSpan.textContent = calculateAge(penduduk.tanggal_lahir);
    if (agamaSpan) agamaSpan.textContent = penduduk.agama || 'Tidak diketahui';
    if (alamatSpan) alamatSpan.textContent = penduduk.alamat || 'Tidak diketahui';
}

function hidePendudukSearchResultsKematian() {
    const results = document.getElementById('penduduk_search_results_kematian');
    if (results) results.classList.add('hidden');
}

// Penduduk Search functions for Pindah Keluar
function searchPendudukPindahKeluar(query) {
    const loading = document.getElementById('penduduk_search_loading_pindah_keluar');
    const results = document.getElementById('penduduk_search_results_pindah_keluar');

    if (loading) loading.classList.remove('hidden');
    if (results) results.classList.add('hidden');

    fetch(`/mutasi/search-penduduk?query=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(penduduk => {
                        const option = document.createElement('div');
                        option.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100';
                        option.innerHTML = `
                            <div class="font-medium text-gray-900">${penduduk.nama}</div>
                            <div class="text-sm text-gray-600">
                                <div class="flex items-center space-x-4">
                                    <span><i class="fas fa-id-card text-blue-500 mr-1"></i>NIK: ${penduduk.nik}</span>
                                    <span><i class="fas fa-users text-green-500 mr-1"></i>KK: ${penduduk.nkk}</span>
                                </div>
                            </div>
                        `;
                        option.addEventListener('click', () => selectPendudukPindahKeluar(penduduk));
                        results.appendChild(option);
                    });
                    results.classList.remove('hidden');
                } else {
                    results.innerHTML = '<div class="p-3 text-gray-500 text-center">Tidak ada penduduk ditemukan</div>';
                    results.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error searching penduduk:', error);
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '<div class="p-3 text-red-500 text-center">Error: ' + error.message + '</div>';
                results.classList.remove('hidden');
            }
        });
}

function selectPendudukPindahKeluar(penduduk) {
    const pendudukIdInput = document.getElementById('penduduk_id_pindah_keluar');
    const selectedDiv = document.getElementById('selected_penduduk_pindah_keluar');
    const searchInput = document.getElementById('penduduk_search_pindah_keluar');
    const results = document.getElementById('penduduk_search_results_pindah_keluar');

    if (pendudukIdInput) pendudukIdInput.value = penduduk.id;
    if (selectedDiv) {
        const nameSpan = document.getElementById('selected_penduduk_name_pindah_keluar');
        const infoSpan = document.getElementById('selected_penduduk_info_pindah_keluar');
        if (nameSpan) nameSpan.textContent = penduduk.nama;
        if (infoSpan) {
            infoSpan.innerHTML = `
                <div class="flex items-center space-x-4 text-sm">
                    <span><i class="fas fa-id-card text-blue-500 mr-1"></i>NIK: ${penduduk.nik}</span>
                    <span><i class="fas fa-users text-green-500 mr-1"></i>KK: ${penduduk.nkk}</span>
                </div>
            `;
        }
        selectedDiv.classList.remove('hidden');
    }
    if (searchInput) searchInput.value = '';
    if (results) results.classList.add('hidden');
}

function hidePendudukSearchResultsPindahKeluar() {
    const results = document.getElementById('penduduk_search_results_pindah_keluar');
    if (results) results.classList.add('hidden');
}

// Penduduk Search functions for Pindah RT/RW
function searchPendudukPindahRTRW(query) {
    const loading = document.getElementById('penduduk_search_loading_pindah_rt_rw');
    const results = document.getElementById('penduduk_search_results_pindah_rt_rw');

    if (loading) loading.classList.remove('hidden');
    if (results) results.classList.add('hidden');

    fetch(`/mutasi/search-penduduk?query=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(penduduk => {
                        const option = document.createElement('div');
                        option.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100';
                        option.innerHTML = `
                            <div class="font-medium text-gray-900">${penduduk.nama}</div>
                            <div class="text-sm text-gray-600">
                                <div class="flex items-center space-x-4">
                                    <span><i class="fas fa-id-card text-blue-500 mr-1"></i>NIK: ${penduduk.nik}</span>
                                    <span><i class="fas fa-users text-green-500 mr-1"></i>KK: ${penduduk.nkk}</span>
                                </div>
                            </div>
                        `;
                        option.addEventListener('click', () => selectPendudukPindahRTRW(penduduk));
                        results.appendChild(option);
                    });
                    results.classList.remove('hidden');
                } else {
                    results.innerHTML = '<div class="p-3 text-gray-500 text-center">Tidak ada penduduk ditemukan</div>';
                    results.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error searching penduduk:', error);
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '<div class="p-3 text-red-500 text-center">Error: ' + error.message + '</div>';
                results.classList.remove('hidden');
            }
        });
}

function selectPendudukPindahRTRW(penduduk) {
    const pendudukIdInput = document.getElementById('penduduk_id_pindah_rt_rw');
    const selectedDiv = document.getElementById('selected_penduduk_pindah_rt_rw');
    const searchInput = document.getElementById('penduduk_search_pindah_rt_rw');
    const results = document.getElementById('penduduk_search_results_pindah_rt_rw');

    if (pendudukIdInput) pendudukIdInput.value = penduduk.id;
    if (selectedDiv) {
        const nameSpan = document.getElementById('selected_penduduk_name_pindah_rt_rw');
        const infoSpan = document.getElementById('selected_penduduk_info_pindah_rt_rw');
        if (nameSpan) nameSpan.textContent = penduduk.nama;
        if (infoSpan) {
            infoSpan.innerHTML = `
                <div class="flex items-center space-x-4 text-sm">
                    <span><i class="fas fa-id-card text-blue-500 mr-1"></i>NIK: ${penduduk.nik}</span>
                    <span><i class="fas fa-users text-green-500 mr-1"></i>KK: ${penduduk.nkk}</span>
                </div>
            `;
        }
        selectedDiv.classList.remove('hidden');
    }
    if (searchInput) searchInput.value = '';
    if (results) results.classList.add('hidden');
}

function hidePendudukSearchResultsPindahRTRW() {
    const results = document.getElementById('penduduk_search_results_pindah_rt_rw');
    if (results) results.classList.add('hidden');
}

// Penduduk Search functions for Pisah KK
function searchPendudukPisah(query) {
    const loading = document.getElementById('penduduk_search_loading_pisah');
    const results = document.getElementById('penduduk_search_results_pisah');

    if (loading) loading.classList.remove('hidden');
    if (results) results.classList.add('hidden');

    fetch(`/mutasi/search-penduduk?query=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(penduduk => {
                        const option = document.createElement('div');
                        option.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100';
                        option.innerHTML = `
                            <div class="font-medium text-gray-900">${penduduk.nama}</div>
                            <div class="text-sm text-gray-600">
                                <div class="flex items-center space-x-4">
                                    <span><i class="fas fa-id-card text-blue-500 mr-1"></i>NIK: ${penduduk.nik}</span>
                                    <span><i class="fas fa-users text-green-500 mr-1"></i>KK: ${penduduk.nkk}</span>
                                </div>
                            </div>
                        `;
                        option.addEventListener('click', () => selectPendudukPisah(penduduk));
                        results.appendChild(option);
                    });
                    results.classList.remove('hidden');
                } else {
                    results.innerHTML = '<div class="p-3 text-gray-500 text-center">Tidak ada penduduk ditemukan</div>';
                    results.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error searching penduduk:', error);
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '<div class="p-3 text-red-500 text-center">Error: ' + error.message + '</div>';
                results.classList.remove('hidden');
            }
        });
}

function selectPendudukPisah(penduduk) {
    const pendudukIdInput = document.getElementById('penduduk_id_pisah');
    const selectedDiv = document.getElementById('selected_penduduk_pisah');
    const searchInput = document.getElementById('penduduk_search_pisah');
    const results = document.getElementById('penduduk_search_results_pisah');

    if (pendudukIdInput) pendudukIdInput.value = penduduk.id;
    if (selectedDiv) {
        const nameSpan = document.getElementById('selected_penduduk_name_pisah');
        const infoSpan = document.getElementById('selected_penduduk_info_pisah');
        if (nameSpan) nameSpan.textContent = penduduk.nama;
        if (infoSpan) {
            infoSpan.innerHTML = `
                <div class="flex items-center space-x-4 text-sm">
                    <span><i class="fas fa-id-card text-blue-500 mr-1"></i>NIK: ${penduduk.nik}</span>
                    <span><i class="fas fa-users text-green-500 mr-1"></i>KK: ${penduduk.nkk}</span>
                </div>
            `;
        }
        selectedDiv.classList.remove('hidden');
    }
    if (searchInput) searchInput.value = '';
    if (results) results.classList.add('hidden');
}

function hidePendudukSearchResultsPisah() {
    const results = document.getElementById('penduduk_search_results_pisah');
    if (results) results.classList.add('hidden');
}

// KK Search functions for Pisah KK
function searchKKPisah(query) {
    const loading = document.getElementById('kkSearchLoading');
    const results = document.getElementById('kkSearchResults');

    if (loading) loading.classList.remove('hidden');
    if (results) results.classList.add('hidden');

    fetch(`/mutasi/search-kk?query=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (results) {
                if (data && data.length > 0) {
                    results.innerHTML = data.map(kk => `
                        <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                             onclick="selectKKPisah(${JSON.stringify(kk).replace(/"/g, '&quot;')})">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">${kk.nkk}</p>
                                    <p class="text-sm text-gray-600">${kk.kepala_keluarga}</p>
                                    <p class="text-xs text-gray-500">RT ${kk.rt}/RW ${kk.rw} - ${kk.dusun}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-blue-600">${kk.jumlah_anggota} anggota</p>
                                </div>
                            </div>
                        </div>
                    `).join('');
                    results.classList.remove('hidden');
                } else {
                    results.innerHTML = '<div class="p-3 text-gray-500 text-center">Tidak ditemukan KK yang sesuai</div>';
                    results.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error searching KK:', error);
            if (loading) loading.classList.add('hidden');
            if (results) {
                results.innerHTML = '<div class="p-3 text-red-500 text-center">Error saat mencari KK</div>';
                results.classList.remove('hidden');
            }
        });
}

function selectKKPisah(kk) {
    const nkkInput = document.getElementById('nkk_existing_pisah');
    const nkkIdInput = document.getElementById('nkk_existing_id');
    const results = document.getElementById('kkSearchResults');
    const existingKKInfo = document.getElementById('existingKKInfo');
    const existingKKDetails = document.getElementById('existingKKDetails');

    if (nkkInput) nkkInput.value = kk.nkk;
    if (nkkIdInput) nkkIdInput.value = kk.nkk; // Store NKK as ID
    if (results) results.classList.add('hidden');

    // Show KK info
    if (existingKKInfo && existingKKDetails) {
        existingKKDetails.innerHTML = `
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-yellow-900">No KK:</span>
                    <span class="text-yellow-700">${kk.nkk}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-medium text-yellow-900">Kepala Keluarga:</span>
                    <span class="text-yellow-700">${kk.kepala_keluarga}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-medium text-yellow-900">Alamat:</span>
                    <span class="text-yellow-700">RT ${kk.rt}/RW ${kk.rw} - ${kk.dusun}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-medium text-yellow-900">Jumlah Anggota:</span>
                    <span class="text-yellow-700">${kk.jumlah_anggota} orang</span>
                </div>
            </div>
        `;
        existingKKInfo.style.display = 'block';
    }
}

function hideKKSearchResultsPisah() {
    const results = document.getElementById('kkSearchResults');
    if (results) results.classList.add('hidden');
}

// Utility function to calculate age
function calculateAge(birthDate) {
    if (!birthDate) return 'Tidak diketahui';

    const today = new Date();
    const birth = new Date(birthDate);

    // Check if date is valid
    if (isNaN(birth.getTime())) {
        return 'Tidak diketahui';
    }

    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }

    return age + ' tahun';
}
