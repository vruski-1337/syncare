<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected function company()
    {
        $company = auth()->user()->company;

        if (! $company) {
            abort(403, 'No company is linked to your account.');
        }

        return $company;
    }

    public function index()
    {
        $invoices = $this->company()->invoices()->paginate(20);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
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

        Invoice::create($data);

        return redirect()->route('invoices.index')->with('success', 'Invoice created');
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

        return redirect()->route('invoices.index')->with('success', 'Invoice updated');
    }

    public function destroy(string $id)
    {
        $invoice = $this->company()->invoices()->findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Deleted');
    }

    public function pdf(string $id)
    {
        $invoice = $this->company()->invoices()->findOrFail($id);
        $pdf = \PDF::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('invoice_'.$invoice->id.'.pdf');
    }
}
