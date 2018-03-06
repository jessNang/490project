<?php
include 'class.movieAPIFind.php';
include 'class.movieAPIDiscover.php';
include 'class.movieAPIRecommendations.php';
include 'class.movieAPIUpcoming.php';
include 'class.movieAPICurrentReleases.php';

require_once('../../../git/rabbitmqphp_example/path.inc');
require_once('../../../git/rabbitmqphp_example/get_host_info.inc');
require_once('../../../git/rabbitmqphp_example/rabbitMQLib.inc');


$client = new rabbitMQClient("../../../git/rabbitmqphp_example/testRabbitMQ.ini","testServer");

$response = array();

$request=info; //this gets the request from the queue

switch($request['type'])
{

	case "discover":	$discoverParams = $request['params']; //place holder data. get from queue
				$page = $request['page']; //get from queue
				$response['type'] = "discover";
				$response['data'] = movieAPIDiscover::_movieDiscover($discoverParams, $page);
				break;
	case "find":		$findParams = $$request['params']; //place holder data. get from queue
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
	default
}



$client->publish($response);


?>

