<?php
function getFacilityName($lname = "%", $given = "pid, id, lname, fname, mname, providerID, DATE_FORMAT(DOB,'%m/%d/%Y') as DOB_TS", $orderby = "name ASC", $limit="all", $start="0")
{
    // Allow the last name to be followed by a comma and some part of a first name.
    // New behavior for searches:
    // Allows comma alone followed by some part of a first name
    // If the first letter of either name is capital, searches for name starting
    // with given substring (the expected behavior).  If it is lower case, it
    // it searches for the substring anywhere in the name.  This applies to either
    // last name or first name or both.  The arbitrary limit of 100 results is set
    // in the sql query below. --Mark Leeds
    $lname = trim($lname);
    $fname = '';
     if (preg_match('/^(.*),(.*)/', $lname, $matches)) {
         $lname = trim($matches[1]);
         $fname = trim($matches[2]);
    }
    $search_for_pieces1 = '';
    $search_for_pieces2 = '';
    if ((strlen($lname) < 1)|| ($lname{0} != strtoupper($lname{0}))) {$search_for_pieces1 = '%';}
    if ((strlen($fname) < 1)|| ($fname{0} != strtoupper($fname{0}))) {$search_for_pieces2 = '%';}

    $sqlBindArray = array();
    $where = "name LIKE ? ";
    array_push($sqlBindArray, $search_for_pieces1.$lname."%", $search_for_pieces2.$fname."%");
    
//        if (!empty($GLOBALS['pt_restrict_field'])) {
//                if ( $_SESSION{"authUser"} != 'admin' || $GLOBALS['pt_restrict_admin'] ) {
//                        $where .= "AND ( patient_data." . add_escape_custom($GLOBALS['pt_restrict_field']) .
//                            " = ( SELECT facility_id FROM users WHERE username = ?) OR patient_data." .
//                            add_escape_custom($GLOBALS['pt_restrict_field']) . " = '' ) ";
//			array_push($sqlBindArray, $_SESSION{"authUser"});
//                }
//        }
//$sql="SELECT $given FROM facility WHERE $where ORDER BY $orderby";
 $sql="SELECT name,id,email,website FROM facility WHERE $where ORDER BY $orderby";
    if ($limit != "all") $sql .= " LIMIT $start, $limit";

    $rez = sqlStatement($sql, $sqlBindArray);

    $returnval=array();
    for($iter=0; $row=sqlFetchArray($rez); $iter++)
        $returnval[$iter] = $row;

//    _set_patient_inc_count($limit, count($returnval), $where, $sqlBindArray);
    return $returnval;
}

function getInsuranceName($lname = "%", $given , $orderby = "name ASC", $limit="all", $start="0")
{
  
    $lname = trim($lname);
//    $fname = '';
    if (preg_match('/^(.*),(.*)/', $lname, $matches)) {
         $lname = trim($matches[1]);
//         $fname = trim($matches[2]);
    }
    $search_for_pieces1 = '';
    $search_for_pieces2 = '';
    if ((strlen($lname) < 1)|| ($lname{0} != strtoupper($lname{0}))) {$search_for_pieces1 = '%';}
//    if ((strlen($fname) < 1)|| ($fname{0} != strtoupper($fname{0}))) {$search_for_pieces2 = '%';}

    $sqlBindArray = array();
    $where = "name LIKE ? ";
    array_push($sqlBindArray, $search_for_pieces1.$lname."%");
    

 $sql="SELECT $given FROM insurance_companies WHERE $where ORDER BY $orderby";
    if ($limit != "all") $sql .= " LIMIT $start, $limit";

    $rez = sqlStatement($sql, $sqlBindArray);

    $returnval=array();
    for($iter=0; $row=sqlFetchArray($rez); $iter++)
        $returnval[$iter] = $row;

//    _set_patient_inc_count($limit, count($returnval), $where, $sqlBindArray);
    return $returnval;
}

function getPharmacyName($lname = "%", $given , $orderby = "name ASC", $limit="all", $start="0")
{
  
    $lname = trim($lname);
    if (preg_match('/^(.*),(.*)/', $lname, $matches)) {
         $lname = trim($matches[1]);
    }
    $search_for_pieces1 = '';
    $search_for_pieces2 = '';
    if ((strlen($lname) < 1)|| ($lname{0} != strtoupper($lname{0}))) {$search_for_pieces1 = '%';}

    $sqlBindArray = array();
    $where = "name LIKE ? ";
    array_push($sqlBindArray, $search_for_pieces1.$lname."%");
    

    $sql="SELECT $given FROM pharmacies WHERE $where ORDER BY $orderby";
    if ($limit != "all") $sql .= " LIMIT $start, $limit";

    $rez = sqlStatement($sql, $sqlBindArray);

    $returnval=array();
    for($iter=0; $row=sqlFetchArray($rez); $iter++)
        $returnval[$iter] = $row;

    return $returnval;
}

