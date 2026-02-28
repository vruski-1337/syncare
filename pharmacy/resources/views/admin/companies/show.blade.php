@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Company Details
    </h2>
@endsection

<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <p><strong>Name:</strong> {{ $company->name }}</p>
                <p><strong>Email:</strong> {{ $company->email }}</p>
                <p><strong>Domain:</strong> {{ $company->domain }}</p>
                <p><strong>Address:</strong> {{ $company->address }}</p>
                <p><strong>Footer:</strong> {{ $company->footer_text }}</p>
            </div>
        </div>
    </div>
</div>
