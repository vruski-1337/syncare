@extends('layouts.app')
@section('header')<h2>Edit Invoice</h2>@endsection
@section('content')
<div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<form method="POST" action="{{ route('invoices.update',$invoice) }}">
    @csrf @method('PUT')
    <div><label>Total</label><input type="number" step="0.01" name="total" value="{{ old('total',$invoice->total) }}"/></div>
    <div><label>Status</label><select name="status">
        <option value="draft" {{ $invoice->status=='draft'?'selected':'' }}>Draft</option>
        <option value="issued" {{ $invoice->status=='issued'?'selected':'' }}>Issued</option>
        <option value="paid" {{ $invoice->status=='paid'?'selected':'' }}>Paid</option>
        <option value="cancelled" {{ $invoice->status=='cancelled'?'selected':'' }}>Cancelled</option>
    </select></div>
    <div><button class="bg-blue-500 text-white px-4 py-2">Save</button></div>
</form>
</div></div>
@endsection
