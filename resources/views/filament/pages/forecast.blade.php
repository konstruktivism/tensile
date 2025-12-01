<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Year
                </label>
                <select 
                    wire:model.live="selectedYear" 
                    class="fi-input block w-[125%] rounded-lg border-none bg-white px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 focus:ring-2 focus:ring-primary-500 disabled:bg-gray-50 disabled:text-gray-500 dark:bg-white/5 dark:text-white dark:focus:ring-primary-400 sm:text-sm sm:leading-6"
                >
                    @foreach($this->getYearOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        @foreach($this->getMonthOptions() as $month => $monthName)
            <x-filament::section>
                <x-slot name="heading">
                    {{ $monthName }} {{ $this->selectedYear }}
                </x-slot>
                <div wire:key="month-table-{{ $month }}-{{ $this->selectedYear }}">
                    @livewire('forecast-month-table', ['month' => $month, 'year' => $this->selectedYear], key("forecast-table-{$month}-{$this->selectedYear}"))
                </div>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>
