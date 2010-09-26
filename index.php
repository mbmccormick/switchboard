<?php

    include "config.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" id="html">
<head>
    <title>Switchboard</title>
    <link rel="stylesheet" href="stylesheet.css" />
</head>
<body>
    <div class="main">
        <table style="width: 100%;" cellspacing="0" cellpadding="0">
            <tr valign="middle">
                <td align="left" style="border: none;">
                    <div class="header">
                        Switchboard
                        <p>Your instant, scalable, open-source call center.</p>
                    </div>
                </td>
                <td align="right" style="border: none;">
                    <div class="navigation">
                        <ul>
                            <li>(877) 836-6090</li>
                            <li><a href="index.php">Dashboard</a></li>
                            <li><a href="agents.php">Agents</a></li>
                            <li><a href="calls.php">Call Log</a></li>
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

                // display dashboard metrics
                echo "<div class='section'>\n";
                echo "<div class='section-header'>\n";
                echo "System Status";
                echo "</div>\n";
                
                $result1 = mysql_query("SELECT * FROM Agents");
                
                $agents = 0;
                $busy = 0;
                $active = 0;
                while($row = mysql_fetch_array($result1))
                {
                    $agents++;
                    if ($row[Status] == "1")
                        $busy++;
                    if ($row[Status] != "2")
                        $active++;
                }
                
                $now = date("Y-m-d") . " 00:00:00";
                $result2 = mysql_query("SELECT * FROM Calls WHERE CreatedDate >= '$now'");
                
                $calls = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0, 16 => 0, 17 => 0, 18 => 0, 19 => 0, 20 => 0, 21 => 0, 22 => 0, 23 => 0);
                while($row = mysql_fetch_array($result2))
                {
                    $hour = date("H", strtotime($row[CreatedDate]));
                    $calls[$hour]++;
                }
                $data = implode(",", $calls);
                
                echo "<table cellpadding='0' cellspacing='0' class='blank'><tr>\n";
                echo "<td>\n";
                echo "<img src='http://chart.apis.google.com/chart?chs=200x130&cht=gm&chco=000000,008000|008000|FFCC33|FF0000&chds=0,$agents&chd=t:$busy&chtt=Current+Capacity' width='200' height='130' alt='Current Capacity' />\n";
                echo "</td>\n";
                echo "<td>\n";
                echo "<img src='http://chart.apis.google.com/chart?chs=200x130&cht=gm&chco=000000,FF0000|FFCC33|008000|008000&chds=0,$agents&chd=t:$active&chtt=Active+Agents' width='200' height='130' alt='Active Agents' />\n";
                echo "</td>\n";
                echo "<td>\n";
                echo "&nbsp;&nbsp;&nbsp;\n";
                echo "</td>\n";
                echo "<td>\n";
                echo "<img style='margin-left: 10px;' src='http://chart.apis.google.com/chart?chxl=1:|12|1|2|3|4|5|6a|7|8|9|10|11|12|1|2|3|4|5|6p|7|8|9|10|11&chxr=0,0,10|1,0,18&chxt=y,x&chbh=a&chs=500x130&cht=bvg&chco=3072F3&chds=0,10&chd=t:$data&chtt=Today%27s+Calls' width='500' height='130' alt='Today%27s Calls' />";
                echo "</td>\n";
                echo "</tr>\n";
                echo "</table>\n";
                
                echo "</div>\n";
                
                // display active calls
                echo "<div class='section'>\n";
                echo "<div class='section-header'>\n";
                echo "Active Calls";
                echo "</div>\n";
                
                $result3 = mysql_query("SELECT Calls.AgentId, Calls.PhoneNumber, Calls.Location, Calls.Status, Calls.CreatedDate, Agents.Id, Agents.Name FROM Calls, Agents WHERE Calls.AgentId = Agents.Id AND (Calls.Status = '0' OR Calls.Status = '1') ORDER BY Calls.CreatedDate ASC");
                
                echo "<table cellpadding='0' cellspacing='0'>\n";
                echo "<tr>\n";
                echo "<th style='width: 150px;'>Caller</th>\n";
                echo "<th style='width: 150px;'>Location</th>\n";
                echo "<th style='width: 200px;'>Date/Time</th>\n";
                echo "<th style='width: 125px;'>Duration</th>\n";
                echo "<th>Status</th>\n";
                echo "</tr>\n";
                
                $count = 0;                
                while($row = mysql_fetch_array($result3))
                {
                    echo "<tr>\n";
                    echo "<td>$row[PhoneNumber]</td>\n";
                    echo "<td>$row[Location]</td>\n";
                    echo "<td>" . date("m/d/Y", strtotime($row[CreatedDate])) . " at " . date("g:i a", strtotime($row[CreatedDate])) . "</td>\n";
                    echo "<td>" . timeDiff(strtotime($row[CreatedDate]), time()) . "</td>\n";
                    if ($row[Status] == "0")
                        echo "<td>Waiting for an agent.</td>\n";
                    elseif ($row[Status] == "1")
                        echo "<td>Connected to $row[Name].</td>\n";   
                    echo "</tr>\n";
                    
                    $count++;
                }
                
                if ($count == 0)
                {
                    echo "<tr><td colspan='5'><i>There are no calls currently in the queue.</i></td></tr>\n";
                }
                echo "</table>\n";  

                echo "</div>\n";
                
                // display active agents
                echo "<div class='section'>\n";
                echo "<div class='section-header'>\n";
                echo "Active Agents";
                echo "</div>\n";
                
                $result4 = mysql_query("SELECT * FROM Agents WHERE Status = '0' ORDER BY Name ASC");
                
                echo "<table cellpadding='0' cellspacing='0'>\n";
                echo "<tr>";
                echo "<th style='width: 150px;'>Name</th>";
                echo "<th style='width: 125px;'>Phone Number</th>";
                echo "<th>Status</th>";
                echo "</tr>\n";
                
                $count = 0;                
                while($row = mysql_fetch_array($result4))
                {
                    echo "<tr>";
                    echo "<td>$row[Name]</td>";
                    echo "<td>$row[PhoneNumber]</td>";
                    if ($row[Status] == "0")
                        echo "<td>Available.</td>";
                    elseif ($row[Status] == "1")
                        echo "<td>Connected to caller.</td>"; 
                    elseif ($row[Status] == "2")
                        echo "<td>Do not disturb.</td>";  
                    echo "</tr>\n";
                    
                    $count++;
                }
                
                if ($count == 0)
                {
                    echo "<tr><td colspan='5'><i>There are no calls currently in the queue.</i></td></tr>\n";
                }
                echo "</table>\n";  

                echo "</div>\n";

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