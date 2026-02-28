@extends('layouts.app')
@section('header')<h2>Products</h2>@endsection
<div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<a href="{{ route('products.create') }}" class="text-blue-500">New Product</a>
<table class="min-w-full mt-4"><thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Qty</th><th>Unit</th><th>Actions</th></tr></thead>
<tbody>@foreach($products as $p)<tr><td>{{ $p->id }}</td><td>{{ $p->name }}</td><td>{{ $p->price }}</td><td>{{ $p->quantity }}</td><td>{{ $p->unit }}</td><td><a href="{{ route('products.edit',$p) }}">Edit</a> <form method="POST" action="{{ route('products.destroy',$p) }}" style="display:inline">@csrf @method('DELETE')<button class="text-red-500" onclick="return confirm('Delete?')">Del</button></form></td></tr>@endforeach</tbody>
</table>{{ $products->links() }}
</div></div>
