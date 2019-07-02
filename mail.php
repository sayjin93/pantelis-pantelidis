<?php // Check if form was submitted:
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {

    // Build POST request:
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = '6Lcqs6sUAAAAAG33vKaxkSQsHQwKyHcr6jkk374I';
    $recaptcha_response = $_POST['recaptcha_response'];

    // Make and decode POST request:
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);

    // Take action based on the score returned:
    if ($recaptcha->score >= 0.5) {
        // Verified - send email
        $name = $_REQUEST['name'];
        $email = $_REQUEST['email'];
        $message = $_REQUEST['message'];
        if (($name == "") || ($email == "") || ($message == "")) {
            echo "All fields are required, please fill <a href=\"\">the form</a> again.";
        } else {
            $from = "From: $name<$email>\r\nReturn-path: $email";
            $subject = "Message sent using your contact form";
            mail("info@pantelispantelidis.gr", $subject, $message, $from);
            echo "Email sent succesfully!";
        }
    } else {
        // Not verified - show form error
        echo "Email did not send! reCaptcha not virified.";
    }

}