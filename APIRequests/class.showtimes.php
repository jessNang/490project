<?php
include_once 'class.ConvertForAPI.php';

//for error logging
require_once('../../../git/rabbitmqphp_example/path.inc');
require_once('../../../git/rabbitmqphp_example/get_host_info.inc');
require_once('../../../git/rabbitmqphp_example/rabbitMQLib.inc');
require_once('../logger.inc');

$request = array();

class showtimes {

	public static function _showtimes($movie, $radius, $lat, $lon)
	{
		//initialize the logger
		$logClient = new rabbitMQClient('../toLog.ini', 'testServer');
        	$logger = new Logger();			

		//set the title and year of movie
		$title = $movie;
		$distance = $radius;

		$imdbID = ConvertForApi::_movieRedirect($title,"");
		//print ("IMDBID: $imdbID" . PHP_EOL);

		$movieID = ConvertForApi::_movieIMDBtoShowtime($imdbID);
		//print ("showtimeID: $movieID" . PHP_EOL);

		$curl = curl_init();

		//echo ("https://api.internationalshowtimes.com/v4/showtimes/?movie_id=$movieID&location=$lat,$lon&distance=$distance&apikey=j4TiQgpVkhJ3R9p3FGIoAjEALYCmjYJI");
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.internationalshowtimes.com/v4/showtimes/?movie_id=$movieID&location=$lat,$lon&distance=$distance&apikey=j4TiQgpVkhJ3R9p3FGIoAjEALYCmjYJI",	
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
			//print_r ($jsonResponse);
			
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
			
			$cinemaIDandName = array();

			for($i = 0; $i < count($arrayResponse); $i++)
			{
				
				if (isset($cinemaIDandName[$arrayResponse[$i]["cinema_id"]]))
				{
					$cinemaName = $cinemaIDandName[$arrayResponse[$i]["cinema_id"]];
				}
				else
				{
					$cinemaName = ConvertForAPI::_showtimeCinemaToString($arrayResponse[$i]["cinema_id"]);
					$cinemaID = $arrayResponse[$i]["cinema_id"];
					$cinemaIDandName[$cinemaID] = $cinemaName;
				}

				$arrayResponse[$i]["cinema_id"] = $cinemaName;
			}
			
			
			//print(PHP_EOL . "id name array" . PHP_EOL);
			//print_r($cinemaIDandName);

			//print(PHP_EOL . "response" . PHP_EOL);
			//print_r($arrayResponse);
			
			return $arrayResponse;
		}
	}
}

?>
