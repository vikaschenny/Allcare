<?php
	require_once (dirname(__FILE__) ."/../sql.inc");
        require_once (dirname(__FILE__) ."/../formdata.inc.php");
	require_once("Patient.class.php");
	require_once("Person.class.php");
	require_once("Provider.class.php");
	require_once("Pharmacy.class.php");

/**
 * class ORDataObject
 *
 */

class ORDataObject {
	var $_prefix;
	var $_table;
	var $_db;

	function ORDataObject() {
	  $this->_db = $GLOBALS['adodb']['db']; 	
	}
	
	function persist() {
		$sql = "REPLACE INTO " . $_prefix . $this->_table . " SET ";
		//echo "<br><br>";
		$fields = sqlListFields($this->_table);
		$db = get_db();
		$pkeys = $db->MetaPrimaryKeys($this->_table);

		foreach ($fields as $field) {
			$func = "get_" . $field;
			//echo "f: $field m: $func status: " .  (is_callable(array($this,$func))? "yes" : "no") . "<br>";
			if (is_callable(array($this,$func))) {
				$val = call_user_func(array($this,$func));

                                //modified 01-2010 by BGM to centralize to formdata.inc.php
			        // have place several debug statements to allow standardized testing over next several months
				if (!is_array($val)) {
				        //DEBUG LINE - error_log("ORDataObject persist before strip: ".$val, 0);
					$val = strip_escape_custom($val);
				        //DEBUG LINE - error_log("ORDataObject persist after strip: ".$val, 0);
				}
			    
				if (in_array($field,$pkeys)  && empty($val)) {
					$last_id = generate_id();
					call_user_func(array(&$this,"set_".$field),$last_id);
					$val = $last_id;
				}

				if (!empty($val)) {
					//echo "s: $field to: $val <br>";
					
                                        //modified 01-2010 by BGM to centralize to formdata.inc.php
			                // have place several debug statements to allow standardized testing over next several months
					$sql .= " `" . $field . "` = '" . add_escape_custom(strval($val)) ."',";
				        //DEBUG LINE - error_log("ORDataObject persist after escape: ".add_escape_custom(strval($val)), 0);
				        //DEBUG LINE - error_log("ORDataObject persist after escape and then stripslashes test: ".stripslashes(add_escape_custom(strval($val))), 0);
				        //DEBUG LINE - error_log("ORDataObject original before the escape and then stripslashes test: ".strval($val), 0);
				}
			}
		}

		if (strrpos($sql,",") == (strlen($sql) -1)) {
				$sql = substr($sql,0,(strlen($sql) -1));
		}

		//echo "<br>$val === sql is: " . $sql . "<br /><br>";
                //echo "<script>alert('sql is: " . $sql . "');</script>";
		sqlQuery($sql);
                
                //insert/update in tbl_allcare_insurance tables
                
        
        if($this->_table=='phone_numbers')
        {
            
/*  insurance attributes begins  */
      if($_POST['hiddensqlGroupRows']>0)
      {
                    
          if($_POST['hiddenarrayGroup']!='')
          {
              $splitGroupArray = @split(",",$_POST['hiddenarrayGroup']);
              
              
                foreach ($splitGroupArray as $spvalue) {
                    $insertValues .= "'".$_POST[$spvalue]."',";
                    $updateValues .= "$spvalue='".$_POST[$spvalue]."',";

                }
                
                $insertValues =substr($insertValues,0,strlen($insertValues)-1);
                $updateValues =substr($updateValues,0,strlen($updateValues)-1);
                
             $groupCheckQuery = sqlStatement("select id from tbl_allcare_insurance1to1 where insurance_company_id=".add_escape_custom($val));
             if(sqlNumRows($groupCheckQuery)>0)
             {
                 
                $groupupdateSql1to1 ="update tbl_allcare_insurance1to1 set $updateValues where insurance_company_id =".add_escape_custom($val);   
                
                sqlStatement($groupupdateSql1to1);                
             }
             else
             {
                 $groupInsertSql1to1 ="insert into tbl_allcare_insurance1to1(pid,insurance_company_id,".$_POST['hiddenarrayGroup'].") 
                     values(0,".add_escape_custom($val).",$insertValues)";   
                
                sqlStatement($groupInsertSql1to1);
             }
              
             
 
              
          }

        }

       sqlStatement("delete from tbl_allcare_insurance1ton where insurance_company_id=".$val);
                  
        foreach ($_POST['hiddenaddcount'] as $key => $value) 
       {
         //  print_r($_POST['hiddenaddcount']);
//               echo "<br>";
           //print_r($_POST[$key]);
           //   echo "<br>$value==".count($_POST[$key]);
           //  echo "<br>";
        // print_r($_POST['hiddenrecid']);echo "<br>";
              $rowsvalues=count($_POST[$key])/$value;
              //$rowsvalues=$_POST['hiddenaddcount'][$key];
           //   echo "<br>".$rowsvalues;
              //print_r($_POST[$key]);
              //echo "<br>";
              $cnt3=0;
              for($cnt1=0;$cnt1<$value;$cnt1++)
                {
                  
                  $insertline='';
                    for($cnt2=0;$cnt2<$rowsvalues;$cnt2++)
                    {
                        
                          $insertline.=  "'".$_POST[$key][$cnt3]."'," ;
                          $cnt3++;
                    }
                    
                  
                      $insertline = substr($insertline,0,strlen($insertline)-1) ;
                  
                      $insertSql = "insert into tbl_allcare_insurance1ton(pid,insurance_company_id,Recordset_ID,".$_POST['hiddenrecid'][$key].") "
                            . "values (0,$val,$key,$insertline)" ;
                       // echo "<br>".$insertSql;
                $result = sqlStatement($insertSql);
                 }               
              
          
       }
     
/*  insurance attributes ends  */
           
            
/*  pharmacy attributes begins  */
       
      if($_POST['hiddensqlGroupRows_pharmacy']>0)
      {
                    
          if($_POST['hiddenarrayGroup_pharmacy']!='')
          {
              $splitGroupArray = @split(",",$_POST['hiddenarrayGroup_pharmacy']);
              
              
                foreach ($splitGroupArray as $spvalue) {
                    $insertValues .= "'".$_POST[$spvalue]."',";
                    $updateValues .= "$spvalue='".$_POST[$spvalue]."',";

                }
                
                $insertValues =substr($insertValues,0,strlen($insertValues)-1);
                $updateValues =substr($updateValues,0,strlen($updateValues)-1);
                
             $groupCheckQuery = sqlStatement("select id from tbl_allcare_pharmacy1to1 where pharmacy_id=".add_escape_custom($val));
             if(sqlNumRows($groupCheckQuery)>0)
             {
                 
                $groupupdateSql1to1 ="update tbl_allcare_pharmacy1to1 set $updateValues where pharmacy_id =".add_escape_custom($val);   
                
                sqlStatement($groupupdateSql1to1);                
             }
             else
             {
                 $groupInsertSql1to1 ="insert into tbl_allcare_pharmacy1to1(pid,pharmacy_id,".$_POST['hiddenarrayGroup_pharmacy'].") 
                     values(0,".add_escape_custom($val).",$insertValues)";   
                
                sqlStatement($groupInsertSql1to1);
             }                                          
          }

        }

       sqlStatement("delete from tbl_allcare_pharmacy1ton where pharmacy_id=".$val);
                  
        foreach ($_POST['hiddenaddcount_pharmacy'] as $key => $value) 
       {
         //  print_r($_POST['hiddenaddcount_pharmacy']);
//               echo "<br>";
           //print_r($_POST[$key]);
           //   echo "<br>$value==".count($_POST[$key]);
           //  echo "<br>";
        // print_r($_POST['hiddenrecid']);echo "<br>";
              $rowsvalues=count($_POST[$key])/$value;
              //$rowsvalues=$_POST['hiddenaddcount_pharmacy'][$key];
           //   echo "<br>".$rowsvalues;
              //print_r($_POST[$key]);
              //echo "<br>";
              $cnt3=0;
              for($cnt1=0;$cnt1<$value;$cnt1++)
                {
                  
                  $insertline='';
                    for($cnt2=0;$cnt2<$rowsvalues;$cnt2++)
                    {
                        
                          $insertline.=  "'".$_POST[$key][$cnt3]."'," ;
                          $cnt3++;
                    }
                    
                  
                      $insertline = substr($insertline,0,strlen($insertline)-1) ;
                  
                      $insertSql = "insert into tbl_allcare_pharmacy1ton(pid,pharmacy_id,Recordset_ID,".$_POST['hiddenrecid'][$key].") "
                            . "values (0,$val,$key,$insertline)" ;
                       // echo "<br>".$insertSql;
                $result = sqlStatement($insertSql);
                 }
              
          
       }
     
/*  pharmacy attributes ends  */         
       
      }
        
             
                
		return true;
	}

