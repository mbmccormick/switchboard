<?php
    
    include "../config.php";
    
    $con = mysql_connect($config_server, $config_username, $config_password);
    if (!$con)
    {
        die("Could not connect: " . mysql_error());
    }

    mysql_select_db($config_database, $con);
    
    // lookup the number to see if this is an agent
    $result1 = mysql_query("SELECT * FROM Agents WHERE Id = '1'");
    
    $row = mysql_fetch_array($result1);
    
    if ($row[Id] != null)
    {
        echo "<?xml version='1.0' encoding='UTF-8' ?>\n";
        echo "<Response>\n";
        echo "<Say voice=\"man\" loop=\"1\">Hello, $row[Name]! Welcome to Switchboard, you have reached McCormick Technologies.</Say>\n";        
        if ($row[Status] == '0')
        {
            echo "<Gather action=\"agent.php?id=$row[Id]&amp;status=2;\" method=\"POST\" numDigits=\"1\">\n";
            echo "<Say voice=\"man\" loop=\"1\">Your status is currently set as Available. To change this to Do Not Disturb, press one.</Say>\n";
        }
        else
        {
            echo "<Gather action=\"agent.php?id=$row[Id]&amp;status=0;\" method=\"POST\" numDigits=\"1\">\n";
            echo "<Say voice=\"man\" loop=\"1\">Your status is currently set as Do Not Disturb. To change this to Available, press one.</Say>\n";
        }
        echo "</Gather>\n";
        echo "</Response>\n";
    }
    else
    {
        // select an agent to connect the caller to
        $result2 = mysql_query("SELECT * FROM Agents WHERE Id = '1'");
        
        $row = mysql_fetch_array($result2);
        
        // insert the call into the database
        $sql = "INSERT INTO Calls (AgentId, CallSid, PhoneNumber, Location, Status, CreatedDate) VALUES
                    ('$row[Id]', '$_POST[CallSid]', '$_POST[From]', '$_POST[FromCity], $_POST[FromState] $_POST[FromCountry]', '1', '" . date("Y-m-d H:i:s") . "')";
        if (!mysql_query($sql, $con))
        {
            die('Error: ' . mysql_error());
        }

        // update the agent's status to connected
        $sql = "UPDATE Agents SET Status = '1' WHERE Id = '$row[Id]'";
        if (!mysql_query($sql, $con))
        {
            die('Error: ' . mysql_error());
        }
        
        echo "<?xml version='1.0' encoding='UTF-8' ?>\n";
        echo "<Response>\n";
        echo "<Say voice=\"man\" loop=\"1\">Please wait while you are connected to an agent. This call may be monitored for quality assurance purposes.</Say>\n";
        echo "<Dial callerId=\"877-836-6090\" action=\"complete.php?AgentId=$row[Id]\" record=\"true\">\n";    
        echo "<Number>\n";
        echo "$row[PhoneNumber]\n";
        echo "</Number>\n";
        echo "</Dial>\n";
        echo "</Response>\n";
    }
    
    mysql_close($con);
    
?>