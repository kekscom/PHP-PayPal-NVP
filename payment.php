<?php

session_start();

require_once("ppconfig.php");
require_once("PayPal.php");

$paypal = new PayPal($ppconfig);

// few sample data
$_SESSION["orderId"] = "0815-".rand(100000, 999999);
$_SESSION["price"] = 14.9;
$formattedPrice = number_format($_SESSION["price"] , 2, ".", ",");

// sends the data to PayPal and returns a token for next steps
$res = $paypal->SetExpressCheckout(array(
  "LOCALECODE" => "DE", // set your desired language code

  "PAYMENTREQUEST_0_INVNUM" => $_SESSION["orderId"], // not needed, but it's checked for duplications
  "PAYMENTREQUEST_0_AMT" => $formattedPrice, // set your formatted total
  "PAYMENTREQUEST_0_CURRENCYCODE" => "EUR", // set your desired currency code
  "PAYMENTREQUEST_0_PAYMENTACTION" => "Sale",

  "L_PAYMENTREQUEST_0_NAME0" => "Item Name", // set your desired item name
  "L_PAYMENTREQUEST_0_QTY0" => 1, // set your desired item amount
  "L_PAYMENTREQUEST_0_AMT0" => $formattedPrice, // set your formatted item price

  // add more items if needed
  
  "REQCONFIRMSHIPPING" => 0, // shipping address doesn't need to be confirmed, avoids confusion for gifts
  "NOSHIPPING" => 1, // shipping address is not even shown, avoids confusion for gifts
  "ALLOWNOTE" => 1, // allow a note from buyer
  "SOLUTIONTYPE" => "Sole", // buyer does not need to create an account
  "RETURNURL" => "http://yourdomain.com/confirm.php", // set your desired url of a confirmation page
  "CANCELURL" => "http://yourdomain.com/checkout.php" // set your desired url to return to
));

// sends the token to PayPal and asks for user authentication
if ($res["ACK"] == "Success") {
  header("Location: ".$config["url"].urlencode($res["TOKEN"]));
  exit;
}

// store response in session in order to monitor it
$_SESSION["res"] = $res;
header("Location: http://yourdomain.com/error.php");

?>