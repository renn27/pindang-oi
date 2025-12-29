import Swal from 'sweetalert2'

const baseConfig = {
    width: '36rem', // mirip max-w-xl
    padding: '0',
    backdrop: true,
    heightAuto: false,
    buttonsStyling: false,
    showClass: {
        popup: 'animate-fade-in scale-95'
    },
    hideClass: {
        popup: 'animate-fade-out scale-95'
    },
    customClass: {
        popup: `
            rounded-3xl
            bg-white dark:bg-gray-900
            border border-gray-200 dark:border-gray-700
            overflow-hidden
        `,
        htmlContainer: 'p-0 m-0',
        actions: 'hidden', // kita bikin footer sendiri
    }
}

const SwalHelper = {
    success(message, title = 'Berhasil') {
        return Swal.fire({
            ...baseConfig,
            timer: 2000,
            showConfirmButton: false,
            html: `
            <div class="px-6 py-6 text-center">
                <div class="mx-auto mb-4 h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                    ✅
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">${title}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">${message}</p>
            </div>
            `
        })
    },

    error(message, title = 'Terjadi Kesalahan') {
        return Swal.fire({
            ...baseConfig,
            html: `
            <div class="px-6 py-6 text-center">
                <div class="mx-auto mb-4 h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                    ❌
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">${title}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">${message}</p>
            </div>
            `
        })
    },

    info(message, title = 'Informasi') {
        return Swal.fire({
            ...baseConfig,
            icon: 'info',
            title,
            text: message,
            confirmButtonText: 'OK',
            backdrop: `
                rgba(0,0,0,0.4)
                backdrop-blur(2px)
            `
        })
    },

    confirmDelete(formId, itemName = 'data ini') {
        return Swal.fire({
            ...baseConfig,
            showConfirmButton: false,
            html: `
            <div class="flex flex-col text-left">

                <!-- HEADER -->
                <div class="px-6 pt-6 pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Hapus Data?
                    </h3>
                </div>

                <!-- BODY -->
                <div class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    Apakah kamu yakin ingin menghapus
                    <span class="font-semibold text-gray-900 dark:text-white">
                        ${itemName}
                    </span>?
                    <div class="mt-3 rounded-lg bg-red-50 dark:bg-red-500/10 px-4 py-3 text-red-600 dark:text-red-400 text-sm">
                        Data yang sudah dihapus tidak dapat dikembalikan.
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="px-6 pb-6 pt-4 flex justify-end gap-3">
                    <button id="swal-cancel"
                        class="px-4 py-2 rounded-lg border border-gray-300
                            bg-white text-sm font-medium text-gray-700
                            hover:bg-gray-50
                            dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                        Batal
                    </button>
                    <button id="swal-confirm"
                        class="px-4 py-2 rounded-lg bg-red-600
                            text-white text-sm font-semibold
                            hover:bg-red-700 focus:ring-2 focus:ring-red-500">
                        Ya, Hapus
                    </button>
                </div>

            </div>
            `,
            didOpen: () => {
                document.getElementById('swal-cancel')
                    .addEventListener('click', () => Swal.close())

                document.getElementById('swal-confirm')
                    .addEventListener('click', () => {
                        Swal.close()
                        document.getElementById(formId).submit()
                    })
            }
        })
    }
}

// global
window.SwalHelper = SwalHelper
export default SwalHelper
