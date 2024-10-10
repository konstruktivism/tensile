<!-- resources/views/projects/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 max-w-5xl">
        <h1 class="text-6xl tracking-tight font-bold mb-6">Projects</h1>

        @if(empty($projects))
        <p class="text-neutral-600">No projects found.</p>
        @else
            <div class="flex flex-col gap-4 divide-neutral-700 divide-y *:pt-6">
                @foreach($projects as $project)
                    <a href="/project/{{ $project->id }}" class="dark-text-neutral-600  py-6 transition-all hover:bg-yellow-50 hover:bg-opacity-5 p-6 hover:rounded-lg">
                        <div class="flex justify-between items-center">
                            <h2 class="text-2xl tracking-tight font-bold">{{ $project->name }}</h2>

                            <h2 class="text-yellow-500 font-semibold">{{ $project->organisation->name }}</h2>
                        </div>
                        <p class="text-neutral-400 mt-2">{{ $project->description }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
