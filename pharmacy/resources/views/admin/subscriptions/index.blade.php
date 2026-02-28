@extends('layouts.app')

@section('header')
    <h2>Subscriptions</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('admin.subscriptions.create') }}" class="text-blue-500">New Subscription</a>
        <table class="min-w-full mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptions as $sub)
                <tr>
                    <td>{{ $sub->id }}</td>
                    <td>{{ $sub->company->name }}</td>
                    <td>{{ $sub->type }}</td>
                    <td>{{ $sub->start_date }}</td>
                    <td>{{ $sub->end_date }}</td>
                    <td>{{ $sub->active ? 'yes' : 'no' }}</td>
                    <td>
                        <a href="{{ route('admin.subscriptions.show',$sub) }}">View</a>
                        <a href="{{ route('admin.subscriptions.edit',$sub) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.subscriptions.destroy',$sub) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="text-red-500" onclick="return confirm('Delete?')">Del</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $subscriptions->links() }}
    </div>
</div>
@endsection
