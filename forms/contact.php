<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './PHPmailer/src/Exception.php';
require './PHPmailer/src/PHPMailer.php';
require './PHPmailer/src/SMTP.php';
date_default_timezone_set('Etc/UTC');

if (isset($_POST['email'])) {
    date_default_timezone_set('Etc/UTC');
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    //Create a new PHPMailer instance
    $mail = new PHPMailer();
    //Send using SMTP to localhost (faster and safer than using mail()) – requires a local mail server
    //See other examples for how to use a remote server such as gmail
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->Port = 465;
    $mail->SMTPAuth = true;
    //Username to use for SMTP authentication
    $mail->Username = 'hi@ahmadusman.com';
    //Password to use for SMTP authentication
    $mail->Password = '989981122Q@z';
    //Set who the message is to be sent from
    $name = $_POST['name']; // You need to define $name variable
    $mail->setFrom('hi@ahmadusman.com', (empty($name) ? 'Contact For Ahmad' : $name));

    //Choose who the message should be sent to
    //You don't have to use a <select> like in this example, you can simply use a fixed address
    //the important thing is *not* to trust an email address submitted from the form directly,
    //as an attacker can substitute their own and try to use your form to send spam

    $mail->addAddress('mirzaconnects@gmail.com');
    //Put the submitter's address in a reply-to header
    //This will fail if the address provided is invalid,
    //in which case we should ignore the whole request
    if ($mail->addReplyTo($_POST['email'], $_POST['name'])) {
        $mail->Subject = 'PHPMailer contact form';
        //Keep it simple - don't use HTML
        $mail->isHTML(false);
        //Build a simple message body
        $mail->Body = <<<EOT
Email: {$_POST['email']}
Name: {$_POST['name']}
Message: {$_POST['text-message']}
EOT;

        //Send the message, check for errors
        if (!$mail->send()) {
            //The reason for failing to send will be in $mail->ErrorInfo
            //but it's unsafe to display errors directly to users - process the error, log it on your server.
            if ($isAjax) {
                http_response_code(500);
                $response = [
                    "status" => false,
                    "message" => 'Sorry, something went wrong. Please try again later.'
                ];
            }
        } else {
            $response = [
                "status" => true,
                "message" => 'Message sent! Thanks for contacting us.'
            ];
        }
    } else {
        $response = [
            "status" => false,
            "message" => 'Invalid email address, message ignored.'
        ];
    }

    if ($isAjax) {
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($response);
        exit();
    }
}
?>
