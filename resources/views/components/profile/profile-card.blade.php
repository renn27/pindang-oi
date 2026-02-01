<div x-data="{
    photoPreview: '{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/user/userlogodefault.png') }}',

    previewPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.photoPreview = URL.createObjectURL(file);
    },

    saveProfile() {
        console.log('Saving profile...');
    }
}">
    <div class="mb-6 rounded-2xl border border-gray-200 p-5 lg:p-6">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex w-full flex-col items-center gap-6 xl:flex-row">
                <div class="h-20 w-20 overflow-hidden rounded-full border border-gray-200">
                    <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/user/userlogodefault.png') }}"
                        class="h-full w-full object-cover"
                    />

                </div>
                <div class="order-3 xl:order-2">
                    <h4 class="mb-2 text-center text-lg font-semibold text-gray-800 xl:text-left">
                        {{ Auth::user()->nama_pegawai }} ({{ Auth::user()->username }})
                    </h4>
                    <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
                        <p class="text-sm text-gray-500">
                            {{ Auth::user()->jabatan }}
                        </p>
                    </div>
                </div>
            </div>

            <button @click="$dispatch('open-profile-info-modal')"
                class="shadow-theme-xs flex w-full items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 lg:inline-flex lg:w-auto">
                <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                        fill="" />
                </svg>
                Edit
            </button>
        </div>
    </div>

    <!-- Profile Info Modal -->
    <x-ui.smart-modal @open-profile-info-modal.window="open = true" :isOpen="false" class="max-w-[700px]">
        <div class="w-full max-w-[700px] overflow-hidden rounded-3xl bg-white flex flex-col max-h-[90vh]">
            <!-- Header Modal (Compact) -->
            <div class="flex-shrink-0 border-b border-gray-200 px-6 py-4">
                <div class="px-2">
                    <h4 class="text-xl font-semibold text-gray-800">
                        Ubah Data
                    </h4>
                    <p class="text-sm text-gray-500 mt-1">
                        Lakukan perubahan atau penambahan data yang diperlukan
                    </p>
                </div>
            </div>

            <!-- Body Area dengan Scroll -->
            <div class="flex-1 overflow-y-auto p-6 lg:p-8">
                <form method="post" action="{{ route('profile.update') }}" class="flex flex-col h-full"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Ganti photo profile di modal -->
                    <div class="col-span-2 mb-8">
                        <h5 class="mb-4 text-lg font-medium text-gray-800">
                            Foto Profil
                        </h5>

                        <div class="flex flex-col items-center gap-4">
                            <!-- Preview Foto dengan Upload Button -->
                            <div class="relative">
                                <div class="h-32 w-32 overflow-hidden rounded-full border-2 border-gray-200">
                                    <img :src="photoPreview" class="h-full w-full object-cover"/>
                                </div>

                                <!-- Upload Button Floating -->
                                <label for="photo-upload"
                                    class="absolute bottom-0 right-0 flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-white border-2 border-gray-300 text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:border-brand-400">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                    </svg>
                                    <input type="file" name="photo" id="photo-upload" accept="image/*"
                                        class="hidden" @change="previewPhoto">
                                </label>
                            </div>

                            <!-- Petunjuk Singkat -->
                            <div class="text-center">
                                <p class="text-sm text-gray-600">
                                    Klik icon kamera untuk mengganti foto
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Format: PNG, JPG, JPEG • Maks. 2MB
                                </p>

                                <!-- Reset Button Minimal -->
                                <div x-show="photoPreview.includes('blob:')" x-transition class="mt-3">
                                    <button type="button"
                                        @click="photoPreview = '{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/user/owner.png') }}'; document.getElementById('photo-upload').value = ''"
                                        class="text-sm text-gray-500 hover:text-gray-700 underline">
                                        Reset ke foto sebelumnya
                                    </button>
                                </div>
                            </div>

                            <!-- Error Messages -->
                            <x-input-error :messages="$errors->get('photo')" class="text-center" />
                        </div>
                    </div>

                    {{-- Bagian Informasi Pribadi --}}
                    <div>
                        <h5 class="mb-5 text-lg font-medium text-gray-800 lg:mb-6">
                            Informasi Pribadi
                        </h5>

                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                            <div class="col-span-2 lg:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Nama Lengkap
                                </label>
                                <input type="text" name="name" id="name"
                                    value="{{ Auth::user()->nama_pegawai }}"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                            </div>

                            <div class="col-span-2 lg:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Username
                                </label>
                                <input type="text" name="username" id="username"
                                    value="{{ Auth::user()->username }}"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                            </div>

                            <div class="col-span-2 lg:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Email
                                </label>
                                <input type="text" id="email" name="email" value="{{ Auth::user()->email }}"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                                @if (Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !Auth::user()->hasVerifiedEmail())
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-800">
                                            {{ __('Your email address is unverified.') }}

                                            <button form="send-verification"
                                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                {{ __('Click here to re-send the verification email.') }}
                                            </button>
                                        </p>

                                        @if (session('status') === 'verification-link-sent')
                                            <p class="mt-2 font-medium text-sm text-green-600">
                                                {{ __('A new verification link has been sent to your email address.') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="col-span-2 lg:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Jabatan
                                </label>
                                <input type="text" id="jabatan" name="jabatan"
                                    value="{{ Auth::user()->jabatan }}"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                            </div>

                            <div class="col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Alamat
                                </label>
                                <input type="text" id="alamat" name="alamat"
                                    value="{{ Auth::user()->alamat }}"
                                    placeholder="{{ Auth::user()->alamat ? '' : 'Alamat belum diisi' }}"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                            </div>
                        </div>
                    </div>

                    <!-- Spacer untuk footer -->
                    <div class="flex-1"></div>
            </div>

            <!-- Footer Modal (Sticky) -->
            <div class="flex-shrink-0 border-t border-gray-200 bg-gray-50 px-6 py-4">
                <div class="flex items-center gap-3 lg:justify-end">
                    <button type="button"
                        @click=" open = false; photoPreview = '{{ Auth::user()->photo ? asset('storage/profile/' . Auth::user()->photo) : asset('images/user/owner.png') }}';"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto">
                        Close
                    </button>
                    <button type="submit"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        Save Changes
                    </button>
                </div>
            </div>
            </form>
        </div>
    </x-ui.smart-modal>
</div>
