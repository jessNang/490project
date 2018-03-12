<?php

//for error logging
require_once('../../../git/rabbitmqphp_example/path.inc');
require_once('../../../git/rabbitmqphp_example/get_host_info.inc');
require_once('../../../git/rabbitmqphp_example/rabbitMQLib.inc');
require_once('../logger.inc');

$request = array();

class ConvertForAPI {
  
	//converts a movie title and year to an IMDB id
	public static function _movieRedirect($movie, $year) {
		$movieName = str_replace(' ', '+', $movie);
 
		$page = @file_get_contents( 'http://www.imdb.com/find?s=all&q='. $movieName. ' ('.$year.')');
		if(@preg_match('~<p style="margin:0 0 0.5em 0;"><b>Media from .*?href="/title\/(.*?)".*?</p>~s', $page, $matches)) {
			$rawData = @file_get_contents( 'http://www.imdb.com/title/'. $matches[1]);     
   		}
		else if(@preg_match('~<td class="result_text">.*?href="/title\/(.*?)".*?</td>~s', $page, $matches)) {
			$rawData = @file_get_contents( 'http://www.imdb.com/title/'. $matches[1]);
		}
		else {
			$rawData = @file_get_contents( 'http://www.imdb.com/find?s=all&q='. $movieName. ' ('.$year.')');
		}
		$parts = explode("title/",$rawData);
		$parts = explode("?",$parts[1]);
		$imdbid = $parts[0];
		return $imdbid;
	}

	//converts a movie IMDB id into a TMDB id
	public static function _movieIMDBtoTMDB($imdbid) {
		
		$logClient = new rabbitMQClient('../toLog.ini', 'testServer');
        	$logger = new Logger();

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
			//echo $jsonResponse;
			$parts = explode("\"id\":", $jsonResponse);
			$parts = explode(",", $parts[1]);
			$tmdbid = $parts[0];
			return $tmdbid;
		}
	}

	//converts an actors name into an IMDB id then a TMDB id
	public static function _actorRedirect($actor) {
		
		$logClient = new rabbitMQClient('../toLog.ini', 'testServer');
        	$logger = new Logger();

		$actorName = str_replace(' ', '+', $actor);
 
	   	$page = @file_get_contents( 'http://www.imdb.com/find?s=all&q='. $actorName);
	   	if(@preg_match('~<p style="margin:0 0 0.5em 0;"><b>Media from .*?href="/name\/(.*?)".*?</p>~s', $page, $matches)) {
	   		$rawData = @file_get_contents( 'http://www.imdb.com/name/'. $matches[1]);     
		}
		else if(@preg_match('~<td class="result_text">.*?href="/name\/(.*?)".*?</td>~s', $page, $matches)) {
			$rawData = @file_get_contents( 'http://www.imdb.com/name/'. $matches[1]);
		}
		else {
			$rawData = @file_get_contents( 'http://www.imdb.com/find?s=all&q='. $actorName);
		}
		$parts = explode("name/",$rawData);
		$parts = explode("?",$parts[1]);
		$imdbid = $parts[0];
		
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
			//echo $jsonResponse;
			$parts = explode("\"id\":", $jsonResponse);
			$parts = explode(",", $parts[1]);
			$tmdbid = $parts[0];
			return $tmdbid;
		}
	
	}
	
	//converts a company's name into a TMDB id
	public static function _companyRedirect($company) {
		
		$logClient = new rabbitMQClient('../toLog.ini', 'testServer');
        	$logger = new Logger();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.themoviedb.org/3/search/company?page=1&query=$company&api_key=78d3b2e412d269add2b072f074d49fa3",
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
			//echo $response;
			//cut out the fluff and get the first id returned
			$parts = explode("\"id\":", $response);
			$parts = explode(",", $parts[1]);
			$companyid = $parts[0];
			return $companyid;
		}
	}
	
	//converts a genre into the genre ID for the TMDB api
	public static function _genreConvertToID($genreString)
	{
		$genreID = array(
			"action" => 28,
			"adventure" => 12,
			"animation" => 16,
			"comedy" => 35,
			"crime" => 80,
			"documentary" => 99,
			"drama" => 18,
			"family" => 10751,
			"fantasy" => 14,
			"history" => 36,
			"horror" => 27,
			"music" => 10402,
			"mystery" => 9648,
			"romance" => 10749,
			"science fiction" => 878,
			"tv movie" => 10770,
			"thriller" => 53,
			"war" => 10752,
			"western" => 37,
		);
		
		return $genreID[$genreString];
	}

	//converts a TMDB genre id into a genre
	public static function _genreConvertToString($genreID)
	{
		$genreString = array(
			28 => "action",
			12 => "adventure",
			16 => "animation",
			35 => "comedy",
			80 => "crime",
			99 => "documentary",
			18 => "drama",
			10751 => "family",
			14 => "fantasy",
			36 => "history",
			27 => "horror",
			10402 => "music",
			9648 => "mystery",
			10749 => "romance",
			878 => "science fiction",
			10770 => "tv movie",
			53 => "thriller",
			10752 => "war",
			37 => "western",
		);
		
		return $genreString[$genreID];
	}

	//convert a search word into a TMDB keyword id
	public static function _getKeywordId($keyword)
	{
		
		$logClient = new rabbitMQClient('../toLog.ini', 'testServer');
        	$logger = new Logger();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.themoviedb.org/3/search/keyword?page=1&query=$keyword&api_key=78d3b2e412d269add2b072f074d49fa3",
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
			//echo $response;
			//cut out the fluff and get the first id returned
			$parts = explode("\"id\":", $response);
			print_r($parts);
			$parts = explode(",", $parts[1]);
			$keywordid = $parts[0];
			return $keywordid;
		}
	}
}
?>
