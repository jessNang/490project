<?php

$currentArray = -1;
$totalArrays = 0;

$noKeyword = false;
$noGenre = false;
$year = false;
$people = false;
$keyword = false;
$genre = false;
$company = false;
$crew = false;
$cast = false;
$adult = false;

for($x=1; $x < $argc; $x++)	//itterate through the command line arguments
{
	$argument = $argv[$x];
	
	if(substr($argument, 0, 1) == "-")
	{
		if(substr($argument , 1, 9) == "noKeyword")
		{	
			if($noKeyword == true)
			{
				$currentArray = $noKeywordIndex;
				continue;
			}
			else
			{
				$noKeyword = true;
				if($currentArray == -1)
				{
					$currentArray = $totalArrays;
					$noKeywordIndex = $currentArray;
					$totalArrays++;				
					continue;
				}
				else
				{
					$currentArray = $totalArrays;;
					$noKeywordIndex = $currentArray;
					$totalArrays++;
					continue;
				}
			}
		}
		elseif(substr($argument , 1, 7) == "noGenre")
		{
			if($noGenre == true)
			{
				$currentArray = $noGenre;
				continue;
			}
			else
			{
				$noGenre = true;
				if($currentArray == -1)
				{
					$currentArray = $totalArrays;
					$noGenre = $currentArray;
					$totalArrays++;				
					continue;
				}
				else
				{
					$currentArray = $totalArrays;;
					$noGenreIndex = $currentArray;
					$totalArrays++;
					continue;
				}
			}
		}
		elseif(substr($argument , 1, 4) == "year")
		{
			if($year == true)
			{
				$currentArray = $year;
				continue;
			}
			else
			{
				$year = true;
				if($currentArray == -1)
				{
					$currentArray = $totalArrays;
					$yearIndex = $currentArray;
					$totalArrays++;				
					continue;
				}
				else
				{
					$currentArray = $totalArrays;;
					$yearIndex = $currentArray;
					$totalArrays++;
					continue;
				}
			}
		}
		elseif(substr($argument , 1, 6) == "people")
		{
			if($people == true)
			{
				$currentArray = $people;
				continue;
			}
			else
			{
				$people = true;
				if($currentArray == -1)
				{
					$currentArray = $totalArrays;
					$peopleIndex = $currentArray;
					$totalArrays++;				
					continue;
				}
				else
				{
					$currentArray = $totalArrays;;
					$peopleIndex = $currentArray;
					$totalArrays++;
					continue;
				}
			}
		}
		elseif(substr($argument , 1, 7) == "keyword")
		{
			if($keyword == true)
			{
				$currentArray = $keywordIndex;
				continue;
			}
			else
			{
				$keyword = true;
				if($currentArray == -1)
				{
					$currentArray = $totalArrays;
					$keywordIndex = $currentArray;
					$totalArrays++;				
					continue;
				}
				else
				{
					$currentArray = $totalArrays;;
					$keywordIndex = $currentArray;
					$totalArrays++;
					continue;
				}
			}
		}
		elseif(substr($argument , 1, 5) == "genre")
		{
			if($genre == true)
			{
				$currentArray = $genreIndex;
				continue;
			}
			else
			{
				$genre = true;
				if($currentArray == -1)
				{
					$currentArray = $totalArrays;
					$genreIndex = $currentArray;
					$totalArrays++;				
					continue;
				}
				else
				{
					$currentArray = $totalArrays;;
					$genreIndex = $currentArray;
					$totalArrays++;
					continue;
				}
			}
		}
		elseif(substr($argument , 1, 7) == "company")
		{
			if($company == true)
			{
				$currentArray = $companyIndex;
				continue;
			}
			else
			{
				$company = true;
				if($currentArray == -1)
				{
					$currentArray = $totalArrays;
					$companyIndex = $currentArray;
					$totalArrays++;				
					continue;
				}
				else
				{
					$currentArray = $totalArrays;;
					$companyIndex = $currentArray;
					$totalArrays++;
					continue;
				}
			}
		}
		elseif(substr($argument , 1, 4) == "crew")
		{
			if($crew == true)
			{
				$currentArray = $crewIndex;
				continue;
			}
			else
			{
				$crew = true;
				if($currentArray == -1)
				{
					$currentArray = $totalArrays;
					$crewIndex = $currentArray;
					$totalArrays++;				
					continue;
				}
				else
				{
					$currentArray = $totalArrays;;
					$crewIndex = $currentArray;
					$totalArrays++;
					continue;
				}
			}
		}
		elseif(substr($argument , 1, 4) == "cast")
		{
			if($cast == true)
			{
				$currentArray = $castIndex;
				continue;
			}
			else
			{
				$cast = true;
				if($currentArray == -1)
				{
					$currentArray = $totalArrays;
					$castIndex = $currentArray;
					$totalArrays++;				
					continue;
				}
				else
				{
					$currentArray = $totalArrays;;
					$castIndex = $currentArray;
					$totalArrays++;
					continue;
				}
			}
		}
		elseif(substr($argument , 1, 5) == "adult")
		{	
			if($adult == true)
			{
				$currentArray = $adultIndex;
				continue;
			}
			else
			{
				$adult = true;
				if($currentArray == -1)
				{
					$currentArray = $totalArrays;
					$adultIndex = $currentArray;
					$totalArrays++;				
					continue;
				}
				else
				{
					$currentArray = $totalArrays;;
					$adultIndex = $currentArray;
					$totalArrays++;
					continue;
				}
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
