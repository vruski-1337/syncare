@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Product Details</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 space-y-2">
                <p><strong>ID:</strong> {{ $product->id }}</p>
                <p><strong>Name:</strong> {{ $product->name }}</p>
                <p><strong>Description:</strong> {{ $product->description ?: '-' }}</p>
                <p><strong>Category:</strong> {{ $product->category ?: '-' }}</p>
                <p><strong>Price:</strong> {{ $product->price }}</p>
                <p><strong>Quantity:</strong> {{ $product->quantity }}</p>
                <p><strong>Unit:</strong> {{ $product->unit ?: '-' }}</p>

                <div class="pt-4 flex gap-4">
                    <a href="{{ route('products.edit', $product) }}" class="text-blue-600">Edit</a>
                    <a href="{{ route('products.index') }}" class="text-gray-600">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
