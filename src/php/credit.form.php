<?php
    require_once('mandrill.class.php');
    require_once('google.validator.php');

    header('Content-Type: application/json;charset=utf-8');

    $google = new GoogleValidator($_POST['g-recaptcha-response']);
    $mandrill = new Mandrill();

    $captchaResult = $google->checker();
    $captchaResultDecode = json_decode($captchaResult, true);
    $emailResult = $mandrill->touch($_POST);
    $adminEmail = $mandrill->adminEmail($_POST);

    if ($captchaResultDecode->success === true) {
        $data = json_decode($emailResult, true);
        echo json_encode($data);
    } else {
        http_response_code(500);
    }
?>