function getUserName($searchby,$lname = "%", $given , $orderby = "name ASC", $limit="all", $start="0")
{
  
   // Allow the last name to be followed by a comma and some part of a first name.
    // New behavior for searches:
    // Allows comma alone followed by some part of a first name
    // If the first letter of either name is capital, searches for name starting
    // with given substring (the expected behavior).  If it is lower case, it
    // it searches for the substring anywhere in the name.  This applies to either
    // last name or first name or both.  The arbitrary limit of 100 results is set
    // in the sql query below. --Mark Leeds
    
    if($searchby=='Name'){
        $lname = trim($lname);
        $fname = '';
        if (preg_match('/^(.*),(.*)/', $lname, $matches)) {
             $lname = trim($matches[1]);
             $fname = trim($matches[2]);
        }
        $search_for_pieces1 = '';
        $search_for_pieces2 = '';
        if ((strlen($lname) < 1)|| ($lname{0} != strtoupper($lname{0}))) {$search_for_pieces1 = '%';}
        if ((strlen($fname) < 1)|| ($fname{0} != strtoupper($fname{0}))) {$search_for_pieces2 = '%';}

        $sqlBindArray = array();
        $where = "(lname LIKE ? AND fname LIKE ? ) AND username!=''";
        array_push($sqlBindArray, $search_for_pieces1.$lname."%", $search_for_pieces2.$fname."%");
    }else if($searchby=='UserName'){
         $uname = trim($lname);
         $sqlBindArray = array();
         $where = "username LIKE ? AND username!=''";
         array_push($sqlBindArray, $uname."%");
    }else if($searchby=='Email'){
         $email = trim($lname);
         $sqlBindArray = array();
         $where = "u.email LIKE ?  AND username!=''";
         array_push($sqlBindArray, $email."%");
    }
    
    

     $sql="SELECT $given FROM users "; 
     if($searchby=='Email') { $sql.="u1 INNER JOIN tbl_user_custom_attr_1to1 u on u.userid=u1.id"; }
     $sql.=" WHERE $where ORDER BY $orderby";
    if ($limit != "all") $sql .= " LIMIT $start, $limit";

    $rez = sqlStatement($sql, $sqlBindArray);

    $returnval=array();
    for($iter=0; $row=sqlFetchArray($rez); $iter++)
        $returnval[$iter] = $row;

    return $returnval;
}
function getAgencyName($searchby,$lname = "%", $given , $orderby = "Name ASC", $limit="all", $start="0") {
    // Allow the last name to be followed by a comma and some part of a first name.
    // New behavior for searches:
    // Allows comma alone followed by some part of a first name
    // If the first letter of either name is capital, searches for name starting
    // with given substring (the expected behavior).  If it is lower case, it
    // it searches for the substring anywhere in the name.  This applies to either
    // last name or first name or both.  The arbitrary limit of 100 results is set
    // in the sql query below. --Mark Leeds
    
    if($searchby=='Name'){
        $lname = trim($lname);
        $fname = '';
        if (preg_match('/^(.*),(.*)/', $lname, $matches)) {
             $lname = trim($matches[1]);
             $fname = trim($matches[2]);
        }
        $search_for_pieces1 = '';
        $search_for_pieces2 = '';
        if ((strlen($lname) < 1)|| ($lname{0} != strtoupper($lname{0}))) {$search_for_pieces1 = '%';}
        if ((strlen($fname) < 1)|| ($fname{0} != strtoupper($fname{0}))) {$search_for_pieces2 = '%';}

        $sqlBindArray = array();
        $where = "(lname LIKE ? AND fname LIKE ? ) AND username!='' AND active =1 AND authorized =1";
        array_push($sqlBindArray, $search_for_pieces1.$lname."%", $search_for_pieces2.$fname."%");
    }else if($searchby=='Email'){
         $email = trim($lname);
         $sqlBindArray = array();
         $where = "email LIKE ?  AND username!='' AND active =1 AND authorized =1";
         array_push($sqlBindArray, $email."%");
    }else if($searchby=='Organization'){
         $org = trim($lname);
         $sqlBindArray = array();
         $where = "organization LIKE ?   AND (lname!='' or fname!='')";
         array_push($sqlBindArray, $org."%");
    }else if($searchby=='Address Book Type'){
         $type = trim($lname);
         $sqlBindArray = array();
         $where = "lo.title LIKE ?   AND (lname!='' or fname!='')";
         array_push($sqlBindArray, "%".$type."%");
    }
    
    
if($searchby=='Address Book Type'){
         $sql="SELECT $given
            FROM users AS u
            LEFT JOIN list_options AS lo ON list_id =  'abook_type'
            AND option_id = u.abook_type WHERE $where"; 
}else{
     $sql="SELECT $given
            FROM users "; 
     $sql.=" WHERE $where ";
}

   
    if ($limit != "all") $sql .= " LIMIT $start, $limit";
   
    $rez = sqlStatement($sql, $sqlBindArray);

    $returnval=array();
    for($iter=0; $row=sqlFetchArray($rez); $iter++)
        $returnval[$iter] = $row;

    return $returnval;
}
?>