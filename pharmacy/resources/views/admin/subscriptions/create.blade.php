@extends('layouts.app')
@section('header')<h2>New Subscription</h2>@endsection
@section('content')
<div class="py-6">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<form method="POST" action="{{ route('admin.subscriptions.store') }}">
    @csrf
    <div><label>Company</label>
        <select name="company_id">
            @foreach($companies as $id=>$name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div><label>Type</label>
        <select name="type">
            <option>yearly</option><option>monthly</option><option>weekly</option><option>custom</option>
        </select>
    </div>
    <div><label>Start Date</label><input type="date" name="start_date"/></div>
    <div><label>End Date</label><input type="date" name="end_date"/></div>
    <div><label>Active</label><input type="checkbox" name="active" value="1" checked/></div>
    <div><button class="bg-blue-500 text-white px-4 py-2">Save</button></div>
</form>
</div>
</div>
@endsection
