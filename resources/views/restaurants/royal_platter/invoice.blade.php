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
            <!-- <img src="{{ url('restaurant_logo/202502271822food-logo.png') }}" height="100px" width="200px" alt="royalplatter"> -->
            <div class="title">ROYALPLATTER</div>
            <div>Shivranjani Cross Road, Satellite, Ahmedabad</div>
            <div>Phone Number: 1234567890</div>
        </div>

        <div class="details">
            <p><strong>Table:</strong> {{ $table }}</p>
            <p><strong>Date:</strong> {{ $date }} {{ $time }}</p>
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
                    @foreach($item_data as $item)
                    @php
                    $itemTotal = $item['price'] * $item['quantity'];
                    $total += $itemTotal;
                    @endphp
                    <tr>
                        <!-- <td>{{ $srNo }}</td> -->
                        <td>{{ $item['name'] }}</td>
                        <td>₹{{ number_format($item['price'], 2) }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>₹{{ number_format($itemTotal, 2) }}</td>
                    </tr>
                    @php $srNo++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>


        <div class="summary">
            <p><strong>Sub-Total:</strong> ₹{{ number_format($total,2) }}</p>
            <?php if ($total_with_tax) { ?>
                <div id="summary-tax">

                    <p><strong>CGST <span id="cgst_rate">({{ $cgst_rate }}%)</span>:</strong> <span id="cgst">₹{{ number_format($cgst,2) }}</span></p>
                    <p><strong>SGST <span id="sgst_rate">({{ $sgst_rate }}%)</span>:</strong> <span id="sgst">₹{{ number_format($sgst,2) }}</span></p>
                </div>
                <hr>
                <p class="total"><strong>Total:</strong> <span id="total_amount">₹{{ number_format($total_with_tax,2) }}</span></p>
            <?php } ?>
            <p><strong>Mode Of Payment:</strong> {{ $payment_mode }}</p>
        </div>

        <div class="footer">
            ** THANK YOU FOR YOUR VISIT! **<br>
            We hope you had a great experience and look forward to serving you again soon.<br>
        </div>
    </div>
</body>

</html>