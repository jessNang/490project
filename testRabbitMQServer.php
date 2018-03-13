#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('logger.inc');

echo "Logger ready, awaiting messages ...".PHP_EOL;

function doLogin($arr)
{
	// receieve request

	echo "received doLogin Request ...".PHP_EOL.PHP_EOL;
	echo "array is".PHP_EOL;
	print_r($arr);
	
	// route to DB
	
	$toDB = new rabbitMQClient("toDB.ini", "testServer");
	
	// get response
	$response = array();
	$response = $toDB->send_request($arr);

	echo "sent array to toDB".PHP_EOL;
	
	echo "response is: ";
	
	print_r($response);

	echo "sent login".PHP_EOL;

	// change type so it doesn't loopback
	$response["type"] = "processedLogin";
	
	echo "new response";
	print_r($response);
	
	return $response;
}

function doRegister($uname, $pass, $em)
{
	// route to db

	$toDB = new rabbitMQClient("toDB.ini","testServer");
	
	// send

	$response = $toDB->send_request($request);
	$response = array('type' => 'processedRegister');
	return $response;
}

// Centralized Logging, ma662

function doLog($type,$thing,$file)
{	
	$logger = new Logger();
	$logger->log($type,$thing,$file);
	echo PHP_EOL."Logger logged: ".$thing.PHP_EOL;
}

function toRocco($msg)
{
        $toDMZ = new rabbitMQClient ('toDMZ.ini', "testServer");
	$response = $toDMZ->send_request($request);

	//send back to Jess
	return ($response);	
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
    case "login":
      return doLogin($request);
    case "register":
      return doRegister($request['username'],$request['password'],$request['email']);
    case "validate_session":
      return doValidate($request['sessionId']);

    case "error":
      return doLog('ERROR', $request['error'],$request['file']);
    case "event":
      return doLog('EVENT', $request['event'],$request['file']);
	
    //routing
    case "upcoming":
	return toRocco($request); 
    case "classics":
        return toRocco($request);
    case "discover":
	return toRocco($request);
    case "find":
	return toRocco($request);
    case "current":
	return toRocco($request);
    case "upcoming":
        return toRocco($request);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

//Which queue am I accessing?

$server = new rabbitMQServer("toLog.ini", "testServer");
$server->process_requests('requestProcessor');
exit();
?>
