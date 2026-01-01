<!DOCTYPE html>
<html>
<head>
    <title>Nouvelle Commande</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px;padding-top: 20px ;padding-bottom: 20px }
        .header { background: #f8f9fa; padding: 20px; text-align: center; border-bottom: 3px solid #007bff; }
        .content { padding: 20px; }
        .order-details { min-width: 250px;  background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .order-items { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .order-items th, .order-items td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .order-items th { background: #007bff; color: white; }
        .total { font-size: 18px; font-weight: bold; color: #28a745; }
        .customer-info { margin: 20px 0; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouvelle Commande Reçue</h1>
            <h2>Commande #{{ $order->order_number }}</h2>
        </div>
        
        <div class="content">
            <div class="customer-info">
                <h3>Informations Client</h3>
                <p><strong>Nom:</strong> {{ $order->customer_name }}</p>
                <p><strong>Téléphone:</strong> {{ $order->customer_phone }}</p>
                <p><strong>Adresse:</strong> {{ $order->customer_address }}</p>
                <p><strong>Ville:</strong> {{ $order->customer_city }}</p>
                @if($order->notes)
                    <p><strong>Notes:</strong> {{ $order->notes }}</p>
                @endif
            </div>
            
            <div class="order-details">
                <h3>Détails de la Commande</h3>
                <table class="order-items">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderItems as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->discount_price ?? $item->price }} Dhs</td>
                                <td>{{ $item->subtotal }} Dhs</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div style="text-align: right;">
                    <p><strong>Sous-total:</strong> {{ $order->subtotal }} Dhs</p>
                    <p><strong>Frais de livraison:</strong> {{ $order->delivery_fee }} Dhs</p>
                    <p class="total">Total: {{ $order->total + $order->delivery_fee }} Dhs</p>
                </div>
            </div>
            
            <div class="footer">
                <p>Date de la commande: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p>Merci de traiter cette commande rapidement.</p>
                <p>© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</body>
</html>