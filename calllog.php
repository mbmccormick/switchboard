<?php

    include "config.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" id="html">
<head>
    <title>Switchboard - Log</title>
    <link rel="stylesheet" href="stylesheet.css" />
</head>
<body>
    <div class="main">
        <table style="width: 100%;" cellspacing="0" cellpadding="0">
            <tr valign="middle">
                <td align="left" style="border: none;">
                    <div class="header">
                        Switchboard - Call Log
                        <p>Your instant, scalable, open-source call center.</p>
                    </div>
                </td>
                <td align="right" style="border: none;">
                    <div class="navigation">
                        <ul>
                            <li>(877) 836-6090</li>
                            <li><a href="index.php">Dashboard</a></li>
                            <li><a href="agents.php">Agents</a></li>
                            <li><a href="calllog.php">Call Log</a></li>
                            <li><a href="options.php">Options</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        </table>
        <div class="content">
            <?php

                $con = mysql_connect($config_server, $config_username, $config_password);
                if (!$con)
                {
                    die("Could not connect: " . mysql_error());
                }

                mysql_select_db($config_database, $con);
                
                $result = mysql_query("SELECT Calls.AgentId, Calls.PhoneNumber, Calls.Location, Calls.Status, Calls.Duration, Calls.RecordingUrl, Calls.CreatedDate, Agents.Id, Agents.Name FROM Calls, Agents WHERE Calls.AgentId = Agents.Id AND Calls.Status = '2' ORDER BY Calls.CreatedDate DESC");
                
                $count = 0;
                while($row = mysql_fetch_array($result))
                {
                    if ($count == 0)
                    {
                        echo "<table cellpadding='0' cellspacing='0'>\n";
                        echo "<tr>";
                        echo "<th style='width: 125px;'>Caller</th>";
                        echo "<th style='width: 150px;'>Location</th>";
                        echo "<th style='width: 200px;'>Date/Time</th>";
                        echo "<th style='width: 100px;'>Duration</th>";
                        echo "<th style='width: 150px;'>Agent</th>";
                        echo "<th>Actions</th>";
                        echo "</tr>\n";
                    }
                    
                    echo "<tr>";
                    echo "<td>$row[PhoneNumber]</td>";
                    echo "<td>$row[Location]</td>";
                    echo "<td>" . date("m/d/Y", strtotime($row[CreatedDate])) . " at " . date("g:i a", strtotime($row[CreatedDate])) . "</td>";
                    echo "<td>" . sec_to_time($row[Duration]) . "</td>";  
                    echo "<td>$row[Name]</td>";  
                    echo "<td><a href='$row[RecordingUrl]' target='_blank'>Play</a>&nbsp;&nbsp;<a href='$row[RecordingUrl]'>Download</a></td>";  
                    echo "</tr>\n";
                    
                    $count++;
                }
                
                if ($count == 0)
                {
                    echo "<i>There are no calls currently in the queue.</i>";
                }
                else
                {
                    echo "</table>";
                }

                mysql_close($con);
                
            ?>
        </div>
        <div class="footer">
            Copyright &copy; 2010 McCormick Technologies LLC. All rights reserved.
        </div>
    </div>
</body>
</html>

<?php

    function time_to_sec($time)
    {
        $hours = substr($time, 0, -6);
        $minutes = substr($time, -5, 2);
        $seconds = substr($time, -2);
    
        return $hours * 3600 + $minutes * 60 + $seconds;
    }
    
    function sec_to_time($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor($seconds % 3600 / 60);
        $seconds = $seconds % 60;
    
        return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
    }     
    
    function timeDiff($firstTime,$lastTime)
    {
        $firstTime=time_to_sec($firstTime);
        $lastTime=time_to_sec($lastTime);
    
        $timeDiff=$lastTime-$firstTime;
    
        return sec_to_time($timeDiff);
    }

?>