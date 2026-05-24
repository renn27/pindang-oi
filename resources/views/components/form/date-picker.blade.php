@props([
    'id' => 'datepicker-' . uniqid(),
    'mode' => 'single',
    'label' => null,
    'placeholder' => 'Select date',
    'name' => null,
    'dateFormat' => 'Y-m-d',
    'minBind' => null,
    'maxBind' => null,
    'minDate' => null,
    'maxDate' => null,
    'inputClass' => 'h-11 px-4 py-2.5 text-sm',
])

<div x-data="{
    flatpickrInstance: null,
    value: null,

    syncLimits() {
        if (this.flatpickrInstance) {
            let minD = this.$el.getAttribute('mindate') || this.$el.getAttribute('minDate');
            let maxD = this.$el.getAttribute('maxdate') || this.$el.getAttribute('maxDate');

            if (minD && minD.length <= 10) minD += ' 00:00:00';
            if (maxD && maxD.length <= 10) maxD += ' 23:59:59';

            try { this.flatpickrInstance.set('minDate', minD || null); } catch (e) {}
            try { this.flatpickrInstance.set('maxDate', maxD || null); } catch (e) {}
        }
    },

    async init() {

        this.$nextTick(() => {
            const setupFlatpickr = async () => {
                const flatpickrFactory = window.flatpickr || (window.loadFlatpickr ? await window.loadFlatpickr() : null);

                if (!flatpickrFactory) {
                    return;
                }

            let initialMin = this.$el.getAttribute('mindate') || this.$el.getAttribute('minDate');
            let initialMax = this.$el.getAttribute('maxdate') || this.$el.getAttribute('maxDate');

            if (initialMin && initialMin.length <= 10) initialMin += ' 00:00:00';
            if (initialMax && initialMax.length <= 10) initialMax += ' 23:59:59';

            this.flatpickrInstance = flatpickrFactory(this.$refs.dateInput, {
                mode: '{{ $mode }}',
                position: 'auto',
                monthSelectorType: 'static',
                dateFormat: '{{ $dateFormat }}', 
                minDate: initialMin || null,
                maxDate: initialMax || null,
                disableMobile: true,
                onOpen: (selectedDates, dateStr, instance) => {
                    // Dynamically update limits in case the root data changed
                    let openMin = this.$el.getAttribute('mindate') || this.$el.getAttribute('minDate');
                    let openMax = this.$el.getAttribute('maxdate') || this.$el.getAttribute('maxDate');

                    if (openMin && openMin.length <= 10) openMin += ' 00:00:00';
                    if (openMax && openMax.length <= 10) openMax += ' 23:59:59';

                    try { instance.set('minDate', openMin || null); } catch (e) {}
                    try { instance.set('maxDate', openMax || null); } catch (e) {}
                },
                onChange: (selectedDates, dateStr) => {
                    this.value = dateStr;
                }
            });

            // Set default value from DB.
            if (this.value) {
                this.flatpickrInstance.setDate(this.value, true);
            }

            // Observer to close Flatpickr if the modal/input hides
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting && this.flatpickrInstance && this.flatpickrInstance.isOpen) {
                        this.flatpickrInstance.close();
                    }
                });
            });
            observer.observe(this.$refs.dateInput);
            };

            setupFlatpickr().catch(() => {});
        });
    },
}" x-init="init()" x-effect="syncLimits()" x-modelable="value" {{ $attributes }}>
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    <div class="relative custom-datepicker">
        <input x-ref="dateInput" type="text" :value="value" @input="value = $event.target.value"
            id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}"
            class="{{ $inputClass }} w-full rounded-lg border appearance-none shadow-theme-xs placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-hidden focus:ring-3 bg-transparent text-gray-800 dark:text-gray-300 border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-brand-300 dark:focus:border-brand-500 focus:ring-brand-500/20"
            autocomplete="off" />
        <span class="absolute text-gray-500 dark:text-gray-400 -translate-y-1/2 pointer-events-none right-3 top-1/2">
            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none"
                class="size-6">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M8 2C8.41421 2 8.75 2.33579 8.75 2.75V3.75H15.25V2.75C15.25 2.33579 15.5858 2 16 2C16.4142 2 16.75 2.33579 16.75 2.75V3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V9V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V9V6C3.25 4.75736 4.25736 3.75 5.5 3.75H7.25V2.75C7.25 2.33579 7.58579 2 8 2ZM8 5.25H5.5C5.08579 5.25 4.75 5.58579 4.75 6V8.25H19.25V6C19.25 5.58579 18.9142 5.25 18.5 5.25H16H8ZM19.25 9.75H4.75V19C4.75 19.4142 5.08579 19.75 5.5 19.75H18.5C18.9142 19.75 19.25 19.4142 19.25 19V9.75Z"
                    fill="currentColor"></path>
            </svg>
        </span>
    </div>
</div>
