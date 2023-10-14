<?php
    require_once('copper.class.php');

    header('Content-Type: application/json;charset=utf-8');

    $copper = new Copper();

    $captchaResultDecode['success'] = true;
    $fullName = $_POST['name'] . ' ' . $_POST['surname'];
    $ALL;

    foreach($_POST as $key => $val) {
        if ($key !== 'submit' || $key !== 'g-recaptcha-response' || $key !== 'isBot' || $key !== 'clickedCaptcha') {
            $ALL = $ALL . 'Field name: '.$key. ', Value: '.$val .'  \n';
        }
    }

    if ($_POST['clickedCaptcha'] === 'ticked') {

        $lead = $copper->sendCopperData($_POST, 'Get in touch');
    
        if ($lead->id) {
            echo json_encode($lead);
        } else {
            http_response_code(500);
        }
    } else {    
        http_response_code(500);
    }
?>
