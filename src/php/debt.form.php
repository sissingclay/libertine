<?php
    require_once('copper.class.php');

    header('Content-Type: application/json;charset=utf-8');

    $copper = new Copper();

    $fullName = $_POST['name'] . ' ' . $_POST['surname'];

    if ($_POST['clickedCaptcha'] === 'ticked') {

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
