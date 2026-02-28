@extends('layouts.app')
@section('header')<h2>Edit Subscription</h2>@endsection
@section('content')
<div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<form method="POST" action="{{ route('admin.subscriptions.update',$subscription) }}">
    @csrf @method('PUT')
    <div><label>Company</label>
        <select name="company_id">
            @foreach($companies as $id=>$name)
                <option value="{{ $id }}" {{ $subscription->company_id==$id?'selected':'' }}>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div><label>Type</label>
        <select name="type">
            <option {{ $subscription->type=='yearly'?'selected':'' }}>yearly</option>
            <option {{ $subscription->type=='monthly'?'selected':'' }}>monthly</option>
            <option {{ $subscription->type=='weekly'?'selected':'' }}>weekly</option>
            <option {{ $subscription->type=='custom'?'selected':'' }}>custom</option>
        </select>
    </div>
    <div><label>Start Date</label><input type="date" name="start_date" value="{{ $subscription->start_date->format('Y-m-d') }}"/></div>
    <div><label>End Date</label><input type="date" name="end_date" value="{{ $subscription->end_date->format('Y-m-d') }}"/></div>
    <div><label>Active</label><input type="checkbox" name="active" value="1" {{ $subscription->active ? 'checked':'' }} /></div>
    <div><button class="bg-blue-500 text-white px-4 py-2">Save</button></div>
</form>
</div></div>
@endsection
