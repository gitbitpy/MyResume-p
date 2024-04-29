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
    require '../vendor/autoload.php';
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
    const form = document.querySelector("form[class='php-email-form']");
const nameInput = document.querySelector("input[name='name']");
const emailInput = document.querySelector("input[name='email']");
const subjectInput = document.querySelector("input[name='subject']");
const messageInput = document.querySelector("textarea[name='message']");

nameInput.isValid = () => !!nameInput.value;
emailInput.isValid = () => isValidEmail(emailInput.value);
subjectInput.isValid = () => !!subjectInput.value;
/**phoneInput.isValid = () => isValidPhone(phoneInput.value);*/
messageInput.isValid = () => !!messageInput.value;

const inputFields = [nameInput, emailInput, subjectInput, messageInput];

const isValidEmail = (email) => {
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
};

/**const isValidPhone = (phone) => {
  const re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
  return re.test(String(phone).toLowerCase());
};*/

let isFormValid = false;

const validateInputs = () => {

  isFormValid = true;
  inputFields.forEach((input) => {
    input.classList.remove("invalid");
    input.nextElementSibling.classList.add("hide");

    if (!input.isValid()) {
      input.classList.add("invalid");
      isFormValid = false;
      input.nextElementSibling.classList.remove("hide");
    }
  });
};

form.addEventListener("submit", (e) => {
    e.preventDefault();
    form.querySelector('.loading').classList.add('d-block');
    
    // Flag to determine if validation should occur
    
    // Validate form inputs
    validateInputs();
    
    if (isFormValid) {
        // Serialize form data
        const data = new FormData(form);
        
        // Perform AJAX request
        fetch('forms/contact.php', {
            method: 'POST',
            body: data
        })
        .then(response => {
            if (response.ok) {
                // Response is OK, show success message
                form.querySelector('.sent-message').classList.add('d-block');
                form.querySelector('.loading').classList.remove('d-block');
                form.reset(); // Reset the form
            } else {
                // Response is not OK, show error message
                throw new Error('Failed to submit form');
                form.querySelector('.loading').classList.remove('d-block');
            }
        })
        .catch(error => {
            // Handle errors
            console.error('Error:', error);
            form.querySelector('.error-message').textContent = 'Failed to submit form';
            form.querySelector('.error-message').classList.add('d-block');
            form.querySelector('.loading').classList.remove('d-block');
        });
    }
});

inputFields.forEach((input) => input.addEventListener("input", validateInputs));
</script>
