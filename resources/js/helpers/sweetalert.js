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

    // confirmDelete(formId, itemName = 'data ini') {
    //     return Swal.fire({
    //         ...baseConfig,
    //         icon: 'warning',
    //         title: 'Hapus Data?',
    //         html: `
    //             <p>
    //                 Yakin ingin menghapus <b>${itemName}</b>?<br>
    //                 <span class="text-red-500">Data akan dihapus permanen.</span>
    //             </p>
    //         `,
    //         showCancelButton: true,
    //         confirmButtonText: 'Ya, Hapus',
    //         cancelButtonText: 'Batal',
    //         backdrop: `
    //             rgba(0,0,0,0.4)
    //             backdrop-blur(2px)
    //         `
    //     }).then(result => {
    //         if (result.isConfirmed) {
    //             document.getElementById(formId).submit()
    //         }
    //     })
    // }

    confirmDelete(formId, itemName = 'data ini') {
        return Swal.fire({
            ...baseConfig,
            html: `
            <div class="flex flex-col">

                <!-- HEADER -->
                <div class="border-b px-6 py-4 text-left">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">
                        Hapus Data?
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Konfirmasi penghapusan data
                    </p>
                </div>

                <!-- BODY -->
                <div class="px-6 py-5 text-sm text-gray-600 dark:text-gray-300">
                    Yakin ingin menghapus <b>${itemName}</b>?<br>
                    <span class="text-red-500">
                        Data akan dihapus permanen.
                    </span>
                </div>

                <!-- FOOTER -->
                <div class="border-t px-6 py-4 flex justify-end gap-3">
                    <button id="swal-cancel"
                        class="px-4 py-2 rounded-lg border bg-white text-sm
                            dark:bg-gray-800 dark:border-gray-700">
                        Batal
                    </button>
                    <button id="swal-confirm"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm hover:bg-red-700">
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
