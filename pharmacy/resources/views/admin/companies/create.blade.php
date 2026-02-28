@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        New Company
    </h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('admin.companies.store') }}">
                    @csrf
                    <div>
                        <label>Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" />
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" />
                    </div>
                    <div>
                        <label>Domain</label>
                        <input type="text" name="domain" value="{{ old('domain') }}" />
                    </div>
                    <div>
                        <label>Footer Text</label>
                        <input type="text" name="footer_text" value="{{ old('footer_text') }}" />
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
