<?php

/**
 * This example shows how to handle a simple contact form safely.
 */

//Import PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



//Don't run this unless we're handling a form submission
if (array_key_exists('email', $_POST)) {
   
    date_default_timezone_set('Etc/UTC');
    require './vendor/autoload.php';
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    //Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    $mail->setFrom('hi@ahmadusman.com');
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
            Subject: {$_POST['subject']}
            Message: {$_POST['message']}
            EOT;
        if (!$mail->send()) {
            //The reason for failing to send will be in $mail->ErrorInfo
            //but it's unsafe to display errors directly to users - process the error, log it on your server.
            if ($isAjax) {
                http_response_code(500);
                }

                $response = [
                    "status" => false,
                    "message" => 'Sorry, something went wrong. Please try again later.'
                ];
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
<h3 id="status-message"><?php if (isset($response)) {
    echo $response['message'];
                        }?></h2>
<form  method="post" role="form" class="php-email-form" id="contact-form">
              <div class="row">
                <div class="col-md-6 form-group">
                  <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
                  <span class="hide">Your name is required</span>
                </div>
                <div class="col-md-6 form-group mt-md-0">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
                  <span class="hide">Your valid email is required</span>
                </div>
              </div>
              <div class="form-group">
                <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required>
                <span class="hide">Subject Field is required</span>
              </div>
              <div class="form-group">
                <textarea class="form-control" name="message" rows="5" placeholder="Message" required></textarea>
                <span class="hide">Message field is required</span>
              </div>
              <div class="my-3">
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Your message has been sent. Thank you!</div>
              </div>
              <div class="text-center"><button type="submit">Send Message</button></div>
            </form>

<script type="application/javascript">
    const form = document.getElementById("contact-form")

    function email(data) {
        const message = document.getElementById("status-message")
        fetch("forms/contact.php", {
            method: "POST",
            body: data,
            headers: {
               'X-Requested-With' : 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(response => {message.innerHTML = response.message})
            .catch(error => {
                error.json().then(response => {
                    message.innerHTML = response.message
                })
            })
    }


    const submitEvent = form.addEventListener("submit", (event) => {
        event.preventDefault();

        const formData = new FormData(form);

        email(formData);
    })
</script>
