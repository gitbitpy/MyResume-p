<?php
if (isset($_POST['email'])) {

    $email_to = "ahmadusman.5.au@gmail.com";
    $email_subject = "You've got a new submission";


    // validation expected data exists
    if (
        !isset($_POST['name']) ||
        !isset($_POST['email']) ||
        !isset($_POST['subject']) ||
        !isset($_POST['message'])
    )

    $name = $_POST['name']; // required
    $email = $_POST['email']; // required
    $subject=$_POST['subject'];//required
    $message = $_POST['message']; // required
    
    if (strlen($message) < 2) {
        $error_message .= 'Message should not be less than 2 characters<br>';
    }

    if (strlen($error_message) > 0) {
        problem($error_message);
    }

    $email_message = "Form details following:\n\n";

    function clean_string($string)
    {
        $bad = array("content-type", "bcc:", "to:", "cc:", "href");
        return str_replace($bad, "", $string);
    }

    $email_message .= "Name: " . clean_string($name) . "\n";
    $email_message .= "Email: " . clean_string($email) . "\n";
    $email_message .= "Subject: " . clean_string($subject) . "\n";
    $email_message .= "Message: " . clean_string($message) . "\n";

    // create email headers
    $headers = 'From: ' . $email . "\r\n" .
        'Reply-To: ' . $email . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    @mail($email_to, $email_subject, $email_message, $headers);
?>
    <script>
 const successMessages = document.querySelectorAll('.sent-message');

// Loop through each element in the NodeList
successMessages.forEach(message => {
  // Add the 'd-block' class to each element
  message.classList.add('d-block');
});
 </script>
    <?php
  }

?>