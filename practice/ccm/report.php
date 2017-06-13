<?php

include_once("../../globals.php");
include_once($GLOBALS["srcdir"]."/api.inc");
function ccm_report( $pid, $encounter, $cols, $id) {

    $dataarray = formFetch("tbl_form_ccm", $id);
    if ($dataarray) {
        print "<table><tr>";
        foreach($dataarray as $a_key => $a_value) {
            if ($a_key == "id" || $a_key == "pid" || $a_key == "user" || $a_key == "count" || $a_key == "authorized" || $a_key == "activity" || $a_key == "date" || $a_value == "" || $a_value == "0000-00-00 00:00:00") {
                    continue;
            }
            if($a_key == 'ccm_data'){
                echo "<tr><td>"; 
                $ccm_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                    function($match) {
                        return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                    },
                $a_value );

                $ccm_data = unserialize($ccm_data2);
                $time = 0;
                echo "<table name= 'ccm' border= '1' style='border-collapse:collapse;'>";
                echo "<tr><th> Type of CCM Interaction </th>";
                echo "<th> Date </th>";
                echo "<th> Minutes </th>";
                echo "<th> Users </th>";
                echo "<th> Location </th>";
                echo "<th> Description </th>";
                echo "<th> Reference </th></tr>";
                for($i=0; $i< count($ccm_data); $i++){
                    echo "<tr>";
                    foreach ($ccm_data[$i] as $a_key2 => $a_value2) {
                        echo "<td>";$title  = '';
                        $a_key2=ucwords(str_replace("_"," ",$a_key2));
                        if($a_key2 != 'Ccmtype'  && $a_key2 != 'Timeinterval' && $a_key2 != 'Users'):
                            print "<span class=text>".($a_value2)."</span>&nbsp;";
                        elseif($a_key2 == 'Ccmtype' ):
                            $ures = sqlStatement("SELECT title FROM list_options WHERE option_id= '$a_value2' and list_id = 'CCM_types'");
                            while ($urow = sqlFetchArray($ures)) {
                                $title = $urow['title'];
                            }
                            if($title == ''):
                                $title = 'None Selected';
                            endif;
                             print "<span class=text>".text($title)."</span>&nbsp;";
                        elseif($a_key2 == 'Timeinterval'):
                            $ures = sqlStatement("SELECT title FROM list_options WHERE option_id= '$a_value2' and list_id = 'Time_Interval'");
                            while ($urow = sqlFetchArray($ures)) {
                                $title = $urow['title'];
                            }
                            if($title == ''):
                                $title = 'None Selected';
                            endif;
                            print "<span class=text>".text($title)."</span>&nbsp;";
                            $time = $time + $title;
                        elseif($a_key2 == 'Users'):
                            $usersres = sqlStatement("SELECT CONCAT(fname,' ', lname) as name FROM users WHERE id = '$a_value2'");
                            while ($urow2 = sqlFetchArray($usersres)) {
                                $title = $urow2['name'];
                            }
                            if($title == ''):
                                $title = '';
                            endif;
                            print "<span class=text>".text($title)."</span>&nbsp;";
                        endif; 
                        echo "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table><span class=bold>".xlt('Total'). ": </span><span class=text>". $time. " minutes</span></td></tr>";
            }
            if($a_key == 'provider_id'){
                echo "<tr><td>";
                $ures = sqlStatement("SELECT id, fname, lname, specialty FROM users WHERE id= $a_value");
                 while ($urow = sqlFetchArray($ures)) {
                     echo "<span class=bold>".xlt('Physician'). ": </span><span class=text>".$urow['fname'] . ' ' . $urow['lname']."</span>";
                }   
                echo "</tr></td>";
             }
             if($a_key == 'signed_date'){
                if($a_value != '0000-00-00' || $a_value != ''):
                    echo "<tr><td>";
                     echo "<span class=bold>".xlt('Signed Date'). ": </span><span class=text>".$a_value."</span>";
                    echo "</tr></td>";
                endif;
             }
//            $a_key=ucwords(str_replace("_"," ",$a_key));
//            print "<td><span class=bold>".xlt($a_key). ": </span><span class=text>".text($a_value)."</span></td>";
            $count++;
            if ($count == $cols) {
                $count = 0;
                print "</tr><tr>\n";
            }
        }
    }
    print "</tr></table>";
}
function sum_time2($totaltime) {
        $i = 0; 
	foreach ($totaltime as $time) {
		sscanf($time, '%d:%d', $hour, $min);
		$i += $hour * 60 + $min;
	}
	if ($h = floor($i / 60)) {
		$i %= 60;
	}
	return sprintf('%02d:%02d', $h, $i);
}
?> 
