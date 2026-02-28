@extends('layouts.app')
@section('header')<h2>New Product</h2>@endsection
<div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<form method="POST" action="{{ route('products.store') }}">
    @csrf
    <div><label>Name</label><input type="text" name="name" value="{{ old('name') }}"/></div>
    <div><label>Description</label><textarea name="description">{{ old('description') }}</textarea></div>
    <div><label>Category</label><input type="text" name="category"/></div>
    <div><label>Price</label><input type="number" step="0.01" name="price"/></div>
    <div><label>Quantity</label><input type="number" step="0.01" name="quantity"/></div>
    <div><label>Unit</label><input type="text" name="unit"/></div>
    <div><button class="bg-blue-500 text-white px-4 py-2">Save</button></div>
</form>
</div></div>
