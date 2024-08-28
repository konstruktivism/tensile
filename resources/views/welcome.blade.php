@extends('layouts.app')

@section('content')
    <main class="container mx-auto p-6">
        <section class="text-center my-12">
            <h2 class="text-6xl font-bold tracking-tight mb-4 text-balance">Flexibility and strength in every release</h2>
            <p class="text-lg text-gray-700">Another minimal project management tool to streamline your weekly workflow.</p>
            <button class="mt-6 px-4 py-2 bg-gradient-to-b from-yellow-400 to-yellow-500 text-dark font-bold uppercase rounded-full">Get Started</button>
        </section>

        <section id="features" class="my-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded shadow">
                    <h4 class="text-xl font-bold mb-2">Task Management</h4>
                    <p class="text-gray-700">Easily manage and track your tasks with our intuitive interface.</p>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    <h4 class="text-xl font-bold mb-2">Team Collaboration</h4>
                    <p class="text-gray-700">Collaborate with your team in real-time and stay updated.</p>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    <h4 class="text-xl font-bold mb-2">Reporting</h4>
                    <p class="text-gray-700">Generate detailed reports to monitor your project's progress.</p>
                </div>
            </div>
        </section>
    </main>
@endsection
