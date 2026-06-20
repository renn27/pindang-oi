import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

const toastConfig = {
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
}

const SwalHelper = {
    raw: Swal,
    fire: (...args) => Swal.fire(...args),
    close: () => Swal.close(),
    
    confirmDelete(formId, itemName) {
        Swal.fire({
            html: `
                <div class="text-center px-2 py-5">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-900/10 mb-6">
                        <svg class="w-8 h-8 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Hapus Data</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-1 font-medium">"${itemName}"</p>
                    <p class="text-sm text-gray-400 dark:text-gray-400 mb-6">Data yang dihapus tidak dapat dikembalikan</p>

                    <div class="flex gap-3 justify-center">
                        <button type="button" data-action="cancel"
                            class="px-5 py-2.5 rounded-lg font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-300 transition-colors">
                            Batal
                        </button>
                        <button type="button" data-action="confirm"
                            class="px-5 py-2.5 rounded-lg font-medium bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 text-white transition-colors">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: false,
            allowOutsideClick: true,
            allowEscapeKey: true,
            customClass: {
                popup: '!rounded-3xl !border !border-gray-200 !shadow-2xl !bg-white dark:!border-gray-700 dark:!bg-gray-900 !p-0 !max-w-sm',
                title: '!hidden',
                htmlContainer: '!p-0 !m-0',
                container: '!p-5'
            },
            backdrop: 'rgba(107, 114, 128, 0.3) dark:rgba(0, 0, 0, 0.5)',
            buttonsStyling: false,
            didOpen: (popup) => {
                // Gunakan event delegation di popup
                popup.addEventListener('click', (e) => {
                    const target = e.target.closest('button[data-action]');
                    if (!target) return;

                    const action = target.dataset.action;
                    if (action === 'cancel') {
                        Swal.close();
                    } else if (action === 'confirm') {
                        const form = document.getElementById(formId);
                        if (form) {
                            form.submit();
                        }
                        Swal.close();
                    }
                });

                // Focus ke tombol batal
                popup.querySelector('button[data-action="cancel"]')?.focus();
            }
        });
    },

    success(message, title = 'Berhasil', duration = 3000) {
        return Swal.fire({
            ...toastConfig,
            timer: duration,
            html: `
                <div class="flex items-start gap-3 p-4">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                        <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white">${title}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">${message}</p>
                    </div>
                    <button onclick="SwalHelper.closeAll()" class="ml-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `,
            didOpen: () => {
                const progressBar = Swal.getHtmlContainer().querySelector('.swal2-timer-progress-bar');
                if (progressBar) {
                    progressBar.style.background = '#10b981';
                }
            }
        })
    },

    error(message, title = 'Terjadi Kesalahan', duration = 4000) {
        return Swal.fire({
            ...toastConfig,
            timer: duration,
            html: `
                <div class="flex items-start gap-3 p-4">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                        <svg class="h-4 w-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white">${title}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">${message}</p>
                    </div>
                    <button onclick="SwalHelper.closeAll()" class="ml-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `,
            didOpen: () => {
                const progressBar = Swal.getHtmlContainer().querySelector('.swal2-timer-progress-bar');
                if (progressBar) {
                    progressBar.style.background = '#ef4444';
                }
            }
        })
    },

    info(message, title = 'Informasi', duration = 3000) {
        return Swal.fire({
            ...toastConfig,
            timer: duration,
            html: `
                <div class="flex items-start gap-3 p-4">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white">${title}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">${message}</p>
                    </div>
                    <button onclick="SwalHelper.closeAll()" class="ml-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `,
            didOpen: () => {
                const progressBar = Swal.getHtmlContainer().querySelector('.swal2-timer-progress-bar');
                if (progressBar) {
                    progressBar.style.background = '#3b82f6';
                }
            }
        })
    },

    warning(message, title = 'Peringatan', duration = 3500) {
        return Swal.fire({
            ...toastConfig,
            timer: duration,
            html: `
                <div class="flex items-start gap-3 p-4">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                        <svg class="h-4 w-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white">${title}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">${message}</p>
                    </div>
                    <button onclick="SwalHelper.closeAll()" class="ml-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                     </button>
                </div>
            `,
            didOpen: () => {
                const progressBar = Swal.getHtmlContainer().querySelector('.swal2-timer-progress-bar');
                if (progressBar) {
                    progressBar.style.background = '#f59e0b';
                }
            }
        })
    },

    stacked(messages) {
        let htmlContent = '';
        
        messages.forEach((msg, index) => {
            const isLast = index === messages.length - 1;
            const borderClass = isLast ? '' : 'border-b border-gray-100 dark:border-gray-800';
            
            let bgIcon, textIcon, svgIcon, title;
            if (msg.type === 'success') {
                bgIcon = 'bg-green-100 dark:bg-green-900/30';
                textIcon = 'text-green-600 dark:text-green-400';
                svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
                title = msg.title || 'Berhasil';
            } else if (msg.type === 'info') {
                bgIcon = 'bg-blue-100 dark:bg-blue-900/30';
                textIcon = 'text-blue-600 dark:text-blue-400';
                svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                title = msg.title || 'Informasi';
            } else if (msg.type === 'error') {
                bgIcon = 'bg-red-100 dark:bg-red-900/30';
                textIcon = 'text-red-600 dark:text-red-400';
                svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
                title = msg.title || 'Terjadi Kesalahan';
            }
            
            htmlContent += `
                <div class="flex items-start gap-3 p-4 ${borderClass}">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full ${bgIcon}">
                        <svg class="h-4 w-4 ${textIcon}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${svgIcon}
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white">${title}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">${msg.message}</p>
                    </div>
                    ${index === 0 ? `
                    <button onclick="SwalHelper.closeAll()" class="ml-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    ` : '<div class="w-6 ml-2"></div>'}
                </div>
            `;
        });

        return Swal.fire({
            ...toastConfig,
            timer: 4500,
            html: `<div>${htmlContent}</div>`,
            didOpen: () => {
                const progressBar = Swal.getHtmlContainer().querySelector('.swal2-timer-progress-bar');
                if (progressBar) {
                    progressBar.style.background = '#10b981'; 
                }
            }
        })
    },

    // Versi loading
    loading(message = 'Memproses...') {
        return Swal.fire({
            ...toastConfig,
            timer: undefined,
            showConfirmButton: false,
            html: `
                <div class="flex items-center gap-3 p-4">
                    <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-500 border-t-transparent dark:border-blue-400"></div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">${message}</p>
                    </div>
                    <button onclick="SwalHelper.closeAll()" class="ml-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `
        })
    },

    // Versi simple
    toast(type, message, duration = 3000) {
        const configs = {
            success: {
                bgColor: 'bg-green-100',
                darkBgColor: 'dark:bg-green-900/30',
                iconColor: '#10b981',
                icon: `<svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`
            },
            error: {
                bgColor: 'bg-red-100',
                darkBgColor: 'dark:bg-red-900/30',
                iconColor: '#ef4444',
                icon: `<svg class="h-4 w-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`
            },
            info: {
                bgColor: 'bg-blue-100',
                darkBgColor: 'dark:bg-blue-900/30',
                iconColor: '#3b82f6',
                icon: `<svg class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`
            },
            warning: {
                bgColor: 'bg-yellow-100',
                darkBgColor: 'dark:bg-yellow-900/30',
                iconColor: '#f59e0b',
                icon: `<svg class="h-4 w-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>`
            }
        }

        const config = configs[type] || configs.info

        return Swal.fire({
            ...toastConfig,
            timer: duration,
            html: `
                <div class="flex items-center gap-3 p-4">
                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full ${config.bgColor} ${config.darkBgColor}">
                        ${config.icon}
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white">${message}</p>
                    </div>
                    <button onclick="SwalHelper.closeAll()" class="ml-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `,
            didOpen: () => {
                const progressBar = Swal.getHtmlContainer().querySelector('.swal2-timer-progress-bar');
                if (progressBar) {
                    progressBar.style.background = config.iconColor;
                }
            }
        })
    },

    // Close semua toast
    closeAll() {
        Swal.close()
    }
}

