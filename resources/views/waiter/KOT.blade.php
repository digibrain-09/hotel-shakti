<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Order Ticket</title>
    <style>
        .print-area {
            /* padding: 20px; */
            margin-bottom: 20px;
        }

        .header {
            text-align: center;
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
            padding: 5px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .total {
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: gray;
        }

        .print-space {
            margin-top: 50px;
            /* Adjust as needed */
        }

        #outer {
            width: 100%;
            text-align: center;
        }

        .inner {
            display: inline-block;
        }

        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 10px;
                width: 45mm;
                margin: 0 !important;
                padding-top: 20px;
                padding-bottom: 50px;
            }

            .print-area {
                border: none;
            }

            button {
                display: none;
            }

            .header img {
                width: 100px;
                height: 80px;
            }

            .print-space {
                height: 100px;
                /* Adjust as needed */
            }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            alert("Order placed successfully!");

            // Show the print button
            document.getElementById("printBtn").style.display = "block";
            document.getElementById("backBtn").style.display = "block";

            // Optional: Automatically click the button on desktop
            if (window.innerWidth > 768) {
                document.getElementById("printBtn").click();
            }

            document.getElementById("printBtn").addEventListener("click", function() {
                window.print();

                // Delay the redirect slightly to improve mobile compatibility
                // setTimeout(function() {
                //     window.location.href = "{{ route('home') }}";
                // }, 500); // adjust delay if needed
            });

            document.getElementById("backBtn").addEventListener("click", function() {
                window.location.href = "{{ route('OrderMenu') }}";

            });
        });
    </script>
</head>

<body>
    <div class="print-area">
        <div class="header">
            <h2>Kitchen Order</h2>
            <hr>
        </div>

        <div class="details">
            <p><strong>Table:</strong> {{ $table->table_name }}</p>
            <p><strong>Date:</strong> {{ $date }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>SrNo</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Add-ons</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $json = json_decode($item_data_for_print);
                $srNo = 1;
                $totalQuantity = 0;
                ?>
                @foreach ($json as $index => $detail)
                <?php
                $addonNames = [];

                $addonData = json_decode($detail->addons, true);

                if (is_array($addonData)) {
                    foreach ($addonData as $addon) {
                        if (isset($addon['name']) && isset($addon['price'])) {
                            $addonNames[] = $addon['name'];
                        }
                    }
                }
                ?>
                @if($detail->print == 'No')
                <tr>
                    <td>{{ $srNo }}</td>
                    <td>{{ $detail->name }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>
                        @if( $addonData == null)
                        {{ '-' }}
                        @else
                        {{ implode(', ', $addonNames) }}
                        @endif
                    </td>
                    <td>
                        @if( $detail->note == null)
                        {{ '-' }}
                        @else
                        {{ $detail->note }}
                        @endif
                    </td>
                </tr>
                @php
                $srNo++;
                $totalQuantity += $detail->quantity;
                @endphp
                @endif
                @endforeach

                <tr>
                    <td colspan="4"><strong>Total Items:</strong></td>
                    <td><strong>{{ $totalQuantity }}</strong></td>
                </tr>

            </tbody>
        </table>

        <div class="footer">
            <h3>** THANK YOU **</h3>
        </div>
        <div class="print-space"></div>

        <div id="outer">
            <div class="inner"><button class="btn btn-primary" id="printBtn" style="display: none;">Print</button></div>
            <div class="inner"><button class="btn btn-primary" id="backBtn" style="display: none;">Back</button></div>
        </div>
    </div>
</body>

</html>