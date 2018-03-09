<?php
include_once 'class.ConvertForAPI.php';

//for error logging
require_once('../../../git/rabbitmqphp_example/path.inc');
require_once('../../../git/rabbitmqphp_example/get_host_info.inc');
require_once('../../../git/rabbitmqphp_example/rabbitMQLib.inc');

$client = new rabbitMQClient("../toLog.ini","testServer");

$request = array();


class movieAPIDiscover {

	public static function _movieDiscover($parameters, $pagenum = 1) {

		$param = array(false,false,false,false,false,false,false,false,false,false);
		$paramPos = array(array(),array(),array(),array(),array(),array(),array(),array(),array(),array());
		$paramValue = array(array(),array(),array(),array(),array(),array(),array(),array(),array(),array());
		$paramName = array("include_adult","with_cast","with_crew","with_companys","with_genres","with_keyword","with_people", "year","without_genres","without_keywords");

		$final = array();
		$finalName = array();

		//figures out which of the discover fields are being used
		for($i = 1; $i < count($parameters); $i++)
		{
			if(substr($parameters[$i],0,1) == "-")
			{
				switch($parameters[$i])
				{
					case "-adult":		$param[0] = true;array_push($paramPos[0], $i);break;
					case "-cast":		$param[1] = true;array_push($paramPos[1], $i);break;
					case "-crew":		$param[2] = true;array_push($paramPos[2], $i);break;
					case "-company":	$param[3] = true;array_push($paramPos[3], $i);break;
					case "-genre":		$param[4] = true;array_push($paramPos[4], $i);break;
					case "-keyword":	$param[5] = true;array_push($paramPos[5], $i);break;
					case "-people":		$param[6] = true;array_push($paramPos[6], $i);break;
					case "-year":		$param[7] = true;array_push($paramPos[7], $i);break;
					case "-noGenre":	$param[8] = true;array_push($paramPos[8], $i);break;
					case "-noKeyword":	$param[9] = true;array_push($paramPos[9], $i);break;
					default:break;
				}
			}
		}
		//sorts the parameters for the search into the proper array
		for($i = 0; $i < count($param); $i++)
		{
			if($param[$i])
			{
				//echo "For " . $paramName[$i] . "Adding:".PHP_EOL;
				for($j = 0; $j < count($paramPos[$i]);$j++)
				{
					$offset = 1;
					while(($paramPos[$i][$j] + $offset) < count($parameters) && substr($parameters[$paramPos[$i][$j] + $offset],0,1) != "-")
					{
						//echo $argv[$paramPos[$i][$j] + $offset].PHP_EOL;
						array_push($paramValue[$i], $parameters[$paramPos[$i][$j] + $offset]);
						$offset++;
					}
					if($offset == 1)
					{
						echo "Missing Arguments".PHP_EOL."Now Exiting".PHP_EOL;
						$requestLog = $logger ->logArray( date('m/d/Y h:i:s a', time())." ".gethostname()." "." Error occured in ".__FILE__." LINE ".__LINE__." Error Code: Missing Arguments" .PHP_EOL);
						$clientLog->publish($requestLog);
						return;
					}
				}
			}
		}

		//removes the empty indexes from the arrays so only the used discover fields are included
		$finalcount = 0;
		for($i = 0; $i < count($param); $i++)
		{
			if($param[$i])
			{
				array_push($final,array());
				$final[$finalcount] = $paramValue[$i];//$final is an array of the search terms
				array_push($finalName,$paramName[$i]);//$finalName is an array of the search fields that are used
				$finalcount++;
			}
		}

		//generates the string of search parameters
		$searchParameters = '';
		for($i = 0; $i < count($finalName); $i++)
		{
			$searchParameters .= "&$finalName[$i]=";
			for($j = 0; $j < count($final[$i]); $j++)
			{
				//converts the inputs into TMDB ids that the api will recognize
				switch($finalName[$i])
				{
					case "without_keywords":
					case "with_keyword":
						$finalVar = ConvertForAPI::_getKeywordId($final[$i][$j]); break;
					case "without_genres":
					case "with_genres":
						$finalVar = ConvertForAPI::_genreConvertToID($final[$i][$j]); break;
					case "year":
						$finalVar = $final[$i][$j]; break;
					case "with_people":
					case "with_crew":
					case "with_cast":			
						$finalVar = ConvertForAPI::_actorRedirect($final[$i][$j]); break;
					case "with_companys":
						$finalVar = ConvertForAPI::_companyRedirect($final[$i][$j]); break;
					case "include_adult":
						$finalVar = $final[$i][$j]; break;
					default:
						break;
				}
		
				if($j == 0)
					$searchParameters .= $finalVar;
				else
					$searchParameters .= "%2C" . $finalVar;
			}
		}

		//echo $searchParameters . PHP_EOL;

		$page = "page=$pagenum";
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.themoviedb.org/3/discover/movie?sort_by=popularity.desc$searchParameters&api_key=78d3b2e412d269add2b072f074d49fa3&language=en-US&$page&include_video=false",
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
			
			$request['type'] = "error";
			$request['data'] = $error;

			$client->send_request($request);
			return $err;
		}
		else
		{
			//echo $jsonResponse;
			$parts = explode("lts\":[", $jsonResponse);
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
	
			for($i = 0; $i < count($arrayResponse); $i++)
			{
				$arrayResponse[$i]["poster_path"] = trim($arrayResponse[$i]["poster_path"], "\\");
				$arrayResponse[$i]["backdrop_path"] = trim($arrayResponse[$i]["backdrop_path"], "\\");
		
				$arrayResponse[$i]["genre_ids"] = trim($arrayResponse[$i]["genre_ids"], "[]");
				$arrayResponse[$i]["genre_ids"] = explode(",", $arrayResponse[$i]["genre_ids"]);

				for($j = 0; $j < count($arrayResponse[$i]["genre_ids"]); $j++)
				{
					$arrayResponse[$i]["genre_ids"][$j] = ConvertForAPI::_genreConvertToString($arrayResponse[$i]["genre_ids"][$j]);
				}
			}
	
			//print_r($arrayResponse);
			return $arrayResponse;
		}
	}
}

?>
