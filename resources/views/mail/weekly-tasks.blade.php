@extends('mail.base')

@section('header')
    {{ __('Work Log week') . ' ' .  $week }}
@endsection

@section('body')
    {{ $project->organisation->name }} · {{ $project->name }}
    @if ($project->is_fixed == 1)
        <div style="color: black; background: #FACC15; display: inline-block;  padding: 1px 6px; border-radius: 4px; text-transform: uppercase; font-size: 12px; margin: 0 0 0 1em;">Fixed Price</div>
    @endif

    <a href="{{ url('/login') }}" style="color: #3B82F6; text-decoration: none; font-size: 14px; display: block; margin-top: 1em;">Client login</a>

    <div style="padding: 1em 0 2em 0;">
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
            <tr style="background-color: {{ $index % 2 == 0 ? '#F3F4F6' : '' }};">
                <td style="width: 125px; vertical-align: top; padding: 1em 0 1em 1em;">{{ \Carbon\Carbon::parse($day['completed_at'])->format('D d-m') }}</td>
                <td style="padding: 1em 0;"><span style="color: #1b1b1b; font-weight: bold;">{{ $day['name'] }}</span><br />
                    @if ($day['is_service'])
                        <div style="color: rgb(34 197 94 / 1); font-size: 12px; text-transform: uppercase; font-family: monospace;">Free of Charge</div>
                    @endif
                </td>
                <td style="width: 100px; text-align: right; padding: 1em; vertical-align: top;">{{ $day['minutes']/60 }}</td>
            </tr>
        @endforeach
            <tr>
                <td colspan="3" style="text-align: right; padding: 1em;">
                    Total hours: {{ $tasks->sum('minutes')/60 }}
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right; padding: 0 1em;">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right; color: rgb(34 197 94 / 1); padding: 0 1em; font-size: 12px; text-transform: uppercase; font-family: monospace;">
                    @if($tasks->where('is_service', 1)->isNotEmpty())Free of Charge: {{ $tasks->where('is_service', 1)->sum('minutes')/60 }}@endif
                </td>
            </tr>
        </tbody>
    </table>
@endsection

