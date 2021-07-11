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
                "name" => $_POST['fullName']
            ]
        );
        
        $adminEmail = $sparkPost->createEmailSend(
            $_POST,
            json_encode([
                "FULLNAME" => $fullName,
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
                "email" => "sharon@libertineconsultants.co.za",
                "name" => 'Debt relief'
            ]
        );

        if ($adminEmail) {
            echo json_encode($lead);
        } else {
            http_response_code(500);
        }
    } else {
        http_response_code(500);
    }
?>
