#!/usr/bin/php

<?php
include 'class.movieAPIFind.php';
include 'class.movieAPIDiscover.php';
include 'class.movieAPIRecommendations.php';
include 'class.movieAPIUpcoming.php';
include 'class.movieAPICurrentReleases.php';
include 'class.movieAPIClassics.php';


$page = 1;
$discoverParams = array("-cast", "Harrison ford"); 
$findParams = array("avatar", 2009);
$recommendParams = array("avatar", 2009);


$discover = movieAPIDiscover::_movieDiscover($discoverParams, $page);
$find = movieAPIFind::_moviefind($findParams);
$recommend = movieAPIRecommendations::_movieRecommend($recommendParams, $page);
$upcoming = movieAPIUpcoming::_movieUpcoming($page);
$current = movieAPICurrentReleases::_movieCurrent($page);
$classics = movieAPIClassics::_movieClassics($page);

echo "Discover movies request with parameters: ";
print_r($discoverParams) . PHP_EOL;
print_r($discover);

echo "Find movie request with parameters: ";
print_r($findParams) . PHP_EOL;
print_r($find);

echo "Recommend movies request with parameters: ";
print_r($recommendParams) . PHP_EOL;
print_r($recommend);

echo "Upcoming movies request." . PHP_EOL;
print_r($upcoming);

echo "Current movies request." . PHP_EOL;
print_r($current);

echo "Classic movies request.". PHP_EOL;
print_r($classics);

?>

