<?php

    // Only process POST reqeusts.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Check Injection
        function sanitize($data) {
            $data   = trim($data);
            $data   = stripslashes($data);
            $data   = htmlspecialchars($data);
            return $data;
        }

        // Get The form data
        $name       = sanitize($_POST['name']);
        $email      = sanitize($_POST['email']);
        $message    = sanitize($_POST['message']);

        // Check that data was sent to the mailer.
        $errors = array();

        if(isset($_POST['name'], $_POST['email'], $_POST['message'])) {
            $fields = array(
                'name'      => $_POST['name'],
                'email'     => $_POST['email'],
                'message'   => $_POST['message']
            );

            foreach ($fields as $field => $data) {
                if(empty($data)) {
                    // $errors[] = "The " . $field . " is required";
                    $errors[] = "Please fill-up the form correctly.";
                    break 1;
                }
            }

            // Check valid email
            if(empty($errors) === true) {
                if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Oops! Invalid E-mail!";
                }
            }

            // Check Human
            if(!isset($_POST['check'])) {
                $errors[] = "Please make sure your are a human";
            }

        }



        // If Get Errors 
        if(empty($errors) === false) {

            // Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo implode("; ",$errors);
            exit;

        }

        // If didn't Get Errors then sending mail
        if(empty($errors) === true) {

            // Set the recipient email address.
            $recipient  = "edl1155@gmail.com,kirkprine@fleshandspirit.org";

            // Set the email subject.
            $subject    = "DR KIRK PRINE: You have a new message from " . $name;

            // Build the email content.
            $vars = array('%name%'=> $name, '%email%'=> $email, '%message%'=> $message);
            $body = strtr(file_get_contents('email.html'), $vars);



            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            // More headers
            $headers .= 'From:DR KIRK PRINE <No-Reply@yourdomain.com>' . "\r\n"; // You have to put your domain
            $headers .= "Reply-To: " . $email . "(" . $name . ")" . "\r\n";

            // Send the email.
            if (mail($recipient, $subject, $body, $headers)) {
                // Set a 200 (okay) response code.
                http_response_code(200);
                echo "Thank You! Your message has been sent.";
            } else {
                // Set a 500 (internal server error) response code.
                http_response_code(500);
                echo "Oops! Something went wrong and we couldn't send your message.";
            }            


        }

    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        // echo "There was a problem with your submission, please try again.";
        header('Location: ../');
    }

?>
