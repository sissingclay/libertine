<?php

/**
 * Agile CRM \ Curl Wrap
 * 
 * The Curl Wrap is the entry point to all services and actions.
 *
 * @author    Agile CRM developers <Ghanshyam>
 */

function curl_wrap($data) {
                                                                  
    $payload = json_encode($data);

    $ch = curl_init();
    $url = "https://api.prosperworks.com/developer_api/v1/leads";

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json', 
        'X-PW-Application: developer_api', 
        'X-PW-AccessToken: ab5e731a587f261b88423aa2118b8232', 
        'X-PW-UserEmail: sharon@libertineconsultants.co.za'
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $output = curl_exec($ch);

    curl_close($ch);

    return $output;
}
