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

        $emailResult = $sparkPost->createEmailSend(
            $_POST, 
            json_encode(["FULLNAME" => $_POST['fullName']]), 
            'apply-c',
            [
                "email" => $_POST['email'],
                "name" => $_POST['fullName']
            ]
        );
        
        $adminEmail = $sparkPost->createEmailSend(
            $_POST,
            json_encode([
                "FULLNAME" => $_POST['fullName'],
                "NUMBERS" => $_POST['number'],
                "EMAILS" => $_POST['email'],
                "CREDITORS" => $_POST['creditors'],
                "DEBTS" => $_POST['debt'],
                "REVIEWS" => $_POST['review'],
                "BLACKLISTEDS" => $_POST['blacklisted'],
                "GARNISHEES" => $_POST['garnishees'],
                "ARREARS" => $_POST['arrears'],
                "POSITIONS" => $_POST['position'],
                "LOCATIONS" => $_POST['location'],
                "EMPLOYEDS" => $_POST['employed'],
                "MARRIEDS" => $_POST['married']
            ]),
            'apply',
            [
                "email" => "info@libertineconsultants.co.za",
                "name" => 'Debt relief'
            ]
        );

        $contact = $agile->sendAgileData($_POST, 'Apply for debt relief');

        if ($contact) {
            echo json_encode($contact);
        } else {
            http_response_code(500);
        }
    } else {
        http_response_code(500);
    }
?>
