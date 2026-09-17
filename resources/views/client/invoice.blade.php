<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <title>Print Page</title>
    <style>
        body {
            font-family: 'Arial', 'DejaVu Sans', sans-serif;
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
        }

        .header img {
            width: 50px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }

        .details,
        .items,
        .summary {
            margin-top: 15px;
        }

        .details p,
        .summary p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .total {
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 100px;
            font-size: 12px;
            color: gray;
        }
    </style>
</head>

<body>
    <div class="print-area">
        <div class="header">
            @if($restaurant)
            <div class="title"> {{ strtoupper($restaurant->restaurant_name) }} </div>
            <div> {{ $restaurant->address }} </div>
            <div> Phone Number: {{ $restaurant->phone }} </div> @endif
        </div>

        <div class="details">
            <p><strong>Bill No.:</strong> #{{ $order->id }}</p>
            <p><strong>Date:</strong> {{ $invoiceData['date'] }} {{ $invoiceData['time'] }}</p>
            <p><strong>Order Type:</strong> {{ $order->order_type }}</p>
            <p style="text-transform: capitalize;"><strong>Table:</strong> {{ $invoiceData['table'] }}</p>
        </div>

        <div class="items">
            @php
            $total = 0;
            $srNo = 1;
            @endphp

            <table>
                <thead>
                    <tr>
                        <!-- <th>srNo</th> -->
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoiceData['item_data'] as $item)
                    @php
                    $itemTotal = $item['price'] * $item['quantity'];

                    $addonTotal = 0;
                    $addonNames = [];

                    $addonData = json_decode($item['addons'], true);

                    if (is_array($addonData)) {
                    foreach ($addonData as $addon) {

                    $addonTotal += $addon['price'] * $item['quantity'];
                    }
                    }

                    $totalWithAddons = $itemTotal + $addonTotal;
                    $total += $totalWithAddons;
                    @endphp
                    <tr>
                        <!-- <td>{{ $srNo }}</td> -->
                        <td>{{ $item['name'] }}</td>
                        <td>₹{{ number_format($item['price'], 2) }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>₹{{ number_format($totalWithAddons, 2) }}</td>
                    </tr>
                    @php $srNo++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>


        <div class="summary">
            <p><strong>Sub-Total:</strong> ₹{{ number_format($total,2) }}</p>
            <?php if ($invoiceData['total_with_tax']) { ?>
                <div id="summary-tax">

                    @if($invoiceData['coupon_code'] == 'null' || $invoiceData['coupon_code'] == NULL)
                    @else
                    <p><strong>Coupon <span id="">({{ $invoiceData['coupon_code']  }})</span>:</strong> <span id=""> - ₹{{ number_format($invoiceData['discount'],2) }}</span></p>
                    <p><strong>Total:</strong> <span id=""> ₹{{ number_format(max(0, $invoiceData['total'] - $invoiceData['discount']),2) }}</span></p>
                    @endif

                    <p><strong>CGST <span id="cgst_rate">({{ $invoiceData['cgst_rate']  }}%)</span>:</strong> <span id="cgst">₹{{ number_format($invoiceData['cgst'],2) }}</span></p>
                    <p><strong>SGST <span id="sgst_rate">({{ $invoiceData['sgst_rate'] }}%)</span>:</strong> <span id="sgst">₹{{ number_format($invoiceData['sgst'],2) }}</span></p>
                </div>
                <hr>
                <p class="total"><strong>Grand Total:</strong> <span id="total_amount">₹{{ number_format($invoiceData['total_with_tax'],2) }}</span></p>
            <?php } ?>
        </div>

        <div class="footer">
            ** THANK YOU FOR YOUR VISIT! **<br>
            We hope you had a great experience and look forward to serving you again soon.<br>
        </div>
    </div>
</body>

</html>