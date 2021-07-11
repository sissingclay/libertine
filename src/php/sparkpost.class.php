<?php 
    require_once('api.keys.php');

    class SparkPost {
        private $glob;

        public function __construct() {
            global $GLOBALS;
            $this->glob =& $GLOBALS;
        }

        public function createEmailSend($userInfo, $data = '{}', $template = 'touch', $recipients) {
            $email = ($recipients) ? $recipients['email'] : $userInfo['email'];
            $name = ($recipients) ? $recipients['name'] : ($userInfo['fullName']) ? $userInfo['fullName'] : $userInfo['name'] .' '. $userInfo['surname'];
            $postData = '{
                "options": {
                    "open_tracking": true,
                    "click_tracking": true
                },
                "content": {
                    "template_id": "'.$template.'",
                    "use_draft_template": false
                },
                "recipients": [
                    {
                        "address": {
                            "email": "'.$email.'",
                            "name": "'.$name.'"
                        },
                        "substitution_data": '.$data.'
                    }
                ]
            }';

            return $this->sendData($postData);
        }

        public function sendData($postString) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->glob['sparkpost_uri']);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: '. $this->glob['sparkpost_key']
            ));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

            $result = curl_exec($ch);
            return $result;
        }
    }
?> 