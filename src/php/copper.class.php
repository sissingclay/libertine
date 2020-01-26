<?php
    include 'curlwrap_v2-copper.php';

    class Copper {
        private $contacts_id;
        private $contact_data;
        private $tagName;

        private function setTag($tag) {
            $this->tagName = $tag;
        }
        
        private function getTag() {
            return $this->tagName;
        }

        private function setContactData($data) {
            $this->contact_data = $data;
        }
        
        private function getContactData() {
            return $this->contact_data;
        }

        private function setContactID($contacts_id) {
            $this->contacts_id = $contacts_id;
        }
        
        private function getContactID() {
            return $this->contacts_id;
        }
    
        public function sendCopperData ($data, $tag, $extraData = NULL) {     

            $this->setTag($tag);
            $this->setContactData($data);

            $contact_check = curl_wrap($this->createContact());
            $contact_other = json_decode($contact_check);

            return $contact_other;
        }

        private function createContact() {
            $fullName = ($this->contact_data['fullName']) ? $this->contact_data['fullName'] : $this->contact_data['name'] . ' ' . $this->contact_data['surname'];
            $contact_json = array(
                "name" => $fullName,
                "email" => array(
                    "email" => $this->contact_data['email'],
                    "category" => "work",
                ),
                "phone_numbers" => [
                    array(
                        "number" => $this->contact_data['number'],
                        "category" => "mobile",
                    ),
                    
                ],
                "tags" => [
                    $this->contact_data['formName'],
                ],
                "custom_fields" => [
                    array(
                        "custom_field_definition_id" => 373442,
                        "value" => $this->contact_data['callAt'],
                    ),
                    array(
                        "custom_field_definition_id" => 371864,
                        "value" => $this->changePhoneNumber(),
                    ),
                    array(
                        "custom_field_definition_id" => 373441,
                        "value" => $this->contact_data['contactMethod'],
                    ),
                    array(
                        "custom_field_definition_id" => 373745,
                        "value" => $this->addNotes(),
                    ),
                    array(
                        "custom_field_definition_id" => 374903,
                        "value" => $this->contact_data['idNumber'],
                    ),
                ],
            );

            return $contact_json;
        }

        private function addNotes() {

            $descr = '';

            if ($this->contact_data['formName'] === 'get-in-touch') {
                $descr = $this->contact_data['enquiry'];
            }

            if ($this->contact_data['formName'] === 'apply') {
                $descr = 'creditors: ' .$this->contact_data['creditors']. ', debt: ' .$this->contact_data['debt']. ', review: ' .$this->contact_data['review']. ', blacklisted: ' .$this->contact_data['blacklisted']. ', garnishees: ' .$this->contact_data['garnishees']. ', arrears: ' .$this->contact_data['arrears']. ', position: ' .$this->contact_data['position']. ', location: ' .$this->contact_data['location']. ', employeds: ' .$this->contact_data['employeds']. ', married: ' .$this->contact_data['married'];
            }

            if ($this->contact_data['formName'] === 'credit-clearance') {
                $descr = 'blacklisted: ' .$this->contact_data['blacklisted']. ', credit provider taken legal action: ' .$this->contact_data['legal']. ', accounts paid up: ' .$this->contact_data['accounts']. ', under debt review: ' .$this->contact_data['review']. ', outstanding accounts: ' .$this->contact_data['creditors']. ', total outstanding debt: ' .$this->contact_data['debt']. ', employed: ' .$this->contact_data['employed'];
            }

            if ($this->contact_data['formName'] === 'prescribed-debt') {
                $descr = 'location: ' .$this->contact_data['location']. ',blacklisted: ' .$this->contact_data['blacklisted']. ', credit provider taken legal action: ' .$this->contact_data['legal']. ', accounts paid up: ' .$this->contact_data['accounts']. ', previously under debt review: ' .$this->contact_data['review']. ', outstanding accounts: ' .$this->contact_data['creditors']. ', total outstanding debt: ' .$this->contact_data['debt']. ', employed: ' .$this->contact_data['employed'];
            }

            return $descr;
        }

        private function changePhoneNumber() {
            return ($this->contact_data['number'][0] === '0') ? "+27" . substr($this->contact_data['number'], 1) : $this->contact_data['number'];
        }
    }
?>