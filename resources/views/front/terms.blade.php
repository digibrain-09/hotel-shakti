@include('front.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="contact-us-area default-padding">
    <div class="container">
        <div class="row">
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            <div class="contact-box">

                <div class="col-md-12 info">
                    <div>
                        <h2>Terms & Conditions</h2>
                        <p>
                            Welcome to ScanTable! By accessing and using our website or services, including scanning QR codes, browsing the menu, and placing orders, you agree to comply with these Terms & Conditions. Please read them carefully.
                        </p>
                    </div><br>

                    <div>
                        <h4>1. Acceptance of Terms</h4>
                        <p>By using our website and services, you acknowledge that you have read, understood, and agree to be bound by these Terms & Conditions, as well as our Privacy Policy. If you do not agree with any part of these terms, please refrain from using our services.</p>
                    </div><br>

                    <div>
                        <h4>2. Service Overview</h4>
                        <p>Our website allows customers to:</p>
                        <ul>
                            <li><i class='fas fa-arrow-right'></i> Scan a QR code to view our menu.</li>
                            <li><i class='fas fa-arrow-right'></i> Browse available food and drink items.</li>
                            <li><i class='fas fa-arrow-right'></i> Place and pay for orders directly through the website.</li>
                        </ul>
                    </div><br>

                    <div>
                        <h4>3. User Responsibilities</h4>
                        <p>By using our services, you agree to:</p>
                        <ul class="list-group">
                            <li><i class='fas fa-arrow-right'></i> Provide accurate and complete information during the ordering process.</li>
                            <li><i class='fas fa-arrow-right'></i> Ensure payment details are valid and sufficient to cover the order.</li>
                            <li><i class='fas fa-arrow-right'></i> Not engage in any fraudulent or harmful activities while using the platform.</li>
                        </ul>
                    </div><br>

                    <div>
                        <h4>4. Ordering and Payment</h4>
                        <p>By using our services, you agree to:</p>
                        <ul>
                            <li><i class='fas fa-arrow-right'></i> Payment: Payment must be made at the time of placing the order. Accepted payment methods will be displayed on the payment page.</li>
                            <li><i class='fas fa-arrow-right'></i> Changes and Cancellations: Orders cannot be modified or canceled after they have been prepared or dispatched. Please contact us immediately if you need assistance with an order.</li>
                        </ul>
                    </div><br>

                    <div>
                        <h4>5. Limitations of Liability</h4>
                        <p>We strive to ensure accurate menu information and availability. However, we are not responsible for:</p>
                        <ul>
                            <li><i class='fas fa-arrow-right'></i> Allergic reactions or adverse effects caused by consuming food or beverages.</li>
                            <li><i class='fas fa-arrow-right'></i> Delays or issues caused by technical errors or system downtime.</li>
                            <li><i class='fas fa-arrow-right'></i> Incorrect or incomplete orders resulting from user error.</li>
                        </ul>
                    </div><br>

                    <div>
                        <h4>6. Privacy Policy</h4>
                        <p>We respect your privacy and handle your personal data in accordance with our Privacy Policy. By using our services, you consent to the collection and use of your information as described therein.</p>
                    </div><br>


                    <div>
                        <h4>7. Contact Information</h4>
                        <p>For any questions or concerns about these Terms & Conditions, please contact us at:</p>
                        <a href="mailto:contact@thedigibrain.co.uk"><p>contact@thedigibrain.co.uk</p></a>
                        <a href="https://api.whatsapp.com/send?phone=4407551439414&text=Hi"><p>Phone: +44 07551 439414</p></a>
                    </div><br>
                </div>
            </div>
        </div>
    </div>
</div>

@include('front.footer')