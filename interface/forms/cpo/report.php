<?php

include_once("../../globals.php");
include_once($GLOBALS["srcdir"]."/api.inc");
function cpo_report( $pid, $encounter, $cols, $id) {

    $data = formFetch("tbl_form_cpo", $id);
    if ($data) {
        print "<table><tr>";
        foreach($data as $key => $value) {
            if ($key == "id" || $key == "pid" || $key == "user" || $key == "count" || $key == "authorized" || $key == "activity" || $key == "date" || $value == "" || $value == "0000-00-00 00:00:00") {
                    continue;
            }
            if($key == 'cpo_data'){
                echo "<tr><td>";
//                $cpo_data = unserialize($value); 
                $cpo_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
                    function($match) {
                        return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                    },
                $value );

                $cpo_data = unserialize($cpo_data2);
                $time = 0;
                echo "<table border= '1' style='border-collapse:collapse;'>";
                if(count($cpo_data) == 0 || empty($cpo_data)){
                    echo "<tr><th> Type of Oversight  </th>";
                    echo "<th> Date </th>";
                    echo "<th> Minutes </th>";
                    echo "<th> Description </th>";
                    echo "<th> Reference </th>";
                    echo "<th> Users </th>";
                    echo "<th> Location </th></tr>";
                    echo "<td height='20'></td>";
                    echo "<td height='20'></td>";
                    echo "<td height='20'></td>";
                    echo "<td height='20'></td>";
                    echo "<td height='20'></td>";
                    echo "<td height='20'></td>";
                    echo "<td height='20'></td></tr>";
                }
                for($j=0; $j< count($cpo_data); $j++){
                    foreach ($cpo_data[$j] as $keyheader1 => $valueheader1) {
                        if($keyheader1 == 'cpotype'){
                            $keyheader1 = 'Type of Oversight';
                        }
                        if($keyheader1 == 'start_date'){
                            $keyheader1 = 'Date';
                        }
                        if($keyheader1 == 'timeinterval'){
                            $keyheader1 = 'Minutes';
                        }
//                         echo "<th>". ucwords(str_replace("_"," ",$keyheader1))." </th>";
                        $displayarray[$keyheader1] = ucwords(str_replace("_"," ",$keyheader1));
                    }
                }
                
                foreach($displayarray as $displaykey => $displayvalue){
                    echo "<th>".$displayvalue ." </th>";
                }
                for($i=0; $i< count($cpo_data); $i++){
                    echo "<tr>";
                    foreach ($cpo_data[$i] as $key1 => $value1) {
                        echo "<td>";$title  = '';
                        $key1=ucwords(str_replace("_"," ",$key1));
                        if($key1 != 'Cpotype'  && $key1 != 'Timeinterval' && $key1 != 'Users'):
                            print "<span class=text>".($value1)."</span>&nbsp;";
                        elseif($key1 == 'Cpotype' ):
                            $ures = sqlStatement("SELECT title FROM list_options WHERE option_id= '$value1' and list_id = 'CPO_types'");
                            while ($urow = sqlFetchArray($ures)) {
                                $title = $urow['title'];
                            }
                            if($title == ''):
                                $title = 'None Selected';
                            endif;
                             print "<span class=text>".text($title)."</span>&nbsp;";
                        elseif($key1 == 'Timeinterval'):
                            $ures = sqlStatement("SELECT title FROM list_options WHERE option_id= '$value1' and list_id = 'Time_Interval'");
                            while ($urow = sqlFetchArray($ures)) {
                                $title = $urow['title'];
                            }
                            if($title == ''):
                                $title = 'None Selected';
                            endif;
                            print "<span class=text>".text($title)."</span>&nbsp;";
                            $time = $time + $title;
                        elseif($key1 == 'Users'):
                            $usersres = sqlStatement("SELECT CONCAT(fname,' ', lname) as name FROM users WHERE id = '$value1'");
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
            if($key == 'provider_id'){
                echo "<tr><td>";
                $ures = sqlStatement("SELECT id, fname, lname, specialty FROM users WHERE id= $value");
                 while ($urow = sqlFetchArray($ures)) {
                     echo "<span class=bold>".xlt('Physician'). ": </span><span class=text>".$urow['fname'] . ' ' . $urow['lname']."</span>";
                }   
                echo "</tr></td>";
             }
             if($key == 'signed_date'){
                if($value != '0000-00-00' || $value != ''):
                    echo "<tr><td>";
                     echo "<span class=bold>".xlt('Signed Date'). ": </span><span class=text>".$value."</span>";
                    echo "</tr></td>";
                endif;
             }
//            $key=ucwords(str_replace("_"," ",$key));
//            print "<td><span class=bold>".xlt($key). ": </span><span class=text>".text($value)."</span></td>";
            $count++;
            if ($count == $cols) {
                $count = 0;
                print "</tr><tr>\n";
            }
        }
    }
    print "</tr></table>";
}
function sum_time($totaltime) {
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
