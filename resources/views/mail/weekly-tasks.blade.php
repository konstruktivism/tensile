@extends('mail.base')

@section('header')
    {{ __('Work Log Week') . ' ' .  $week }}
@endsection

@section('body')
    {{ $project->organisation->name }} · {{ $project->name }}

    <div style="padding: 2em; border: 1px solid #e5e7eb; margin: 2em 0; border-radius: 4px;">
        {{ $project->description }}
    </div>

    <table>
        <thead>
        <tr>
            <th style="width: 125px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1em; font-weight: normal;">Datum</th>
            <th style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1em; font-weight: normal;">Deliverables</th>
            <th style="border-bottom: 1px solid #e5e7eb; width: 100px; text-align: right; padding-bottom: 1em; font-weight: normal;">Uren</th>
        </tr>
        </thead>
        <tbody>
        @foreach($tasks as $index => $day)
            <tr>
                <td style="width: 125px; vertical-align: top; padding: 1em 0;">{{ \Carbon\Carbon::parse($day['completed_at'])->format('D d-m') }}</td>
                <td style="padding: 1em 0;"><strong style="color: #1b1b1b;">{{ $day['name'] }}</strong><br>{{ $day['description'] }}</td>
                <td style="width: 100px; text-align: right; padding: 1em 0; vertical-align: top;">{{ $day['hours'] }}</td>
            </tr>
        @endforeach
            <tr>
                <td colspan="3" style="text-align: right;">
                    Total aantal uren: {{ $tasks->sum('hours') }}
                </td>
            </tr>
        </tbody>
    </table>
@endsection

