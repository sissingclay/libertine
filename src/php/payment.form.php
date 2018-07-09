<?php
    require_once('sparkpost.class.php');
    require_once('google.validator.php');
    require_once('agile.class.php');
    require_once('pay.fast.class.php');

    header('Content-Type: application/json;charset=utf-8');

    $google = new GoogleValidator($_POST['g-recaptcha-response']);
    $sparkPost = new SparkPost();
    $agile = new Agile();
    $payFast = new PayFast();

    $captchaResult = $google->checker();
    $captchaResultDecode = json_decode($captchaResult, true);
    $fullName = $_POST['name'] . ' ' . $_POST['surname'];

    if ($captchaResultDecode->success === true) {
        $data = json_decode($emailResult, true);
        $payFast->processPayment($_POST);
        
        echo json_encode($data);
    } else {
        http_response_code(500);
    }
?>