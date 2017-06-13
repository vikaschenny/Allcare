<?php
include_once("../../globals.php");


$category_arr1= array(
        'patients' =>'linked_patient_folders',
        'users'=> 'linked_user_folders' ,
        'insurance'=>'linked_insurance_folders',
        'pharmacy' =>'linked_pharmacy_folders',
        'address_Book'=> "linked_addrbk_folders" ,
        'facility'=>'linked_facility_folders'
    );
$obj=$_REQUEST['category'];
$field='';
if(in_array($obj,array_flip($category_arr1))){
  $field= $category_arr1[$obj]; 
}
$sel=sqlStatement("select * from tbl_allcare_userfolder_links where user_id=".$_REQUEST['userid']);
$row=sqlFetchArray($sel);
 if($_REQUEST['action']=='save'){
    if(empty($row) && $_REQUEST['status']=='check'){
        $arr1=serialize($_REQUEST['folderid']);
       // echo "insert into tbl_allcare_userfolder_links (user_id,$field) values ('".$_REQUEST['userid']."','".$arr1."')"; 
        $ins=sqlStatement("insert into tbl_allcare_userfolder_links (user_id,$field) values ('".$_REQUEST['userid']."','".$arr1."')");
        //echo $_REQUEST['status']; 
        exit();
    }else if(!empty($row)){
        if(is_array(unserialize($row[$field]))===true)
         $arr=unserialize($row[$field]);
        else 
         $arr[]=unserialize($row[$field]);
       if(!empty($arr)){

            if($_REQUEST['status']=='check') {
                array_push($arr,$_REQUEST['folderid']);
                $arr_sel=serialize($arr);
            }else {
                $key = array_search($_REQUEST['folderid'], $arr);
                unset($arr[$key]);
                $arr_sel=serialize($arr);
                
            }
           // echo "update tbl_allcare_userfolder_links set $field='".$arr_sel."' where user_id=".$_REQUEST['userid'];
           $update=sqlStatement("update tbl_allcare_userfolder_links set $field='".$arr_sel."' where user_id=".$_REQUEST['userid']);
        }else {
            if($_REQUEST['status']=='check') {
                array_push($arr,$_REQUEST['folderid']);
                $arr_sel=serialize($arr);
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