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
        
        $adminEmail = $sparkPost->createEmailSend(
            $_POST,
            json_encode([
                "FULLNAME" => $_POST['fullname'],
                "SURNAME" => $_POST['surname'],
                "EMAIL" => $_POST['email'], 
                "CONTACT" => $_POST['number'], 
                "CREDITORS" => $_POST['creditors'],
                "DEBTS" => $_POST['debt'],
                "REVIEWS" => $_POST['review'],
                "BLACKLISTEDS" => $_POST['blacklisted'],
                "ARREARS" => $_POST['arrears'],
                "POSITIONS" => $_POST['position'],
                "LOCATIONS" => $_POST['location'],
                "EMPLOYEDS" => $_POST['employed']
            ]),
            'credit_analysis_internal',
            [
                "email" => "clay@libertineconsultants.co.za",
                "name" => 'Credit analysis'
            ]
        );

        $contact = $agile->sendAgileData($_POST, 'Credit clearance', array(
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
