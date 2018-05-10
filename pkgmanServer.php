#!/usr/bin/php
<?php
require_once('rabbitmqphp/path.inc');
require_once('rabbitmqphp/get_host_info.inc');
require_once('rabbitmqphp/rabbitMQLib.inc');
require_once('rabbitmqphp/logger.inc');

echo "PKGMAN Ready! Send me P A C K A G E S ...".PHP_EOL.PHP_EOL.PHP_EOL;

function doInstall($serverType, $package, $location)
{
	$package = trim($package, "\t\n\r\0\x0B");
	echo('Received: '.$package.' For: '.$location.PHP_EOL);
	
	//copy to backup
	$output = shell_exec("cp /home/myles/SystemArchive/active/$package /home/myles/SystemArchive/backup");
	echo($output.'Successfully coppied to Backup!'.PHP_EOL.PHP_EOL);
	
	//install	
	switch ($location)
	{
		case 'QA':
		sleep(5);
		
		// extract to temp

		$output	= shell_exec("tar -xzvf /home/myles/SystemArchive/active/$package -C /home/myles/SystemArchive/temp");
		print('Successfully extracted!'.PHP_EOL.PHP_EOL.$output.PHP_EOL.PHP_EOL);
					
		
		//get directory.conf
		$pkgFolder = (explode('.', $package))[0];
		
		print('pkgFolder is: '.$pkgFolder);
		$directoryFile = '/home/myles/SystemArchive/temp/home/rocco/updates/'.$pkgFolder.'/directory.conf';
		
		#CHANGE TO ROCCO

		// follow the God
		$fileDirectory = file_get_contents($directoryFile);
		echo($fileDirectory);
		
		$parts = explode(";", $fileDirectory);

		for($i = 0; $i < count($parts); $i++)
       		{
               		$chunk = explode(" ", $parts[$i]);
	                $chunk1 = trim($chunk[1], "\t\n\r\0\x0B");
	                $chunk0 = trim($chunk[0], "\t\n\r\0\x0B");
        	        $filePaths[$chunk1] = $chunk0;
	        }
		print_r($filePaths);

		//do the same for files in package
		$fileNum = shell_exec("find /home/myles/SystemArchive/temp/home/rocco/updates/$pkgFolder -type f | wc -l");
		
		chdir('/home/myles/SystemArchive/temp/home/rocco/updates/'.$pkgFolder);
	
		//$fileNames = shell_exec("find /home/myles/SystemArchive/temp/home/myles/updates/$pkgFolder -type f");

		$fileNames = shell_exec("find -type f");
		$fileNames = explode('./', $fileNames);
		array_shift($fileNames);
		print("Packaged files: ".PHP_EOL);
		print_r($fileNames);


				

		for($i = 0; $i < ($fileNum); $i++)
		{
			$fileNames[$i] = trim($fileNames[$i], "\t\n\r\0\x0B");

		//	echo(gettype($fileNames[$i]));

			//$fileNames[$i]
			if (($fileNames[$i] != "directory.conf") && ($fileNames[$i] != 'updateNum.txt'))
			{
			//check with filePaths array
				if(array_key_exists($fileNames[$i], $filePaths))
				{
					$fileName = $fileNames[$i];
					$filePath = $filePaths[$fileName];

					shell_exec("sshpass -p 'password' scp $fileName rocco@$serverType-qa:$filePath"); 				
				        //echo('TESTTESTTEST'."scp $fileName rocco@$serverType-qa:$filePath");	
#					shell_exec("sshpass -p 'pass' scp $fileName myles@pkgman:$filePath/SCPTEST/");           
					echo('I think it copied?'.PHP_EOL);
				}
			//if matches, install using filePath array path with SCP				
			}
		}
		break;
	
		case 'DEPLOYMENT':
		sleep(5);
		
		// extract to temp

		$output	= shell_exec("tar -xzvf /home/myles/SystemArchive/active/$package -C /home/myles/SystemArchive/temp");
		print('Successfully extracted!'.PHP_EOL.PHP_EOL.$output.PHP_EOL.PHP_EOL);
					
		
		//get directory.conf
		$pkgFolder = (explode('.', $package))[0];
		$directoryFile = "/home/myles/SystemArchive/temp/home/rocco/updates/$pkgFolder/directory.conf";
		
		#CHANGE TO ROCCO

		// follow the God
		$fileDirectory = file_get_contents($directoryFile);
		echo('fileList is: '.$fileDirectory);
		
		$parts = explode(";", $fileDirectory);

		for($i = 0; $i < count($parts); $i++)
       		{
               		$chunk = explode(" ", $parts[$i]);
	                $chunk1 = trim($chunk[1], "\t\n\r\0\x0B");
	                $chunk0 = trim($chunk[0], "\t\n\r\0\x0B");
        	        $filePaths[$chunk1] = $chunk0;
	        }
		print_r($filePaths);

		//do the same for files in package
		$fileNum = shell_exec("find /home/myles/SystemArchive/temp/home/rocco/updates/$pkgFolder -type f | wc -l");
		chdir("/home/myles/SystemArchive/temp/home/rocco/updates/$pkgFolder");
		//$fileNames = shell_exec("find /home/myles/SystemArchive/temp/home/myles/updates/$pkgFolder -type f");
		$fileNames = shell_exec("find -type f");
		$fileNames = explode('./', $fileNames);
		array_shift($fileNames);
		print_r($fileNames);


				

		for($i = 0; $i < ($fileNum); $i++)
		{
			$fileNames[$i] = trim($fileNames[$i], "\t\n\r\0\x0B");

		//	echo(gettype($fileNames[$i]));

			//$fileNames[$i]
			if (($fileNames[$i] != "directory.conf") && ($fileNames[$i] != 'updateNum.txt'))
			{
			//check with filePaths array
				if(array_key_exists($fileNames[$i], $filePaths))
				{
					$fileName = $fileNames[$i];
					$filePath = $filePaths[$fileName];

					shell_exec("sshpass -p 'password' scp $fileName rocco@$serverType-prod:$filePath"); 				
				
					echo("TESTSTRING: sshpass -p 'password' scp $fileName rocco@$serverType-prod:$filePath".PHP_EOL);

#					shell_exec("sshpass -p 'pass' scp $fileName myles@pkgman:$filePath/SCPTEST/");           
					echo('Straight to production!'.PHP_EOL);
				}
			//if matches, install using filePath array path with SCP				
			}
		}
                //clear temp
                shell_exec("rm -rf /home/myles/SystemArchive/temp/*");		
		
		echo('temp Cleared!'.PHP_EOL.PHP_EOL);
		echo($package.' successfully installed to '.$location);
		break;
	}
}

