<?php
include_once 'class.ConvertForAPI.php';

//for error logging
require_once('../../../git/rabbitmqphp_example/path.inc');
require_once('../../../git/rabbitmqphp_example/get_host_info.inc');
require_once('../../../git/rabbitmqphp_example/rabbitMQLib.inc');
require_once('../logger.inc');

$request = array();

class movieAPIFind {

	public static function _movieFind($parameters)
	{
		//initialize the logger
		$logClient = new rabbitMQClient('../toLog.ini', 'testServer');
        	$logger = new Logger();			

		//set the title and year of movie
		$title = $parameters[0];
		if(count($parameters) > 1)
		  $year = $parameters[1];
		else
		 $year = '';

		$imdbid = ConvertForAPI::_movieRedirect($title, $year);	//convert title and year into an IMDB id

		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.themoviedb.org/3/find/$imdbid?external_source=imdb_id&language=en-US&api_key=78d3b2e412d269add2b072f074d49fa3",
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
			$error = (date('m/d/Y h:i:s a', time())." ".gethostname()." "." Error occured in ".__FILE__." LINE ".__LINE__." cURL Error #: ".$err.PHP_EOL);
			
			$eventMessage = "ERROR: " . $error;
        		$sendLog = $logger->logArray('error',$eventMessage,__FILE__);
			$testVar = $logClient->publish($sendLog);
		}
		else
		{
			//format the string response into an array with readable key-value pairs
			$parts = explode("}],", $jsonResponse);
			$parts = explode(":[{", $parts[0]);
			$parts = explode(",\"", $parts[1]);	
	
			for($i = 0; $i < count($parts); $i++)
			{
				$chunk = explode("\":",$parts[$i]);
				$chunk[0] = trim($chunk[0], "\"");
				$chunk[1] = trim($chunk[1], "\"");
				$arrayResponse[$chunk[0]] = $chunk[1];
			}
	
			$arrayResponse["genre_ids"] = trim($arrayResponse["genre_ids"], "[]");
			$arrayResponse["genre_ids"] = explode(",", $arrayResponse["genre_ids"]);

			//properly format genre ids
			for($i = 0; $i < count($arrayResponse["genre_ids"]); $i++)
			{
				$arrayResponse["genre_ids"][$i] = ConvertForAPI::_genreConvertToString($arrayResponse["genre_ids"][$i]);
			}
			return $arrayResponse;
		}
	}
}

?>
