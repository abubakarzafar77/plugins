<link rel="stylesheet" href="style.css" />
<body>

<?php


require_once "Sendega.php";

$msg = 'Kjære bruker. Takk for at du er abonnent hos mattevideo.no. Du har nå gylding tilgang en ny måned.';
$msisdn = 4746861634;
$extID = 0;
$pricegroup = 9900;
$dateToSendSMS = '2012-06-30 22:37:00';

$sendega = new Sendega('http://www.mattevideo.no/deliveryReport');

echo 'sending: '.$msg.'<br/>';
echo 'to: '.$msisdn.'<br/>';
echo 'extID: '.$extID.'<br/>';
echo 'price: '.$pricegroup.'<br/>';
echo 'date: '.$dateToSendSMS.'<br/>';

echo '<pre>';

//    YYYY-MM-DD HH:MI:SS
//					sendSms($msisdn, $msg, $extId = 0, $priceGroup = 0, $dateToSendSMS = "")


//$result = $sendega->sendSms($msisdn, $msg, 0, $pricegroup, $dateToSendSMS);

print_r($result);
echo '</pre>';


?>