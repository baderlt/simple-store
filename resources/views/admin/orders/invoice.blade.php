@php
    $storeName = settings('store_name', 'Simple Store');
    $storeLogo = settings('logo');
    $storeAddress = settings('address');
    $storePhone = settings('phone');
    $storeEmail = settings('email');
    $storeWhatsapp = settings('whatsapp');
    $isFreeDelivery = (float) $order->delivery_fee === 0.0;
    $statusLabel = $order->status_label;
    $statusClass = match ($order->status) {
        'delivered' => 'status-success',
        'cancelled' => 'status-danger',
        'out_for_delivery' => 'status-purple',
        'preparing' => 'status-info',
        default => 'status-warning',
    };
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('invoice') }} {{ $order->order_number }} · {{ $storeName }}</title>
    @include('layouts.google-fonts')
    @include('layouts.typography-overrides')
    <style>
        :root {
            --brand: #059669;
            --brand-dark: #065f46;
            --brand-soft: #ecfdf5;
            --ink: #17211d;
            --muted: #69746f;
            --line: #e5e9e7;
            --surface: #f5f7f6;
            --white: #fff;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--surface);
            color: var(--ink);
            font-family: "Jost", Arial, sans-serif;
            font-size: 14px;
            line-height: 1.55;
        }

        [dir="rtl"] body { font-family: "Tajawal", "Noto Sans Arabic", Arial, sans-serif; }

        .invoice-shell {
            width: min(100% - 32px, 980px);
            margin: 32px auto;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .toolbar-actions { display: flex; gap: 10px; }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 18px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--white);
            color: var(--ink);
            font: inherit;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .button-primary {
            border-color: var(--brand);
            background: var(--brand);
            color: var(--white);
        }

        .invoice {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 24px;
            background: var(--white);
            box-shadow: 0 24px 70px rgba(24, 52, 40, .09);
        }

        .invoice::before {
            position: absolute;
            inset: 0 0 auto;
            height: 7px;
            background: linear-gradient(90deg, var(--brand-dark), #10b981, #6ee7b7);
            content: "";
        }

        .invoice-content { padding: 48px; }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 32px;
            padding-bottom: 32px;
            border-bottom: 1px solid var(--line);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .brand-logo {
            width: 76px;
            height: 76px;
            flex: 0 0 76px;
            object-fit: contain;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: var(--white);
            padding: 8px;
        }

        .brand-mark {
            display: grid;
            width: 64px;
            height: 64px;
            flex: 0 0 64px;
            place-items: center;
            border-radius: 18px;
            background: linear-gradient(145deg, var(--brand-dark), var(--brand));
            color: var(--white);
            font-size: 25px;
            font-weight: 800;
        }

        .brand h1 {
            margin: 0;
            font-size: clamp(20px, 3vw, 28px);
            line-height: 1.15;
        }

        .brand p, .invoice-heading p { margin: 5px 0 0; color: var(--muted); }

        .invoice-heading { text-align: end; }

        .eyebrow {
            display: block;
            margin-bottom: 6px;
            color: var(--brand);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .invoice-heading h2 {
            margin: 0;
            font-size: clamp(28px, 5vw, 42px);
            letter-spacing: -.04em;
            line-height: 1;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-top: 14px;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
        }

        .status::before {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
            content: "";
        }

        .status-warning { background: #fffbeb; color: #b45309; }
        .status-info { background: #eff6ff; color: #2563eb; }
        .status-purple { background: #f5f3ff; color: #7c3aed; }
        .status-success { background: var(--brand-soft); color: var(--brand-dark); }
        .status-danger { background: #fef2f2; color: #dc2626; }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin: 28px 0;
        }

        .meta-card {
            min-width: 0;
            padding: 15px 16px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fbfcfb;
        }

        .meta-card span {
            display: block;
            margin-bottom: 5px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .meta-card strong { overflow-wrap: anywhere; }

        .address-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
            margin-bottom: 30px;
        }

        .address-card {
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: 18px;
        }

        .address-card.customer {
            border-color: #bbf7d0;
            background: linear-gradient(145deg, var(--brand-soft), #fff);
        }

        .address-card h3 {
            margin: 0 0 13px;
            color: var(--brand-dark);
            font-size: 12px;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .address-card strong { display: block; margin-bottom: 6px; font-size: 17px; }
        .address-card p { margin: 3px 0; color: var(--muted); overflow-wrap: anywhere; }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: 18px;
        }

        table { width: 100%; border-collapse: collapse; }

        th {
            padding: 14px 16px;
            background: #193129;
            color: var(--white);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .06em;
            text-align: start;
            text-transform: uppercase;
            white-space: nowrap;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid var(--line);
            vertical-align: middle;
        }

        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:nth-child(even) { background: #fafcfb; }
        .product-name { font-weight: 700; }
        .number { text-align: end; white-space: nowrap; }

        .summary-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(300px, 42%);
            gap: 28px;
            align-items: start;
            margin-top: 30px;
        }

        .payment-note {
            padding: 20px;
            border-radius: 18px;
            background: var(--surface);
        }

        .payment-note h3 { margin: 0 0 8px; font-size: 14px; }
        .payment-note p { margin: 4px 0; color: var(--muted); }

        .totals {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 18px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            padding: 11px 16px;
            border-bottom: 1px solid var(--line);
        }

        .totals-row:last-child { border-bottom: 0; }
        .totals-row span:first-child { color: var(--muted); }
        .discount, .free { color: var(--brand); font-weight: 800; }

        .grand-total {
            align-items: center;
            padding: 17px 16px;
            background: var(--brand-soft);
        }

        .grand-total span:first-child { color: var(--brand-dark); font-weight: 800; }
        .grand-total strong { color: var(--brand-dark); font-size: 21px; }

        .notes {
            margin-top: 22px;
            padding: 18px 20px;
            border-inline-start: 4px solid var(--brand);
            border-radius: 12px;
            background: #fafcfb;
        }

        .notes strong { display: block; margin-bottom: 4px; }
        .notes p { margin: 0; color: var(--muted); white-space: pre-line; }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 24px;
            margin-top: 38px;
            padding-top: 24px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: 12px;
        }

        .footer strong { display: block; margin-bottom: 4px; color: var(--ink); font-size: 15px; }
        .footer-reference { text-align: end; }

        @media (max-width: 720px) {
            .invoice-shell { width: min(100% - 20px, 980px); margin: 10px auto; }
            .toolbar { align-items: stretch; }
            .toolbar > span { display: none; }
            .toolbar-actions { width: 100%; }
            .button { flex: 1; padding-inline: 10px; }
            .invoice { border-radius: 18px; }
            .invoice-content { padding: 28px 20px; }
            .invoice-header { flex-direction: column; gap: 24px; }
            .invoice-heading { width: 100%; text-align: start; }
            .meta-grid { grid-template-columns: 1fr; }
            .address-grid { grid-template-columns: 1fr; }
            .summary-row { grid-template-columns: 1fr; }
            .footer { flex-direction: column; align-items: flex-start; }
            .footer-reference { text-align: start; }
            th, td { padding: 12px; }
        }

        @page { size: A4; margin: 12mm; }

        @media print {
            :root { --surface: #fff; }
            body { background: #fff; font-size: 11px; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
            .invoice-shell { width: 100%; margin: 0; }
            .invoice { border: 0; border-radius: 0; box-shadow: none; }
            .invoice-content { padding: 22px 24px; }
            .invoice-header { padding-bottom: 20px; }
            .brand-logo { width: 62px; height: 62px; flex-basis: 62px; }
            .meta-grid { margin: 18px 0; }
            .address-grid { margin-bottom: 20px; }
            .address-card { padding: 15px; }
            .summary-row { margin-top: 20px; }
            .footer { margin-top: 24px; }
            tr, .address-card, .meta-card, .summary-row, .notes { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <main class="invoice-shell">
        <div class="toolbar no-print">
            <span>{{ __('invoice_preview') }}</span>
            <div class="toolbar-actions">
                <a class="button" href="{{ route('admin.orders.show', $order) }}">{{ __('back_to_order') }}</a>
                <button class="button button-primary" type="button" onclick="window.print()">{{ __('print_invoice') }}</button>
            </div>
        </div>

        <article class="invoice">
            <div class="invoice-content">
                <header class="invoice-header">
                    <div class="brand">
                        @if($storeLogo && file_exists(public_path('storage/'.$storeLogo)))
                            <img class="brand-logo" src="{{ asset('storage/'.$storeLogo) }}" alt="{{ $storeName }}">
                        @else
                            <span class="brand-mark">{{ mb_strtoupper(mb_substr($storeName, 0, 1)) }}</span>
                        @endif
                        <div>
                            <h1>{{ $storeName }}</h1>
                            @if($storeAddress)
                                <p>{{ $storeAddress }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="invoice-heading">
                        <span class="eyebrow">{{ __('official_document') }}</span>
                        <h2>{{ __('invoice') }}</h2>
                        <p dir="ltr">#{{ $order->order_number }}</p>
                        <span class="status {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                </header>

                <section class="meta-grid">
                    <div class="meta-card">
                        <span>{{ __('invoice_number') }}</span>
                        <strong dir="ltr">{{ $order->order_number }}</strong>
                    </div>
                    <div class="meta-card">
                        <span>{{ __('issue_date') }}</span>
                        <strong>{{ $order->created_at->translatedFormat('d F Y, H:i') }}</strong>
                    </div>
                    <div class="meta-card">
                        <span>{{ __('payment_method') }}</span>
                        <strong>{{ __('payment_on_delivery') }}</strong>
                    </div>
                </section>

                <section class="address-grid">
                    <div class="address-card">
                        <h3>{{ __('seller') }}</h3>
                        <strong>{{ $storeName }}</strong>
                        @if($storeAddress)<p>{{ $storeAddress }}</p>@endif
                        @if($storePhone)<p dir="ltr">{{ $storePhone }}</p>@endif
                        @if($storeEmail)<p dir="ltr">{{ $storeEmail }}</p>@endif
                        @if($storeWhatsapp && $storeWhatsapp !== $storePhone)<p dir="ltr">WhatsApp: {{ $storeWhatsapp }}</p>@endif
                    </div>

                    <div class="address-card customer">
                        <h3>{{ __('billed_to') }}</h3>
                        <strong>{{ $order->customer_name }}</strong>
                        <p>{{ $order->customer_address }}</p>
                        <p>{{ $order->customer_city }}</p>
                        <p dir="ltr">{{ $order->customer_phone }}</p>
                    </div>
                </section>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('product') }}</th>
                                <th class="number">{{ __('unit_price') }}</th>
                                <th class="number">{{ __('quantity') }}</th>
                                <th class="number">{{ __('total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->items as $item)
                                <tr>
                                    <td class="product-name">{!! bidi_text($item->display_name) !!}</td>
                                    <td class="number">{{ number_format($item->discount_price ?? $item->price, 2) }} DH</td>
                                    <td class="number">{{ $item->quantity }}</td>
                                    <td class="number"><strong>{{ number_format($item->subtotal, 2) }} DH</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">{{ __('no_items') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <section class="summary-row">
                    <div class="payment-note">
                        <h3>{{ __('payment_details') }}</h3>
                        <p>{{ __('payment_on_delivery') }}</p>
                        <p>{{ __('amount_due_on_delivery') }}</p>
                    </div>

                    <div class="totals">
                        <div class="totals-row">
                            <span>{{ __('subtotal') }}</span>
                            <strong>{{ number_format($order->subtotal, 2) }} DH</strong>
                        </div>
                        @if((float) $order->discount_amount > 0)
                            <div class="totals-row">
                                <span>{{ __('discount') }}</span>
                                <strong class="discount">-{{ number_format($order->discount_amount, 2) }} DH</strong>
                            </div>
                        @endif
                        <div class="totals-row">
                            <span>{{ __('delivery_fee') }}</span>
                            @if($isFreeDelivery)
                                <strong class="free">{{ __('free') }}</strong>
                            @else
                                <strong>{{ number_format($order->delivery_fee, 2) }} DH</strong>
                            @endif
                        </div>
                        <div class="totals-row grand-total">
                            <span>{{ __('total_due') }}</span>
                            <strong>{{ number_format($order->total, 2) }} DH</strong>
                        </div>
                    </div>
                </section>

                @if($order->notes)
                    <section class="notes">
                        <strong>{{ __('order_notes') }}</strong>
                        <p>{{ $order->notes }}</p>
                    </section>
                @endif

                <footer class="footer">
                    <div>
                        <strong>{{ __('thank_you_for_your_order') }}</strong>
                        <span>{{ __('invoice_support_message') }}</span>
                    </div>
                    <div class="footer-reference">
                        <span>{{ $storeName }} · {{ now()->year }}</span>
                        @if($storePhone || $storeEmail)
                            <br><span dir="ltr">{{ collect([$storePhone, $storeEmail])->filter()->join(' · ') }}</span>
                        @endif
                    </div>
                </footer>
            </div>
        </article>
    </main>
</body>
</html>
