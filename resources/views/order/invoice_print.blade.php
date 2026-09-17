<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Invoice #{{ $order_id }}</title>


    {{-- =========================================================
         Gujarati Font
         ========================================================= --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Gujarati:wght@400;500;600;700&display=swap"
        rel="stylesheet">


    {{-- =========================================================
         html2canvas
         Used ONLY on Android to convert invoice into image
         before sharing with RAWBT.
         ========================================================= --}}

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


    <style>
        /* ========================================================
           GLOBAL
           ======================================================== */

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #eeeeee;

            color: #000000;

            font-family:
                'Noto Sans Gujarati',
                Arial,
                sans-serif;

            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }


        /* ========================================================
           PRINT BUTTON AREA
           ======================================================== */

        .print-controls {
            width: 100%;
            text-align: center;
            padding: 15px 10px;
            background: #eeeeee;
        }

        .print-button {
            border: none;

            background: #111111;
            color: #ffffff;

            padding: 12px 25px;

            border-radius: 6px;

            font-size: 16px;
            font-weight: 600;

            cursor: pointer;

            min-width: 180px;
        }

        .print-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .status {
            margin-top: 8px;

            text-align: center;

            font-size: 13px;

            color: #333333;
        }


        /* ========================================================
           58MM RECEIPT
           ======================================================== */

        .print-wrapper {
            width: 58mm;

            margin: 20px auto;

            background: #ffffff;
        }

        .print-area {
            width: 58mm;

            background: #ffffff;

            padding: 2mm;

            color: #000000;

            overflow: hidden;
        }


        /* ========================================================
           HEADER
           ======================================================== */

        .header {
            width: 100%;

            text-align: center;
        }

        .header-logo {
            display: block;

            max-width: 42mm;
            max-height: 25mm;

            width: auto;
            height: auto;

            margin: 0 auto 3px auto;

            object-fit: contain;
        }

        .title {
            font-size: 17px;

            line-height: 1.4;

            font-weight: 700;

            margin-top: 3px;

            word-break: break-word;
        }

        .restaurant-address {
            font-size: 10px;

            line-height: 1.4;

            margin-top: 2px;

            word-break: break-word;
        }

        .restaurant-phone {
            font-size: 10px;

            line-height: 1.4;

            margin-top: 2px;
        }


        /* ========================================================
           DETAILS
           ======================================================== */

        .details {
            margin-top: 8px;

            font-size: 10px;

            line-height: 1.5;
        }

        .details p {
            margin: 2px 0;
        }


        /* ========================================================
           ITEMS TABLE
           ======================================================== */

        .items {
            margin-top: 8px;

            width: 100%;
        }

        table {
            width: 100%;

            border-collapse: collapse;

            table-layout: fixed;
        }

        th,
        td {
            padding: 3px 1px;

            text-align: left;

            vertical-align: top;

            font-size: 9px;

            line-height: 1.45;

            word-wrap: break-word;

            overflow-wrap: anywhere;
        }

        th {
            font-weight: 700;

            border-bottom: 1px solid #000000;
        }

        td {
            border-bottom: 1px dashed #888888;
        }


        /*
         Column widths
        */

        th:nth-child(1),
        td:nth-child(1) {
            width: 43%;
        }

        th:nth-child(2),
        td:nth-child(2) {
            width: 19%;
        }

        th:nth-child(3),
        td:nth-child(3) {
            width: 13%;

            text-align: center;
        }

        th:nth-child(4),
        td:nth-child(4) {
            width: 25%;

            text-align: right;
        }


        /* ========================================================
           ITEM NAMES
           ======================================================== */

        .item-name {
            line-height: 1.4;

            word-break: break-word;
        }

        .english-name {
            font-family:
                Arial,
                sans-serif;

            font-size: 9px;

            line-height: 1.4;
        }

        .gujarati-text {
            font-family:
                'Noto Sans Gujarati',
                sans-serif !important;

            font-size: 9px;

            line-height: 1.6;

            margin-top: 1px;

            word-break: break-word;
        }


        /* ========================================================
           SUMMARY
           ======================================================== */

        .summary {
            width: 100%;

            margin-top: 8px;

            font-size: 10px;

            line-height: 1.5;
        }

        .summary p {
            margin: 3px 0;
        }

        .summary-row {
            display: flex;

            justify-content: space-between;

            width: 100%;
        }

        .summary-label {
            text-align: left;
        }

        .summary-value {
            text-align: right;
        }

        .summary hr {
            border: 0;

            border-top: 1px solid #000000;

            margin: 5px 0;
        }

        .total {
            font-size: 12px;

            font-weight: 700;
        }


        /* ========================================================
           FOOTER
           ======================================================== */

        .footer {
            width: 100%;

            text-align: center;

            margin-top: 15px;

            font-size: 8px;

            line-height: 1.5;
        }

        .footer p {
            margin: 3px 0;
        }


        /* ========================================================
           DESKTOP SCREEN
           ======================================================== */

        @media screen and (min-width: 600px) {

            .print-wrapper {
                box-shadow:
                    0 2px 12px rgba(0, 0, 0, 0.15);
            }

        }


        /* ========================================================
           DESKTOP / BROWSER PRINT
           ======================================================== */

        @media print {

            @page {
                size: 58mm auto;

                margin: 0;
            }

            html,
            body {
                width: 58mm;

                margin: 0 !important;
                padding: 0 !important;

                background: #ffffff !important;
            }

            .print-controls {
                display: none !important;
            }

            .print-wrapper {
                width: 58mm;

                margin: 0 !important;

                box-shadow: none !important;
            }

            .print-area {
                width: 58mm;

                margin: 0;

                padding: 2mm;
            }

            .header-logo {
                max-width: 42mm;
                max-height: 24mm;
            }

            th,
            td {
                font-size: 9px;
            }

            .gujarati-text {
                font-family:
                    'Noto Sans Gujarati',
                    Arial,
                    sans-serif !important;
            }
        }
    </style>

</head>


<body>


    {{-- =============================================================
     INVOICE
     ============================================================= --}}

    <div class="print-wrapper">

        <div
            class="print-area"
            id="invoice">


            {{-- =====================================================
             RESTAURANT HEADER
             ===================================================== --}}

            <div class="header">


                {{-- Logo --}}

                @if (!empty($logo) && $logo != 0)

                <img
                    src="{{ url('restaurant_logo/' . $logo) }}"
                    class="header-logo"
                    alt="Restaurant Logo">

                @else

                <img
                    src="{{ asset('assets/img/final.png') }}"
                    class="header-logo"
                    alt="Restaurant Logo">

                @endif


                {{-- Restaurant Name --}}

                @if (!empty($restaurant_name) && $restaurant_name != 0)

                <div class="title">
                    {{ strtoupper($restaurant_name) }}
                </div>

                @endif


                {{-- Address --}}

                @if (!empty($address) && $address != 0)

                <div class="restaurant-address">
                    {{ $address }}
                </div>

                @endif


                {{-- Phone --}}

                @if (!empty($phone) && $phone != 0)

                <div class="restaurant-phone">
                    Phone: {{ $phone }}
                </div>

                @endif


            </div>



            {{-- =====================================================
             ORDER DETAILS
             ===================================================== --}}

            <div class="details">

                <p>
                    <strong>Bill No.:</strong>
                    #{{ $order_id }}
                </p>

                <p>
                    <strong>Date:</strong>
                    {{ $date ?? '' }}

                    {{ $time ?? '' }}
                </p>

                <p>
                    <strong>Order Type:</strong>
                    {{ $order_type }}
                </p>

                <p>
                    <strong>Table:</strong>
                    {{ $table ?? 'N/A' }}
                </p>

            </div>



            {{-- =====================================================
             ITEMS
             ===================================================== --}}

            @php

            $total = 0;

            @endphp


            <div class="items">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Item
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Qty
                            </th>

                            <th>
                                Total
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        @foreach ($item_data ?? [] as $item)


                        @php

                        $price =
                        (float) ($item['price'] ?? 0);

                        $quantity =
                        (float) ($item['quantity'] ?? 0);


                        $itemTotal =
                        $price * $quantity;


                        $addonTotal = 0;


                        $addonData = json_decode(
                        $item['addons'] ?? '',
                        true
                        );


                        if (is_array($addonData)) {

                        foreach ($addonData as $addon) {

                        $addonPrice =
                        (float) ($addon['price'] ?? 0);

                        $addonTotal +=
                        $addonPrice * $quantity;

                        }

                        }


                        $totalWithAddons =
                        $itemTotal + $addonTotal;


                        $total +=
                        $totalWithAddons;

                        @endphp



                        <tr>


                            {{-- =================================================
                             ITEM NAME
                             ================================================= --}}

                            <td class="item-name">


                                {{-- English Name --}}

                                <div class="english-name">

                                    {{ $item['name'] ?? '' }}

                                </div>


                                {{-- Gujarati Name --}}

                                @foreach ($items ?? [] as $menuItem)

                                @if (
                                isset($menuItem->item_name) &&
                                $menuItem->item_name == ($item['name'] ?? '')
                                )

                                @if (!empty($menuItem->item_name_gu))

                                <div class="gujarati-text">

                                    {{ $menuItem->item_name_gu }}

                                </div>

                                @endif

                                @break

                                @endif

                                @endforeach


                            </td>



                            {{-- Price --}}

                            <td>

                                ₹{{ number_format($price, 2) }}

                            </td>



                            {{-- Quantity --}}

                            <td>

                                {{ $item['quantity'] ?? 0 }}

                            </td>



                            {{-- Total --}}

                            <td>

                                ₹{{ number_format(
                                $totalWithAddons,
                                2
                            ) }}

                            </td>


                        </tr>


                        @endforeach


                    </tbody>

                </table>

            </div>



            {{-- =====================================================
             SUMMARY
             ===================================================== --}}

            <div class="summary">


                {{-- =================================================
                 GST ENABLED
                 ================================================= --}}

                @if ($GST_status == true)


                <div class="summary-row">

                    <span class="summary-label">
                        <strong>Sub-Total:</strong>
                    </span>

                    <span class="summary-value">
                        ₹{{ number_format($total, 2) }}
                    </span>

                </div>



                {{-- Coupon --}}

                @if (
                !empty($coupon_code) &&
                $coupon_code != 'null' &&
                $coupon_code != NULL &&
                $coupon_code != 0
                )

                <div class="summary-row">

                    <span class="summary-label">

                        <strong>
                            Coupon ({{ $coupon_code }}):
                        </strong>

                    </span>

                    <span class="summary-value">

                        - ₹{{ number_format(
                                $discount ?? 0,
                                2
                            ) }}

                    </span>

                </div>


                <div class="summary-row">

                    <span class="summary-label">
                        <strong>Total:</strong>
                    </span>

                    <span class="summary-value">

                        ₹{{ number_format(
                                max(
                                    0,
                                    $total - ($discount ?? 0)
                                ),
                                2
                            ) }}

                    </span>

                </div>

                @endif



                {{-- CGST --}}

                <div class="summary-row">

                    <span class="summary-label">

                        <strong>
                            CGST ({{ $cgst_rate ?? 0 }}%):
                        </strong>

                    </span>

                    <span class="summary-value">

                        ₹{{ number_format(
                            $cgst ?? 0,
                            2
                        ) }}

                    </span>

                </div>



                {{-- SGST --}}

                <div class="summary-row">

                    <span class="summary-label">

                        <strong>
                            SGST ({{ $sgst_rate ?? 0 }}%):
                        </strong>

                    </span>

                    <span class="summary-value">

                        ₹{{ number_format(
                            $sgst ?? 0,
                            2
                        ) }}

                    </span>

                </div>


                <hr>



                {{-- Grand Total --}}

                <div class="summary-row total">

                    <span class="summary-label">

                        Grand Total:

                    </span>

                    <span class="summary-value">

                        ₹{{ number_format(
                            $total_with_tax ?? 0,
                            2
                        ) }}

                    </span>

                </div>


                @else


                {{-- =================================================
                     GST DISABLED
                     ================================================= --}}


                <div class="summary-row">

                    <span class="summary-label">

                        <strong>
                            Sub-Total:
                        </strong>

                    </span>

                    <span class="summary-value">

                        ₹{{ number_format(
                            $total,
                            2
                        ) }}

                    </span>

                </div>



                {{-- Coupon --}}

                @if (
                !empty($coupon_code) &&
                $coupon_code != 'null' &&
                $coupon_code != NULL &&
                $coupon_code != 0
                )

                <div class="summary-row">

                    <span class="summary-label">

                        <strong>
                            Coupon ({{ $coupon_code }}):
                        </strong>

                    </span>

                    <span class="summary-value">

                        - ₹{{ number_format(
                                $discount ?? 0,
                                2
                            ) }}

                    </span>

                </div>


                <div class="summary-row">

                    <span class="summary-label">

                        <strong>
                            Total:
                        </strong>

                    </span>

                    <span class="summary-value">

                        ₹{{ number_format(
                                max(
                                    0,
                                    $total - ($discount ?? 0)
                                ),
                                2
                            ) }}

                    </span>

                </div>

                @endif



                <div class="summary-row total">

                    <span class="summary-label">

                        Grand Total:

                    </span>

                    <span class="summary-value">

                        ₹{{ number_format(
                            $total_with_tax ?? 0,
                            2
                        ) }}

                    </span>

                </div>


                @endif


            </div>



            {{-- =====================================================
             FOOTER
             ===================================================== --}}

            <div class="footer">

                <p>
                    <strong>
                        ** THANK YOU FOR YOUR VISIT! **
                    </strong>
                </p>

                <p>
                    We hope you had a great experience.
                </p>

                <p>
                    We look forward to serving you again soon.
                </p>

            </div>


        </div>

    </div>

    {{-- =============================================================
     PRINT BUTTON
     ============================================================= --}}

    <div class="print-controls">

        <button
            type="button"
            id="printButton"
            class="print-button"
            onclick="printBill()">
            🖨️ Print Bill
        </button>

        <div
            id="printStatus"
            class="status"></div>

    </div>


    <script>
        /*
|--------------------------------------------------------------------------
| ScanTable Dual Printing
|--------------------------------------------------------------------------
|
| Android:
|   HTML → PNG → Android Share → RAWBT
|
| Desktop/Laptop:
|   HTML → window.print() → Existing Printer Driver
|
|--------------------------------------------------------------------------
*/


        /* ================================================================
           DEVICE DETECTION
           ================================================================ */

        function isAndroidDevice() {

            return /Android/i.test(
                navigator.userAgent
            );

        }


        /* ================================================================
           MAIN PRINT FUNCTION
           ================================================================ */

        async function printBill() {

            if (isAndroidDevice()) {

                await printAndroid();

            } else {

                printDesktop();

            }

        }


        /* ================================================================
           DESKTOP / LAPTOP PRINT
           ================================================================ */

        function printDesktop() {

            const status =
                document.getElementById('printStatus');

            status.innerText =
                'Opening printer...';


            /*
            |--------------------------------------------------------------------------
            | Browser print
            |--------------------------------------------------------------------------
            */

            window.print();


            /*
            |--------------------------------------------------------------------------
            | Clear status after a short delay
            |--------------------------------------------------------------------------
            */

            setTimeout(function() {

                status.innerText = '';

            }, 1500);

        }


        /* ================================================================
           ANDROID PRINT
           ================================================================ */

        async function printAndroid() {

            const button =
                document.getElementById('printButton');

            const status =
                document.getElementById('printStatus');


            try {


                /*
                |--------------------------------------------------------------------------
                | Disable button
                |--------------------------------------------------------------------------
                */

                button.disabled = true;

                button.innerText =
                    'Preparing Bill...';


                status.innerText =
                    'Preparing Gujarati invoice...';



                /*
                |--------------------------------------------------------------------------
                | Wait until fonts are completely loaded
                |--------------------------------------------------------------------------
                |
                | This is very important for Gujarati.
                |
                */

                if (document.fonts) {

                    await document.fonts.ready;

                }



                /*
                |--------------------------------------------------------------------------
                | Wait for invoice images/logo
                |--------------------------------------------------------------------------
                */

                const images =
                    document.querySelectorAll(
                        '#invoice img'
                    );


                await Promise.all(

                    Array.from(images).map(
                        function(img) {

                            if (img.complete) {

                                return Promise.resolve();

                            }


                            return new Promise(
                                function(resolve) {

                                    img.onload =
                                        resolve;

                                    img.onerror =
                                        resolve;

                                }
                            );

                        }
                    )

                );



                /*
                |--------------------------------------------------------------------------
                | Small delay
                |--------------------------------------------------------------------------
                |
                | Gives Chrome time to finish Gujarati font rendering.
                |
                */

                await new Promise(
                    function(resolve) {

                        setTimeout(
                            resolve,
                            300
                        );

                    }
                );



                /*
                |--------------------------------------------------------------------------
                | Get invoice
                |--------------------------------------------------------------------------
                */

                const invoice =
                    document.getElementById(
                        'invoice'
                    );



                /*
                |--------------------------------------------------------------------------
                | Convert invoice HTML to PNG
                |--------------------------------------------------------------------------
                */

                status.innerText =
                    'Creating printable image...';


                const canvas =
                    await html2canvas(
                        invoice, {

                            /*
                            |--------------------------------------------------------------------------
                            | Higher scale = better thermal print quality
                            |--------------------------------------------------------------------------
                            */

                            scale: 3,

                            backgroundColor: '#ffffff',

                            useCORS: true,

                            allowTaint: false,

                            logging: false,

                            imageTimeout: 15000,

                            /*
                            |--------------------------------------------------------------------------
                            | Preserve dimensions
                            |--------------------------------------------------------------------------
                            */

                            width: invoice.offsetWidth,

                            height: invoice.offsetHeight

                        }
                    );



                /*
                |--------------------------------------------------------------------------
                | Convert canvas to PNG Blob
                |--------------------------------------------------------------------------
                */

                const blob =
                    await new Promise(
                        function(resolve) {

                            canvas.toBlob(
                                resolve,
                                'image/png',
                                1.0
                            );

                        }
                    );


                if (!blob) {

                    throw new Error(
                        'Unable to create invoice image.'
                    );

                }



                /*
                |--------------------------------------------------------------------------
                | Create Share File
                |--------------------------------------------------------------------------
                */

                const file =
                    new File(
                        [
                            blob
                        ],
                        'ScanTable-Invoice-{{ $order_id }}.png', {
                            type: 'image/png'
                        }
                    );



                /*
                |--------------------------------------------------------------------------
                | Android Share
                |--------------------------------------------------------------------------
                */

                if (
                    navigator.share &&
                    navigator.canShare &&
                    navigator.canShare({
                        files: [file]
                    })
                ) {


                    status.innerText =
                        'Opening RAWBT...';


                    await navigator.share({

                        title: 'ScanTable Invoice',

                        text: 'Invoice #{{ $order_id }}',

                        files: [file]

                    });


                    status.innerText =
                        'Invoice shared successfully.';


                } else {


                    /*
                    |--------------------------------------------------------------------------
                    | Fallback
                    |--------------------------------------------------------------------------
                    |
                    | If Android/browser does not support file sharing,
                    | open the PNG so the user can manually share it.
                    |
                    */

                    const imageUrl =
                        URL.createObjectURL(
                            blob
                        );


                    const newWindow =
                        window.open(
                            imageUrl,
                            '_blank'
                        );


                    if (!newWindow) {

                        /*
                        |--------------------------------------------------------------------------
                        | Last fallback
                        |--------------------------------------------------------------------------
                        */

                        const link =
                            document.createElement(
                                'a'
                            );

                        link.href =
                            imageUrl;

                        link.download =
                            'ScanTable-Invoice-{{ $order_id }}.png';

                        link.click();

                    }


                    status.innerText =
                        'Invoice image created. Share it with RAWBT.';

                }


            } catch (error) {


                /*
                |--------------------------------------------------------------------------
                | User cancelled Android Share
                |--------------------------------------------------------------------------
                */

                if (
                    error &&
                    error.name === 'AbortError'
                ) {

                    status.innerText =
                        'Printing cancelled.';

                } else {


                    console.error(
                        'ScanTable printing error:',
                        error
                    );


                    status.innerText =
                        'Unable to print invoice.';


                    alert(
                        'Unable to prepare the invoice for printing. Please try again.'
                    );

                }


            } finally {


                /*
                |--------------------------------------------------------------------------
                | Restore button
                |--------------------------------------------------------------------------
                */

                button.disabled =
                    false;

                button.innerText =
                    '🖨️ Print Bill';


            }

        }
    </script>


</body>

</html>