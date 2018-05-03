<?php
include_once 'class.ConvertForAPI.php';

//for error logging
require_once('../../../git/rabbitmqphp_example/path.inc');
require_once('../../../git/rabbitmqphp_example/get_host_info.inc');
require_once('../../../git/rabbitmqphp_example/rabbitMQLib.inc');
require_once('../logger.inc');

$request = array();

class showtimes {

	public static function _showtimes($parameters)
	{
		//initialize the logger
		$logClient = new rabbitMQClient('../toLog.ini', 'testServer');
        	$logger = new Logger();			

		//set the title and year of movie
		$title = $parameters[0];
		

		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.internationalshowtimes.com/v4/cinemas/?apikey=j4TiQgpVkhJ3R9p3FGIoAjEALYCmjYJI",	//need to finish making the url for the request
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_POSTFIELDS => "{}",
		));

		$jsonResponse = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err)
		{
			echo "cURL Error #:" . $err; 
			/*			
			$error = (date('m/d/Y h:i:s a', time())." ".gethostname()." "." Error occured in ".__FILE__." LINE ".__LINE__." cURL Error #: ".$err.PHP_EOL);
			
			$eventMessage = "ERROR: " . $error;
        		$sendLog = $logger->logArray('error',$eventMessage,__FILE__);
			$testVar = $logClient->publish($sendLog);
			*/
		}
		else
		{
			//format the string response into an array with readable key-value pairs
			print_r ($jsonResponse);
	
			return $arrayResponse;
		}
	}
}

?>
