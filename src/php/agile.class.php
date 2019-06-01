<?php
    include 'curlwrap_v2.php';
    class Agile {
        private $contacts_id;
        private $contact_data;
    
        public function sendAgileData ($data, $tag, $extraData = NULL) {

            $this->contact_data = $data;
            $contact_email = $this->contact_data['email'];

            $contact_check = curl_wrap("contacts/search/email/" . $contact_email, null, "GET", "application/json");
            $contact_check = json_decode($contact_check, false, 512, JSON_BIGINT_AS_STRING);

            if (!$contact_check) {
                $this->createContact();
            } else {
                $this->setContactID($contact_check->id);
            }

            if ($extraData) {
                $this->updateContact($extraData);
            }

            $this->addNotes();

            return $this->getContactID();
        }

        public function updateContact($updateProp) {
            $contact_json = array(
                "id" => $this->getContactID(), //It is mandatory field. Id of contact
                "properties" => $updateProp
            );
              
            $contact_json = json_encode($contact_json);
            curl_wrap("contacts/edit-properties", $contact_json, "PUT", "application/json");
        }

        private function setContactID($contacts_id) {
            $this->contacts_id = $contacts_id;
        }
        
        private function getContactID() {
            return $this->contacts_id;
        }


        private function addNotes() {
            if ($this->contact_data['enquiry']) {
                $descr = $this->contact_data['enquiry'];
            }

            if ($this->contact_data['creditors']) {
                $descr = 'creditors: ' .$this->contact_data['creditors']. ', debt: ' .$this->contact_data['debt']. ', review: ' .$this->contact_data['review']. ', blacklisted: ' .$this->contact_data['blacklisted']. ', garnishees: ' .$this->contact_data['garnishees']. ', arrears: ' .$this->contact_data['arrears']. ', position: ' .$this->contact_data['position']. ', location: ' .$this->contact_data['location']. ', employeds: ' .$this->contact_data['employeds']. ', married: ' .$this->contact_data['married'];
            }

            $note_json = array(
                "subject" => "Website enquiry",
                "description" => $descr,
                "contact_ids" => array($this->contact_id)
            );

            $note_json = json_encode($note_json);
            curl_wrap("notes", $note_json, "POST", "application/json");
        }

        private function createContact() {
            $fullName = ($this->contact_data['fullName']) ? $this->contact_data['fullName'] : $this->contact_data['name'] . ' ' . $this->contact_data['surname'];
            $contact_json = array(
                "tags" => array($tag),
                "properties" => array(
                    array(
                        "name" => "first_name",
                        "value" => $fullName,
                        "type" => "SYSTEM"
                    ),
                    array(
                        "name" => "email",
                        "value" => $this->contact_data['email'],
                        "type" => "SYSTEM"
                    ),  
                    array(
                        "name" => "phone",
                        "value" => $this->contact_data['number'],
                        "type" => "SYSTEM"
                    ),
                    array(
                        "name" => "modified_phone_number",
                        "value" => $this->changePhoneNumber(),
                        "type" => "CUSTOM"
                    )
                )
            );

            $contact_json = json_encode($contact_json);
            $result = curl_wrap("contacts", $contact_json, "POST", "application/json");
            $result = json_decode($result, false, 512, JSON_BIGINT_AS_STRING);
            $this->setContactID($result->id);

            return $result;
        }

        private function changePhoneNumber() {
            return "+27" . ($this->contact_data['number'][0] === '0') ? $str = substr($this->contact_data['number'], 1) : $this->contact_data['number'];
        }
    }
?>
