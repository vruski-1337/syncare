@extends('layouts.app')
@section('header')<h2>Edit Product</h2>@endsection
<div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<form method="POST" action="{{ route('products.update',$product) }}">
    @csrf @method('PUT')
    <div><label>Name</label><input type="text" name="name" value="{{ old('name',$product->name) }}"/></div>
    <div><label>Description</label><textarea name="description">{{ old('description',$product->description) }}</textarea></div>
    <div><label>Category</label><input type="text" name="category" value="{{ old('category',$product->category) }}"/></div>
    <div><label>Price</label><input type="number" step="0.01" name="price" value="{{ old('price',$product->price) }}"/></div>
    <div><label>Quantity</label><input type="number" step="0.01" name="quantity" value="{{ old('quantity',$product->quantity) }}"/></div>
    <div><label>Unit</label><input type="text" name="unit" value="{{ old('unit',$product->unit) }}"/></div>
    <div><button class="bg-blue-500 text-white px-4 py-2">Save</button></div>
</form>
</div></div>
