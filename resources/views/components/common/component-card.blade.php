@props([
    'title',
    'desc' => '',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800']) }}>
    <!-- Card Header -->
    <div class="px-6 py-5">
        <h3 class="text-base font-medium text-gray-800 dark:text-white">
            {{ $title }}
        </h3>
        @if($desc)
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $desc }}
            </p>
        @endif
    </div>

    <!-- Card Body -->
    <div class="p-4 border-t border-gray-100 dark:border-gray-700 sm:p-6">
        <div class="space-y-6">
            {{ $slot }}
        </div>
    </div>
</div>