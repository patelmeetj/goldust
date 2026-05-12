<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


$captcha_response = $_POST['g-recaptcha-response'] ?? '';
$secret_key = "6LfMtKQrAAAAAOKzTzIPejGwWLq-3yS-QkkqJAVi"; // Replace with your secret key
$verify_url = "https://www.google.com/recaptcha/api/siteverify";
$data = array(
    'secret' => $secret_key,
    'response' => $captcha_response
);
$options = array(
    'http' => array(
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($data)
    )
);
$context = stream_context_create($options);
$response = file_get_contents($verify_url, false, $context);
$response_keys = json_decode($response, true);

if ($response_keys["success"]) {
    // reCAPTCHA verification successful, process your form data here
    // Example: echo "Form submitted successfully!";

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if file is uploaded
        if (isset($_FILES['fileupload']) && $_FILES['fileupload']['error'] == UPLOAD_ERR_OK) {
            $attachment_tmp_name = $_FILES['fileupload']['tmp_name'];
            $attachment_name = $_FILES['fileupload']['name'];
        } else {
            $attachment_tmp_name = '';
            $attachment_name = '';
        }

        // Data from the form
        $name = trim($_POST['txtName'] ?? 'N/A');
        $email = trim($_POST['txtEmail'] ?? 'N/A');
        $contactNumber = trim($_POST['txtContact'] ?? 'N/A');
        $subject = trim($_POST['txtSubject'] ?? 'N/A');
        $message = trim($_POST['txtMessage'] ?? 'N/A');


        // Admin Email
        $adminEmail = 'info@goldusttownship.com';
        $adminSubject = 'New Enquiry Received';

        // Email to Admin
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ashvin.empbweb@gmail.com'; // Use your SMTP username
            $mail->Password = 'eprs hgak jcgt fnuv'; // Use your SMTP password or App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('info@goldusttownship.com', 'Goldust');
            $mail->addAddress($adminEmail); // Admin email

            $mail->isHTML(true);
            $mail->Subject = $adminSubject;
            // The body content of the admin email goes here
            $mail->Body = '<!DOCTYPE html>
                <html>
<head>
<title>Inquiry Mail</title>
</head>

<body>
<div bgcolor="#FFFFFF" marginwidth="0" marginheight="0">

<table width="900" border="5" align="center" cellpadding="0" cellspacing="0" style="border-color:#0a0f4e;padding:10px">

<tr>
<td>

<table width="900" style="padding:5px">
<tbody>

<tr>
<td colspan="3">
<img src="https://goldust.in/img/logo_old.png" style="max-width:200px"/>
</td>
</tr>

<tr>
<td colspan="2">
<h3>Contact Inquiry Details of ' . $name . '</h3>
</td>

<td>
<h5 style="font-size:15px;float:right;text-align:right">
Date:&nbsp;' . date("d/m/Y") . '
</h5>
</td>
</tr>

<tr>
<td colspan="3"><hr/></td>
</tr>

<tr>
<td>

<table width="780" style="padding-left:10px">

<tr>
<td style="width:460px">
<span style="font-size:14px;font-weight:bold;">Name</span>
</td>

<td style="width:90px">
<span style="font-size:14px;font-weight:bold;margin-left:10px;">:</span>
</td>

<td>
<label style="font-size:14px;">' . $name . '</label>
</td>
</tr>

<tr>
<td>
<span style="font-size:14px;font-weight:bold;">Email</span>
</td>

<td>
<span style="font-size:14px;font-weight:bold;margin-left:10px;">:</span>
</td>

<td>
<label style="font-size:14px;">' . $email . '</label>
</td>
</tr>

<tr>
<td>
<span style="font-size:14px;font-weight:bold;">Contact Number</span>
</td>

<td>
<span style="font-size:14px;font-weight:bold;margin-left:10px;">:</span>
</td>

<td>
<label style="font-size:14px;">' . $contactNumber . '</label>
</td>
</tr>

<tr>
<td>
<span style="font-size:14px;font-weight:bold;">Subject</span>
</td>

<td>
<span style="font-size:14px;font-weight:bold;margin-left:10px;">:</span>
</td>

<td>
<label style="font-size:14px;">' . $subject . '</label>
</td>
</tr>

<tr>
<td>
<span style="font-size:14px;font-weight:bold;">Message</span>
</td>

<td>
<span style="font-size:14px;font-weight:bold;margin-left:10px;">:</span>
</td>

<td>
<label style="font-size:14px;">' . $message . '</label>
</td>
</tr>

</table>

</td>
</tr>

<tr>
<td colspan="3"><hr/></td>
</tr>

<tr>
<td colspan="3">
<h3>"Website Contact Form"</h3>
</td>
</tr>

<tr>
<td colspan="3">
<span style="font-size:11px;color:#545353">
<b>Please do not reply to this email address as this is an automated email.</b>
</span>
</td>
</tr>

</tbody>
</table>

</td>
</tr>

</table>

</div>
</body>
</html>
';


            // Add attachment only for admin email
            if ($attachment_tmp_name != '') {
                $mail->addStringAttachment(file_get_contents($attachment_tmp_name), $attachment_name);
            }

            $mail->send();
            echo 'Admin message has been sent.';
        } catch (Exception $e) {
            echo 'Message could not be sent to admin. Mailer Error: ' . $mail->ErrorInfo;
        }

        // Confirmation Email to User
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'barodaweb.inquiry@gmail.com'; // Same SMTP username
            $mail->Password = 'rjdh ngzl urbr fmgm'; // Same SMTP password or App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('info@goldusttownship.com', 'Goldust');
            $mail->addAddress($email, "$name "); // The user who filled the form

            $mail->isHTML(true);
            $mail->Subject = "Thank you for inquiry with Goldust!";
            // Here you can include the HTML content you've provided for the user email
            $mail->isHTML(true); // Tell PHPMailer to use HTML
            $mail->Body = '<!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <title> Mail to Client </title>
    </head>
    
    <body>
    <div bgcolor="#FFFFFF" marginwidth="0" marginheight="0">
    
    <table width="900" border="5" align="center" cellpadding="0" cellspacing="0" style="border-color: #0a0f4e; padding: 10px">
    
    <tr>
    <td>
    
    <table width="900" style="padding: 5px">
    <tbody>
    
    <tr>
    <td colspan="3">
    <img src="https://goldust.in/img/logo_old.png" alt="" title="" style="max-width: 200px" />
    </td>
    </tr>
    
    <tr>
    <td style="width: 100px" colspan="2">
    <h3>
    Dear <label style="font-size: 14px; font-weight: bold">' . $name . ',</label>
    </h3>
    </td>
    
    <td style="width: 290px">
    <h5 style="font-size: 15px; float: right; text-align: right">
    Date:&nbsp;&nbsp;' . date("d/m/Y") . '
    </h5>
    </td>
    </tr>
    
    <tr>
    <td colspan="3">
    <hr style="border-color:#0a0f4e;" />
    </td>
    </tr>
    
    <tr>
    <td colspan="3">
    
    We thank you for enquiring with "Goldust" through our website.<br /><br />
    
    Please be rest assured that your enquiry will have our best attention and we shall get in touch with you shortly.<br /><br />
    
    If you do not receive a response from us within two working days we request you to write to us on 
    <a href="mailto:info@goldusttownship.com">info@goldusttownship.com</a><br /><br />
    
    <br /><br />
    
    Best Regards,<br /><br /><br />
    
    Team "Goldust"
    
    </td>
    </tr>
    
    <tr>
    <td colspan="3">
    <hr style="border-color: #0a0f4e;" />
    <h3>"Goldust"</h3>
    </td>
    </tr>
    
    <tr>
    <td colspan="3">
    <span style="font-size: 11px; color: #0a0f4e">
    <b>Please do not reply to this email address as this is an automated email.</b>
    </span>
    </td>
    </tr>
    
    </tbody>
    </table>
    
    </td>
    </tr>
    
    </table>
    
    </div>
    </body>
    </html>';

            $mail->send();
            echo 'Confirmation message has been sent to the user.';
        } catch (Exception $e) {
            echo 'Confirmation message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }

        // Confirmation Email to User
        $mail = new PHPMailer(true);
        try {
            // Your existing email sending code...

            // Redirect after sending email
            header('Location: index.html');
            exit; // Ensure script execution stops after redirection
        } catch (Exception $e) {
            echo 'Confirmation message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }
    }

} else {
    echo "Captcha verification failed.";
}

?>