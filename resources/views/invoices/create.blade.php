@extends('layouts.app')

@section('content')
<h1 class="mb-4 text-lg font-semibold">Create Invoice</h1>
@include('invoices.form', ['invoice' => null, 'action' => route('invoices.store'), 'method' => 'POST'])
@endsection
