@extends('layouts.app')

@section('content')
<h1 class="mb-4 text-lg font-semibold">Create Quotation</h1>
@include('quotations.form', ['quotation' => null, 'action' => route('quotations.store'), 'method' => 'POST'])
@endsection
