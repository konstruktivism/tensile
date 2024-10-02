@extends('mail.base')

@section('header')
    {{ __('Work Log for') . ' ' . \Carbon\Carbon::now()->subMonth()->locale('en')->translatedFormat('F') }}
@endsection

@section('body')
    {{ $project->organisation->name }} · {{ $project->name }}

    <div style="padding: 2em; border: 1px solid #e5e7eb; margin: 2em 0; border-radius: 4px;">
        {{ $project->description }}
    </div>

    <table style="width: 100%;">
        <thead>
        <tr>
            <th style="width: 125px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1em; font-weight: normal;">Date</th>
            <th style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1em; font-weight: normal;">Deliverables</th>
            <th style="border-bottom: 1px solid #e5e7eb; width: 100px; text-align: right; padding-bottom: 1em; font-weight: normal;">Hours</th>
        </tr>
        </thead>
        <tbody>
        @foreach($tasks as $index => $day)
            <tr>
                <td style="width: 125px; vertical-align: top; padding: 0.5em 0;">
                    {{ \Carbon\Carbon::parse($day['completed_at'])->format('D d-m') }}
                    <br />
                    <span style="font-size: .8em; text-transform: uppercase; opacity: .6;">#{{ \Carbon\Carbon::parse($day['completed_at'])->week }}</span>

                </td>
                <td style="padding: 0.5em 0; vertical-align: top;"><strong style="color: #1b1b1b;">{{ $day['name'] }}</strong><br>{{ $day['description'] }}</td>
                <td style="width: 100px; text-align: right; padding: 0.5em 0; vertical-align: top;">{{ $day['minutes']/60 }}</td>
            </tr>
        @endforeach
            <tr>
                <td colspan="3" style="text-align: right;">
                    Total hours: {{ $tasks->sum('minutes')/60 }}
                </td>
            </tr>
        </tbody>
    </table>

    <a href="{{ env('APP_URL') }}" style="display: block; margin-top: 2em; text-align: center; color: #1b1b1b; text-underline: #1b1b1b">Login</a>
@endsection

