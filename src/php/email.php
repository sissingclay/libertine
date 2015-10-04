<?php
/**
 * Created by PhpStorm.
 * User: gosia
 * Date: 04/10/15
 * Time: 16:20
 */

require($_SERVER["DOCUMENT_ROOT"].'/sendgrid-php/sendgrid-php.php');

$sendgrid_username  = 'libertine';
$sendgrid_password  = 'julie007';
$to                 = 'claysissing@gmail.com';
$name               = 'Joe Doe';

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$sendgrid = new SendGrid($sendgrid_username, $sendgrid_password, array("turn_off_ssl_verification" => true));

$email    = new SendGrid\Email();
$email->addTo($to)->
setFrom($to)->
setSubject('Get in touch -  %yourname%')->
setText(
    'Owl are you doing?'
)->
setHtml(
    '<strong>%how% are you doing?</strong>'
)->
addSubstitution("%yourname%", array($_POST['fullname']));

$response = $sendgrid->send($email);

if ($response) {
    echo done;
}