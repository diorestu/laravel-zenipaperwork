@extends('layouts.app')

@section('content')
<h1 class="mb-4 text-lg font-semibold">Edit Invoice</h1>
@include('invoices.form', ['action' => route('invoices.update', $invoice), 'method' => 'PUT'])
@endsection
