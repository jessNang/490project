<?php

class Logger
{
	private $eL;
	
	//public function __construct($logfile = "/home/myles/Desktop/rabbitmqphp_example/Logs/errors.log") //temporary path

	public function __construct()
	{
		$logfile = getcwd()."/Logs/error.log";
		$this->eL = fopen($logfile,"a");
	}
	public function log($message)
	{
		fwrite($this->eL, $message.PHP_EOL);
	}
  	public function __destruct()
	{
		fclose($this->eL);
	}

	public function logArray($info)
	{
	 $request = array();
	 $request['type'] = "error";
	 $request['error'] = $info;

	 return $request;
	}

}
?>
