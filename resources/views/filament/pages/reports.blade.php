<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex gap-4 items-end">
            <div class="w-[125%]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Report Type
                </label>
                <select 
                    wire:model.live="reportType" 
                    class="fi-input block w-full rounded-lg border-none bg-white px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 focus:ring-2 focus:ring-primary-500 disabled:bg-gray-50 disabled:text-gray-500 dark:bg-white/5 dark:text-white dark:focus:ring-primary-400 sm:text-sm sm:leading-6"
                >
                    <option value="week">Week</option>
                    <option value="month">Month</option>
                </select>
            </div>
            <div class="w-[125%]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Year
                </label>
                <select 
                    wire:model.live="selectedYear" 
                    class="fi-input block w-full rounded-lg border-none bg-white px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 focus:ring-2 focus:ring-primary-500 disabled:bg-gray-50 disabled:text-gray-500 dark:bg-white/5 dark:text-white dark:focus:ring-primary-400 sm:text-sm sm:leading-6"
                >
                    @foreach($this->getYearOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if($this->reportType === 'week')
                <div class="w-[125%]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Week
                    </label>
                    <select 
                        wire:model.live="selectedWeek" 
                        class="fi-input block w-full rounded-lg border-none bg-white px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 focus:ring-2 focus:ring-primary-500 disabled:bg-gray-50 disabled:text-gray-500 dark:bg-white/5 dark:text-white dark:focus:ring-primary-400 sm:text-sm sm:leading-6"
                    >
                        <option value="">All weeks</option>
                        @foreach($this->getWeekOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="w-[125%]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Month
                    </label>
                    <select 
                        wire:model.live="selectedMonth" 
                        class="fi-input block w-full rounded-lg border-none bg-white px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 focus:ring-2 focus:ring-primary-500 disabled:bg-gray-50 disabled:text-gray-500 dark:bg-white/5 dark:text-white dark:focus:ring-primary-400 sm:text-sm sm:leading-6"
                    >
                        <option value="">All months</option>
                        @foreach($this->getMonthOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
