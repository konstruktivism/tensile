<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-medium">Import of Calendar</h2>

        <div class="flex items-center justify-between">

            <p class="mb-3">Actions to Import manually. Daily Import is run at 6:00 every day.</p>

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
