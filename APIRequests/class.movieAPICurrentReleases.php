<?php
include_once 'class.ConvertForAPI.php';

//for error logging
require_once('../../../git/rabbitmqphp_example/path.inc');
require_once('../../../git/rabbitmqphp_example/get_host_info.inc');
require_once('../../../git/rabbitmqphp_example/rabbitMQLib.inc');
require_once('../logger.inc');

$request = array();

class movieAPICurrentReleases {

	public static function _movieCurrent($pagenum = 1) {
		//initialize the logger
		$logClient = new rabbitMQClient('../toLog.ini', 'testServer');
        	$logger = new Logger();	

		//set page field for request
		$page = "$pagenum";

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.themoviedb.org/3/movie/now_playing?page=$page&language=en-US&api_key=78d3b2e412d269add2b072f074d49fa3",
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
			$parts = explode("},{", $parts[1]);
	
			for($i = 0; $i < count($parts); $i++)
			{
				$parts[$i] = trim($parts[$i], "{}]\\");
				$masterArray[$i] = explode(",\"", $parts[$i]);
		
				for($j = 0; $j < count($masterArray[$i]); $j++)
				{
					$chunk = explode("\":",$masterArray[$i][$j]);
					$chunk[0] = trim($chunk[0], "\"");
					$chunk[1] = trim($chunk[1], "\"");
					$arrayResponse[$i][$chunk[0]] = $chunk[1];
				}
			}
			
			//properly format poster and backdrop paths as well as genre ids
			for($i = 0; $i < count($arrayResponse); $i++)
			{
				$arrayResponse[$i]["poster_path"] = trim($arrayResponse[$i]["poster_path"], "\\");
				$arrayResponse[$i]["backdrop_path"] = trim($arrayResponse[$i]["backdrop_path"], "\\");
		
				$arrayResponse[$i]["genre_ids"] = trim($arrayResponse[$i]["genre_ids"], "[]");
				$arrayResponse[$i]["genre_ids"] = explode(",", $arrayResponse[$i]["genre_ids"]);

				for($j = 0; $j < count($arrayResponse[$i]["genre_ids"]); $j++)
				{
					$arrayResponse[$i]["genre_ids"][$j] = ConvertForApi::_genreConvertToString($arrayResponse[$i]["genre_ids"][$j]);
				}
			}
			return $arrayResponse;
		}
	}
}

?>
