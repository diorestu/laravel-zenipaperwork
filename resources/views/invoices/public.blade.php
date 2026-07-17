@extends('layouts.guest')

@section('content')
<div class="w-full">
    <x-document.preview :document="$invoice" />
</div>
@endsection
