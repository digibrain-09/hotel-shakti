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
            setTimeout(function() {
                window.print();
            }, 500); // Delay printing to ensure page loads properly

            // window.onafterprint = function() {
            //     window.location.href = "/manager_order"; // Redirect after printing
            // };
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

    </div>
</body>

</html>