<table class="min-w-full">
    <thead>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price,2) }}</td>
                <td>{{ number_format($item->quantity * $item->price,2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<p><strong>Grand Total:</strong> {{ number_format($invoice->total,2) }}</p>
