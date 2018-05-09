<?php
//getComment.php
//gets the comments from the db

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('doPingDb.php');

$iniFile="";
doPing();

$client = new rabbitMQClient($iniFile,"testServer");
	
//passing comment info array to be inserted into database
$request = array();
$request['type'] = "getComment";

$response = $client->send_request($request);

echo $response['output'];
?>

