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
    $mail->addAddress('mirzaconnects@gmail.com');
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
<h2 id="status-message"><?php if (isset($response)) {
  echo $response['message'];
                      }?></h2>
<form method="POST" id="contact-form">
  <label for="name">Name: <input type="text" name="name" id="name"></label><br>
  <label for="email">Email address: <input type="email" name="email" id="email"></label><br>
  <label for="message">Message: <textarea name="text-message" id="message" rows="8" cols="20"></textarea></label><br>
  <input type="submit" value="Send">
</form>

<script type="application/javascript">
  const form = document.getElementById("contact-form")

  function email(data) {
      const message = document.getElementById("status-message")
      fetch("", {
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
