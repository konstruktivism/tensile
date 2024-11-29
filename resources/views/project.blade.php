<!-- resources/views/projects/read.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="mx-auto p-4 flex flex-col dark:text-gray-300 w-full max-w-3xl">
        <div class="mb-12 flex flex-col items-center">
            <h1 class="text-lg font-bold text-center uppercase">
                {{ $project->organisation->name }}
                @if($project->organisation->name !== $project->name)
                    ·
                    {{ $project->name }}
                @endif
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

        <h1 class="text-3xl font-bold tracking-tight mb-4">Work Log</h1>

        @foreach ($tasksByMonthAndWeekWithMinutes as $month => $weeks)
            <h2 class="font-bold ml-3 mt-12">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h2>
            @foreach ($weeks as $week => $data)
                <a href="{{ route('project.viewWeek', ['project' => $project->id, 'week' => $week]) }}" class="border-b dark:border-white dark:border-opacity-25 flex justify-between p-3">
                    <div class="w-3/5 lg:w-1/2 text-sm flex justify-between items-center">
                        <div class="flex gap-2 items-center">
                            <h2>Week {{ $week }} @if ($week == now()->format('W'))
                                    <span class="hidden lg:inline-flex border border-neutral-500 text-neutral-500 rounded px-1.5 py-1 text-xs uppercase font-bold ml-2 -mt-1">Current</span> @endif
                            </h2>

                            @if($data['tasks']->contains('is_service', 1)) <div class="uppercase bg-green-500 text-xs w-2 h-2 inline-block rounded-full"></div> @endif
                        </div>

                        {{ count($data['tasks']) }} {{ count($data['tasks']) == 1 ? 'task' : 'tasks' }}
                    </div>

                    <div class="w-1/4 lg:w-1/4 text-right">{{ round($data['total_minutes']/60, 2) }} {{ $data['total_minutes'] == 1 ? 'hour' : 'hours' }}</div>

                    @if ($project->is_fixed == 0)
                        <div class="w-1/4 text-right">{{ \App\Helpers\CurrencyHelper::formatCurrency($data['total_minutes_without_service']/60 * $project->hour_tariff) }}</div>
                    @endif
                </a>
            @endforeach

            <div class="flex justify-between p-3">
                <div class="w-1/2 lg:w-1/2 flex gap-3 items-center">
                    @if ($weeks->sum(fn($week) => collect($week['tasks'])->where('is_service', 1)->count()) > 0)
                        <div class="uppercase bg-green-500 text-xs w-2 h-2 inline-block rounded-full"></div>

                        {{ $weeks->sum(fn($week) => collect($week['tasks'])->where('is_service', 1)->sum('minutes'))/60 }} {{ $weeks->sum(fn($week) => collect($week['tasks'])->where('is_service', 1)->sum('minutes')) == 1 ? 'hour' : 'hours' }}

                        ({{ \App\Helpers\CurrencyHelper::formatCurrency($weeks->sum(fn($week) => collect($week['tasks'])->where('is_service', 1)->sum('minutes'))/60 * $project->hour_tariff) }})
                    @endif
                </div>

                <div class="w-1/5 lg:w-1/4 text-right font-bold">{{ round($weeks->sum('total_minutes')/60, 2) }} {{ $weeks->sum('total_minutes') == 1 ? 'hour' : 'hours' }}</div>

                @if ($project->is_fixed == 0)
                    <div class="w-1/4 text-right font-bold">{{ \App\Helpers\CurrencyHelper::formatCurrency($weeks->sum(fn($week) => collect($week['tasks'])->where('is_service', '!=', 1)->sum('minutes'))/60 * $project->hour_tariff) }}</div>
                @endif
            </div>
        @endforeach

        <div class="flex items-center gap-3 mt-12">
            <div class="uppercase bg-green-500 text-xs w-2 h-2 inline-block rounded-full"></div>

            Free of Charge /  Service of Konstruktiv
        </div>

        <div class="flex mt-12 justify-between p-3 font-bold dark:bg-dark bg-white drop-shadow dark:drop-shadow-md dark:drop-shadow-neutral-950 border-t  border-neutral-100 dark:border-neutral-800 rounded-lg">
            <h2 class="w-1/2 lg:w-1/2"></h2>

            <div class="w-1/4 lg:w-1/4 text-right">{{ round($tasksByMonthAndWeekWithMinutes->flatten(1)->sum('total_minutes')/60, 2) }} {{ $tasksByMonthAndWeekWithMinutes->flatten(1)->sum('total_minutes') == 1 ? 'hour' : 'hours' }}</div>

            @if ($project->is_fixed == 0)
                <div class="w-1/4 text-right">{{ \App\Helpers\CurrencyHelper::formatCurrency($tasksByMonthAndWeekWithMinutes->flatten(1)->filter(fn($week) => !$week['tasks']->contains('is_service', 1))->sum('total_minutes')/60 * $project->hour_tariff) }}</div>
            @endif
        </div>
    </div>
@endsection
