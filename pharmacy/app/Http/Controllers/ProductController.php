<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected function company()
    {
        return auth()->user()->company;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->company()->products()->paginate(20);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'unit' => 'nullable|string|max:50',
        ]);

        $data['company_id'] = $this->company()->id;
        \App\Models\Product::create($data);
        return redirect()->route('products.index')->with('success', 'Product saved');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = $this->company()->products()->findOrFail($id);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = $this->company()->products()->findOrFail($id);
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = $this->company()->products()->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'unit' => 'nullable|string|max:50',
        ]);

        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Product updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = $this->company()->products()->findOrFail($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Deleted');
    }
}
