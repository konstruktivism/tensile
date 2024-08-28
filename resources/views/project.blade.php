<!-- resources/views/projects/read.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">{{ $project->name }}</h1>
        <p class="text-gray-600">{{ $project->description }}</p>

        <!-- Loop through the related tasks of this project -->
        @foreach($project->tasks as $task)
            {{ $task->id }}
            {{ $task->name }}
            {{ $task->description }}
            {{ $task->completed }}
        @endforeach

        <div class="mt-4">
            <a href="{{ route('projects') }}" class="text-blue-500 hover:underline">Back to Projects</a>
        </div>

    </div>
@endsection
