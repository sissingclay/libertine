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
    $emailResult = $sparkPost->createEmailSend($_POST, json_encode(["FULLNAME" => $_POST['name'] . ' ' . $_POST['surname']]));
    $adminEmail = $sparkPost->createEmailSend(
        $_POST,
        json_encode([
            "NAME" => $_POST['name'],
            "SURNAME" => $_POST['surname'],
            "EMAIL" => $_POST['email'], 
            "CONTACT" => $_POST['number'], 
            "MESSAGE" => $_POST['enquiry']
        ]),
        'credit_analysis_internal',
        [
            "email" => "info@libertineconsultants.co.za",
            "name" => 'Credit analysis'
        ]
    );
    $agile->sendAgileData($_POST, 'Credit analysis form');

    if ($captchaResultDecode->success === true) {
        $data = json_decode($emailResult, true);
        echo json_encode($data);
    } else {
        http_response_code(500);
    }
?>