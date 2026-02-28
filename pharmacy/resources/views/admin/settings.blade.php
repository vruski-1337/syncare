@extends('layouts.app')
@section('header')<h2>Settings</h2>@endsection
<div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<form method="POST" action="{{ route('admin.settings') }}">
    @csrf
    <div><label>GMail Client ID</label><input type="text" name="gmail_client_id" value="{{ old('gmail_client_id',$settings['gmail_client_id']) }}"/></div>
    <div><label>GMail Client Secret</label><input type="text" name="gmail_client_secret" value="{{ old('gmail_client_secret',$settings['gmail_client_secret']) }}"/></div>
    <div><label>Database URI</label><input type="text" name="database_uri" value="{{ old('database_uri',$settings['database_uri']) }}"/></div>
    <div><label>Footer Text</label><input type="text" name="footer_text" value="{{ old('footer_text',$settings['footer_text']) }}"/></div>
    <div><button class="bg-blue-500 text-white px-4 py-2">Save</button></div>
</form>
</div></div>
