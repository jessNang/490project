#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('logger.inc');

$client = new rabbitMQClient("logging.ini","testServer");
$er = "test";

$logger = new Logger();
$request = $logger->logArray( date('m/d/Y H:i:s a', time()). " ".
gethostname(). " ". "Error occured in ". __FILE__. " LINE ". __LINE__. " Error Code: ".$er.PHP_EOL);

$logger->log($request['error']);

$response = $client->publish($request);
?>
