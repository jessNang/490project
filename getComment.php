<?php
//getComment.php
//gets the comments from the db

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("db.ini","testServer");
	
//passing comment info array to be inserted into database
$request = array();
$request['type'] = "getComment";

$response = $client->send_request($request);

if($response['valid'] === true){
	echo $response['output'];
}
else{
	echo "Getting comment unsuccessful";
}
?>