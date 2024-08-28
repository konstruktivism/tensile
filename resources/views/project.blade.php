<!-- resources/views/projects/read.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="mx-auto p-4 flex flex-col gap-3">
        <a href="{{ route('projects') }}" class="text-blue-500 hover:underline">Projecten</a>

        <h1 class="text-2xl font-bold mb-4">Project: {{ $project->name }}</h1>

        <p class="text-gray-600">Omschrijving: {{ $project->description }}</p>

        Client: {{ $project->organisation->name }}

        <h1 class="text-2xl font-bold mb-4">Taken</h1>

        <div>
            @foreach ($tasksByWeekWithHours as $week => $data)
            <div class="border divide-x rounded flex justify-between p-3">
                    <h2>Week {{ $week }} - Total Hours: {{ $data['total_hours'] }}</h2>
                </div>

                @foreach ($data['tasks'] as $task)
                <div class="border divide-x flex justify-between border-t-0">
                    <div class="flex-1 p-3">{{ $task->id }}</div>
                    <div class="flex-1 p-3">
                        <h4 class="font-bold">{{ $task->name }}</h4>
                    {{ $task->description }}</div>
                    <div class="flex-1 p-3">{{ $task->hours }}</div>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>
@endsection
