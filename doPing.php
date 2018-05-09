<?php

function doPing()
{
	$host = "rmqdb";
	
	global $iniFile;
	$iniFile = "";

	exec("ping -c 4 " . $host, $output, $result);

	if ($result == 0)
		$iniFile = "testRabbitMQ.ini";
	else
		$iniFile = "testRabbitMQ.ini";

	return $iniFile;
}

?>
