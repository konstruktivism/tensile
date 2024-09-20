<!-- resources/views/projects/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-6xl font-bold mb-4">Projecten</h1>

        @if(empty($projects))
        <p class="text-neutral-600">No projects found.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($projects as $project)
                    <a href="/project/{{ $project->id }}" class="bg-white dark:bg-neutral-900 dark-text-neutral-600 border dark:border-neutral-800 drop-shadow-md rounded-lg p-4">
                        <h2 class="text-4xl font-bold">{{ $project->name }}</h2>
                        <h2 class="text-yellow-500 font-semibold">{{ $project->organisation->name }}</h2>
                        <p class="text-gray-600 mt-2">{{ $project->description }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
