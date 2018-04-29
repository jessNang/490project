#!/usr/bin/php

<?php
/*
require_once('../../../git/rabbitmqphp_example/path.inc');
require_once('../../../git/rabbitmqphp_example/get_host_info.inc');
require_once('../../../git/rabbitmqphp_example/rabbitMQLib.inc');
require_once('../logger.inc');

echo "Waiting to push updates ... ".PHP_EOL;

function requestProcessor($request)
{
	$logClient = new rabbitMQClient('../toLog.ini', 'testServer');
        $logger = new Logger();	

	$response = array();
	//print_r($request);	
	var_dump($request);

	

	return $response;
}


$server = new rabbitMQServer("../push.ini","testServer");
$server->process_requests('requestProcessor');
exit();

*/

while(true)
{
	$fileNames = array();
	$continue = true;
	//gets files to put in new update
	do
	{
		// get input for what file to put into the package
		$file = readline("Enter a file name: ");
		//print (PHP_EOL);
		switch($file)
		{
			case "PUSH":	$continue = false;break;
			default:	array_push($fileNames, $file);break;
		}
	} while($continue == true);
	
	//print_r($fileNames);

	//finds original file locations
	$fileDirectory = file_get_contents("/home/rocco/Desktop/490project/directory.conf");

	//print($fileDirectory . PHP_EOL);

	$parts = explode(";", $fileDirectory);
	for($i = 0; $i < count($parts); $i++)
	{
		$chunk = explode(" ", $parts[$i]);
		$filePaths[$chunk[1]] = $chunk[0];
	}

	//print_r($filePaths);

	//creates the directory for the new update
	$updateNumber = file_get_contents("/home/rocco/Desktop/490project/updateNum.txt");
	$updateNum = (int)$updateNumber;
	$updateNum++;

	//print($updateNum . PHP_EOL);

	file_put_contents("/home/rocco/Desktop/490project/updateNum.txt", $updateNum);
	
	$updateName = "dmz_$updateNum";
	$updateFolder = "/home/rocco/updates/$updateName";
	mkdir($updateFolder);
	
	//copies files to be packaged
	copy("/home/rocco/Desktop/490project/updateNum.txt", $updateFolder . "/updateNum.txt");
	copy("/home/rocco/Desktop/490project/directory.conf", $updateFolder . "/directory.conf");

	for($i = 0; $i < count($fileNames); $i++)
	{
		$fileSource = "." . $filePaths[$fileNames[$i]] . "/" . $fileNames[$i];
		print($fileSource . PHP_EOL);
		$fileDestination = $updateFolder . "/" . $fileNames[$i];
		print($fileDestination . PHP_EOL);
		chdir("/home");
		copy($fileSource, $fileDestination);
		//$output = shell_exec("cp '$fileSource' '$fileDestination'");
	}
	
	//creates the update package
	chdir("/home/rocco/updates");
	$output = shell_exec("tar -zcvf $updateName.tar.gz $updateFolder");

	/*
	//sends the new package
	$output = shell_exec("scp $updateName.tar.gz myles@pkgmanager:/home/myles/System Archive/active");
	// send rabbit message to pkgmanager saying that a new package is available for QA
	*/
	
	//sleep(15);
	//$output = shell_exec("clear");
}


?>
