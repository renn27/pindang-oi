<div x-data="{saveProfile(){
    console.log('Saving profile...');
}}">
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl lg:p-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h4 class="text-lg font-semibold text-gray-800 lg:mb-6">
                     Informasi Pribadi
                </h4>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">
                    <div>
                        <p class="mb-2 text-xs leading-normal text-gray-500">Nama Lengkap</p>
                        <p class="text-sm font-medium text-gray-800">{{ Auth::user()->nama_pegawai }}</p>
                    </div>

                    <div>
                        <p class="mb-2 text-xs leading-normal text-gray-500">Email</p>
                        <p class="text-sm font-medium text-gray-800">{{ Auth::user()->email }}</p>
                    </div>

                    <div>
                        <p class="mb-2 text-xs leading-normal text-gray-500">Jabatan</p>
                        <p class="text-sm font-medium text-gray-800">{{ Auth::user()->jabatan }}</p>
                    </div>

                    <div>
                        <p class="mb-2 text-xs leading-normal text-gray-500">Alamat</p>
                        <p class="text-sm font-medium text-gray-800">{{ Auth::user()->alamat }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
