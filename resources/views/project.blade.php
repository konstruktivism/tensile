<!-- resources/views/projects/read.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="mx-auto p-4 flex flex-col gap-3 dark:text-gray-300 w-full max-w-3xl">
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

        <h1 class="text-3xl font-bold tracking-tight mb-4">Summary</h1>

        @foreach ($tasksByMonthAndWeekWithMinutes as $month => $weeks)
            <h2 class="font-bold ml-3 mt-4">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h2>
            @foreach ($weeks as $week => $data)
                <a href="{{ route('project.viewWeek', ['project' => $project->id, 'week' => $week]) }}" class="border-b dark:border-white dark:border-opacity-25 flex justify-between p-3 pt-0">
                    <h2 class="w-1/4 text-sm">Week {{ $week }} @if ($week == now()->format('W')) <span class="border border-neutral-500 text-neutral-500 rounded px-1.5 py-1 text-xs uppercase font-bold ml-2">Current</span> @endif</h2>

                    <div class="w-1/2 text-right">{{ $data['total_minutes']/60 }} {{ $data['total_minutes'] == 1 ? 'uur' : 'uren' }}</div>

                    @if ($project->is_fixed == 0)
                        <div class="w-1/5 text-right">{{ \App\Helpers\CurrencyHelper::formatCurrency($data['total_minutes']/60 * $project->hour_tariff) }}</div>
                    @endif
                </a>
            @endforeach
        @endforeach

        <div class="flex justify-between p-3 font-bold border-yellow-400 pb-3 border-b">
            <h2 class="w-1/4"></h2>

            <div class="w-1/2 text-right">{{ $tasksByMonthAndWeekWithMinutes->flatten(1)->sum('total_minutes')/60 }} {{ $tasksByMonthAndWeekWithMinutes->flatten(1)->sum('total_minutes') == 1 ? 'uur' : 'uren' }}</div>

            @if ($project->is_fixed == 0)
                <div class="w-1/5 text-right">{{ \App\Helpers\CurrencyHelper::formatCurrency($tasksByMonthAndWeekWithMinutes->flatten(1)->sum('total_minutes')/60 * $project->hour_tariff) }}</div>
            @endif
        </div>

    </div>
@endsection
