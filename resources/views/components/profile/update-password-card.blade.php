<div class="w-full max-w-[500px] overflow-hidden rounded-3xl bg-white dark:bg-gray-900 flex flex-col max-h-[90vh]">
    <!-- Header Modal (Compact) -->
    <div class="flex-shrink-0 border-b border-gray-200 dark:border-gray-700 px-6 py-5">
        <div class="px-2">
            <h4 class="text-xl font-semibold text-gray-800 dark:text-white">
                Update Password
            </h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Pastikan password baru Anda kuat dan mudah diingat
            </p>
        </div>
    </div>

    <!-- Body Area dengan Scroll -->
    <div class="flex-1 overflow-y-auto p-6 lg:p-8" x-data="{
        currentPasswordVisible: false,
        newPasswordVisible: false,
        confirmPasswordVisible: false,
        
        togglePassword(field) {
            if (field === 'current') this.currentPasswordVisible = !this.currentPasswordVisible;
            if (field === 'new') this.newPasswordVisible = !this.newPasswordVisible;
            if (field === 'confirm') this.confirmPasswordVisible = !this.confirmPasswordVisible;
        }
    }">
        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col h-full" id="updatePasswordForm">
            @csrf
            @method('PUT')

            <!-- Current Password -->
            <div class="mb-6">
                <label for="update_password_current_password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Password Saat Ini
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input :type="currentPasswordVisible ? 'text' : 'password'" 
                           name="current_password" 
                           id="update_password_current_password"
                           x-ref="currentPassword"
                           class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 pl-10 pr-12 text-sm text-gray-800 dark:text-gray-300 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-brand-300 dark:focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:focus:ring-brand-500/20"
                           autocomplete="current-password"
                           placeholder="Masukkan password saat ini" />
                    
                    <!-- Eye Toggle Button -->
                    <button type="button" 
                            @click="togglePassword('current')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400">
                        <svg x-show="!currentPasswordVisible" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="currentPasswordVisible" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @if ($errors->updatePassword->has('current_password'))
                    <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $errors->updatePassword->first('current_password') }}</p>
                @endif
            </div>

            <!-- New Password -->
            <div class="mb-6">
                <label for="update_password_password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Password Baru
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <input :type="newPasswordVisible ? 'text' : 'password'" 
                           name="password" 
                           id="update_password_password"
                           x-ref="newPassword"
                           class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 pl-10 pr-12 text-sm text-gray-800 dark:text-gray-300 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-brand-300 dark:focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:focus:ring-brand-500/20"
                           autocomplete="new-password"
                           placeholder="Minimal 8 karakter" />
                    
                    <!-- Eye Toggle Button -->
                    <button type="button" 
                            @click="togglePassword('new')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400">
                        <svg x-show="!newPasswordVisible" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="newPasswordVisible" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Kombinasi huruf, angka, dan simbol</p>
                @if ($errors->updatePassword->has('password'))
                    <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $errors->updatePassword->first('password') }}</p>
                @endif
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="update_password_password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Konfirmasi Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <input :type="confirmPasswordVisible ? 'text' : 'password'" 
                           name="password_confirmation" 
                           id="update_password_password_confirmation"
                           x-ref="confirmPassword"
                           class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 pl-10 pr-12 text-sm text-gray-800 dark:text-gray-300 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-brand-300 dark:focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:focus:ring-brand-500/20"
                           autocomplete="new-password"
                           placeholder="Ketik ulang password baru" />
                    
                    <!-- Eye Toggle Button -->
                    <button type="button" 
                            @click="togglePassword('confirm')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400">
                        <svg x-show="!confirmPasswordVisible" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="confirmPasswordVisible" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @if ($errors->updatePassword->has('password_confirmation'))
                    <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                @endif
            </div>

            <!-- Success Message Area -->
            <div x-data="{ showSuccess: false }" x-init="if(@js(session('status') === 'password-updated')) { showSuccess = true; setTimeout(() => showSuccess = false, 3000); }">
                <div x-show="showSuccess" x-transition
                     class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/30 p-4 border border-green-200 dark:border-green-800">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-600 dark:text-green-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-green-800 dark:text-green-300">
                            Password berhasil diperbarui
                        </p>
                    </div>
                </div>
            </div>

            <!-- Spacer untuk footer -->
            <div class="flex-1"></div>
    </div>

    <!-- Footer Modal (Sticky) -->
    <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-6 py-5">
        <div class="flex items-center gap-3 justify-end">
            <button type="button"
                @click="open = false"
                class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors min-w-[100px]">
                Batal
            </button>
            <button type="submit"
                class="rounded-lg bg-brand-500 dark:bg-brand-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 dark:hover:bg-brand-700 transition-colors min-w-[100px]">
                Simpan
            </button>
        </div>
    </div>
    </form>
</div>