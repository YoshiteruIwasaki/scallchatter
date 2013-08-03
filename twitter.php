<?php

require_once('twitteroauth/twitteroauth.php');

$consumerKey = "W2uKuIKRL6OFY1bk6CPxg";
$consumerSecret = "F21L0RvRteXm885QAeWr02AgwWrP2fsdIIbJT3as";
$accessToken = "84463312-n0gYTkGYfgaYc7fWlv4HCNDKXW5bDNYT1MRwPrU";
$accessTokenSecret = "7hE3hAkEcW7c9zL77wjfiuhbQkC3FAdxyMDSjDzBk";

$count = 1;

if (isset($_GET['since_id'])) {
	$params = array("count"=>$count,"since_id"=>$_GET['since_id']);
}
else {
	$params = array("count"=>$count);
}
$twObj = new TwitterOAuth($consumerKey,$consumerSecret,$accessToken,$accessTokenSecret);
//JSON形式で情報を取得 varは1.1に
$request = $twObj->OAuthRequest("https://api.twitter.com/1.1/statuses/home_timeline.json","GET",$params);

header("Content-Type: application/json; charset=utf-8");
echo $_GET['callback'] . '(' . $request. ')';
