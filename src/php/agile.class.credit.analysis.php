<?php
    include 'curlwrap_v2.php';
    class AgileCreditAnalysis {
        public function sendAgileData ($contact_id, $data) {
            $contact_json = array(
                "id" => $contact_id, //It is mandatory field. Id of contact
                "properties" => array(
                    array(
                        "name" => "Id number",
                        "value" => $data['idNumber'],
                        "type" => "CUSTOM"
                    ),
                    array(
                        "name" => "preferred_contact_method",
                        "value" => $data['contactMethod'],
                        "type" => "CUSTOM"
                    ),
                    array(
                        "name" => "when_to_call",
                        "value" => ($data['contactMethod'][0] === 'phone') ? $data['callAt'] : '',
                        "type" => "CUSTOM"
                    ),
                )
            );
              
            $contact_json = json_encode($contact_json);
            curl_wrap("contacts/edit-properties", $contact_json, "PUT", "application/json");
        }
    }
?>