<?php
    require_once('sparkpost.class.php');
    require_once('google.validator.php');
    require_once('copper.class.php');

    header('Content-Type: application/json;charset=utf-8');

    $google = new GoogleValidator($_POST['g-recaptcha-response']);
    $sparkPost = new SparkPost();
    $copper = new Copper();

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

        $lead = $copper->sendCopperData($_POST, 'Apply for debt relief');

        if ($lead->id) {
            echo json_encode($lead);
        } else {
            http_response_code(500);
        }
    } else {
        http_response_code(500);
    }
?>
