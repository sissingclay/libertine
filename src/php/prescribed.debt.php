<?php
    require_once('sparkpost.class.php');
    require_once('google.validator.php');
    require_once('copper.class.php');

    header('Content-Type: application/json;charset=utf-8');

    $google = new GoogleValidator($_POST['g-recaptcha-response']);
    $sparkPost = new SparkPost();
    $copper = new Copper();

    $captchaResult = $google->checker();
    // $captchaResultDecode = json_decode($captchaResult, true);
    $captchaResultDecode = { 'success': true };
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
                "email" => "info@libertineconsultants.co.za",
                "name" => 'Credit analysis'
            ]
        );

        $lead = $copper->sendCopperData($_POST, 'Credit clearance');

        if ($lead->id) {
            echo json_encode($lead);
        } else {
            http_response_code(500);
        }
    } else {
        http_response_code(500);
    }
?>
