<?php
include 'class.ConvertForAPI.php';

//$clientLog = new rabbitMQClient("logging.ini","testServer");
//$logger = new Logger();

$title = $argv[1];
if($argc > 2)
  $year = $argv[2];
else
 $year = '';

$imdbid = ConvertForApi::_movieRedirect($title, $year);

//echo "title: " . $title . PHP_EOL;
//echo "year: " . $year . PHP_EOL;
//echo "imdbid: " . $imdbid . PHP_EOL;

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
	//$requestLog = $logger ->logArray( date('m/d/Y h:i:s a', time())." ".gethostname()." "." Error occured in ".__FILE__." LINE ".__LINE__." Error Code: cURL Error #:" . $err.PHP_EOL);
	//$error = $clientLog->publish($requestLog);
}
else
{
	//echo $jsonResponse;
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

	for($i = 0; $i < count($arrayResponse["genre_ids"]); $i++)
	{
		$arrayResponse["genre_ids"][$i] = ConvertForApi::_genreConvertToString($arrayResponse["genre_ids"][$i]);
	}

	print_r($arrayResponse);
}

?>
