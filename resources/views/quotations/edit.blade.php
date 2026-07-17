@extends('layouts.app')

@section('content')
<h1 class="mb-4 text-lg font-semibold">Edit Quotation</h1>
@include('quotations.form', ['action' => route('quotations.update', $quotation), 'method' => 'PUT'])
@endsection
