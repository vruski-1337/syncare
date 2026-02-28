@extends('layouts.app')
@section('header')<h2>Invoices</h2>@endsection
@section('content')
<div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<a href="{{ route('invoices.create') }}" class="text-blue-500">New Invoice</a>
<table class="min-w-full mt-4"><thead><tr><th>ID</th><th>Number</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>@foreach($invoices as $inv)<tr><td>{{ $inv->id }}</td><td>{{ $inv->number }}</td><td>{{ $inv->total }}</td><td>{{ $inv->status }}</td><td><a href="{{ route('invoices.show',$inv) }}">View</a> <a href="{{ route('invoices.edit',$inv) }}">Edit</a> <form method="POST" action="{{ route('invoices.destroy',$inv) }}" style="display:inline">@csrf @method('DELETE')<button class="text-red-500" onclick="return confirm('Delete?')">Del</button></form></td></tr>@endforeach</tbody></table>{{ $invoices->links() }}
</div></div>
@endsection
