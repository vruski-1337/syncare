@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Subscription Details</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 space-y-2">
                <p><strong>ID:</strong> {{ $subscription->id }}</p>
                <p><strong>Company:</strong> {{ $subscription->company?->name ?? '-' }}</p>
                <p><strong>Type:</strong> {{ $subscription->type }}</p>
                <p><strong>Start Date:</strong> {{ $subscription->start_date }}</p>
                <p><strong>End Date:</strong> {{ $subscription->end_date }}</p>
                <p><strong>Active:</strong> {{ $subscription->active ? 'Yes' : 'No' }}</p>
                <p><strong>Reminder Sent:</strong> {{ $subscription->reminder_sent_at ?: 'Not sent' }}</p>

                <div class="pt-4 flex gap-4">
                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="text-blue-600">Edit</a>
                    <a href="{{ route('admin.subscriptions.index') }}" class="text-gray-600">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
