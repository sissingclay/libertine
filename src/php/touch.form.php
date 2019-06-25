<?php
    require_once('sparkpost.class.php');
    require_once('google.validator.php');
    require_once('agile.class.php');

    header('Content-Type: application/json;charset=utf-8');

    $google = new GoogleValidator($_POST['g-recaptcha-response']);
    $sparkPost = new SparkPost();
    $agile = new Agile();

    $captchaResult = $google->checker();
    $captchaResultDecode = json_decode($captchaResult, true);

    if ($captchaResultDecode['success']) {

        // $emailResult = $sparkPost->createEmailSend(
        //     $_POST, 
        //     json_encode(["FULLNAME" => $_POST['fullName']])
        // );
        
        // $adminEmail = $sparkPost->createEmailSend(
        //     $_POST,
        //     json_encode([
        //         "FULLNAME" => $_POST['fullName'], 
        //         "EMAIL" => $_POST['email'], 
        //         "CONTACT" => $_POST['number'], 
        //         "MESSAGE" => $_POST['enquiry']
        //     ]),
        //     'touch-internal',
        //     [
        //         "email" => "clay@libertineconsultants.co.za",
        //         "name" => 'Get in touch'
        //     ]
        // );

        $contact = $agile->sendAgileData($_POST, 'Get in touch');
    
        if ($contact) {
            echo json_encode($contact);
        } else {
            http_response_code(500);
        }
    } else {    
        http_response_code(500);
    }
?>
