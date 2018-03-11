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
		# example usage:
		# require_once(logger.inc);
		# $logClient = new rabbitMQClient('toLog.ini', 'testServer');
		# $logger = new Logger();
		# $sendLog = $logger->logArray('error',$errorMessage,__FILE__);
		# $logClient->publish($sendLog);

		$request = array();
		if ($type == 'error')
		{
			$request['type'] = $type;
			$request['error'] = $info;
			$request['file'] = $file.' on '.gethostname();
		}
		else 
	 	{
			 $request['type'] = $type;
			 $request['event'] = $info;
			 $request['file'] = $file.' on '.gethostname();

		}
			 return $request;
	}

}
?>
