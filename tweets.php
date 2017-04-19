<?php
//keys and tokens
$consumer_key = 'Ux1qaf6MujsbOBTzjkmuKhOKm';
$consumer_secret = 'Vk2jm87EIsPLoHgmra5J8oiKzxXJrkRWCafmhj8KbM7btxIGIC';
$access_token = '564223341-DAnMKPCB3cFNtyuljhemFvsV2WCnd9lYHoH1pLEh';
$access_token_secret = '9q2boQtGfh5fGrUrndFgIXZHYJPD7hYbAzgUUshTjRRng';

//Include library
require "twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

$connection = new TwitterOAuth($consumer_key,$consumer_secret,
    $access_token, $access_token_secret);
$content = $connection->get("account/verify_credentials");

$statuses = $connection->get("search/tweets", ["q" => "ghostbusters"]);


print_r($statuses);
    
?>