function forceInstall($serverType, $package, $location)
{
	$package = trim($package, "\t\n\r\0\x0B");
	echo('Going to install: '.$package.' into: '.$location.PHP_EOL);
	

	//install	
	switch ($location)
	{
		case 'QA':
		sleep(5);
		
		// extract to temp

		$output	= shell_exec("tar -xzvf /home/myles/SystemArchive/backup/$package -C /home/myles/SystemArchive/temp");
		print('Successfully extracted!'.PHP_EOL.PHP_EOL.$output.PHP_EOL.PHP_EOL);
					
		
		//get directory.conf
		$pkgFolder = (explode('.', $package))[0];
		$directoryFile = '/home/myles/SystemArchive/temp/home/myles/updates/'.$pkgFolder.'/directory.conf';
		
		#CHANGE TO ROCCO

		// follow the God
		$fileDirectory = file_get_contents($directoryFile);
		echo($fileDirectory);
		
		$parts = explode(";", $fileDirectory);

		for($i = 0; $i < count($parts); $i++)
       		{
               		$chunk = explode(" ", $parts[$i]);
	                $chunk1 = trim($chunk[1], "\t\n\r\0\x0B");
	                $chunk0 = trim($chunk[0], "\t\n\r\0\x0B");
        	        $filePaths[$chunk1] = $chunk0;
	        }
		print_r($filePaths);

		//do the same for files in package
		$fileNum = shell_exec("find /home/myles/SystemArchive/temp/home/myles/updates/$pkgFolder -type f | wc -l");
		chdir('/home/myles/SystemArchive/temp/home/myles/updates/'.$pkgFolder);
		//$fileNames = shell_exec("find /home/myles/SystemArchive/temp/home/myles/updates/$pkgFolder -type f");
		$fileNames = shell_exec("find -type f");
		$fileNames = explode('./', $fileNames);
		array_shift($fileNames);
		print("Packaged files: ".PHP_EOL);
		print_r($fileNames);


				

		for($i = 0; $i < ($fileNum); $i++)
		{
			$fileNames[$i] = trim($fileNames[$i], "\t\n\r\0\x0B");

		//	echo(gettype($fileNames[$i]));

			//$fileNames[$i]
			if (($fileNames[$i] != "directory.conf") && ($fileNames[$i] != 'updateNum.txt'))
			{
			//check with filePaths array
				if(array_key_exists($fileNames[$i], $filePaths))
				{
					$fileName = $fileNames[$i];
					$filePath = $filePaths[$fileName];

					$output = shell_exec("sshpass -p 'password' scp $fileName rocco@$serverType-qa:$filePath"); 				
					
#					shell_exec("sshpass -p 'pass' scp $fileName myles@pkgman:$filePath/SCPTEST/");           
					echo('I think it copied?'.PHP_EOL);
				}
			//if matches, install using filePath array path with SCP				
			}
		}
		echo('Installed into QA! Goodbye!'.PHP_EOL);
		break;
	
		case 'DEPLOYMENT':
		sleep(5);
		
		// extract to temp

		$output	= shell_exec("tar -xzvf /home/myles/SystemArchive/backup/$package -C /home/myles/SystemArchive/temp");
		print('Successfully extracted!'.PHP_EOL.PHP_EOL.$output.PHP_EOL.PHP_EOL);
					
		
		//get directory.conf
		$pkgFolder = (explode('.', $package))[0];
		$directoryFile = "/home/myles/SystemArchive/temp/home/rocco/updates/$pkgFolder/directory.conf";
		
		#CHANGE TO ROCCO

		// follow the God
		$fileDirectory = file_get_contents($directoryFile);
		echo($fileDirectory);
		
		$parts = explode(";", $fileDirectory);

		for($i = 0; $i < count($parts); $i++)
       		{
               		$chunk = explode(" ", $parts[$i]);
	                $chunk1 = trim($chunk[1], "\t\n\r\0\x0B");
	                $chunk0 = trim($chunk[0], "\t\n\r\0\x0B");
        	        $filePaths[$chunk1] = $chunk0;
	        }
		print_r($filePaths);

		//do the same for files in package
		$fileNum = shell_exec("find /home/myles/SystemArchive/temp/home/myles/updates/$pkgFolder -type f | wc -l");
		chdir('/home/myles/SystemArchive/temp/home/myles/updates/'.$pkgFolder);
		//$fileNames = shell_exec("find /home/myles/SystemArchive/temp/home/myles/updates/$pkgFolder -type f");
		$fileNames = shell_exec("find -type f");
		$fileNames = explode('./', $fileNames);
		array_shift($fileNames);
		print_r($fileNames);


				

		for($i = 0; $i < ($fileNum); $i++)
		{
			$fileNames[$i] = trim($fileNames[$i], "\t\n\r\0\x0B");

		//	echo(gettype($fileNames[$i]));

			//$fileNames[$i]
			if (($fileNames[$i] != "directory.conf") && ($fileNames[$i] != 'updateNum.txt'))
			{
			//check with filePaths array
				if(array_key_exists($fileNames[$i], $filePaths))
				{
					$fileName = $fileNames[$i];
					$filePath = $filePaths[$fileName];

					shell_exec("sshpass -p 'password' scp $fileName rocco@$serverType-prod:$filePath"); 				
					
#					shell_exec("sshpass -p 'pass' scp $fileName myles@pkgman:$filePath/SCPTEST/");           
					echo('Straight to production!'.PHP_EOL);
				}
			//if matches, install using filePath array path with SCP				
			}
		}
                //clear temp
                shell_exec("rm -rf /home/myles/SystemArchive/temp/*");
		
		echo('temp Cleared!'.PHP_EOL.PHP_EOL);
		echo($package.' successfully installed to '.$location);
		break;
	}
	exit('Operation success, peace!');
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "frontend_package":
      return doInstall('frontend',$request['packageName'], $request['destination']);

    case "rmqdb_package":
      return doInstall('rmqdb',$request['packageName'], $request['destination']);

    case "dmz_package":
      return doInstall('dmz',$request['packageName'], $request['destination']);
	
    case "force_install":
      return forceInstall($request['serverType'], $request['packageName'], $request['destination']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

//Which queue am I accessing?

$server = new rabbitMQServer("/home/myles/Desktop/Phase2/rabbitmqphp/toPM.ini", "testServer");
$server->process_requests('requestProcessor');
exit();
?>
