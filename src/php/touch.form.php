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
    $emailResult = $sparkPost->createEmailSend($_POST, json_encode(["FULLNAME" => $_POST['fullName']]));
    $adminEmail = $sparkPost->createEmailSend(
        $_POST,
        json_encode([
            "FULLNAME" => $_POST['fullName'], 
            "EMAIL" => $_POST['email'], 
            "CONTACT" => $_POST['number'], 
            "MESSAGE" => $_POST['enquiry']
        ]),
        'touch-internal',
        [
            "email" => "info@libertineconsultants.co.za",
            "name" => 'Get in touch'
        ]
    );
    $agile->sendAgileData($_POST, 'Get in touch');

    if ($captchaResultDecode->success === true) {
        $data = json_decode($emailResult, true);
        echo json_encode($data);
    } else {
        http_response_code(500);
    }
?>