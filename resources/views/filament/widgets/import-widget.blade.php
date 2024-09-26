<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-medium mb-4">Import of Calendar</h2>

        <div class="flex flex-col lg:flex-row gap-6 lg:items-center lg:justify-between">

            <p>Actions to Import manually. Daily Import is run at 6:00 every day.</p>

            <div class="flex items-center shrink gap-3">
                <x-filament::button wire:click="import">
                    Import
                </x-filament::button>

                <x-filament::button wire:click="import30days">
                    Import 30 days
                </x-filament::button>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
