<?php

// Configuration option.
// Enter the email address that you want to emails to be sent to.
// Example $address = "joe.doe@yourdomain.com";

//$address = "example@themeforest.net";
$address = "developer.digibrain@gmail.com";


// Configuration option.
// i.e. The standard subject will appear as, "You've been contacted by John Doe."

// Example, $e_subject = '$name . ' has contacted you via Your Website.';

$e_subject = 'Contact Form';


// Configuration option.
// You can change this if you feel that you need to.
// Developers, you may wish to add more fields to the form, in which case you must be sure to add them here.

$e_body = "You have been contacted by ".$data['name']."";
$e_content = "\"".$data['message']."\"";
$e_reply = "You can contact ".$data['name']." via email, ".$data['email']."";

$msg = wordwrap( $e_body . $e_content . $e_reply, 70 );

$headers = "From: ".$data['email']."" . PHP_EOL;
$headers .= "Reply-To: ".$data['email']."" . PHP_EOL;
$headers .= "MIME-Version: 1.0" . PHP_EOL;
$headers .= "Content-type: text/plain; charset=utf-8" . PHP_EOL;
$headers .= "Content-Transfer-Encoding: quoted-printable" . PHP_EOL;

mail($address, $e_subject, $msg, $headers);