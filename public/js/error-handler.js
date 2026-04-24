// Global Error Handler untuk Frontend
class ErrorHandler {
    constructor() {
        this.setupGlobalErrorHandling();
        this.setupAjaxErrorHandling();
    }

    setupGlobalErrorHandling() {
        // Handle unhandled promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Unhandled promise rejection:', event.reason);
            this.showErrorNotification('Terjadi kesalahan yang tidak terduga');
        });

        // Handle JavaScript errors
        window.addEventListener('error', (event) => {
            console.error('JavaScript error:', event.error);
            this.showErrorNotification('Terjadi kesalahan pada aplikasi');
        });
    }

    setupAjaxErrorHandling() {
        // Intercept fetch requests
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            try {
                const response = await originalFetch(...args);

                if (!response.ok) {
                    // Handle different response types
                    let errorData = {};
                    const contentType = response.headers.get('content-type');

                    if (contentType && contentType.includes('application/json')) {
                        errorData = await response.json().catch(() => ({}));
                    } else {
                        // For HTML responses (like 405), just use status code
                        errorData = { message: `HTTP ${response.status}` };
                    }

                    this.handleHttpError(response.status, errorData);
                }

                return response;
            } catch (error) {
                console.error('Fetch error:', error);
                this.showErrorNotification('Gagal menghubungi server');
                throw error;
            }
        };
    }

    handleHttpError(statusCode, errorData) {
        const messages = {
            400: 'Permintaan tidak valid',
            401: 'Anda harus login terlebih dahulu',
            403: 'Anda tidak memiliki izin untuk aksi ini',
            404: 'Data tidak ditemukan',
            413: 'File yang diupload terlalu besar (maksimal 5MB)',
            419: 'Sesi telah berakhir, silakan refresh halaman',
            422: 'Data yang dimasukkan tidak valid',
            429: 'Terlalu banyak permintaan, silakan tunggu sebentar',
            500: 'Terjadi kesalahan server',
            503: 'Server sedang maintenance'
        };

        const message = errorData.message || messages[statusCode] || 'Terjadi kesalahan';

        if (statusCode === 401) {
            // Redirect to login
            window.location.href = '/login';
        } else if (statusCode === 413) {
            // Special handling for file upload errors
            this.showFileUploadError();
        } else {
            this.showErrorNotification(message);
        }
    }

    showErrorNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    showFileUploadError() {
        // Special notification for file upload errors
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-file-upload mr-2"></i>
                <span>File terlalu besar! Maksimal 5MB</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 7 seconds (longer for file upload errors)
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 7000);
    }

    showSuccessNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Initialize error handler when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.errorHandler = new ErrorHandler();
});

// Export for use in other scripts
window.ErrorHandler = ErrorHandler;
