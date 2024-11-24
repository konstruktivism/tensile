@extends('mail.base')

@section('header')
    {{ __('Work Log for') . ' ' . \Carbon\Carbon::now()->subMonth()->locale('en')->translatedFormat('F') }}
@endsection

@section('body')
    {{ $project->organisation->name }} · {{ $project->name }}
    @if ($project->is_fixed == 1)
        <div style="color: black; background: #FACC15; display: inline-block;  padding: 1px 6px; border-radius: 4px; text-transform: uppercase; font-size: 12px; margin: 0 0 0 1em;">Fixed Price</div>
    @endif

    <a href="{{ url('/login') }}" style="color: #3B82F6; text-decoration: none; font-size: 14px; display: block; margin-top: 1em;">Client login</a>

    <table style="width: 100%;">
        <thead>
        <tr>
            <th style="width: 50%; border-bottom: 1px solid #e5e7eb; padding-bottom: 1em; font-weight: normal;">Week</th>
            <th style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1em; font-weight: normal; text-align: right;">Tasks</th>
            <th style="border-bottom: 1px solid #e5e7eb; width: 100px; text-align: right; padding-bottom: 1em; font-weight: normal;">Hours</th>
        </tr>
        </thead>
        <tbody>
        @foreach($tasks->groupBy(function($task) {
            return \Carbon\Carbon::parse($task['completed_at'])->week;
            }) as $week => $weekTasks)
            <tr>
                <td style="padding: 1em 0;">
                    <strong style="color: black;">Week {{ $week }}</strong>  ({{ \Carbon\Carbon::now()->setISODate(\Carbon\Carbon::now()->year, $week)->startOfWeek()->format('d M') }} - {{ \Carbon\Carbon::now()->setISODate(\Carbon\Carbon::now()->year, $week)->endOfWeek()->format('d M') }})
                </td>
                <td style="padding: 1em 0; text-align: right;">
                    {{ $weekTasks->count() }} tasks
                </td>
                <td style="padding: 1em 0; text-align: right;">
                    {{ $weekTasks->sum('minutes')/60 }}
                </td>
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
@endsection
