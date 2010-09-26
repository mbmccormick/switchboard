<?php

    include "config.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" id="html">
<head>
    <title>Switchboard - Agents</title>
    <link rel="stylesheet" href="stylesheet.css" />
</head>
<body>
    <div class="main">
        <table style="width: 100%;" cellspacing="0" cellpadding="0">
            <tr valign="middle">
                <td align="left" style="border: none;">
                    <div class="header">
                        Switchboard - Agents
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
                
                $result = mysql_query("SELECT * FROM Agents ORDER BY Name ASC");
                
                $count = 0;
                while($row = mysql_fetch_array($result))
                {
                    if ($count == 0)
                    {
                        echo "<table cellpadding='0' cellspacing='0'>\n";
                        echo "<tr>";
                        echo "<th style='width: 150px;'>Name</th>";
                        echo "<th style='width: 125px;'>Phone Number</th>";
                        echo "<th>Status</th>";
                        echo "</tr>\n";
                    }
                    
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
                    echo "<i>There are no agents setup in the system.</i>";
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
