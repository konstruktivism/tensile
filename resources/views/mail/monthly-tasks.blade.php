@extends('mail.base')

@section('header')
    {{ __('Work Log for') . ' ' . \Carbon\Carbon::now()->subMonth()->locale('en')->translatedFormat('F') }}
@endsection

@section('body')
    {{ $project->organisation->name }} · {{ $project->name }}     @if ($project->is_fixed == 1)<div style="color: black; background: #FACC15; display: inline-block;  padding: 1px 6px; border-radius: 4px; text-transform: uppercase; font-size: 12px; margin: 0 0 0 1em;">Fixed Price</div>@endif

    <div style="padding: 1em 0 2em 0; font-style: italic;">
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
                <td style="padding: 0.5em 0; vertical-align: top;"><strong style="color: #1b1b1b;">{{ $day['name'] }}</strong></td>
                <td style="width: 100px; text-align: right; padding: 0.5em 0; vertical-align: top;">{{ $day['minutes']/60 }}</td>
            </tr>
        @endforeach
            <tr>
                <td colspan="3" style="text-align: right;">
                    Total hours: {{ $tasks->sum('minutes')/60 }}
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;  color: rgb(34 197 94 / 1);">
                    @if($tasks->where('is_service', 1)->isNotEmpty())
                        Free of Charge: {{ $tasks->where('is_service', 1)->sum('minutes')/60 }}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <a href="{{ env('APP_URL') }}" style="display: block; margin-top: 2em; text-align: center; color: #1b1b1b; text-underline: #1b1b1b">Login</a>
@endsection

