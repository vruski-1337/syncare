<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = \App\Models\Company::paginate(15);
        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'domain' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'footer_text' => 'nullable|string|max:255',
            'active' => 'boolean',
        ]);

        \App\Models\Company::create($data);
        return redirect()->route('admin.companies.index')->with('success', 'Company created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = \App\Models\Company::findOrFail($id);
        return view('admin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $company = \App\Models\Company::findOrFail($id);
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $company = \App\Models\Company::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'domain' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'footer_text' => 'nullable|string|max:255',
            'active' => 'boolean',
        ]);

        $company->update($data);
        return redirect()->route('admin.companies.index')->with('success', 'Company updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = \App\Models\Company::findOrFail($id);
        $company->delete();
        return redirect()->route('admin.companies.index')->with('success', 'Company removed');
    }

    /**
     * Dashboard for company users (owners/managers)
     */
    public function dashboard()
    {
        $company = auth()->user()->company;
        if (! $company) {
            abort(404);
        }
        // placeholder stats
        $stats = [
            'products' => $company->products()->count(),
            'invoices' => $company->invoices()->count(),
        ];
        return view('company.dashboard', compact('company','stats'));
    }
}
