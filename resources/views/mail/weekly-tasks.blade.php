@extends('mail.base')

@section('header')
    {{ __('Work Log week') . ' ' .  $week }}
@endsection

@section('body')
    {{ $project->organisation->name }} · {{ $project->name }}
    @if ($project->is_fixed == 1)
        <div style="color: black; background: #FACC15; display: inline-block;  padding: 1px 6px; border-radius: 4px; text-transform: uppercase; font-size: 12px; margin: 0 0 0 1em;">Fixed Price</div>
    @endif

    <div style="padding: .5em 0; font-size: 13px; font-style: italic;">
        {{ $project->description }}
    </div>

    <div style="padding: 0 0 2em 0;">
        <a href="{{ url('/login') }}" style="color: #3B82F6; text-decoration: none; font-size: 14px; display: block; margin-top: 1em;">Login as a client and view all work logs</a>
    </div>

    <table style="width: 100%;">
        <thead>
        <tr>
            <th style="width: 125px; border-bottom: 1px solid #e5e7eb; padding-left: 1em; padding-bottom: 1em; font-weight: bold; color: black;">Date</th>
            <th style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1em; font-weight: bold; color: black;">Deliverables</th>
            <th style="border-bottom: 1px solid #e5e7eb; width: 100px; text-align: right; padding-bottom: 1em; font-weight: bold; color: black;">Hours</th>
            <th style="border-bottom: 1px solid #e5e7eb; width: 100px; text-align: right; padding-bottom: 1em; padding-right: 1em; font-weight: bold; color: black;">Price</th>
        </tr>
        </thead>
        <tbody>
        @foreach($tasks as $index => $day)
            <tr style="background-color: {{ $index % 2 == 0 ? '#F3F4F6' : '' }};">
                <td style="width: 125px; vertical-align: top; padding: 1em 0 1em 1em;">{{ \Carbon\Carbon::parse($day['completed_at'])->format('D d-m') }}</td>
                <td style="padding: 1em 0;"><span style="color: #1b1b1b;">{{ $day['name'] }}</span><br />
                    @if ($day['is_service'])
                        <div style="color: rgb(34 197 94 / 1); font-size: 12px; text-transform: uppercase; font-family: monospace;">Free of Charge</div>
                    @endif
                </td>
                <td style="width: 100px; text-align: right; padding: 1em 0; vertical-align: top;">{{ round($day['minutes']/60, 2) }}</td>
                <td style="width: 100px; text-align: right; padding: 1em 1em 1em 0; vertical-align: top;">
                    @if ($project->is_fixed == 1)

                    @else
                        &euro; {{ round(round($day['minutes']/60, 2)  * $project->hour_tariff) }}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align: right; padding: 1em; color: black; font-weight: bold;">
                Total hours : {{ round($tasks->sum('minutes')/60, 2) }}
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; padding: 0 1em; color: black; font-weight: bold;">
                @if ($project->is_fixed == 1)

                @else
                    Total : &euro; {{ round(round($tasks->sum('minutes')/60, 2) * $project->hour_tariff) }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; padding: 0 1em;">
                &nbsp;
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; color: rgb(34 197 94 / 1); padding: 0 1em; font-size: 12px; text-transform: uppercase; font-family: monospace;">
                @if($tasks->where('is_service', 1)->isNotEmpty())Free of Charge: {{ $tasks->where('is_service', 1)->sum('minutes')/60 }}@endif
            </td>
        </tr>
        </tbody>
    </table>
@endsection

