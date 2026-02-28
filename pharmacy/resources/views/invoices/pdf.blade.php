<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        .invoice-copy { width: 100%; border-bottom: 2px dashed #333; margin-bottom: 20px; }
        .half { width: 48%; display: inline-block; vertical-align: top; }
    </style>
</head>
<body>
    <div class="invoice-copy">
        <div class="half">
            <h2>Store Copy</h2>
            @include('invoices.partials.details', ['invoice' => $invoice])
        </div>
        <div class="half">
            <h2>Customer Copy</h2>
            @include('invoices.partials.details', ['invoice' => $invoice])
        </div>
    </div>
</body>
</html>
