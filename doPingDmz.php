<?php

function doPing(){
    $host="rmqdb-prod";
    
    global $iniFile;
    $iniFile = "";

    exec("ping -c 4 " . $host, $output, $result);

    if ($result == 0)
        $iniFile = "dmz.ini";
    else
        $iniFile = "dmz2.ini";    
    ;
    return $iniFile;
}
    
    
?>

