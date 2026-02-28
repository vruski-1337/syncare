@extends('layouts.app')
@section('header')<h2>New Invoice</h2>@endsection
@section('content')
<div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<form method="POST" action="{{ route('invoices.store') }}">
    @csrf
    <div><label>Total</label><input type="number" step="0.01" name="total"/></div>
    <div><label>Status</label><select name="status">
        <option value="draft">Draft</option>
        <option value="issued">Issued</option>
        <option value="paid">Paid</option>
        <option value="cancelled">Cancelled</option>
    </select></div>
    <div><button class="bg-blue-500 text-white px-4 py-2">Save</button></div>
</form>
</div></div>
@endsection
