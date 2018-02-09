<?php

$currentArray = -1;

for($x=1; $x < $argc; $x++)	//itterate through the command line arguments
{
	$argument = $argv[$x];
	
	if(substr($argument, 0, 1) == "-")
	{
		if(substr($argument , 1, 9) == "noKeyword")
		{	
			$noKeyword = true;
			if($currentArray == -1)
			{
				$currentArray = 0;
				$noKeywordIndex = $currentArray;				
				continue;
			}
			else
			{
				$currentArray++;
				$noKeywordIndex = $currentArray;
				continue;
			}
		}
		elseif(substr($argument , 1, 7) == "noGenre")
		{
			$noGenre = true;
			if($currentArray == -1)
			{
				$currentArray = 0;
				$noGenreIndex = $currentArray;
				continue;
			}
			else
			{
				$currentArray++;
				$noGenreIndex = $currentArray;
				continue;
			}
		}
		elseif(substr($argument , 1, 4) == "year")
		{
			$year = true;
			if($currentArray == -1)
			{
				$currentArray = 0;
				$yearIndex = $currentArray;
				continue;
			}
			else
			{
				$currentArray++;
				$yearIndex = $currentArray;
				continue;
			}
		}
		elseif(substr($argument , 1, 6) == "people")
		{
			$people = true;
			if($currentArray == -1)
			{
				$currentArray = 0;
				$peopleIndex = $currentArray;
				continue;
			}
			else
			{
				$currentArray++;
				$peopleIndex = $currentArray;
				continue;
			}
		}
		elseif(substr($argument , 1, 7) == "keyword")
		{
			$keyword = true;
			if($currentArray == -1)
			{
				$currentArray = 0;
				$keywordIndex = $currentArray;
				continue;
			}
			else
			{
				$currentArray++;
				$keywordIndex = $currentArray;
				continue;
			}
		}
		elseif(substr($argument , 1, 5) == "genre")
		{
			$genre = true;
			if($currentArray == -1)
			{
				$currentArray = 0;
				$genreIndex = $currentArray;
				continue;
			}
			else
			{
				$currentArray++;
				$genreIndex = $currentArray;
				continue;
			}
		}
		elseif(substr($argument , 1, 7) == "company")
		{
			$company = true;
			if($currentArray == -1)
			{
				$currentArray = 0;
				$companyIndex = $currentArray;
				continue;
			}
			else
			{
				$currentArray++;
				$companyIndex = $currentArray;
				continue;
			}
		}
		elseif(substr($argument , 1, 4) == "crew")
		{
			$crew = true;
			if($currentArray == -1)
			{
				$currentArray = 0;
				$crewIndex = $currentArray;
				continue;
			}
			else
			{
				$currentArray++;
				$crewIndex = $currentArray;
				continue;
			}
		}
		elseif(substr($argument , 1, 4) == "cast")
		{
			$cast = true;
			if($currentArray == -1)
			{
				$currentArray = 0;
				$castIndex = $currentArray;
				continue;
			}
			else
			{
				$currentArray++;
				$castIndex = $currentArray;
				continue;
			}
		}
		elseif(substr($argument , 1, 5) == "adult")
		{	
			$adult = true;
			if($currentArray == -1)
			{
				$currentArray = 0;
				$adultIndex = $currentArray;
				continue;
			}
			else
			{
				$currentArray++;
				$adultIndex = $currentArray;
				continue;
			}
		}
		else
		{
			echo "invalid argument " . $argument . PHP_EOL;
			continue;
		}
	}

	$parameters[$currentArray][] = $argument;
}

print_r($parameters);
//print_r($parameters[8]);
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
