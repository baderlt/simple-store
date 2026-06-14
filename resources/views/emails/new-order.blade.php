@php
    $storeName = settings('store_name', config('app.name'));
    $primaryColor = settings('primary_color', '#B7791F');
    $logo = settings('logo');
    $logoUrl = $logo ? asset('storage/' . $logo) : null;
    $currency = 'DH';
    $formatMoney = fn ($amount) => number_format((float) $amount, 2, ',', ' ') . ' ' . $currency;
@endphp
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Nouvelle commande</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f1eb; color:#292524; font-family:Arial, Helvetica, sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">
        Nouvelle commande {{ $order->order_number }} de {{ $order->customer_name }} — {{ $formatMoney($order->total) }}.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; background-color:#f4f1eb;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px; border-collapse:collapse; background-color:#ffffff;">
                    <tr>
                        <td align="center" style="padding:28px 24px 22px; border-top:5px solid {{ $primaryColor }}; border-bottom:1px solid #e7e5e4;">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" width="120" alt="{{ $storeName }}" style="display:block; width:auto; max-width:120px; max-height:70px; border:0; margin:0 auto 12px;">
                            @else
                                <div style="font-family:Georgia, 'Times New Roman', serif; font-size:25px; line-height:32px; font-weight:bold; color:{{ $primaryColor }};">{{ $storeName }}</div>
                            @endif
                            <div style="margin-top:10px; font-size:12px; line-height:18px; font-weight:bold; letter-spacing:1.4px; text-transform:uppercase; color:#78716c;">
                                Nouvelle commande reçue
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 28px 10px;">
                            <h1 style="margin:0 0 12px; font-family:Georgia, 'Times New Roman', serif; font-size:28px; line-height:36px; color:#1c1917;">
                                Une nouvelle commande vous attend
                            </h1>
                            <p style="margin:0; font-size:16px; line-height:25px; color:#57534e;">
                                {{ $order->customer_name }} vient de passer une commande. Retrouvez ci-dessous toutes les informations nécessaires à son traitement.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; background-color:#fafaf9; border:1px solid #e7e5e4;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <div style="font-size:11px; line-height:16px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#78716c;">Numéro de commande</div>
                                        <div style="margin-top:4px; font-size:18px; line-height:25px; font-weight:bold; color:#1c1917;">#{{ $order->order_number }}</div>
                                    </td>
                                    <td align="right" style="padding:18px 20px;">
                                        <div style="font-size:11px; line-height:16px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#78716c;">Date</div>
                                        <div style="margin-top:4px; font-size:14px; line-height:22px; color:#44403c;">{{ $order->created_at->format('d/m/Y à H:i') }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:8px 28px 0;">
                            <h2 style="margin:0 0 14px; font-family:Georgia, 'Times New Roman', serif; font-size:21px; line-height:28px; color:#1c1917;">Votre sélection</h2>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse;">
                                @foreach($orderItems as $item)
                                    <tr>
                                        <td valign="top" style="padding:14px 8px 14px 0; border-bottom:1px solid #e7e5e4;">
                                            <div style="font-size:15px; line-height:22px; font-weight:bold; color:#292524;">{{ $item->display_name }}</div>
                                            <div style="font-size:13px; line-height:20px; color:#78716c;">Quantité : {{ $item->quantity }} × {{ $formatMoney($item->final_unit_price) }}</div>
                                        </td>
                                        <td width="115" align="right" valign="top" style="padding:14px 0 14px 8px; border-bottom:1px solid #e7e5e4; font-size:15px; line-height:22px; font-weight:bold; color:#292524; white-space:nowrap;">
                                            {{ $formatMoney($item->subtotal) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 28px 26px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse;">
                                <tr>
                                    <td style="padding:5px 0; font-size:14px; line-height:21px; color:#57534e;">Sous-total</td>
                                    <td align="right" style="padding:5px 0; font-size:14px; line-height:21px; color:#292524;">{{ $formatMoney($order->subtotal) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px 0 13px; font-size:14px; line-height:21px; color:#57534e;">Livraison</td>
                                    <td align="right" style="padding:5px 0 13px; font-size:14px; line-height:21px; color:#292524;">{{ (float) $order->delivery_fee === 0.0 ? 'Offerte' : $formatMoney($order->delivery_fee) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 0 0; border-top:2px solid #292524; font-size:17px; line-height:24px; font-weight:bold; color:#1c1917;">Total</td>
                                    <td align="right" style="padding:14px 0 0; border-top:2px solid #292524; font-size:20px; line-height:26px; font-weight:bold; color:{{ $primaryColor }};">{{ $formatMoney($order->total) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 28px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; background-color:#fafaf9;">
                                <tr>
                                    <td valign="top" style="padding:20px;">
                                        <div style="font-size:12px; line-height:18px; font-weight:bold; text-transform:uppercase; letter-spacing:.8px; color:#78716c;">Livraison</div>
                                        <div style="margin-top:7px; font-size:14px; line-height:22px; color:#292524;">
                                            {{ $order->customer_name }}<br>
                                            {{ $order->customer_address }}<br>
                                            {{ $order->customer_city }}<br>
                                            <a href="tel:{{ $order->customer_phone }}" style="color:{{ $primaryColor }}; text-decoration:underline;">{{ $order->customer_phone }}</a>
                                        </div>
                                    </td>
                                    <td valign="top" style="padding:20px;">
                                        <div style="font-size:12px; line-height:18px; font-weight:bold; text-transform:uppercase; letter-spacing:.8px; color:#78716c;">Paiement</div>
                                        <div style="margin-top:7px; font-size:14px; line-height:22px; color:#292524;">Paiement à la livraison</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    @if($order->notes)
                        <tr>
                            <td style="padding:0 28px 28px;">
                                <div style="padding:16px 18px; border-left:4px solid {{ $primaryColor }}; background-color:#fffbeb; font-size:14px; line-height:22px; color:#44403c;">
                                    <strong>Note :</strong> {{ $order->notes }}
                                </div>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td align="center" style="padding:0 28px 32px;">
                            <a href="{{ route('admin.orders.show', $order) }}" style="display:inline-block; padding:13px 24px; background-color:{{ $primaryColor }}; color:#ffffff; font-size:14px; line-height:20px; font-weight:bold; text-decoration:none; border-radius:4px;">Voir et traiter la commande</a>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:24px 28px; border-top:1px solid #e7e5e4; background-color:#fafaf9; font-size:12px; line-height:19px; color:#78716c;">
                            Message automatique envoyé par {{ $storeName }}.
                            <br>© {{ date('Y') }} {{ $storeName }}. Tous droits réservés.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
