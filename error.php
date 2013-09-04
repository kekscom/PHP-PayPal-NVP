<?php

session_start();

header("Content-Type: text/plain");

echo "*** PayPal API has returned an error ***\n"

$res = $_SESSION["res"]; 

echo "Ack:\t".$res["ACK"]."\n";
echo "Correlation ID:\t".$res["CORRELATIONID"]."\n";
echo "Version:\t".$res["VERSION"]."\n";

$count = 0;
while (isset($res["L_SHORTMESSAGE".$count])) {
  echo "Error Number:\t".$res["L_ERRORCODE".$count]."\n";
  echo "Short Message:\t".$res["L_SHORTMESSAGE".$count]."\n";
  echo "Long Message:\t".$$res["L_LONGMESSAGE".$count]."\n";

  $count++; 
}

?>