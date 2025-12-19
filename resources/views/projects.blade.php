<!-- resources/views/projects/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 max-w-5xl">
        <h1 class="text-6xl tracking-tight font-bold mb-6">Projects</h1>

        @if(empty($activeProjects))
        <p class="text-neutral-600">No active projects found.</p>
        @else
            <div class="flex flex-col divide-neutral-200 dark:divide-neutral-700 divide-y *:py-3">
                @foreach($activeProjects as $project)
                    <a href="/project/{{ $project->id }}" class="dark:text-white py-6 transition-all hover:opacity-90 p-3">
                        <div class="flex items-center gap-3">
                            <h2 class="text-xs px-3 py-1 uppercase rounded font-bold {{ $project->organisation->name == 'Konstruktiv' ? 'bg-yellow-400 text-black' : 'bg-blue-600 text-white' }}">
                                {{ $project->organisation->name }}
                            </h2>

                            <h2 class="text-xl tracking-tight font-bold">{{ $project->name }}</h2>

                            <p class="text-neutral-400 mt-1">{{ $project->description }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif



        <h1 class="text-2xl tracking-tight font-bold mb-6 mt-12">Older projects</h1>

        @if(empty($inactiveProjects))
            <p class="text-neutral-600">No inactive projects found.</p>
        @else
            <div class="flex flex-col divide-neutral-200 dark:divide-neutral-700 divide-y *:py-3">
                @foreach($inactiveProjects as $project)
                    <a href="/project/{{ $project->id }}" class="dark:text-white py-6 transition-all hover:opacity-90 p-3">
                        <div class="flex items-center gap-3">
                            <h2 class="text-xs px-3 py-1 uppercase rounded font-bold {{ $project->organisation->name == 'Konstruktiv' ? 'bg-yellow-400 text-black' : 'bg-blue-600 text-white' }}">
                                {{ $project->organisation->name }}
                            </h2>

                            <h2 class="text-xl tracking-tight font-bold">{{ $project->name }}</h2>

                            <p class="text-neutral-400 mt-1">{{ $project->description }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
