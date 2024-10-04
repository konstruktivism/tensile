<!-- resources/views/projects/read.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="mx-auto lg:p-4 flex flex-col gap-3 dark:text-gray-300 w-full max-w-3xl">
        <div class="mb-12 flex flex-col items-center">
            <h1 class="text-lg font-bold text-center uppercase">
                {{ $project->organisation->name }}
                ·
                {{ $project->name }}
            </h1>

            <p class="font-italic text-center">{{ $project->description }}</p>

            <div class="text-yellow-400 relative">
                <svg width="100" height="50" viewBox="0 0 200 50" xmlns="http://www.w3.org/2000/svg" class="fill-current stroke-2">
                    <path d="M10 30 C 20 10, 40 10, 50 30 S 80 50, 90 30 S 120 10, 130 30 S 160 50, 170 30 S 200 10, 210 50" stroke="currentColor" fill="transparent"/>
                </svg>

                <div class="dark:text-black top-0 z-0 blur-sm left-2 absolute">
                    <svg width="100" height="50" viewBox="0 0 200 50" xmlns="http://www.w3.org/2000/svg" class="fill-current stroke-2">
                        <path d="M10 30 C 20 10, 40 10, 50 30 S 80 50, 90 30 S 120 10, 130 30 S 160 50, 170 30 S 200 10, 210 50" stroke="currentColor" fill="transparent"/>
                    </svg>
                </div>

                <div class="text-yellow-300 z-20 top-0 left-1.5 rotate-180 absolute">
                    <svg width="100" height="50" viewBox="0 0 200 50" xmlns="http://www.w3.org/2000/svg" class="fill-current stroke-2">
                        <path d="M10 30 C 20 10, 40 10, 50 30 S 80 50, 90 30 S 120 10, 130 30 S 160 50, 170 30 S 200 10, 210 50" stroke="currentColor" fill="transparent"/>
                    </svg>
                </div>
                <div class="text-white top-0 z-10 blur-sm left-2 rotate-180 absolute">
                    <svg width="100" height="50" viewBox="0 0 200 50" xmlns="http://www.w3.org/2000/svg" class="fill-current stroke-2">
                        <path d="M10 30 C 20 10, 40 10, 50 30 S 80 50, 90 30 S 120 10, 130 30 S 160 50, 170 30 S 200 10, 210 50" stroke="currentColor" fill="transparent"/>
                    </svg>
                </div>
            </div>

        </div>

        <div class="flex justify-between mb-4">
            @if ($previousWeekTasks->isNotEmpty())
                <a href="{{ route('project.viewWeek', ['project' => $project->id, 'week' => $week - 1]) }}" class="text-neutral-300 rounded border border-opacity-60 px-2 py-1">< Week {{ $week-1 }}</a>
            @else
                <span></span>
            @endif

            @if ($nextWeekTasks->isNotEmpty())
                <a href="{{ route('project.viewWeek', ['project' => $project->id, 'week' => $week + 1]) }}"class="text-neutral-300 rounded border border-opacity-60 px-2 py-1">Week {{ $week+1 }} ></a>
            @else
                <span></span>
            @endif
        </div>

        <h1 class="text-6xl font-bold tracking-tight mb-4">Week {{ $week }}</h1>

        <div class="flex justify-between py-3 lg:p-3 uppercase opacity-50 font-light">
            <h2 class="w-1/4">Datum</h2>

            <div class="w-1/2">Deliverables</div>

            <div class="w-1/5 text-right">Hours</div>

            @if ($project->is_fixed == 0)
                <div class="w-1/5 text-right">Price</div>
            @endif
        </div>

        @foreach($tasks as $index => $day)
            <div class="flex justify-between py-3 lg:p-3 pt-0 @if($index !== count($tasks) - 1) border-b dark:border-neutral-500 dark:border-opacity-25 @endif">
                <h2 class="w-1/4 text-sm text-balance">{{ \Carbon\Carbon::parse($day['completed_at'])->format('D d-m') }}</h2>

                <div class="w-1/2 flex flex-col">
                    {{ $day['name'] }}

                    <div class="opacity-60">{{ $day['description'] }}</div>

                    @if ($day['is_service'])
                        <div class="uppercase mt-2 text-xs text-green-500">Free of Charge</div>
                    @endif
                </div>

                <div class="w-1/5 text-right">{{ $day['minutes']/60 }}</div>

                @if ($project->is_fixed == 0)
                    <div class="w-1/5 text-right">
                        {{ \App\Helpers\CurrencyHelper::formatCurrency($day['minutes']/60 * 65) }}
                    </div>
                @endif
            </div>
        @endforeach

        <div class="flex justify-between p-3 font-bold dark:bg-dark bg-white drop-shadow dark:drop-shadow-md dark:drop-shadow-neutral-950 border-t  border-neutral-100 dark:border-neutral-800 rounded-lg">
            <h2 class="w-1/4"></h2>

            <div class="w-1/2 flex flex-col"></div>

            <div class="w-1/5 text-right">{{ $tasks->sum('minutes')/60 }}</div>

            @if ($project->is_fixed == 0)
                <div class="w-1/5 text-right">{{ \App\Helpers\CurrencyHelper::formatCurrency($tasks->sum('minutes')/60 * $project->hour_tariff) }}</div>
            @endif
        </div>

        <div class="my-3">
            <div class="mt-12">
                <h2 class="text-md font-bold mb-4 text-center uppercase">{{ \Carbon\Carbon::now()->format('F Y') }}</h2>
                <div class="w-full bg-gradient-to-r from-blue-50 to-blue-500 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-yellow-300 to-yellow-500 h-full relative" id="progress-bar"></div>
                </div>
                <div class="w-full text-black/70 dark:text-white flex py-2">
                    <div class="opacity-50" style="width: 50%;" id="days-passed">
                        <!-- JavaScript will update this -->
                    </div>

                    <div class="text-right" style="width: 50%;" id="days-left">
                        <!-- JavaScript will update this -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const currentDay = today.getDate();
            const currentMonth = today.getMonth();
            const currentYear = today.getFullYear();

            // Calculate days passed in the current week
            // Calculate days passed in the current month
            const daysPassedInMonth = currentDay;

            // Calculate remaining days in the current month
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            const daysLeftInMonth = daysInMonth - currentDay;

            const progressBar = document.getElementById('progress-bar');
            const progressPercentage = (daysPassedInMonth / daysInMonth) * 100;
            progressBar.style.width = `${progressPercentage}%`;

            document.getElementById('days-passed').textContent = `${daysPassedInMonth} days done`;
            document.getElementById('days-left').textContent = `${daysLeftInMonth} days left`;
        });
    </script>
@endpush
