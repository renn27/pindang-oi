<section class="bg-white rounded-xl border border-gray-200 p-6">
    <header class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">
            Update Password
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Gunakan password yang kuat dan sulit ditebak untuk menjaga keamanan akun Anda.
        </p>
    </header>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <!-- Current Password -->
        <div>
            <x-input-label
                for="update_password_current_password"
                value="Password Saat Ini"
                class="mb-1"
            />
            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="block w-full rounded-lg"
                autocomplete="current-password"
            />
            <x-input-error
                :messages="$errors->updatePassword->get('current_password')"
                class="mt-1"
            />
        </div>

        <!-- New Password -->
        <div>
            <x-input-label
                for="update_password_password"
                value="Password Baru"
                class="mb-1"
            />
            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="block w-full rounded-lg"
                autocomplete="new-password"
            />
            <x-input-error
                :messages="$errors->updatePassword->get('password')"
                class="mt-1"
            />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label
                for="update_password_password_confirmation"
                value="Konfirmasi Password"
                class="mb-1"
            />
            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="block w-full rounded-lg"
                autocomplete="new-password"
            />
            <x-input-error
                :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-1"
            />
        </div>

        <!-- Action -->
        <div class="flex items-center gap-4 pt-2">
            <x-primary-button class="px-6">
                Simpan Perubahan
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600"
                >
                    Password berhasil diperbarui
                </p>
            @endif
        </div>
    </form>
</section>
