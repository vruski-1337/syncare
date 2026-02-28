<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = \App\Models\Subscription::with('company')->paginate(25);
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $companies = \App\Models\Company::pluck('name','id');
        return view('admin.subscriptions.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'type' => 'required|in:yearly,monthly,weekly,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'active' => 'boolean',
        ]);

        \App\Models\Subscription::create($data);
        return redirect()->route('admin.subscriptions.index')->with('success','Subscription created');
    }

    public function show(string $id)
    {
        $subscription = \App\Models\Subscription::findOrFail($id);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function edit(string $id)
    {
        $subscription = \App\Models\Subscription::findOrFail($id);
        $companies = \App\Models\Company::pluck('name','id');
        return view('admin.subscriptions.edit', compact('subscription','companies'));
    }

    public function update(Request $request, string $id)
    {
        $subscription = \App\Models\Subscription::findOrFail($id);
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'type' => 'required|in:yearly,monthly,weekly,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'active' => 'boolean',
        ]);
        $subscription->update($data);
        return redirect()->route('admin.subscriptions.index')->with('success','Subscription updated');
    }

    public function destroy(string $id)
    {
        $subscription = \App\Models\Subscription::findOrFail($id);
        $subscription->delete();
        return redirect()->route('admin.subscriptions.index')->with('success','Subscription deleted');
    }
}
