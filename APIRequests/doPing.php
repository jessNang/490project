<?php

function doPing()
{
	$host = "rmqdb";
	
	global $iniFile;
	$iniFile = "";

	exec("ping -c 4 " . $host, $output, $result);

	if ($result == 0)
		$iniFile = "toDMZ.ini";
	else
		$iniFile = "toDMZ2.ini";

	return $iniFile;
}

?>
