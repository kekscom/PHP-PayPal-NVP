<?php

/*
PayPal.php provides very simple access to PayPal's NVP API.

It is heavily inspired by Mahmoud Al-Qudsi's paypalnvp-php
See https://github.com/NeoSmart/paypalnvp-php

PayPal NVP API: https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/DoExpressCheckoutPayment_API_Operation_NVP/

For implementation examples, start see payment.php.
*/

class PayPal {

    private $version = "87.0";
    private $config;

    public function __construct($config) {
        $this->config = $config;
    }

    private function encodeNvpString($fields) {
        $nvpstr = "";
        foreach ($fields as $key=>$value) {
            $nvpstr .= sprintf("%s=%s&", urlencode(strtoupper($key)), urlencode($value));
        }
        return $nvpstr;
    }

    private function decodeNvpString($nvpstr) {
        $pairs = explode("&", $nvpstr);
        $fields = array();
        foreach ($pairs as $pair) {
            $items = explode("=", $pair);
            $fields[strtoupper(urldecode($items[0]))] = urldecode($items[1]);
        }
        return $fields;
    }

    private function nvpAction($method, $requestFields) {
        $requestFields["USER"]      = $this->config["username"];
        $requestFields["PWD"]       = $this->config["password"];
        $requestFields["SIGNATURE"] = $this->config["signature"];
        $requestFields["VERSION"]   = $this->version;
        $requestFields["METHOD"]    = $method;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config["endpoint"]);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeNvpString($requestFields));

        $responseFields = $this->decodeNvpString(curl_exec($ch));
        curl_close($ch);

        return $responseFields;
    }

    public function __call($method, $args) {
        return $this->nvpAction($method, $args[0]);
    }
}

?>