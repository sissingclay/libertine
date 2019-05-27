<?php
    require_once('sparkpost.class.php');
    require_once('google.validator.php');
    require_once('agile.class.php');

    header('Content-Type: application/json;charset=utf-8');

    $google = new GoogleValidator($_POST['g-recaptcha-response']);
    $sparkPost = new SparkPost();
    $agile = new Agile();
    $agileCredit = new AgileCreditAnalysis();

    $captchaResult = $google->checker();
    $captchaResultDecode = json_decode($captchaResult, true);
    $fullName = $_POST['name'] . ' ' . $_POST['surname'];

    if ($captchaResultDecode->success === true) {
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

        $contact_id = $agile->sendAgileData($_POST, 'Credit analysis');
        $agileCredit->sendData($contact_id, $_POST);

        $data = json_decode($emailResult, true);
        echo json_encode($data);
    } else {
        http_response_code(500);
    }
?>