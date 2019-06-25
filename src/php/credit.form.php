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
                "name" => $fullName
            ]
        );
        
        $adminEmail = $sparkPost->createEmailSend(
            $_POST,
            json_encode([
                "NAME" => $_POST['name'],
                "SURNAME" => $_POST['surname'],
                "EMAIL" => $_POST['email'], 
                "CONTACT" => $_POST['number'], 
                "IDNUMBER" => $_POST['idNumber'],
                "PREFERREDCONTACTMETHOD" => $_POST['contactMethod'],
                "WHENTOCALL" => $_POST['callAt'],
            ]),
            'credit_analysis_internal',
            [
                "email" => "clay@libertineconsultants.co.za",
                "name" => 'Credit analysis'
            ]
        );

        $contact = $agile->sendAgileData($_POST, 'Credit analysis', array(
            array(
                "name" => "id_number",
                "value" => $_POST['idNumber'],
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
