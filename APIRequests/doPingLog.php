<?php

function doPingLog()
{
	$host = "rmqdb";
	
	global $iniFile;
	$iniFileLog = "";

	exec("ping -c 4 " . $host, $output, $result);

	if ($result == 0)
		$iniFileLog = "toLog.ini";
	else
		$iniFileLog = "toLog2.ini";

	return $iniFileLog;
}

?>
