<?php

session_start();

require_once("ppconfig.php");
require_once("PayPal.php");

$paypal = new PayPal($ppconfig);

$formattedPrice = number_format($_SESSION["price"] , 2, ".", ",");

// sends parameters to PayPal and asks for payment authorization
$res = $paypal->DoExpressCheckoutPayment(array(
  "TOKEN" => $_REQUEST["token"],
  "PAYERID" => $_REQUEST["PayerID"],
  "PAYMENTREQUEST_0_AMT" => $formattedPrice,
  "PAYMENTREQUEST_0_CURRENCYCODE" => "EUR",
  "PAYMENTREQUEST_0_PAYMENTACTION" => "Sale",
  "IPADDRESS" => $_SERVER["SERVER_NAME"]
));

if ($res["ACK"] == "Success") {
		header("Location: http://yourdomain.com/thankyou.php");
		exit;
}

$_SESSION["res"] = $res;
header("Location: http://yourdomain.com/error.php");

?>