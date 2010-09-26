<?php
    
    include "../config.php";
    
    // update the agent in the database
    $con = mysql_connect($config_server, $config_username, $config_password);
    if (!$con)
    {
        die("Could not connect: " . mysql_error());
    }

    mysql_select_db($config_database, $con);
    
    $sql = "UPDATE Agents SET Status = '$_GET[status]' WHERE Id = '$_GET[id]'";
    if (!mysql_query($sql, $con))
    {
        die('Error: ' . mysql_error());
    }

    echo "<?xml version='1.0' encoding='UTF-8' ?>\n";
    echo "<Response>\n";
    echo "<Say voice='man' loop='1'>Thank you, your status has now been updated. Goodbye!</Say>\n";
	echo "<Hangup/>\n";
    echo "</Response>\n";
    
?>