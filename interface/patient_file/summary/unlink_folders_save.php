<?php
include_once("../../globals.php");


$category_arr1= array(
        'patients' =>'unlinked_patient_folders',
        'users'=> 'unlinked_user_folders' ,
        'insurance'=>'unlinked_insurance_folders',
        'pharmacy' =>'unlinked_pharmacy_folders',
        'address_Book'=> "unlinked_addrbk_folders" ,
        'facility'=>'unlinked_facility_folders'
    );
$obj=$_REQUEST['category'];
$field='';
if(in_array($obj,array_flip($category_arr1))){
  $field= $category_arr1[$obj]; 
}
$sel=sqlStatement("select * from tbl_allcare_userfolder_links where user_id=".$_REQUEST['userid']);
$row=sqlFetchArray($sel);
 if($_REQUEST['action']=='save'){
    if(empty($row) && $_REQUEST['status']=='uncheck'){
        $arr1=serialize($_REQUEST['folderid']);
       // echo "insert into tbl_allcare_userfolder_links (user_id,$field) values ('".$_REQUEST['userid']."','".$arr1."')"; 
        $ins=sqlStatement("insert into tbl_allcare_userfolder_links (user_id,$field) values ('".$_REQUEST['userid']."','".$arr1."')");
        //echo $_REQUEST['status']; 
        exit();
    }else if(!empty($row)){
        if(is_array(unserialize($row[$field]))===true)
         $arr=unserialize($row[$field]);
        else 
         $arr[]=$row[$field];
       if(!empty($arr)){

            if($_REQUEST['status']=='uncheck') {
                 $arr12= array_merge($arr,$_REQUEST['folderid']);
               $arr_sel=serialize($arr12);
            }else {
                foreach($_REQUEST['folderid'] as $key => $val){
                    $key = array_search($val, $arr);
                    //if(in_array($val,$arr)){
                        unset($arr[$key]);
                    //}
                }
                $arr_sel=serialize($arr);
                
            }
           // echo "update tbl_allcare_userfolder_links set $field='".$arr_sel."' where user_id=".$_REQUEST['userid'];
           $update=sqlStatement("update tbl_allcare_userfolder_links set $field='".$arr_sel."' where user_id=".$_REQUEST['userid']);
        }else {
            if($_REQUEST['status']=='uncheck') {
                $arr12=array_merge($arr,$_REQUEST['folderid']);
                $arr_sel=serialize($arr12);
                //echo "update tbl_allcare_userfolder_links set $field='".$arr_sel."' where user_id=".$_REQUEST['userid'];
                $update=sqlStatement("update tbl_allcare_userfolder_links set $field='".$arr_sel."' where user_id=".$_REQUEST['userid']); 
            }
        }
       echo $_REQUEST['status'];exit();
    }

}else {
    if(is_array(unserialize($row[$field]))===true)
         $linked_folder=unserialize($row[$field]);
       else 
          $linked_folder[]=unserialize($row[$field]);
       
    echo json_encode(array_filter($linked_folder));   
}
?>