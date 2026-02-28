<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected function company()
    {
        return auth()->user()->company;
    }

    public function index()
    {
        $invoices = $this->company()->invoices()->paginate(20);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        // invoice line items handled separately later
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'total' => 'required|numeric',
            'status' => 'required|in:draft,issued,paid,cancelled',
        ]);

        $data['company_id'] = $this->company()->id;
        $data['user_id'] = auth()->id();
        $data['number'] = 'INV-' . time();
        \App\Models\Invoice::create($data);
        return redirect()->route('invoices.index')->with('success','Invoice created');
    }

    public function show(string $id)
    {
        $invoice = $this->company()->invoices()->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(string $id)
    {
        $invoice = $this->company()->invoices()->findOrFail($id);
        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, string $id)
    {
        $invoice = $this->company()->invoices()->findOrFail($id);
        $data = $request->validate([
            'total' => 'required|numeric',
            'status' => 'required|in:draft,issued,paid,cancelled',
        ]);
        $invoice->update($data);
        return redirect()->route('invoices.index')->with('success','Invoice updated');
    }

    public function destroy(string $id)
    {
        $invoice = $this->company()->invoices()->findOrFail($id);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success','Deleted');
    }
}
