<?php
    
    include "../config.php";
    
    // update the call in the database
    $con = mysql_connect($config_server, $config_username, $config_password);
    if (!$con)
    {
        die("Could not connect: " . mysql_error());
    }

    mysql_select_db($config_database, $con);
    
    $sql = "UPDATE Calls SET Duration = '$_POST[DialCallDuration]', RecordingUrl = '$_POST[RecordingUrl]', Status = '2' WHERE CallSid = '$_POST[CallSid]'";
    if (!mysql_query($sql, $con))
    {
        die('Error: ' . mysql_error());
    }

    // update the agent's status to connected
    $sql = "UPDATE Agents SET Status = '0' WHERE Id = '$_GET[AgentId]'";
    if (!mysql_query($sql, $con))
    {
        die('Error: ' . mysql_error());
    }
    
    echo "<?xml version='1.0' encoding='UTF-8' ?>\n";
    echo "<Response>\n";
	echo "<Hangup/>\n";
    echo "</Response>\n";
    
?>