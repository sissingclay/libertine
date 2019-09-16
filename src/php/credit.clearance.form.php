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
    $fullName = $_POST['name'] . ' ' . $_POST['surname'];

    if ($captchaResultDecode['success']) {

        $emailResult = $sparkPost->createEmailSend(
            $_POST, 
            json_encode(["FULLNAME" => $fullName]), 
            'apply-c',
            [
                "email" => $_POST['email'],
                "name" => $_POST['fullname']
            ]
        );

        $contact = $agile->sendAgileData($_POST, 'Credit clearance', array(
            array(
                "name" => "location",
                "value" => $_POST['location'],
                "type" => "SYSTEM"
            ),
            array(
                "name" => "preferred_contact_method",
                "value" => $_POST['contactMethod'],
                "type" => "CUSTOM"
            ),
            array(
                "name" => "when_to_call",
                "value" => $_POST['callAt'],
                "type" => "CUSTOM"
            )
        ));

        if ($contact) {
            echo json_encode($contact);
        } else {
            http_response_code(500);
        }
    } else {
        http_response_code(500);
    }
?>
