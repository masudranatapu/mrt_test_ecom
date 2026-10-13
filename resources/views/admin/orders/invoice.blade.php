<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $order->order_number }}</title>
    @vite(['resources/sass/admin.scss'])
    <style>
        body { background: #f3f4f6; }
        .invoice { max-width: 820px; margin: 2rem auto; background: #fff; padding: 2.5rem; border-radius: .75rem; }
        @media print {
            body { background: #fff; }
            .invoice { margin: 0; padding: 0; box-shadow: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>

<body>
    <div class="text-center mt-3 no-print">
        <button onclick="window.print()" class="btn btn-primary px-4">Print Invoice</button>
    </div>

    <div class="invoice shadow-sm">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="fw-bold mb-0">{{ config('app.name') }}</h2>
                <div class="text-muted small">Dhaka, Bangladesh &middot; support@example.com</div>
            </div>
            <div class="text-end">
                <h4 class="fw-bold text-primary mb-0">INVOICE</h4>
                <div class="small">{{ $order->order_number }}</div>
                <div class="small text-muted">{{ $order->created_at->format('d M Y') }}</div>
            </div>
        </div>

        <div class="row mb-4 small">
            <div class="col-6">
                <div class="text-muted text-uppercase fw-semibold mb-1">Bill / Ship to</div>
                <div class="fw-semibold">{{ $order->shipping_name }}</div>
                <div>{{ $order->shipping_phone }}</div>
                <div>{{ $order->shipping_full_address }}</div>
                @if ($order->customer?->business_name)
                    <div>{{ $order->customer->business_name }}</div>
                @endif
            </div>
            <div class="col-6 text-end">
                <div class="text-muted text-uppercase fw-semibold mb-1">Payment</div>
                <div>{{ $order->payment_method_label }}</div>
                <div>Status: <strong>{{ $order->payment_status_label }}</strong></div>
                @if ($order->due_date)
                    <div>Due: {{ $order->due_date->format('d M Y') }}</div>
                @endif
            </div>
        </div>

        <table class="table table-bordered small">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th class="text-end">Price</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product_name }}<div class="text-muted">SKU: {{ $item->sku }}</div></td>
                        <td class="text-end">{{ money($item->price) }}</td>
                        <td class="text-center">{{ $item->quantity }} {{ $item->unit }}</td>
                        <td class="text-end">{{ money($item->line_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end">Subtotal</td>
                    <td class="text-end">{{ money($order->subtotal) }}</td>
                </tr>
                @if ((float) $order->discount > 0)
                    <tr>
                        <td colspan="4" class="text-end">Discount</td>
                        <td class="text-end">−{{ money($order->discount) }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="4" class="text-end">Delivery</td>
                    <td class="text-end">{{ money($order->shipping_charge) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end fw-bold">Grand Total</td>
                    <td class="text-end fw-bold">{{ money($order->total) }}</td>
                </tr>
            </tfoot>
        </table>

        <p class="text-center text-muted small mt-4 mb-0">Thank you for your business!</p>
    </div>
</body>

</html>
