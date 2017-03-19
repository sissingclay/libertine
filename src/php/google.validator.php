<?php
    class GoogleValidator {
        private $uri = "https://www.google.com/recaptcha/api/siteverify";
        private $secret = "6LckhBYTAAAAAH-I_F0JlhnPhZzcGOFcTEta75Q2";

        function __construct($captcha) {
            $this->captcha = $captcha;
        }

        public function checker() {
            $botCheckData   = array(
                "secret"    => $this->secret,
                "response"  => $this->captcha
            );

            return $this->sendData($botCheckData);
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