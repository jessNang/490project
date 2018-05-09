#!/usr/bin/php

<?php

require_once('../../git/rabbitmqphp_example/path.inc');
require_once('../../git/rabbitmqphp_example/get_host_info.inc');
require_once('../../git/rabbitmqphp_example/rabbitMQLib.inc');
require_once('logger.inc');


while(true)
{
#	$serverType = 'PKGMAN';
	$serverType = 'dmz';
#	$serverType = 'frontend';
#	$serverType = 'rmqdb';

	// the version #
	$updateFile = "/home/rocco/Desktop/490project/updateNum.txt";		

	// the directories file
	$directoryFile = "/home/rocco/Desktop/490project/directory.conf";

	$fileNames = array();
	$continue = true;

	// gets files to put in new update
	do
	{
		// get input for what file to put into the package
		$file = readline("Enter a file name: ");
		//print (PHP_EOL);
		
		switch($file)
		{
			case "PUSH":	$continue = false;break;
			case "FORCE_INSTALL":
			  $client = new rabbitMQClient('/home/rocco/Desktop/490project/toPM.ini', 'testServer');
			  $request = array();
			  $request['type'] = 'force_install';
			  $request['serverType'] = readline('What server do you want to install to? (dmz, rmqdb, frontend)');
			  $request['packageName'] = readline('What package do you want to install? (eg dmz_XX.tar.gz)');
			  $request['destination'] = readline('What server tier? (QA, DEPLOYMENT)');
			
			  $client->publish($request);
			  $continue = false;
			  break;
			default:	array_push($fileNames, $file);break;
		}
	} while($continue == true);
	
		//print('input fileNames are: '.PHP_EOL);		
		//print_r($fileNames);

	// finds original file locations
	$fileDirectory = file_get_contents($directoryFile);

	//print($fileDirectory . PHP_EOL);

	$parts = explode(";", $fileDirectory);

	//print("partsArray is: ".PHP_EOL);
	//print_r($parts);

	for($i = 0; $i < count($parts); $i++)
	{
		$chunk = explode(" ", $parts[$i]);
		$chunk1 = trim($chunk[1], "\t\n\r\0\x0B");
		$chunk0 = trim($chunk[0], "\t\n\r\0\x0B");
		$filePaths[$chunk1] = $chunk0;
	}

	//print('filePaths are: ');
	//print_r($filePaths);

	// creates the directory for the new update
	$updateNumber = file_get_contents("$updateFile");
	$updateNum = (int)$updateNumber;
	$updateNum++;

	//print($updateNum . PHP_EOL);

	file_put_contents("$updateFile", $updateNum);
	
	$updateName = $serverType.'_'.$updateNum;
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
	$output = shell_exec("sshpass -p 'pass' scp $updateName.tar.gz myles@pkgman:/home/myles/SystemArchive/active");
	// send rabbit message to pkgmanager saying that a new package is available for QA
		#new rabbitMQClient needs full path for inis not in the local directory
	$client = new rabbitMQClient("/home/rocco/Desktop/490project/toPM.ini","testServer");

	$request = array();
	$request['type'] = $serverType.'_package';
	$request['packageName'] = $updateName.'.tar.gz';
	$request['destination'] = 'QA';

	$client->publish($request);

	print('Package: '.$updateName.'.tar.gz was pushed to PKGMAN'.PHP_EOL);
	print(shell_exec("tar -tvf $updateFolder.tar.gz"));
		
	//sleep(5);
	print(PHP_EOL . PHP_EOL);
	

	$depReady = false;

	// ready for deployment? y/n
	while($depReady != true)
	{	
		$depReady = readline('Is '.$updateName.'.tar.gz ready for DEPLOYMENT? y/n: '.PHP_EOL);
	
		if ($depReady == 'y')
		{
			$client = new rabbitMQClient("/home/rocco/Desktop/490project/toPM.ini","testServer");
			$request = array();
		
			$request['type'] = $serverType.'_package';
			$request['packageName'] = $updateName.'.tar.gz';
			$request['destination'] = 'DEPLOYMENT';

			$client->publish($request);

			$depReady = true;
		}
		else if ($depReady == 'n')
		{
			echo("Ok then, not sent. See you next time!".PHP_EOL);
			$depReady = false;
		}
		else
		{
			$depReady = false;
		}
	}
	echo('Operation completed'.PHP_EOL);
}
?>
