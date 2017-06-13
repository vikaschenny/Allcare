<?php 
 require_once("../../../globals.php");
 $sql=sqlStatement("select * from DriveSync_log where category='patient'   and status LIKE '%patient folder_created(auto Creation)%'");
 while($row=sqlFetchArray($sql)){
     
     $arr1[]=str_replace("https://drive.google.com/drive/folders/","",$row['google_folder']);
 }
//to get deleted files 
 $del=sqlStatement("select * from DriveSync_log where category='patient'   and status LIKE '%deleted from%'");
 while($rowdel=sqlFetchArray($del)){
     
     $del1[]=$rowdel['google_folder'];
 }
 
//  $sql1=sqlStatement("select DISTINCT(patient_id) from DriveSync_log where category='patient' and date='2016-12-22' and status LIKE '%patient folder_created(auto Creation)%'");
// echo $row1=mysql_num_rows($sql1);
   $f=sqlStatement("select patient_folder from patient_data where patient_folder!=''");
   while($r=sqlFetchArray($f)){
       $arr[]=$r['patient_folder'];
   }
//   echo "<pre>"; print_r(array_diff($arr1,$arr)); echo "</pre>";
   $resultc=array_diff($arr1,$arr);
   $result=array_diff($resultc,$del1);
echo count($result);
//echo "<pre>"; print_r($result); echo "</pre>";
//   if(in_array("0B0x_tbqdBDPhX296WEZyS0lXVmc",$result)){
//       echo "sfdfsd";
//   }
//$i=0;
//foreach($result as $key => $val){
// if($i<40){ 
//   $curl = curl_init(); 
//   curl_setopt($curl,CURLOPT_URL, 'https://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/deletefile_web/smartmbbs@ketha.org/emr/'.$_SESSION['authUser'].'/patient/'.$val);
//   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
//   $result = curl_exec($curl);
//   curl_close($curl);
//   $i++;
// }
//}
  
   
   
//   $curl = curl_init(); 
//   curl_setopt($curl,CURLOPT_URL, 'https://' . $_SERVER['HTTP_HOST'] . '/api/DriveSync/listallfilespagetoken_web/smartmbbs@ketha.org/0B0x_tbqdBDPhWERCajZMemV6VEE/folders/empty');
//   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
//   $result12 = curl_exec($curl);
//   curl_close($curl);
//   foreach(json_decode($result12) as $key => $val){
//       foreach($val as $k1 => $v1){
//           if($k1=='id'){
//               $res23[]=$v1;
//           }
//           
//       }
//   }
//   //echo "<pre>"; print_r($res23); echo "</pre>";
//   $result123=array_diff($res23,$arr);
//   echo count($result123);
//    echo "<pre>"; print_r($result123); echo "</pre>";
//    $final=array_diff($result123,$result);
//     echo "<pre>"; print_r($final); echo "</pre>";
?>