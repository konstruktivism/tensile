@php
    $yearOptions = $this->getYearOptions();
    $selectedYear = is_array($this->filters['year'] ?? null) 
        ? ($this->filters['year'][0] ?? now()->year)
        : ($this->filters['year'] ?? now()->year);
@endphp

<div class="flex justify-end items-center gap-2 w-full">
    <div>
        <select 
            wire:model.live="filters.year" 
            style="width: 160px; min-width: 160px;"
            class="fi-input block rounded-lg border-none bg-white px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 focus:ring-2 focus:ring-primary-500 disabled:bg-gray-50 disabled:text-gray-500 dark:bg-white/5 dark:text-white dark:focus:ring-primary-400 sm:text-sm sm:leading-6"
        >
            @foreach($yearOptions as $value => $label)
                <option value="{{ $value }}" {{ $selectedYear == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    
    <x-filament::button wire:click="importWeeks">
        Import
    </x-filament::button>
</div>