// CSS untuk toast - dengan dark mode
const style = document.createElement('style')
style.textContent = `
    /* Reset semua styling SweetAlert2 */
    .swal2-container {
        padding: 0 !important;
        margin: 0 !important;
        z-index: 999999 !important;
    }

    /* Toast position di kanan atas */
    .swal2-container.swal2-top-end {
        position: fixed !important;
        top: 20px !important;
        right: 20px !important;
        left: auto !important;
        bottom: auto !important;
        transform: none !important;
        width: auto !important;
        max-width: 400px !important;
    }

    /* Toast container utama */
    .swal2-popup.swal2-toast {
        background: transparent !important;
        background-color: transparent !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        box-shadow: none !important;
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 380px !important;
        min-width: 380px !important;
        max-width: 380px !important;
    }

    /* Toast content */
    .swal2-html-container {
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Custom container untuk toast content */
    .swal2-popup.swal2-toast > div {
        background: white !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 8px !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
        margin: 8px 0 !important;
        overflow: hidden !important;
    }

    /* Dark mode untuk toast content */
    .dark .swal2-popup.swal2-toast > div {
        background: #1f2937 !important;
        border-color: #374151 !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
    }

    /* Hilangkan semua overlay/background blur */
    .swal2-backdrop-show,
    .swal2-no-backdrop {
        background: transparent !important;
    }

    /* Progress bar */
    .swal2-timer-progress-bar {
        height: 2px !important;
        background: transparent !important;
        opacity: 0.3 !important;
    }

    .swal2-timer-progress-bar::after {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        height: 100% !important;
        width: 100% !important;
        background: currentColor !important;
        border-radius: 0 0 8px 8px !important;
    }

    /* Animation masuk dari kanan */
    .swal2-popup.swal2-toast.swal2-show {
        animation: slideInRight 0.3s ease-out !important;
    }

    .swal2-popup.swal2-toast.swal2-hide {
        animation: slideOutRight 0.3s ease-in !important;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    /* Responsive */
    @media (max-width: 640px) {
        .swal2-popup.swal2-toast {
            width: 320px !important;
            min-width: 320px !important;
            max-width: 320px !important;
        }

        .swal2-container.swal2-top-end {
            right: 10px !important;
            top: 15px !important;
        }
    }

    /* Loading spinner animation */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Hilangkan semua pseudo-elements yang mungkin memberikan background */
    .swal2-popup.swal2-toast::before,
    .swal2-popup.swal2-toast::after {
        display: none !important;
    }

    /* Dark mode untuk modal confirm delete */
    .dark .swal2-container .swal2-popup {
        background-color: #1f2937 !important;
        color: #f3f4f6 !important;
    }
`
document.head.appendChild(style)

// Global
window.SwalHelper = SwalHelper
export default SwalHelper
