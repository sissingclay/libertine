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

$_POST              = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$form               = $_POST['email'];

$sendgrid           = new SendGrid($sendgrid_username, $sendgrid_password, array("turn_off_ssl_verification" => true));
$email              = new SendGrid\Email();

if($_POST['formName'] == 'get in touch' && empty($_POST['isBot']))
{
    $email->addTo($to)->
    setFrom($form)->
    setSubject('Get in touch form - %fullName%')->
    setText(
        'Enquiry: %enquiry% \n
        Contact Number: %number%\n
        Full name: %fullName%\n
        email: %email%'
    )->
    setHtml(
        'Enquiry: %enquiry% \n
        Contact Number: %number%\n
        Full name: %fullName%\n
        email: %email%'
    )->
    addSubstitution("%fullName%", array($_POST['fullName']))->
    addSubstitution("%number%", array($_POST['number']))->
    addSubstitution("%email%", array($_POST['email']))->
    addSubstitution("%enquiry%", array($_POST['enquiry']));
}


if($_POST['formName'] == 'apply' && empty($_POST['isBot']))
{
    $email->addTo($to)->
    setFrom($form)->
    setSubject('Apply for debt relief form - %fullName%')->
    setText(
        'employed: %enquiry% \n
        location: %enquiry% \n
        position: %enquiry% \n
        arrears: %enquiry% \n
        garnishees: %enquiry% \n
        blacklisted: %enquiry% \n
        review: %enquiry% \n
        debt: %enquiry% \n
        creditors: %enquiry% \n
        married: %enquiry% \n
        Contact Number: %number%\n
        Full name: %fullName%\n
        email: %email%'
    )->
    setHtml(
        'employed: %employed% \n
        location: %location% \n
        position: %position% \n
        arrears: %arrears% \n
        garnishees: %garnishees% \n
        blacklisted: %blacklisted% \n
        review: %review% \n
        debt: %debt% \n
        creditors: %creditors% \n
        married: %married% \n
        Contact Number: %number%\n
        Full name: %fullName%\n
        email: %email%'
    )->
    addSubstitution("%fullName%", array($_POST['fullName']))->
    addSubstitution("%number%", array($_POST['number']))->
    addSubstitution("%email%", array($_POST['email']))->
    addSubstitution("%creditors%", array($_POST['creditors']))->
    addSubstitution("%debt%", array($_POST['debt']))->
    addSubstitution("%review%", array($_POST['review']))->
    addSubstitution("%blacklisted%", array($_POST['blacklisted']))->
    addSubstitution("%garnishees%", array($_POST['garnishees']))->
    addSubstitution("%arrears%", array($_POST['arrears']))->
    addSubstitution("%position%", array($_POST['position']))->
    addSubstitution("%location%", array($_POST['location']))->
    addSubstitution("%employed%", array($_POST['employed']))->
    addSubstitution("%married%", array($_POST['married']));
}

$response = $sendgrid->send($email);

if ($response) {
    echo done;
}