<?php
include 'class.IMDBSearch.php';

$title = $argv[1];
$year = $argv[2];
$imdbPage = IMDBSearch::_movieRedirect($title, $year);

$parts = explode("title/",$imdbPage);
$parts = explode("?",$parts[1]);
$imdbid = $parts[0];

echo "title: " . $title . PHP_EOL;
echo "year: " . $year . PHP_EOL;
echo "imdbid: " . $imdbid . PHP_EOL;

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

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
  return $response;
}

?>
