@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Invoice Details</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 space-y-2">
                <p><strong>ID:</strong> {{ $invoice->id }}</p>
                <p><strong>Number:</strong> {{ $invoice->number }}</p>
                <p><strong>Total:</strong> {{ $invoice->total }}</p>
                <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
                <p><strong>Created:</strong> {{ $invoice->created_at }}</p>

                <div class="pt-4 flex gap-4">
                    <a href="{{ route('invoices.edit', $invoice) }}" class="text-blue-600">Edit</a>
                    <a href="{{ route('invoices.pdf', $invoice) }}" class="text-green-600">Download PDF</a>
                    <a href="{{ route('invoices.index') }}" class="text-gray-600">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
