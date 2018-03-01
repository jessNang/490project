<?php

class Logger
{
	private $eL;
	
	
	public function __construct()
	{
		$logfile = getcwd()."/Logs/log";
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

	public function logArray($info, $type)
	{
		 $request = array();
		 if ($type == 'error')
		 {
		 	$request['type'] = $type;
			$request['error'] = $info;
		 }
		 else 
	 	 {
			 $request['type'] = $type;
			 $request['event'] = $info;

		 }
			 return $request;
	}

}
?>
