<?php

function doPing(){
    $host="rmqdb-prod";
    
    global $iniFile;
    $iniFile = "";

    exec("ping -c 4 " . $host, $output, $result);

    if ($result == 0)
        $iniFile = "db.ini";
    else
        $iniFile = "db2.ini";    
    ;
    return $iniFile;
}
    
    
?>

