<?php 
    class Mandrill {
        private $uri        = 'https://mandrillapp.com/api/1.0/messages/send-template.json';
        private $api_key    = "k4JeokURtEcIlFCVxHnNGA";

        public function touch($userInfo) {
            $fullName = $_POST['name'].' '.$_POST['surname'];
            $postString = '{
                "key": "'.$this->api_key.'",
                "template_name": "touch",
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
                            "name": "'.$fullName.'",
                            "type": "to"
                        }
                    ],
                    "headers": {
                        "Reply-To": "info@libertineconsultants.co.za"
                    },
                    "global_merge_vars": [
                        {
                            "content": "'.$fullName.'",
                            "name": "fullName"
                        }
                    ]
                }
            }';

            return $this->sendData($postString);
        }

        public function adminEmail($userInfo) {
            $postString = '{
                "key": "'.$this->api_key.'",
                "template_name": "credit_analysis_internal",
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
                        "Reply-To": "'.$userInfo['email'].'"
                    },
                    "global_merge_vars": [
                        {
                            "content": "'.$userInfo['name'].'",
                            "name": "name"
                        },
                        {
                            "content": "'.$userInfo['surname'].'",
                            "name": "surname"
                        },
                        {
                            "content": "'.$userInfo['email'].'",
                            "name": "email"
                        },
                        {
                            "content": "'.$userInfo['idNumber'].'",
                            "name": "idnumber"
                        },
                        {
                            "content": "'.$userInfo['number'].'",
                            "name": "contact"
                        }
                    ]
                }
            }';

            return $this->sendData($postString);
        }

        public function sendData($postString) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->uri);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

            $result = curl_exec($ch);
            return $result;
        }
    }
?> 