#!/usr/bin/php

<?php
include 'class.movieAPIFind.php';
include 'class.movieAPIDiscover.php';
include 'class.movieAPIRecommendations.php';
include 'class.movieAPIUpcoming.php';
include 'class.movieAPICurrentReleases.php';
include 'class.movieAPIClassics.php';

require_once('../../../git/rabbitmqphp_example/path.inc');
require_once('../../../git/rabbitmqphp_example/get_host_info.inc');
require_once('../../../git/rabbitmqphp_example/rabbitMQLib.inc');
require_once('../logger.inc');

echo "Listening for API requests ... ".PHP_EOL;

function requestProcessor($request)
{
	$logClient = new rabbitMQClient('../toLog.ini', 'testServer');
        $logger = new Logger();	

	$response = array();
	//print_r($request);	
	var_dump($request);

	
	if(!isset($request['type']))	//check to see that a request type was set
	{
		$eventMessage = "API request type not set";
        	$sendLog = $logger->logArray('error',$eventMessage,__FILE__);
		$testVar = $logClient->publish($sendLog);
		return "API request type not set";
	}

	//figure out which type of request was used and get the appropriate data
	switch($request['type'])
	{ 

		case "discover":	$discoverParams = $request['params']; //get params from queue
					$page = $request['page']; //get page number from queue
					$response['type'] = "discover";	//set response type
					$response['data'] = movieAPIDiscover::_movieDiscover($discoverParams, $page);	//send api request and set the response data

					//log that the event was processed
					$eventMessage = "Processing api request: " .$request['type'];
        				$sendLog = $logger->logArray('event',$eventMessage,__FILE__);
				        $testVar = $logClient->publish($sendLog);
					break;

		case "find":		$findParams = $request['params']; //get params from queue
					$response['type'] = "find";
					$response['data'] = movieAPIFind::_moviefind($findParams);

					//log that the event was processed
					$eventMessage = "Processing api request: " .$request['type'];
        				$sendLog = $logger->logArray('event',$eventMessage,__FILE__);
				        $testVar = $logClient->publish($sendLog);
					break;

		case "recommend":	$recommendParams = $request['params']; //get params from queue
					$page = $request['page']; //get page number from queue
					$response['type'] = "recommend";
					$response['data'] = movieAPIRecommendations::_movieRecommend($recommendParams);
					
					//log that the event was processed
					$eventMessage = "Processing api request: " .$request['type'];
        				$sendLog = $logger->logArray('event',$eventMessage,__FILE__);
				        $testVar = $logClient->publish($sendLog);
					break;

		case "upcoming":	$page = $request['page']; //get page number from queue
					$response['type'] = "upcoming";
					$response['data'] = movieAPIUpcoming::_movieUpcoming($page);

					//log that the event was processed
					$eventMessage = "Processing api request: " .$request['type'];
        				$sendLog = $logger->logArray('event',$eventMessage,__FILE__);
				        $testVar = $logClient->publish($sendLog);
					break;

		case "current":		$page = $request['page']; //get page number from queue
					$response['type'] = "current";
					$response['data'] = movieAPICurrentReleases::_movieCurrent($page);

					//log that the event was processed
					$eventMessage = "Processing api request: " .$request['type'];
        				$sendLog = $logger->logArray('event',$eventMessage,__FILE__);
				        $testVar = $logClient->publish($sendLog);
					break;

		case "classics":	$page = $request['page']; //get page number from queue
					$response['type'] = "classics";
					$response['data'] = movieAPIClassics::_movieClassics($page);

					//log that the event was processed
					$eventMessage = "Processing api request: " .$request['type'];
        				$sendLog = $logger->logArray('event',$eventMessage,__FILE__);
				        $testVar = $logClient->publish($sendLog);
					break;

		case "showtimes":	$showtimeParams = $request['params']; //get params from queue
					$response['type'] = "showtimes";
					$response['data'] = showtimes::_showtimes($showtimeParams);

					//log that the event was processed
					$eventMessage = "Processing api request: " .$request['type'];
        				$sendLog = $logger->logArray('event',$eventMessage,__FILE__);
				        $testVar = $logClient->publish($sendLog);
					break;

		default:		//log that the request type was invalid
					$eventMessage = "Invalid api request: " .$request['type'];
        				$sendLog = $logger->logArray('error',$eventMessage,__FILE__);
				        $testVar = $logClient->publish($sendLog);
					break;
	}
	//print_r($response);	
	return $response;
}


$server = new rabbitMQServer("../toDMZ.ini","testServer");
$server->process_requests('requestProcessor');
exit();
?>

