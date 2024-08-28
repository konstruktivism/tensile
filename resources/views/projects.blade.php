<!-- resources/views/projects/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-6xl font-bold mb-4">Projects</h1>

        @if(empty($projects))
        <p class="text-gray-600">No projects found.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($projects as $project)
                    <a href="/project/{{ $project->id }}" class="bg-white shadow-md rounded-lg p-4">
                        <h2 class="text-xl font-semibold">{{ $project->name }}</h2>
                        <p class="text-gray-600 mt-2">{{ $project->description }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
