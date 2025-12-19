<x-filament-panels::page>
    <div class="space-y-8">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Task Imports</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Import calendar events from Google Calendar as tasks.
            </p>
            <div class="flex gap-3 items-end">
                <x-filament::button wire:click="importTasksThisWeek" class="!bg-yellow-400 hover:opacity-90">
                    Import This Week
                </x-filament::button>
                <x-filament::button wire:click="importTasksThisMonth" class="!bg-yellow-400 hover:opacity-90">
                    Import This Month
                </x-filament::button>
                <div class="flex gap-2 items-center">
                    <select 
                        wire:model.live="selectedMonthForImport" 
                        class="fi-input block rounded-lg border-none bg-white px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 focus:ring-2 focus:ring-primary-500 disabled:bg-gray-50 disabled:text-gray-500 dark:bg-white/5 dark:text-white dark:focus:ring-primary-400 sm:text-sm sm:leading-6"
                    >
                        <option value="">Select Month</option>
                        @foreach($this->getMonthOptionsForImport() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-filament::button 
                        wire:click="importMissingTasksForMonth" 
                        class="!bg-yellow-400 hover:opacity-90"
                        :disabled="empty($selectedMonthForImport)"
                    >
                        Import Month
                    </x-filament::button>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Forecast Imports</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Import future calendar events from Google Calendar as forecast tasks.
            </p>
            <div class="flex gap-3">
                <x-filament::button wire:click="importForecasts" class="!bg-yellow-400 hover:opacity-90">
                    Import Forecasts
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>

