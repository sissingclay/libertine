<?php
    require_once('sparkpost.class.php');

    header('Content-Type: application/json;charset=utf-8');

    $sparkPost = new SparkPost();

    $fullName = $_POST['name'] . ' ' . $_POST['surname'];

    if ($_POST['clickedCaptcha'] === 'ticked') {

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
                "FULLNAME" => $fullName, 
                "EMAIL" => $_POST['email'], 
                "CONTACT" => $_POST['number'], 
                "MESSAGE" => $_POST['enquiry'],
                "ALL" => $ALL
            ]),
            'credit-analysis-internal',
            [
                "email" => "sharon@libertineconsultants.co.za",
                "name" => 'Sharon-Rose Duminy'
            ]
        );

        if ($adminEmail) {
            echo json_encode($adminEmail);
        } else {
            http_response_code(500);
        }
    } else {
        http_response_code(500);
    }
?>
