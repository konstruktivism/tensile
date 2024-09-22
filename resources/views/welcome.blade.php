@extends('layouts.app')

@section('content')
    <main class="container mx-auto p-6 flex flex-col items-center">
        <section class="text-center my-12 flex flex-col gap-6">
            <h2 class="text-4xl lg:text-8xl font-bold tracking-tight mb-4 text-balance">{{ config('app.slogan') }}</h2>

            <p class="text-lg bg-clip-text text-transparent bg-gradient-to-r from-neutral-400 to-neutral-500">Another minimal project management tool to streamline your weekly workflow.</p>

            <div class="self-center bg-gradient-to-r from-yellow-100 to-yellow-200 border-b-2 border-yellow-200 text-yellow-600 flex flex-col gap-1 px-12 py-6 items-center rounded-lg" role="alert">
                <h2 class="font-mono italic text-md drop-shadow-px">Notice</h2>

                <p class="drop-shadow-px">Currently in Beta for clients of Konstruktiv</p>
            </div>
        </section>

        <section id="features" class="my-12 flex flex-col items-center gap-3 lg:w-1/2">
            <h2 class="text-4xl font-bold tracking-tight">Features</h2>

            <div class="grid grid-cols-1 divide-y divide-neutral-200 dark:divide-neutral-700 *:py-12">
                @foreach ([
                    ['title' => 'Weekly Notifications', 'description' => 'Of all the logged tasks of your project delivered to your mailbox.'],
                    ['title' => 'The right data from your Tools', 'description' => 'The right information gathered from Jira, GitHub for a simple summary.'],
                    ['title' => 'Reporting', 'description' => 'Generate detailed reports to monitor your project\'s progress.']
                ] as $feature)
                    <div>
                        <h4 class="text-xl tracking-tight font-bold mb-2">{{ $feature['title'] }}</h4>
                        <p class="text-neutral-700 dark:text-neutral-400">{{ $feature['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </main>
@endsection
