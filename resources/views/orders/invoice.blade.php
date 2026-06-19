<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 40px; color: #333; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 3px solid #22c55e; padding-bottom: 20px; }
        .header h1 { color: #22c55e; font-size: 32px; margin-bottom: 10px; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { width: 48%; }
        .info-box h3 { color: #22c55e; margin-bottom: 10px; font-size: 16px; }
        .info-box p { margin: 5px 0; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin: 30px 0; }
        th { background: #22c55e; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .totals { margin-top: 20px; text-align: right; }
        .totals table { width: 300px; margin-left: auto; }
        .totals td { padding: 8px; }
        .total-row { font-weight: bold; font-size: 18px; background: #f0fdf4; }
        .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 20px; }
        @media print { body { padding: 20px; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>FACTURE</h1>
        <p style="font-size: 14px; color: #666; margin-top: 5px;">N° {{ $order->order_number }}</p>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>BOUTIQUE</h3>
            <p><strong>{{ settings('store_name', 'Simple Store') }}</strong></p>
            <p>{{ settings('address', 'Adresse') }}</p>
            <p>Tél: {{ settings('phone', 'N/A') }}</p>
            <p>Email: {{ settings('email', 'N/A') }}</p>
        </div>

        <div class="info-box">
            <h3>FACTURÉ À</h3>
            <p><strong>{{ $order->customer_name }}</strong></p>
            <p>{{ $order->customer_address }}</p>
            <p>{{ $order->customer_city }}</p>
            <p>Tél: {{ $order->customer_phone }}</p>
            <p style="margin-top: 10px;"><strong>Date:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix Unit.</th>
                <th class="text-right">Qté</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td><bdi dir="auto">{{ $item->display_name }}</bdi></td>
                    <td>{{ number_format($item->discount_price ?? $item->price, 2) }} DH</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 2) }} DH</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Sous-total:</td>
                <td class="text-right">{{ number_format($order->subtotal, 2) }} DH</td>
            </tr>
            @if($order->discount_amount > 0)
                <tr>
                    <td>Réduction:</td>
                    <td class="text-right" style="color: #22c55e;">-{{ number_format($order->discount_amount, 2) }} DH</td>
                </tr>
            @endif
            <tr>
                <td>Frais de livraison:</td>
                <td class="text-right">{{ number_format($order->delivery_fee, 2) }} DH</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL:</td>
                <td class="text-right">{{ number_format($order->total, 2) }} DH</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 30px; padding: 15px; background: #f9fafb; border-left: 4px solid #22c55e;">
        <p><strong>Mode de paiement:</strong> Paiement à la livraison (Cash)</p>
        <p><strong>Statut:</strong> 
            @php
                $statusLabels = [
                    'pending' => 'En attente',
                    'preparing' => 'En préparation',
                    'out_for_delivery' => 'En livraison',
                    'delivered' => 'Livré',
                    'cancelled' => 'Annulé',
                ];
            @endphp
            {{ $statusLabels[$order->status] }}
        </p>
        @if($order->notes)
            <p><strong>Notes:</strong> {{ $order->notes }}</p>
        @endif
    </div>

    <div class="footer">
        <p>Merci pour votre confiance !</p>
        <p>{{ settings('store_name', 'Simple Store') }} - {{ settings('phone', '') }}</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" style="background: #22c55e; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px;">
            Imprimer
        </button>
    </div>
</body>
</html>