	function populate() {
		$sql = "SELECT * from " . $this->_prefix  . $this->_table . " WHERE id = '" . add_escape_custom(strval($this->id))  . "'";
		$results = sqlQuery($sql);
		  if (is_array($results)) {
			foreach ($results as $field_name => $field) {
				$func = "set_" . $field_name;
				//echo "f: $field m: $func status: " .  (is_callable(array($this,$func))? "yes" : "no") . "<br>";
				if (is_callable(array($this,$func))) {

					if (!empty($field)) {
						//echo "s: $field_name to: $field <br>";
						call_user_func(array(&$this,$func),$field);

					}
				}
			}
		}
	}

	function populate_array($results) {
		  if (is_array($results)) {
			foreach ($results as $field_name => $field) {
				$func = "set_" . $field_name;
				//echo "f: $field m: $func status: " .  (is_callable(array($this,$func))? "yes" : "no") . "<br>";
				if (is_callable(array($this,$func))) {

					if (!empty($field)) {
						//echo "s: $field_name to: $field <br>";
						call_user_func(array(&$this,$func),$field);

					}
				}
			}
		}
	}

	/**
	 * Helper function that loads enumerations from the data as an array, this is also efficient
	 * because it uses psuedo-class variables so that it doesnt have to do database work for each instance
	 *
	 * @param string $field_name name of the enumeration in this objects table
	 * @param boolean $blank optional value to include a empty element at position 0, default is true
	 * @return array array of values as name to index pairs found in the db enumeration of this field  
	 */
	function _load_enum($field_name,$blank = true) {
		if (!empty($GLOBALS['static']['enums'][$this->_table][$field_name]) 
			&& is_array($GLOBALS['static']['enums'][$this->_table][$field_name])
			&& !empty($this->_table)) 												{
				
			return $GLOBALS['static']['enums'][$this->_table][$field_name];		
		}
		else {
			$cols = $this->_db->MetaColumns($this->_table);
			if ($cols && !$cols->EOF) {
				//why is there a foreach here? at some point later there will be a scheme to autoload all enums 
				//for an object rather than 1x1 manually as it is now
				foreach($cols as $col) {
	  		      if ($col->name == $field_name && $col->type == "enum") {
                                for($idx=0;$idx<count($col->enums);$idx++)
                                {
                                    $col->enums[$idx]=str_replace("'","",$col->enums[$idx]);
                                }
	  		        $enum = $col->enums;
	  		        //for future use
	  		        //$enum[$col->name] = $enum_types[1];
	  		      }
			    }
			   array_unshift($enum," ");
			   
			   //keep indexing consistent whether or not a blank is present
			   if (!$blank) {
			     unset($enum[0]);
			   }
			   $enum = array_flip($enum);
			  $GLOBALS['static']['enums'][$this->_table][$field_name] = $enum;
			}	
			return $enum;
		}
	}
	
	function _utility_array($obj_ar,$reverse=false,$blank=true, $name_func="get_name", $value_func="get_id") {
		$ar = array();
		if ($blank) {
			$ar[0] = " ";
		}
		if (!is_array($obj_ar)) return $ar;
		foreach($obj_ar as $obj) {
			$ar[$obj->$value_func()] = $obj->$name_func();	
		}
		if ($reverse) {
			$ar = array_flip($ar);	
		}
		return $ar;
	}

} // end of ORDataObject
?>
