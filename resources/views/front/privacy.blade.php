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
                        <h2>Privacy Policy</h2>
                        <p>
                        Welcome to ScanTable! We value your privacy and are committed to protecting your personal information. This Privacy Policy outlines how we collect, use, and safeguard the information you provide when you use our website and services, including scanning QR codes and placing orders.
                        </p>
                    </div><br>

                    <div>
                        <h4>1. Information We Collect</h4>
                        <ul>
                            <!-- <li><i class='fas fa-arrow-right'></i> Personal Information: When you place an order or sign up for our services, we may collect personal information such as your name, email address, phone number, and payment details.</li> -->
                            <li><i class='fas fa-arrow-right'></i> Non-Personal Information: We may collect non-identifiable information such as your IP address, browser type, and pages visited on our website to improve our services.</li>
                            <li><i class='fas fa-arrow-right'></i> QR Code Data: Scanned QR code data is processed to fulfill your requests (e.g., accessing menus, placing orders, or other services) and is not stored longer than necessary.</li>
                        </ul>
                    </div><br>

                    <div>
                        <h4>2. Changes to This Privacy Policy</h4>
                        <p>We may update this Privacy Policy periodically. Any changes will be posted on this page with the updated effective date. We encourage you to review this page regularly.</p>
                    </div><br>

                    <div>
                        <h4>3. Contact Us</h4>
                        <p>If you have any questions or concerns about this Privacy Policy or our data practices, please contact us:</p>
                        <a href="mailto:contact@thedigibrain.co.uk">
                            <p>contact@thedigibrain.co.uk</p>
                        </a>
                        <a href="https://api.whatsapp.com/send?phone=4407551439414&text=Hi">
                            <p>Phone: +44 07551 439414</p>
                        </a>
                    </div><br>
                </div>
            </div>
        </div>
    </div>
</div>

@include('front.footer')