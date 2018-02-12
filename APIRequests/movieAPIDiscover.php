<?php
$param = array(false,false,false,false,false,false,false,false,false,false);
$paramPos = array(array(),array(),array(),array(),array(),array(),array(),array(),array(),array());
$paramValue = array(array(),array(),array(),array(),array(),array(),array(),array(),array(),array());
$paramName = array("noKeyword","noGenre","year","people","keyword","genre","company","crew","cast","adult");
$final = array();
$finalName = array();

//figures out which of the discover fields are being used
for($i = 1; $i < $argc; $i++)
{
	if(substr($argv[$i],0,1) == "-")
	{
		switch($argv[$i])
		{
			case "-noKeyword": $param[0] = true;array_push($paramPos[0], $i);break;
			case "-noGenre":   $param[1] = true;array_push($paramPos[1], $i);break;
			case "-year":      $param[2] = true;array_push($paramPos[2], $i);break;
			case "-people":    $param[3] = true;array_push($paramPos[3], $i);break;
			case "-keyword":   $param[4] = true;array_push($paramPos[4], $i);break;
			case "-genre":     $param[5] = true;array_push($paramPos[5], $i);break;
			case "-company":   $param[6] = true;array_push($paramPos[6], $i);break;
			case "-crew":      $param[7] = true;array_push($paramPos[7], $i);break;
			case "-cast":      $param[8] = true;array_push($paramPos[8], $i);break;
			case "-adult":     $param[9] = true;array_push($paramPos[9], $i);break;
			default:break;
		}
	}
}
//sorts the parameters for the search into the proper array
for($i = 0; $i < count($param); $i++)
{
	if($param[$i])
	{
		echo "For " . $paramName[$i] . "Adding:".PHP_EOL;
		for($j = 0; $j < count($paramPos[$i]);$j++)
		{
			$offset = 1;
			while(($paramPos[$i][$j] + $offset) < $argc && substr($argv[$paramPos[$i][$j] + $offset],0,1) != "-")
			{
				echo $argv[$paramPos[$i][$j] + $offset].PHP_EOL;
				array_push($paramValue[$i], $argv[$paramPos[$i][$j] + $offset]);
				$offset++;
			}
			if($offset == 1)
			{
				echo "Missing Arguments".PHP_EOL."Now Exiting".PHP_EOL;
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
		array_push($finalName,$paramName[$i]);//$finalName is an array of 
		$finalcount++;
	}
}

print_r($finalName);



/*
$SearchParameters = "without_keywords={$parameters[0]}&without_genres={$parameters[1]}&year={$parameters[2]}&with_people={$parameters[3]}&with_keywords={$parameters[4]}&with_genres={$parameters[5]}&with_companies={$parameters[6]}&with_crew={$parameters[7]}&with_cast={$$parameters[8]}&include_adult={$$parameters[9][0]}"

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.themoviedb.org/3/discover/movie?$SearchParameters&sort_by=popularity.desc&language=en-US&api_key=78d3b2e412d269add2b072f074d49fa3&page=1&include_video=false",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "{}",
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
*/
?>
