@extends('mail.base')

@section('header')
    {{ __('Work Log') . ' ' . \Carbon\Carbon::now()->subMonth()->locale('en')->translatedFormat('F') }}
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
            <th style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1em; font-weight: bold; color: black; text-align:right;">Deliverables</th>
            <th style="border-bottom: 1px solid #e5e7eb; width: 100px; text-align: right; padding-bottom: 1em; font-weight: bold; color: black;">Hours</th>
            <th style="border-bottom: 1px solid #e5e7eb; width: 100px; text-align: right; padding-bottom: 1em; padding-right: 1em; font-weight: bold; color: black;">Price</th>
        </tr>
        </thead>
        <tbody>
        @foreach($tasks->groupBy(function($task) {
            return \Carbon\Carbon::parse($task['completed_at'])->week;
            }) as $week => $weekTasks)
            <tr style="background-color: {{ $week % 2 == 0 ? '#F3F4F6' : '' }};">
                <td style="width: 125px; vertical-align: top; padding: 1em 0 1em 1em;">
                    <strong style="color: black;">Week {{ $week }}</strong><br />
                    {{ \Carbon\Carbon::now()->setISODate(\Carbon\Carbon::now()->year, $week)->startOfWeek()->format('d M') }} - {{ \Carbon\Carbon::now()->setISODate(\Carbon\Carbon::now()->year, $week)->endOfWeek()->format('d M') }}
                </td>
                <td style="padding: 1em 0;text-align:right;  vertical-align: top;">
                    {{ $weekTasks->count() }} tasks
                </td>
                <td style="width: 100px; text-align: right; padding: 1em 0; vertical-align: top;">
                    {{ round($weekTasks->sum('minutes')/60, 2) }}
                </td>
                <td style="width: 100px; text-align: right; padding: 1em 1em 1em 0; vertical-align: top;">
                    @if ($project->is_fixed == 1)

                    @else
                        &euro; {{ round(round($weekTasks->sum('minutes')/60, 2)  * $project->hour_tariff) }}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align: right; padding: 1em; color: black; font-weight: bold;">
                Total hours: {{ round($tasks->sum('minutes')/60, 2) }}
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; padding: 0 1em; color: black; font-weight: bold;">
                @if ($project->is_fixed == 1)

                @else
                    Subtotal : &euro; {{ round(round($tasks->sum('minutes')/60, 2) * $project->hour_tariff) }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; padding: 1em; color: rgb(34 197 94 / 1);">
                @if($tasks->where('is_service', 1)->isNotEmpty())
                    – Free of Charge : {{ round($tasks->where('is_service', 1)->sum('minutes')/60, 2) }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; padding: 0 1em; color: black; font-weight: bold;">
                @if ($project->is_fixed == 1)

                @else
                    Total : &euro; {{ round(round(round($tasks->sum('minutes')/60, 2) * $project->hour_tariff) - (round($tasks->where('is_service', 1)->sum('minutes')/60, 2) * $project->hour_tariff))  }}
                @endif
            </td>
        </tr>
        </tbody>
    </table>
@endsection
