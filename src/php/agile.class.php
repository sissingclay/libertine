<?php
    include 'curlwrap_v2.php';
    class Agile {
        public function sendAgileData ($data, $tag) {
            $contact_email = $data['email'];
            $fullName = ($data['fullName']) ? $data['fullName'] : $data['name'] . ' ' . $data['surname'];
            $contact_json = array(
                "tags"=>array($tag),
                "properties"=>array(
                    array(
                        "name" => "first_name",
                        "value" => $fullName,
                        "type" => "SYSTEM"
                    ),
                    array(
                        "name" => "email",
                        "value" => $contact_email,
                        "type" => "SYSTEM"
                    ),  
                    array(
                        "name" => "phone",
                        "value" => $data['number'],
                        "type" => "SYSTEM"
                    ),
                )
            );

            $contact_json = json_encode($contact_json);
            $result = curl_wrap("contacts", $contact_json, "POST", "application/json");
            $result = json_decode($result, false, 512, JSON_BIGINT_AS_STRING);
            $contact_id = $result->id;

            if ($data['enquiry']) {
                $descr = $data['enquiry'];
            }

            if ($data['creditors']) {
                $descr = 'creditors: ' .$data['creditors']. ', debt: ' .$data['debt']. ', review: ' .$data['review']. ', blacklisted: ' .$data['blacklisted']. ', garnishees: ' .$data['garnishees']. ', arrears: ' .$data['arrears']. ', position: ' .$data['position']. ', location: ' .$data['location']. ', employeds: ' .$data['employeds']. ', married: ' .$data['married'];
            }

            $note_json = array(
                "subject" => "Website enquiry",
                "description" => $descr,
                "contact_ids" => array($contact_id)
            );

            $note_json = json_encode($note_json);
            curl_wrap("notes", $note_json, "POST", "application/json");
        }
    }
?>