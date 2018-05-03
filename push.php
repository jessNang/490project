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
	$updateFile = "/home/rocco/Desktop/490project/updateNum.txt";	
	$directoryFile = "/home/rocco/Desktop/490project/directory.conf";
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
	$fileDirectory = file_get_contents($directoryFile);

	//print($fileDirectory . PHP_EOL);

	$parts = explode(";", $fileDirectory);
	for($i = 0; $i < count($parts); $i++)
	{
		$chunk = explode(" ", $parts[$i]);
		$chunk1 = trim($chunk[1], "\t\n\r\0\x0B");
		$chunk0 = trim($chunk[0], "\t\n\r\0\x0B");
		$filePaths[$chunk1] = $chunk0;
	}

	//print_r($filePaths);

	//creates the directory for the new update
	$updateNumber = file_get_contents("$updateFile");
	$updateNum = (int)$updateNumber;
	$updateNum++;

	//print($updateNum . PHP_EOL);

	file_put_contents("$updateFile", $updateNum);
	
	$updateName = "dmz_$updateNum";
	$updateFolder = "/home/rocco/updates/$updateName";
	mkdir($updateFolder);
	
	//copies files to be packaged
	copy($updateFile, $updateFolder . "/updateNum.txt");
	copy($directoryFile, $updateFolder . "/directory.conf");
	
	for($i = 0; $i < count($fileNames); $i++)
	{
		$fileSource = $filePaths[$fileNames[$i]] . "/" . $fileNames[$i];
		//print($fileSource . PHP_EOL);
		$fileDestination = $updateFolder . "/" . $fileNames[$i];
		//print($fileDestination . PHP_EOL);

		$command = "cp $fileSource $fileDestination";
		//echo "shell command:\n$command\n";
		$output = shell_exec($command);
	}
	
	//creates the update package
	chdir("/home/rocco/updates");
	$output = shell_exec("tar -zcvf $updateName.tar.gz $updateFolder");

	
	//sends the new package
	$output = shell_exec("scp $updateName.tar.gz myles@pkgmanager:/home/myles/System Archive/active");
	// send rabbit message to pkgmanager saying that a new package is available for QA
	
	
	sleep(5);
	print(PHP_EOL . PHP_EOL);
}


?>
