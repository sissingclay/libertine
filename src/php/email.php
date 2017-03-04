<?php
/**
 * Created by PhpStorm.
 * User: gosia
 * Date: 04/10/15
 * Time: 16:20
 */
include 'curlwrap_v2.php';

$botCheck       = "https://www.google.com/recaptcha/api/siteverify";
$botCheck_api   = "6LckhBYTAAAAAH-I_F0JlhnPhZzcGOFcTEta75Q2";

$botCheckData   = array(
    "secret"    => "6LckhBYTAAAAAH-I_F0JlhnPhZzcGOFcTEta75Q2",
    "response"  => $_POST['g-recaptcha-response']
);

$isBotCheck     = sendData($botCheck, $botCheckData);
$isBotChecked   = json_decode($isBotCheck, true);

$uri        = 'https://mandrillapp.com/api/1.0/messages/send-template.json';
$api_key    = "k4JeokURtEcIlFCVxHnNGA";
$data       = null;

if($_POST['formName'] == 'get in touch' && empty($_POST['isBot']) && $isBotCheck[0]['success'])
{

    $template_name = "touch";

    $postString = '{
        "key": "'.$api_key.'",
        "template_name": "touch_internal",
        "template_content": [
            {
                "name": "first",
                "content": "example content"
            }
        ],
        "message": {
            "from_email": "website@libertineconsultants.co.za",
            "from_name": "Website",
            "to": [
                {
                    "email": "info@libertineconsultants.co.za",
                    "name": "Info",
                    "type": "to"
                }
            ],
            "headers": {
                "Reply-To": "'.$_POST['email'].'"
            },
            "global_merge_vars": [
                {
                    "content": "'.$_POST['fullName'].'",
                    "name": "full_name"
                },
                {
                    "content": "'.$_POST['email'].'",
                    "name": "email"
                },
                {
                    "content": "'.$_POST['enquiry'].'",
                    "name": "message"
                },
                {
                    "content": "'.$_POST['number'].'",
                    "name": "contact"
                }
            ]
        }
    }';

    $result = sendData($uri, $postString);
    $data   = json_decode($result, true);
    sendAgileData($_POST, 'Get in touch');
}

if($_POST['formName'] == 'apply' && empty($_POST['isBot']) && $isBotCheck[0]['success'])
{

    $template_name = "apply_c";

    $test = '{
        "key": "'.$api_key.'",
        "template_name": "apply",
        "template_content": [
            {
                "name": "first",
                "content": "example content"
            }
        ],
        "message": {
            "from_email": "website@libertineconsultants.co.za",
            "from_name": "Website",
            "to": [
                {
                    "email": "info@libertineconsultants.co.za",
                    "name": "Info",
                    "type": "to"
                }
            ],
            "headers": {
                "Reply-To": "'.$_POST['email'].'"
            },
            "global_merge_vars": [
                {
                    "content": "'.$_POST['fullName'].'",
                    "name": "fullname"
                },
                {
                    "content": "'.$_POST['number'].'",
                    "name": "numbers"
                },
                {
                    "content": "'.$_POST['email'].'",
                    "name": "emails"
                },
                {
                    "content": "'.$_POST['creditors'].'",
                    "name": "creditors"
                },
                {
                    "content": "'.$_POST['debt'].'",
                    "name": "debts"
                },
                {
                    "content": "'.$_POST['review'].'",
                    "name": "reviews"
                },
                {
                    "content": "'.$_POST['blacklisted'].'",
                    "name": "blacklisteds"
                },
                {
                    "content": "'.$_POST['garnishees'].'",
                    "name": "garnishees"
                },
                {
                    "content": "'.$_POST['arrears'].'",
                    "name": "arrears"
                },
                {
                    "content": "'.$_POST['position'].'",
                    "name": "positions"
                },
                {
                    "content": "'.$_POST['location'].'",
                    "name": "locations"
                },
                {
                    "content": "'.$_POST['employed'].'",
                    "name": "employeds"
                },
                {
                    "content": "'.$_POST['married'].'",
                    "name": "marrieds"
                }
            ]
        }
    }';

    $result = sendData($uri, $test);
    $data   = json_decode($result, true);
    sendAgileData($_POST, 'Apply for debt relief');
}

function sendData($uri, $postString) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

    $result = curl_exec($ch);

    return $result;
}

if ($data[0]["status"] == 'sent') {

    $postString = '{
        "key": "'.$api_key.'",
        "template_name": "'.$template_name.'",
        "template_content": [
            {
                "name": "first",
                "content": "example content"
            }
        ],
        "message": {
            "from_email": "info@libertineconsultants.co.za",
            "from_name": "Libertine Consultants",
            "to": [
                {
                    "email": "'.$_POST['email'].'",
                    "name": "'.$_POST['fullName'].'",
                    "type": "to"
                }
            ],
            "headers": {
                "Reply-To": "info@libertineconsultants.co.za"
            },
            "global_merge_vars": [
                {
                    "content": "'.$_POST['fullName'].'",
                    "name": "fullName"
                }
            ]
        }
    }';

    sendData($uri, $postString);
}

function sendAgileData ($data, $tag) {

    $contact_email = $data['email'];
    $contact_json = array(
        "tags"=>array($tag),
        "properties"=>array(
            array(
                "name"=>"first_name",
                "value"=>$data['fullName'],
                "type"=>"SYSTEM"
            ),
            array(
                "name"=>"email",
                "value"=>$contact_email,
                "type"=>"SYSTEM"
            ),  
            array(
                "name"=>"phone",
                "value"=>$data['number'],
                "type"=>"SYSTEM"
            ),
        )
    );

    $contact_json = json_encode($contact_json);
    $result = curl_wrap("contacts", $contact_json, "POST", "application/json");
    $result = json_decode($result, false, 512, JSON_BIGINT_AS_STRING);
    $contact_id = $result->id;

    if ($_POST['enquiry']) {
        $descr = $_POST['enquiry'];
    }

    if (!$_POST['enquiry']) {
        $descr = 'creditors: ' .$_POST['creditors']. ', debt: ' .$_POST['creditors']. ', review: ' .$_POST['review']. ', blacklisted: ' .$_POST['blacklisted']. ', garnishees: ' .$_POST['garnishees']. ', arrears: ' .$_POST['arrears']. ', position: ' .$_POST['position']. ', location: ' .$_POST['location']. ', employeds: ' .$_POST['employeds']. ', married: ' .$_POST['married'];
    }

    $note_json = array(
        "subject"=>"Website enquiry",
        "description"=>$descr,
        "contact_ids"=>array($contact_id)
    );

    $note_json = json_encode($note_json);
    curl_wrap("notes", $note_json, "POST", "application/json");
}
