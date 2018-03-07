<?php

//ma662

class Logger
{
	private $eL;
	
	
	public function __construct()
	{
		$logfile = getcwd()."/Logs/log";
		$this->eL = fopen($logfile,"a");
	}
	public function log($type,$message,$file)
	{
		#for centralized logging server only

		$time = date('m/d/Y H:i:s', time());
		$format = "-".$type.PHP_EOL.$time."  ".$message." in file ".$file;

		fwrite($this->eL, $format.PHP_EOL.PHP_EOL);
	}
  	public function __destruct()
	{
		fclose($this->eL);
	}

	public function logArray($type,$info,$file)
	{
		#example usage: logArray('error',$errorMessage,__FILE__)

		$request = array();
		if ($type == 'error')
		{
			$request['type'] = $type;
			$request['error'] = $info;
			$request['file'] = $file;
		}
		else 
	 	{
			 $request['type'] = $type;
			 $request['event'] = $info;
			 $request['file'] = $file;

		}
			 return $request;
	}

}
?>
