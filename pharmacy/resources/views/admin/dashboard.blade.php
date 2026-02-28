@extends('layouts.app')
@section('header')<h2>Administrator Dashboard</h2>@endsection
@section('content')
<div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<p>Companies: {{ $companyCount }}</p>
<p>Subscriptions: {{ $subscriptionCount }}</p>
<p>Users: {{ $usersCount }}</p>

<h3>Reset User Password</h3>
<form method="POST" action="{{ route('admin.reset-credentials') }}">
    @csrf
    <div><label>User ID</label><input type="number" name="user_id" required /></div>
    <div><label>New password</label><input type="password" name="password" required /></div>
    <div><label>Confirm</label><input type="password" name="password_confirmation" required /></div>
    <div><button class="bg-blue-500 text-white px-4 py-2">Reset</button></div>
</form>

</div></div>
@endsection
