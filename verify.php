<?php
$access_token = 'DdpSTv6Uqr27sP+tFqF1poQGxMxRuERJjt4WF6zHUgjXkgY295RrXWrDlCyBifwXAzApCVcYLjgZ7dfDiKQ0kAEasY+OBT03FRRFIm0ImIEixIipLcz7M78Qa1HneZ9CzE+Tj2276n0RK+rKiyuRjQdB04t89/1O/w1cDnyilFU=';

$url = 'https://api.line.me/v1/oauth/verify';

$headers = array('Authorization: Bearer ' . $access_token);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
curl_close($ch);

echo $result;
?>
