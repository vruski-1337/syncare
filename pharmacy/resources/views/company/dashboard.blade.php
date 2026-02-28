@extends('layouts.app')
@section('header')<h2>Company Dashboard</h2>@endsection
<div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<p>Welcome, {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
<p>Company: {{ $company->name }}</p>
<ul>
    <li>Products: {{ $stats['products'] }}</li>
    <li>Invoices: {{ $stats['invoices'] }}</li>
</ul>
</div></div>
