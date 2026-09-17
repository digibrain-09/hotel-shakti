@include('front.header')

<!-- Start Breadcrumb 
    ============================================= -->
<div class="breadcrumb-area shadow text-center dark bg-fixed text-light" style="background-image: url(assets3/img/contact-banner.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Contact Us</h1>
                <ul class="breadcrumb">
                    <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                    <li class="active">Contact</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<!-- Start Contact 
    ============================================= -->
<div class="contact-us-area default-padding">
    <div class="container">
        <div class="row">
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            <div class="contact-box">

                <!-- Start Form -->
                <div class="col-md-5 form-box">
                    <div class="form-content">
                        <div class="heading">
                            <h3>Drop us a line</h3>
                        </div>
                        <form action="{{ route('mail') }}" method="POST">
                            @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group">
                                        <input class="form-control" id="name" name="name" placeholder="Name" type="text">
                                        <span class="alert-error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input class="form-control" id="email" name="email" placeholder="Email*" type="email">
                                        <span class="alert-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input class="form-control" id="phone" name="phone" placeholder="Phone" type="text">
                                        <span class="alert-error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group message">
                                        <textarea class="form-control" id="message" name="message" placeholder="Tell Us About Project *"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <button type="submit" name="submit" id="submit">
                                        Send Message <i class="fa fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Alert Message -->
                            <div class="col-md-12 alert-notification">
                                <div id="message" class="alert-msg"></div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- End Form -->

                <div class="col-md-6 col-md-offset-1 info">
                    <h2>Contact Us</h2>
                    <p>
                        We’re here to help! Whether you have a question, need assistance, or want to learn more about our services, feel free to reach out. Connect with us via email, phone, or by filling out the contact form. Let’s create something amazing together!
                    </p>
                    <div class="address-items">
                        <div class="row">
                            <!-- Item -->
                            <div class="col-md-6 col-sm-6 equal-height">
                                <div class="item">
                                    <div class="icon"><i class="fas fa-map-marked-alt"></i></div>
                                    <span>Shiv Kuber, Uganda Road, HDFC Bank, 2nd Floor, s7 Porbandar - 360575</span>
                                </div>
                            </div>
                            <!-- End Item -->
                            <!-- Item -->
                            <div class="col-md-6 col-sm-6 equal-height">
                                <div class="item">
                                    <div class="icon"><i class="fas fa-phone"></i></div>
                                    <a href="https://api.whatsapp.com/send?phone=4407551439414&text=Hi"><span> +44 07551 439414</span></a>
                                </div>
                            </div>
                            <!-- End Item -->
                            <!-- Item -->
                            <div class="col-md-6 col-sm-6 equal-height">
                                <div class="item">
                                    <div class="icon"><i class="fas fa-envelope-open"></i> </div>
                                    <a href="mailto:info@scantable.online"><span>info@scantable.online</span></a>
                                </div>
                            </div>
                            <!-- End Item -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Contact -->

<!-- Start Google Maps 
    ============================================= -->
<div class="maps-area">
    <div class="container-full">
        <div class="row">
            <div class="google-maps">
                <!-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3708.592290252933!2d69.60868667159298!3d21.640805369940995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3956354c6d85094d%3A0x3d9127af75807e42!2sShiv%20Kuber!5e0!3m2!1sen!2sin!4v1740809453761!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d10165297.527228605!2d-18.756730650000012!3d51.53592330000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4876123df9b6da37%3A0x6c8d68c85f799a8d!2sDIGIBRAIN!5e0!3m2!1sen!2sin!4v1767072865923!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
        </div>
    </div>
</div>
<!-- End Google Maps -->

@include('front.footer')