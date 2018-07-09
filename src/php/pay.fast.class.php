<?php 
    class PayFast {
        private $url            = 'https://sandbox.payfast.co.za/eng/process';
        private $merchant_key   = "k4JeokURtEcIlFCVxHnNGA";
        private $merchant_id    = "k4JeokURtEcIlFCVxHnNGA";

        function __construct($url) {
            $this->$url = $url;
        }

        public function processPayment($userInfo) {

            $fields = array(
                'merchant_id' => urlencode($_POST['last_name']),
                'merchant_key' => urlencode($_POST['first_name']),
                'return_url' => urlencode($_POST['title']),
                'cancel_url' => urlencode($_POST['title']),
                'notify_url' => urlencode($_POST['institution']),
                'name_first' => urlencode($_POST['name']),
                'name_last' => urlencode($_POST['surname']),
                'email_address' => urlencode($_POST['email']),
                'cell_number' => urlencode($_POST['number']),
                'm_payment_id' => urlencode($_POST['phone']),
                'amount' => urlencode($_POST['amount']),
                'item_name' => urlencode($_POST['phone']),
                'item_description' => urlencode($_POST['phone']),
                'email_confirmation' => 1,
                'confirmation_address' => 'info@libertineconsultants.co.za',
                'payment_method' => 'cc,dc'
            );

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
https://sandbox.payfast.co.za/eng/process
merchant_id
merchant_key
return_url
cancel_url
notify_url
m_payment_id
amount
item_name
item_description
