<!DOCTYPE html>
<html lang="en">
<body style="margin:0;padding:0;background-color:#141414;color:#ece8e0;font-family:system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;font-weight:300;">
    <div style="background-color:#141414;padding:32px 16px;">
        <div style="margin:0 auto;max-width:640px;border:1px solid #1e1e1e;background-color:#191919;">
            <div style="border-bottom:1px solid #1e1e1e;padding:24px 28px;">
                <div style="font-size:12px;letter-spacing:0.22em;text-transform:uppercase;color:#585858;">
                    Sure Shot <span style="color:#c4924a;">Supply</span>
                </div>
            </div>

            <div style="padding:28px;">
                <div style="font-size:10px;letter-spacing:0.2em;text-transform:uppercase;color:#585858;">Order reference</div>
                <div style="margin-top:8px;color:#c4924a;font-size:16px;">{{ $order->reference }}</div>

                <h1 style="margin:20px 0 0;color:#ece8e0;font-family:Georgia,serif;font-size:36px;font-style:italic;font-weight:400;line-height:1.15;">
                    Order confirmed.
                </h1>

                <p style="margin:18px 0 0;color:#d0ccc4;font-size:15px;line-height:1.8;">
                    Thank you for your order. We'll be in touch once your items have shipped.
                </p>

                <div style="margin-top:28px;border:1px solid #1e1e1e;background-color:#141414;">
                    <div style="padding:18px 20px;border-bottom:1px solid #1e1e1e;">
                        <div style="font-size:10px;letter-spacing:0.2em;text-transform:uppercase;color:#585858;">Date</div>
                        <div style="margin-top:8px;color:#d0ccc4;">{{ $order->created_at->format('j F Y') }}</div>
                    </div>

                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                        @foreach ($order->items as $item)
                            <tr>
                                <td style="padding:16px 20px;border-top:1px solid #252525;color:#ece8e0;font-size:14px;line-height:1.6;">
                                    {{ $item->name }}
                                    @if ($item->quantity > 1)
                                        <span style="color:#585858;"> × {{ $item->quantity }}</span>
                                    @endif
                                </td>
                                <td align="right" style="padding:16px 20px;border-top:1px solid #252525;color:#d0ccc4;font-size:14px;line-height:1.6;">
                                    ${{ number_format(($item->price * $item->quantity) / 100, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </table>

                    @if ($order->shipping_rate_label && $order->shipping_country)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:18px 20px;border-top:1px solid #252525;">
                            <div style="font-size:10px;letter-spacing:0.2em;text-transform:uppercase;color:#585858;">Shipping</div>
                            <div style="color:#d0ccc4;font-size:14px;">{{ $order->shipping_rate_label }} — ${{ number_format($order->shipping_amount / 100, 2) }}</div>
                        </div>
                    @endif

                    <div style="display:flex;justify-content:space-between;align-items:center;padding:18px 20px;border-top:1px solid #1e1e1e;">
                        <div style="font-size:10px;letter-spacing:0.2em;text-transform:uppercase;color:#585858;">Total</div>
                        <div style="color:#c4924a;font-size:18px;">${{ number_format($order->total / 100, 2) }} AUD</div>
                    </div>
                </div>

                <p style="margin:28px 0 0;color:#d0ccc4;font-size:14px;line-height:1.8;">
                    Questions about your order? Reply to this email and we'll sort it out. — SureShotSupply
                </p>
            </div>
        </div>
    </div>
</body>
</html>
