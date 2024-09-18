# {{ $project->organisation->name }} · {{ $project->name }}

*{{ $project->description }}*

---

## Week {{ $week }}

| Datum | Deliverables | Uren | Prijs |
|-------|--------------|------|-------|
@foreach($tasks as $index => $day)
| {{ \Carbon\Carbon::parse($day['completed_at'])->format('D d-m') }} | **{{ $day['name'] }}**<br>{{ $day['description'] }} | {{ $day['hours'] }} | &euro; {{ $day['hours'] * 65 }} |
@endforeach

---

**Total Hours:** {{ $tasks->sum('hours') }}
**Total Price:** &euro; {{ $tasks->sum('hours') * $project->hour_tariff }}

---

**{{ \Carbon\Carbon::now()->format('F Y') }}**
