@props(['date'])
<span>{{ $date ? \Illuminate\Support\Carbon::parse($date)->format('d M Y') : '-' }}</span>
