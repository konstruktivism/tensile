<!-- resources/views/projects/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 max-w-5xl">
        <h1 class="text-6xl tracking-tight font-bold mb-6">Projects</h1>

        @if(empty($projects))
        <p class="text-neutral-600">No projects found.</p>
        @else
            <div class="flex flex-col divide-neutral-200 dark:divide-neutral-700 divide-y *:py-3">
                @foreach($projects as $project)
                    <a href="/project/{{ $project->id }}" class="dark:text-white py-6 transition-all hover:bg-gray-100 dark:hover:bg-neutral-800 p-3">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl tracking-tight font-bold">{{ $project->name }}</h2>

                            <h2 class="text-sm bg-yellow-400 text-black px-3 py-1 rounded font-bold">{{ $project->organisation->name }}</h2>
                        </div>
                        <p class="text-neutral-400 mt-2">{{ $project->description }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
