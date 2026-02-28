@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Companies
    </h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <a href="{{ route('admin.companies.create') }}" class="text-blue-500">New Company</a>

                <table class="min-w-full mt-4">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Domain</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $company)
                            <tr>
                                <td>{{ $company->id }}</td>
                                <td>{{ $company->name }}</td>
                                <td>{{ $company->email }}</td>
                                <td>{{ $company->domain }}</td>
                                <td>
                                    <a href="{{ route('admin.companies.edit',$company) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.companies.destroy',$company) }}" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500" onclick="return confirm('Delete?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $companies->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
