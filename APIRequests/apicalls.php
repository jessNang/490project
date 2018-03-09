#!/usr/bin/php

<?php
include 'class.movieAPIFind.php';
include 'class.movieAPIDiscover.php';
include 'class.movieAPIRecommendations.php';
include 'class.movieAPIUpcoming.php';
include 'class.movieAPICurrentReleases.php';

require_once('../../../git/rabbitmqphp_example/path.inc');
require_once('../../../git/rabbitmqphp_example/get_host_info.inc');
require_once('../../../git/rabbitmqphp_example/rabbitMQLib.inc');

echo "Listening for API requests ... ".PHP_EOL;

function requestProcessor($request)
{
	$response = array();	
	var_dump($request);
	if(!isset($request['type']))
	{
		return "ERROR: unsupported message type";
	}

	
	switch($request['type'])
	{ 

		case "discover":	$discoverParams = $request['params']; //place holder data. get from queue
					$page = $request['page']; //get from queue
					$response['type'] = "discover";
					$response['data'] = movieAPIDiscover::_movieDiscover($discoverParams, $page);
					break;
		case "find":		$findParams = $request['params']; //place holder data. get from queue
					$response['type'] = "find";
					$response['data'] = movieAPIFind::_moviefind($findParams);
					break;
		case "recommend":	$recommendParams = $request['params']; //place holder data. get from queue
					$page = $request['page']; //get from queue
					$response['type'] = "recommend";
					$response['data'] = movieAPIRecommendations::_movieRecommend($recommendParams, $page);
					break;
		case "upcoming":	$page = $request['page']; //get from queue
					$response['type'] = "upcoming";
					$response['data'] = movieAPIUpcoming::_movieUpcoming($page);
					break;
		case "current":		$page = $request['page']; //get from queue
					$response['type'] = "current";
					$response['data'] = movieAPICurrentReleases::_movieCurrent($page);
					break;
		case "classics":	$page = $request['page']; //get from queue
					$response['type'] = "classics";
					$response['data'] = movieAPICurrentReleases::_movieClassics($page);
					break;
		default:		return array("type" => 'log', 'message'=>"Server received request and processed");
	}
	return $response;
}

$server = new rabbitMQServer("../toDMZ.ini","testServer");
$server->process_requests('requestProcessor');
exit();
?>

