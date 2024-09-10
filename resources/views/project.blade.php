<!-- resources/views/projects/read.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="mx-auto p-4 flex flex-col gap-3 dark:text-gray-300">
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

                <div class="text-black top-0 z-0 blur-sm left-2 absolute">
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

        <h1 class="text-6xl font-bold tracking-tight mb-4">Week 36</h1>

        <div class="flex justify-between p-3 uppercase opacity-50 font-light">
            <h2 class="w-1/4">Datum</h2>

            <div class="w-1/2">Deliverables</div>

            <div class="w-1/5 text-right">Uren</div>

            <div class="w-1/5 text-right">Prijs</div>
        </div>

        <div class="border-b dark:border-white dark:border-opacity-25 flex justify-between p-3 pt-0">
            <h2 class="w-1/4 text-sm">02-09-2024</h2>

            <div class="w-1/2">E-mailnotificaties</div>

            <div class="w-1/5 text-right">2</div>

            <div class="w-1/5 text-right">&euro; {{ 2 * 65 }}</div>
        </div>

        <div class="border-b dark:border-white dark:border-opacity-25  flex justify-between p-3 pt-0">
            <h2 class="w-1/4 text-sm">03-09-2024</h2>

            <div class="w-1/2">
                Design Landingspagina

                Support items

                Projectmanagement
            </div>

            <div class="w-1/5 text-right">2</div>

            <div class="w-1/5 text-right">&euro; {{ 2 * 65 }}</div>
        </div>

        <div class="flex justify-between p-3 font-bold border-yellow-400 pb-3 border-b">
            <h2 class="w-1/4"></h2>

            <div class="w-1/2"></div>

            <div class="w-1/5 text-right">4</div>

            <div class="w-1/5 text-right">&euro; {{ 4 * 65 }}</div>
        </div>

        <div class="my-3">
            <div class="mt-12">
                <h2 class="text-md font-bold mb-4 text-center uppercase">September 2024</h2>
                <div class="w-full bg-gradient-to-r from-yellow-100 to-blue-500 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-yellow-300 to-yellow-500 h-full striped" style="width: 25%;"></div>
                </div>
                <div class="w-full text-white flex py-2">
                    <div class="opacity-50" style="width: 25%;">
                        7 days
                    </div>

                    <div class=" text-right" style="width: 75%;">
                        24 days left for a new release with impact
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
