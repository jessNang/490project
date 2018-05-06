<?php
//addComment.php
//adds comment into database
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$error = '';
$comment_name = '';
$comment_content = '';

if(empty($_POST["comment_name"])){
	$error .= '<p class="text-danger">Name is required</p>';
} else{
	$comment_name = $_POST["comment_name"];
}

if(empty($_POST["comment_content"])){
	$error .= '<p class="text-danger">Comment is required</p>';
} else{
	$comment_content = $_POST["comment_content"];
}

$comment_id = $_POST["comment_id"];

if($error == ''){
	$client = new rabbitMQClient("db.ini","testServer");
		
	//passing comment info array to be inserted into database
	$request = array();
	$request['type'] = "addComment";
	$request['error'] = $error;
	$request['commentName'] = $comment_name;
	$request['commentContent'] = $comment_content;
	$request['parentCommentId'] = $comment_id;

	$response = $client->send_request($request);

	if($response['valid'] === true){
		echo $response['output'];
	}
}
?>