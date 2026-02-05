@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="User Profile" />
    <div class="rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between mb-5 lg:mb-7">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Profile</h3>
            <button @click="$dispatch('open-update-password-modal')"
                class="shadow-theme-xs flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">
                <svg class="fill-current dark:text-gray-300" width="18" height="18" viewBox="0 0 18 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                        fill="" />
                </svg>
                Update Password
            </button>
        </div>

        <x-profile.profile-card />
        <x-profile.personal-info-card />

        <!-- Modal Update Password -->
        <x-ui.smart-modal @open-update-password-modal.window="open = true" :isOpen="false" class="max-w-[500px]">
            <x-profile.update-password-card />
        </x-ui.smart-modal>
    </div>
@endsection
