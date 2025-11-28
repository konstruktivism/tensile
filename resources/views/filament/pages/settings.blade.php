<x-filament-panels::page>
    <div class="space-y-8">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Task Imports</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Import calendar events from Google Calendar as tasks.
            </p>
            <div class="flex gap-3">
                <x-filament::button wire:click="importTasksThisWeek">
                    Import This Week
                </x-filament::button>
                <x-filament::button wire:click="importTasksThisMonth">
                    Import This Month
                </x-filament::button>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Forecast Imports</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Import future calendar events from Google Calendar as forecast tasks.
            </p>
            <div class="flex gap-3">
                <x-filament::button wire:click="importForecasts">
                    Import Forecasts
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>

