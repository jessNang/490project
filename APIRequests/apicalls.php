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

$request = array();


$discoverParams = ["-company", "Disney", "-people", "harrison ford"];
$discover = movieAPIDiscover::_movieDiscover($discoverParams);


$request['type'] = "discover";
$request['data'] = $discover;

$client->send_request($request);


/*
$findParams = ["avatar", "2009"];
$recommendParams = ["godzilla"];

$find = movieAPIFind::_moviefind($findParams);
$recommend = movieAPIRecommendations::_movieRecommend($recommendParams);
$upcoming = movieAPIUpcoming::_movieUpcoming();
$current = movieAPICurrentReleases::_movieCurrent();

print( "find" . PHP_EOL);
print_r($find);
echo PHP_EOL;

print( "recommend" . PHP_EOL);
print_r($recommend);
echo PHP_EOL;

print( "upcoming" . PHP_EOL);
print_r($upcoming);
echo PHP_EOL;

print( "current" . PHP_EOL);
print_r($current);
echo PHP_EOL;
*/

exit();
?>

