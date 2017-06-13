<?php
session_start();
error_reporting(0);
require_once 'google-api-php-client v3/src/Google/autoload.php';
require_once 'google-api-php-client v3/src/Google/Client.php';
require_once "google-api-php-client v3/src/Google/Service/Oauth2.php";	
require_once "google-api-php-client v3/src/Google/Service/Drive.php";
require '../AesEncryption/GibberishAES.php';
//require '../../modules/PHPMailer/PHPMailerAutoload.php';
//require_once 'Email_reader.php';
//require_once "Fetch/Server.php";

ini_set('post_max_size', '1000M');
ini_set('upload_max_filesize', '200M');
ini_set('max_execution_time', 600); 

$f3 = require('fatfree/lib/base.php');

$f3->set('DB', new DB\SQL(
        'mysql:host=smartmbbsdb.cklyp7uk4jgt.us-west-2.rds.amazonaws.com;port=3306;dbname=allcaretphc', 
        'allcaretphc',
        'Db0Em4DbDfRrP0d'
//    'mysql:host=mariadb-009.wc2.dfw3.stabletransit.com;port=3306;dbname=551948_devcopbx',
//    '551948_devcopbx',
//    '3gEX2EABkPbiAVmN'  
)); 
/* hema */
 
//$f3->set('EMRDB', new DB\SQL(
//    'mysql:host=mariadb-130.wc2.dfw3.stabletransit.com;port=3306;dbname=551948_qa3all', 
//        '551948_qa3all',
//        'M8qXUOLV4'
//));

/* ============== */

$f3->route('GET /brew/@email',
    function($f3) {

        $db=$f3->get('DB');
        $f3->get('PARAMS.email');
        $data=$f3->set('result',$db->exec('select refresh_token from drive_users where email=?',$f3->get('PARAMS.email')));
        echo $data[0]['refresh_token'];
    }
);

/* hema */

$f3->route('GET /getdrivesyncdetails/@userid',
    function($f3) {

        $db=$f3->get('DB');
        $f3->get('PARAMS.userid');
        $data=$f3->set('result',$db->exec('select notes from list_options where list_id="AllcareDriveSync" and option_id = "email"' ));
        
        $data2=$f3->set('resultant',$db->exec('select drive_sync_folder from tbl_user_custom_attr_1to1 where userid= ?',$f3->get('PARAMS.userid') ));
        
        $dataresult['notes'] = $data[0]['notes'];
        $dataresult['drive_sync_folder'] = $data2[0]['drive_sync_folder'];
        $json = json_encode( $dataresult );
        echo $json;
    }
);

$f3->route('GET /createdrivefolder/@userid/@email',
    function($f3) {

        $db=$f3->get('DB');
        $userId = $f3->get('PARAMS.userid');
        $email  = $f3->get('PARAMS.email');

        // create new folder for user
        $emrdb=$f3->get('DB');						
        $folderdata =$f3->set('result',$emrdb->exec('select provider_folder from  tbl_drivesync_authentication where email=?',$email));
        $folderquery = $folderdata[0]['provider_folder'];

        $foldernamequery = $f3->set('result',$emrdb->exec("$folderquery AND id=?",$userId));
        $foldername = $foldernamequery[0]['provider_folder'];
        $newfolderId = createFolder($email,$foldername,$f3); 
        $newfolder = explode(":",$newfolderId);
        $folderId['folderid']  = $newfolder[0];

        // insert in emr db
        $folderdata =$f3->set('result',$emrdb->exec('UPDATE tbl_user_custom_attr_1to1 SET drive_sync_folder = "'.$folderId['folderid'] .'"  where userid=?',$userId));
            
        echo json_encode($folderId);
    }
);

/* ============== */

$f3->route('GET /getAuthURL',
function($f3)
{

 $client = new Google_Client();
 $client->setApplicationName("DriveSync");
 $client->setScopes(array('https://www.googleapis.com/auth/drive'));
 $client->setAuthConfigFile('ggl_conf.json');
 $client->setAccessType('offline');

 $authUrl = $client->createAuthUrl();
 echo $authUrl;
	  
  //} 
}			 
);

$f3->route('GET /generateAccessToken/@useremail',
function ($f3)
{ 
        $useremail=$f3->get('PARAMS.useremail');
        $db=$f3->get('DB');
        $data=$f3->set('result',$db->exec('select refresh_token from drive_users where email=?',$f3->get('PARAMS.useremail')));
	   
		
	$refreshToken=$data[0]['refresh_token'];
	//echo $refreshToken;
        $client = new Google_Client();
        $client->setApplicationName("DriveSync");
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAuthConfigFile('ggl_conf.json');
        $client->setAccessType('offline');
        $client->refreshToken($refreshToken);
        $newtoken=$client->getAccessToken();
	
        $db=$f3->get('DB');
        $data=$f3->set('result',$db->exec("UPDATE drive_users SET accesstoken='".$newtoken."' WHERE email='".$useremail."'"));
	
		

});
$f3->route('GET /createfolder/@useremail/@foldername',
function($f3)
{
  	
        $useremail=$f3->get('PARAMS.useremail');
        $folderName=$f3->get('PARAMS.foldername');
        updateToken($useremail,$f3);
        createFolder($useremail,$folderName,$f3);
  
}		
);

$f3->route('GET /saveCode',
function($f3)
{

    session_start();
    if (isset($_SESSION['access_token']) && $_SESSION['refresh_token']) {
        $code = $_SESSION['access_token'];
	$email= $_SESSION['useremail'];
	$refreshToken=$_SESSION['refresh_token']; 
//			 $redirect_uri ="https://qa2allcare.texashousecalls.com/interface/main/allcarereports/drivesync_config.php?status=1"; 
//                         header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	echo "Authorization accessed ";   
        $t=time();
        updateLog('Oauth Access Provided',$t,$email,'Success',get_client_ip(),'Oauth: '. $email,$f3);
    } else{
	$t=time();
        updateLog('Oauth Access Denied',$t,$email,'Failed',get_client_ip(),'Oauth: '. $email,$f3);
//                          $redirect_uri ="https://qa2allcare.texashousecalls.com/interface/main/allcarereports/drivesync_config.php?status=0"; 
//                         header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	echo "Unable to access your account please use another account";   
   }

});

$f3->route('GET /uploadHTML/@useremail/@filename/@folderid/@encodedURL',
function($f3)
{
    printFile($f3->get('PARAMS.useremail'),$f3->get('PARAMS.encodedURL'),$f3->get('PARAMS.filename'),$f3->get('PARAMS.folderid'),$f3);			
		
});

$f3->route('GET /getfiles/@useremail/@parentid',
function($f3)
{
    getSubtreeForFolder($f3->get('PARAMS.useremail'),$f3->get('PARAMS.parentid'),$f3);			
		
});
/* hema */
$f3->route('GET /renameFile/@fileId/@newTitle/@email',
function($f3)
{
    renameFile($f3->get('PARAMS.fileId'),$f3->get('PARAMS.newTitle'),$f3->get('PARAMS.email'),$f3);			
		
});
function renameFile( $fileId, $newTitle,$email,$framework) {
    $useremail=$email;
 
    //update key
		
    updateToken($useremail,$framework);
 
    $db=$framework->get('DB'); 
    $data=$framework->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));
			
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $driveService = new Google_Service_Drive($client);
    
//    $fileMetadata = new Google_Service_Drive_DriveFile(array(
//            'name' => $originalFile,
//             'parents' => array($folderId)
//        ));
    $file = new Google_Service_Drive_DriveFile();
    $file->setName($newTitle);
		
    $updatedFile = $driveService->files->update($fileId, $file, array(
      'fields' => 'name'
    ));
//    $fileMetadata = new Google_Service_Drive_DriveFile(array(
//		  'name' => $folder,
//		  'mimeType' => 'application/vnd.google-apps.folder'
//    ));
//    $file = $driveService->files->create($fileMetadata, array(
//		  'fields' => 'id'
//    ));
//    printf("%s\n", $file->id.":");

    $t=time();
    
    if($updatedFile->name == $newTitle){
        echo "Updated";
        updateLog('Renamed successfully',$t,$useremail,'Success',get_client_ip(),'Renamed file in drive:'.  $newTitle ,$framework);
    }else{
        echo "Failed";
        updateLog('Rename failed',$t,$useremail,'Failed',get_client_ip(),'Renamed file in drive:'.  $newTitle ,$framework);
    }
//  return $updatedFile;
//  try {
//    $file = new Google_Service_Drive_DriveFile();
//    $file->setTitle($newTitle);
//
//    $updatedFile = $service->files->patch($fileId, $file, array(
//      'fields' => 'title'
//    ));
//
//    return $updatedFile;
//  } catch (Exception $e) {
//    print "An error occurred: " . $e->getMessage();
//  }
}
$f3->route('GET|POST /uploadMobileHTML',
function($f3,$params)
{
    $web = \Web::instance(); 
    //print_r( $params);echo "text"; print_r($_SESSION);
//    print_r($_POST); 
    $email      = $_POST['useremail'];
    $encodedurl = $_POST['encodedURL'];
    $fileName   = $_POST['filename'];
    $folderId   = $_POST['folderid']; 
    $framework  = $f3;
    
//    printMobileFile($params['useremail'],$params['encodedString'],$params['filename'],$params['folderid'],$f3);		
     $tmpFile=uniqid().".pdf";
	
        $url=base64_decode($encodedurl);
//        $url=base64_decode($encodedurl);
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, $url);
//    //return the transfer as a string
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    // $output contains the output string
//    $output = curl_exec($ch);
//    // close curl resource to free up system resources
//     curl_close($ch);  
    include('mpdf/mpdf.php');
    $mpdf=new mPDF('c', 'A4','','' , 2 , 2 , 2 , 2 , 2 , 2,'');
     // $mpdf=new mPDF('win-1252','A4','','',15,10,16,10,10,10);
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->list_indent_first_level = 0; 
    $mpdf->WriteHTML(html_entity_decode($url));
    $mpdf->Output($tmpFile, 'F');
    chmod($tmpFile,0777);
        $web = \Web::instance();

        $overwrite = true; // set to true, to overwrite an existing file; Default: false
        $slug = true; // rename file to filesystem-friendly version
       
//         if($folderId == 0){
//            // create new folder for user
//            $emrdb=$f3->get('DB');						
//            $folderdata =$f3->set('result',$emrdb->exec('select provider_folder from  tbl_drivesync_authentication where email=?',$email));
//            $folderquery = $folderdata[0]['provider_folder'];
//
//            $foldernamequery = $f3->set('result',$emrdb->exec("$folderquery AND id=?",$userId));
//            $foldername = $foldernamequery[0]['provider_folder'];
//            $newfolderId = createFolder($email,$foldername,$f3); 
//            $newfolder = explode(":",$newfolderId);
//            $folderId  = $newfolder[0];
//         }
        
//        $files = $web->receive(
//                function($file,$formFieldName){
//                     var_dump($formFieldName); exit();
//                    // var_dump($file);
////                    $filename=explode("/",$file['name']);
////                    $filename=$filename[1];
////                    $originalFile=$filename;
////                    $_SESSION['originalFile_upload']=$originalFile;
//
//                        /* looks like:
//                          array(5) {
//                              ["name"] =>     string(19) "csshat_quittung.png"
//                              ["type"] =>     string(9) "image/png"
//                              ["tmp_name"] => string(14) "/tmp/php2YS85Q"
//                              ["error"] =>    int(0)
//                              ["size"] =>     int(172245)
//                            }
//                        */
//                        // $file['name'] already contains the slugged name now
//
//                        // maybe you want to check the file size
//            //                if($file['size'] > (2 * 1024 * 1024)) // if bigger than 2 MB
//            //                    return false; // this file is not valid, return false will skip moving it
//
//                        // everything went fine, hurray!
//                    return true; // allows the file to be moved from php tmp dir to your defined upload dir
//                },
//                $overwrite,
//                $slug
//        );

//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        //return the transfer as a string
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        // $output contains the output string
//        $output = curl_exec($ch);
//        // close curl resource to free up system resources
//        curl_close($ch);  
//        include('mpdf/mpdf.php');
//        $mpdf=new mPDF('c','A4','','' , 0 , 0 , 0 , 0 , 0 , 0);
//         // $mpdf=new mPDF('win-1252','A4','','',15,10,16,10,10,10);
//        $mpdf->SetDisplayMode('fullpage');
//        $mpdf->list_indent_first_level = 0; 
//        $mpdf->WriteHTML($url);
//        $mpdf->Output($tmpFile, 'F');
//        chmod($tmpFile,0777);
        //upload to dirve
        updateToken($email,$framework);
        $db=$framework->get('DB');
        $data=$framework->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));
			
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);

        //$folderID=getFolderID($email,$foldername);

        $fileMetadata = new Google_Service_Drive_DriveFile(array(
          'name' => $fileName.'.pdf',
          'parents' => array($folderId)
        ));
//        echo $data = html_entity_decode ($url);
        $data = file_get_contents($tmpFile);
//        echo $data = "<html><body><div id='show_div_cc7294' style='display:block'><div style='clear:both;'> <div id='1Mobile-Chief-Complaint1' style='clear:both;'><ul class='1Mobile-Chief-Complaint1' ><li><b>Chief Complaint File Link:</b>ZSXC</li><li><b>Chief Complaint Status:</b>pending</li><li>aZSXD</li><ul/></div></div></div></body></html>";


        $createdFile = $service->files->create($fileMetadata, array(
                    'data' => $data,
                    'mimeType' => 'application/pdf',
                    'uploadType' => 'multipart',
                    'fields' => 'id'
            ));
        printf("File ID: %s\n", $createdFile->id);
                //print_r($createdFile);
//        unlink($tmpFile);
        $t=time();
        updateLog('Genrated file from html',$t,$email,'Success',get_client_ip(),'Created a new file from html, encoded url'. $encodedurl,$framework);
		
});
$f3->route('GET|POST /uploadMobilePDFHTML',
function($f3,$params)
{
    $web = \Web::instance(); 
    //print_r( $params);echo "text"; print_r($_SESSION);
//    print_r($_POST); 
    $email      = $_POST['useremail'];
    $temp_file_name = $_POST['temp_file_name'];
    $fileName   = $_POST['filename'];
    $encodedurl = "../mobileMedicalRecords/".$temp_file_name.".pdf";
    $folderId   = $_POST['folderid']; 
    $framework  = $f3;

    $overwrite = true; // set to true, to overwrite an existing file; Default: false
    $slug = true; // rename file to filesystem-friendly version

    //upload to dirve
    updateToken($email,$framework);
    $db=$framework->get('DB');
    
    $data=$framework->set('result',$db->exec('select accesstoken from drive_users where email=? ',$email));

    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $service = new Google_Service_Drive($client);

    //$folderID=getFolderID($email,$foldername);

    $fileMetadata = new Google_Service_Drive_DriveFile(array(
      'name' => $fileName.'.pdf',
      'parents' => array($folderId)
    ));
//        echo $data = html_entity_decode ($url);
//        $data = file_get_contents($temp_file_name);

    $data = file_get_contents($encodedurl); 
//        echo $data = "<html><body><div id='show_div_cc7294' style='display:block'><div style='clear:both;'> <div id='1Mobile-Chief-Complaint1' style='clear:both;'><ul class='1Mobile-Chief-Complaint1' ><li><b>Chief Complaint File Link:</b>ZSXC</li><li><b>Chief Complaint Status:</b>pending</li><li>aZSXD</li><ul/></div></div></div></body></html>";


    $createdFile = $service->files->create($fileMetadata, array(
                'data' => $data,
                'mimeType' => 'application/pdf',
                'uploadType' => 'multipart',
                'fields' => 'id'
        ));
    printf("File ID: %s\n", $createdFile->id);
            //print_r($createdFile);
//        unlink($tmpFile);
    $t=time();
    unlink("../mobileMedicalRecords/".$temp_file_name.".pdf");
    updateLog('Genrated file from html',$t,$email,'Success',get_client_ip(),'Created a new file from html, encoded url'. $encodedurl,$framework);
		
});

/* ========== */

$f3->route('GET /oauth/@useremail',
function($f3)
{
        session_start();
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $_SESSION['useremail']=$f3->get('PARAMS.useremail');

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
//            echo $_SESSION['access_token'];
            $client->setAccessToken($_SESSION['access_token']);

            $service = new Google_Service_Drive($client);
            $results = $service->files->listFiles();
            echo json_encode($results);

        } else {
            $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/api/DriveSync/oauth2callback_web.php';
           // $redirect_uri ="https://qa2allcare.texashousecalls.com/interface/main/allcarereports/drivesync_config.php?from=1"; 
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
}		
);
$f3->route('GET|POST|PUT /uploadfile/@email/@folderId/@userid',
    function($f3){
        $f3->set('UPLOADS','uploads/'); // don't forget to set an Upload directory, and make it writable!
        $email=	$f3->get('PARAMS.email');	
        $folderId   = $f3->get('PARAMS.folderId');
        $userId   = $f3->get('PARAMS.userid');
        
        updateToken($email,$f3);
        
        $db=$f3->get('DB');						
        $data=$f3->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));	 
        $web = \Web::instance();

        $overwrite = true; // set to true, to overwrite an existing file; Default: false
        $slug = true; // rename file to filesystem-friendly version
       
//         if($folderId == 0){
//            // create new folder for user
//            $emrdb=$f3->get('DB');						
//            $folderdata =$f3->set('result',$emrdb->exec('select provider_folder from  tbl_drivesync_authentication where email=?',$email));
//            $folderquery = $folderdata[0]['provider_folder'];
//
//            $foldernamequery = $f3->set('result',$emrdb->exec("$folderquery AND id=?",$userId));
//            $foldername = $foldernamequery[0]['provider_folder'];
//            $newfolderId = createFolder($email,$foldername,$f3); 
//            $newfolder = explode(":",$newfolderId);
//            $folderId  = $newfolder[0];
//         }
        
        $files = $web->receive(
                function($file,$formFieldName){
                    // var_dump($formFieldName); 
                    // var_dump($file);
                    $filename=explode("/",$file['name']);
                    $filename=$filename[1];
                    $originalFile=$filename;
                    $_SESSION['originalFile_upload']=$originalFile;

                        /* looks like:
                          array(5) {
                              ["name"] =>     string(19) "csshat_quittung.png"
                              ["type"] =>     string(9) "image/png"
                              ["tmp_name"] => string(14) "/tmp/php2YS85Q"
                              ["error"] =>    int(0)
                              ["size"] =>     int(172245)
                            }
                        */
                        // $file['name'] already contains the slugged name now

                        // maybe you want to check the file size
            //                if($file['size'] > (2 * 1024 * 1024)) // if bigger than 2 MB
            //                    return false; // this file is not valid, return false will skip moving it

                        // everything went fine, hurray!
                    return true; // allows the file to be moved from php tmp dir to your defined upload dir
                },
                $overwrite,
                $slug
        );

       
        
			 
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);

        //$folderID=getFolderID($email,$foldername);

        $originalFile=$_SESSION['originalFile_upload'];

        chmod_r("uploads", 0777, 0777);

        $data1 = file_get_contents("uploads/".$originalFile);
				
        $extension=getFileExtension($originalFile);

        $mime_types= array(
                "xls" =>'application/vnd.ms-excel',
                "xlsx" =>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                "xml" =>'text/xml',
                "ods"=>'application/vnd.oasis.opendocument.spreadsheet',
                "csv"=>'text/plain',
                "tmpl"=>'text/plain',
                "pdf"=> 'application/pdf',
                "php"=>'application/x-httpd-php',
                "jpg"=>'image/jpeg',
                "png"=>'image/png',
                "gif"=>'image/gif',
                "bmp"=>'image/bmp',
                "txt"=>'text/plain',
                "doc"=>'application/msword',
                "js"=>'text/js',
                "swf"=>'application/x-shockwave-flash',
                "mp3"=>'audio/mpeg',
                "zip"=>'application/zip',
                "rar"=>'application/rar',
                "tar"=>'application/tar',
                "arj"=>'application/arj',
                "cab"=>'application/cab',
                "html"=>'text/html',
                "htm"=>'text/html',
                "default"=>'application/octet-stream',
                "folder"=>'application/vnd.google-apps.folder'
        );



        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => $originalFile,
             'parents' => array($folderId)
        ));
        $createdFile = $service->files->create($fileMetadata, array(
                'data' => $data1,
                'mimeType' => $mime_types[$extension],
                'uploadType' => 'multipart',
                'fields' => 'id'
              ));
//        printf("File ID: %s\n", $createdFile->id);
					
	//$files will contain all the files uploaded, in your case 1 hence $files[0];
        $answer = array( 'answer' => 'Files transfer completed', 'fileid' =>$createdFile->id);
        
        $json = json_encode( $answer );
        echo $json;

    }
);

//$f3->route('GET|POST|PUT /uploadfile/@email/@folderId/@userid',
//    function($f3){ 
//        $f3->set('UPLOADS','uploads/'); // don't forget to set an Upload directory, and make it writable!
//        $email      = $f3->get('PARAMS.email');	
//        $folderId   = $f3->get('PARAMS.folderId');
//        
//        updateToken($email,$f3);
//        
//        $db=$f3->get('DB');						
//        $data=$f3->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));	 
//	$web = \Web::instance();
//
//        $overwrite = true; // set to true, to overwrite an existing file; Default: false
//        $slug = true; // rename file to filesystem-friendly version
//        
//        if($folderId == 0){
//            // create new folder for user
//            $emrdb=$f3->get('DB');						
//            $folderdata =$f3->set('result',$emrdb->exec('select provider_folder from  tbl_drivesync_authentication where email=?',$email));
//            echo $folderquery = $folderdata[0]['provider_folder'];
////            $useremail=$f3->get('PARAMS.useremail');
////	    $folderName=$f3->get('PARAMS.foldername');
//            echo $newfolderId = createFolder($email,$foldername,$f3);
//            $explodedString = explode(":",$newfolderId);print_r($explodedString);
//            echo $folderId = $explodedString[0];
//        }
//	
//        $files = $web->receive(function($file,$formFieldName){
//           
//            
//            $filename=explode("/",$file['name']);
//            $filename=$filename[1];
//            $originalFile=$filename;
//            $_SESSION['originalFile_upload']=$originalFile;
//					
//                /* looks like:
//                  array(5) {
//                      ["name"] =>     string(19) "csshat_quittung.png"
//                      ["type"] =>     string(9) "image/png"
//                      ["tmp_name"] => string(14) "/tmp/php2YS85Q"
//                      ["error"] =>    int(0)
//                      ["size"] =>     int(172245)
//                    }
//                */
//                // $file['name'] already contains the slugged name now
//
//                // maybe you want to check the file size
////                if($file['size'] > (2 * 1024 * 1024)) // if bigger than 2 MB
////                    return false; // this file is not valid, return false will skip moving it
//
//                // everything went fine, hurray!
//                return true; // allows the file to be moved from php tmp dir to your defined upload dir
//            },
//            $overwrite,
//            $slug
//        );
//
//       
//        //$files will contain all the files uploaded, in your case 1 hence $files[0];
//        $answer = array( 'answer' => 'Files transfer completed' );
//			 
//        $client = new Google_Client();
//        $client->setAuthConfigFile('ggl_conf.json');
//        $client->addScope(Google_Service_Drive::DRIVE);
//        $client->setAccessType("offline");
//        $client->setAccessToken($data[0]['accesstoken']);
//        $service = new Google_Service_Drive($client);
//
//        //$folderID=getFolderID($email,$foldername);
//
//        $originalFile=$_SESSION['originalFile_upload'];
//
//        chmod_r("uploads", 0777, 0777);
//
//        $data1 = file_get_contents("uploads/".$originalFile);
//
//        $extension=getFileExtension($originalFile);
//
//        $mime_types= array(
//                "xls" =>'application/vnd.ms-excel',
//                "xlsx" =>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
//                "xml" =>'text/xml',
//                "ods"=>'application/vnd.oasis.opendocument.spreadsheet',
//                "csv"=>'text/plain',
//                "tmpl"=>'text/plain',
//                "pdf"=> 'application/pdf',
//                "php"=>'application/x-httpd-php',
//                "jpg"=>'image/jpeg',
//                "png"=>'image/png',
//                "gif"=>'image/gif',
//                "bmp"=>'image/bmp',
//                "txt"=>'text/plain',
//                "doc"=>'application/msword',
//                "js"=>'text/js',
//                "swf"=>'application/x-shockwave-flash',
//                "mp3"=>'audio/mpeg',
//                "zip"=>'application/zip',
//                "rar"=>'application/rar',
//                "tar"=>'application/tar',
//                "arj"=>'application/arj',
//                "cab"=>'application/cab',
//                "html"=>'text/html',
//                "htm"=>'text/html',
//                "default"=>'application/octet-stream',
//                "folder"=>'application/vnd.google-apps.folder'
//        );
//
//
//
//        $fileMetadata = new Google_Service_Drive_DriveFile(array(
//          'name' => $originalFile,
//           'parents' => array($folderId)
//        ));
//        $createdFile = $service->files->create($fileMetadata, array(
//                  'data' => $data1,
//                  'mimeType' => $mime_types[$extension],
//                  'uploadType' => 'multipart',
//                  'fields' => 'id',
//        ));
//        printf("File ID: %s\n", $createdFile->id);
//					
//					
//        $json = json_encode( $answer );
//        echo $json;
//
//    }
//);


$f3->route('GET /storeEmailAttachment/@email',
function($f3)
{
	storeEmail($f3->get('PARAMS.email'),$f3);
});

$f3->route('GET /listall/@email',
function($f3)
{
	listAllFolders($f3->get('PARAMS.email'),$f3);
});
/* hema */
$f3->route('GET /listallfiles/@email/@folderid/@filetype',
function($f3)
{
	listAllFilesInFolder($f3->get('PARAMS.email'),$f3->get('PARAMS.folderid'),$f3->get('PARAMS.filetype'),$f3);
});

/* --------------------- */
$f3->route('GET /movefile/@useremail/@fileid/@tofolderid',
function($f3)
{
	movefile($f3->get('PARAMS.useremail'),$f3->get('PARAMS.fileid'),$f3->get('PARAMS.tofolderid'),$f3);
});

$f3->route('GET /sharefile/@fromuseremail/@fileid/@touseremail/@permission',
function($f3)
{
	sharefile($f3->get('PARAMS.fromuseremail'),$f3->get('PARAMS.fileid'),$f3->get('PARAMS.touseremail'),$f3->get('PARAMS.permission'),$f3);
});
$f3->route('GET /sharefilePermission/@fromuseremail/@fileid',
function($f3)
{
	sharefileAnyone($f3->get('PARAMS.fromuseremail'),$f3->get('PARAMS.fileid'),$f3);
});
$f3->route('GET /getfileinfo/@useremail/@fileid',
function($f3)
{
	$email=$f3->get('PARAMS.useremail');
	$fileId=$f3->get('PARAMS.fileid');
	updateToken($email,$f3);
	
	 $db=$f3->get('DB');
	  
	 $data=$f3->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));
			
		    $client = new Google_Client();
			$client->setAuthConfigFile('ggl_conf.json');
			$client->addScope(Google_Service_Drive::DRIVE);
			$client->setAccessType("offline");
			$client->setAccessToken($data[0]['accesstoken']);
			$service = new Google_Service_Drive($client);
                        try {
                            $file = $service->files->get($fileId, array('fields' => 'appProperties,capabilities,contentHints,createdTime,description,explicitlyTrashed,fileExtension,folderColorRgb,fullFileExtension,headRevisionId,iconLink,id,imageMediaMetadata,kind,lastModifyingUser,md5Checksum,mimeType,modifiedByMeTime,modifiedTime,name,originalFilename,ownedByMe,owners,parents,permissions,properties,quotaBytesUsed,shared,sharedWithMeTime,sharingUser,size,spaces,starred,thumbnailLink,trashed,version,videoMediaMetadata,viewedByMe,viewedByMeTime,viewersCanCopyContent,webContentLink,webViewLink,writersCanShare'));
                            echo json_encode($file);
                        } catch (Exception $e) {
                            if ($e->getCode() == 404) {
                                // Apply exponential backoff.
                               echo '404 ERROR:';

                             } else {
                    //                    // Other error, re-throw. 
                    //                    throw;
                             }
                        }
			
	
                    $t=time();
                    updateLog('File info captured',$t,$email,'Success',get_client_ip(),'File info captured: '.  $filename.'<---'.$useremail,$f3);
});

$f3->route('GET /deletefile/@useremail/@fileid',
function($f3)
{
	$email=$f3->get('PARAMS.useremail');
	$fileId=$f3->get('PARAMS.fileid');
	updateToken($email,$f3);
	
	 $db=$f3->get('DB');
	  
	         $data=$f3->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));
			 
	      
			
		    $client = new Google_Client();
			$client->setAuthConfigFile('ggl_conf.json');
			$client->addScope(Google_Service_Drive::DRIVE);
			$client->setAccessType("offline");
			$client->setAccessToken($data[0]['accesstoken']);
			$service = new Google_Service_Drive($client);
			$deleted = $service->files->delete($fileId);
                        if(empty($deleted))
                            echo "Deleted";		
                        else
                            echo "Failed";
			$t=time();
          updateLog('File Deleted',$t,$email,'Success',get_client_ip(),'File Delete from: '.  $filename.'<---'.$useremail,$f3);
			
});
$f3->route('GET /updatefile/@useremail/@fileid/@filedesc',
function($f3)
{
	$email=$f3->get('PARAMS.useremail');
	$filedisc=$f3->get('PARAMS.filedesc');
	$fileId=$f3->get('PARAMS.fileid');
	updateTokenWeb($email,$f3);
	
	        $db=$f3->get('DB');
	        $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email=? and domain='web'",$email));
			
		    $client = new Google_Client();
			$client->setAuthConfigFile('ggl_conf.json');
			$client->addScope(Google_Service_Drive::DRIVE);
			$client->setAccessType("offline");
			$client->setAccessToken($data[0]['accesstoken']);
			$service = new Google_Service_Drive($client);

		    updateFile($service, $fileId, $filedisc);
		  
});
function updateFile($service, $fileId, $data) {
        try {
            $emptyFile = new Google_Service_Drive_DriveFile();
            if($data=='star')
                $emptyFile->starred=true;
            else if($data=='unstar')
                $emptyFile->starred=false;
            else
                $emptyFile->setDescription($data);
            
            $service->files->update($fileId, $emptyFile, array(
                'uploadType' => 'multipart',
            ));
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }

function sharefile($email,$fileId,$touseremail,$permission,$framework)
{
	updateToken($email,$framework);
	
	 
	       $db=$framework->get('DB');
	       $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='".$email."'"));
			
		    $client = new Google_Client();
			$client->setAuthConfigFile('ggl_conf.json');
			$client->addScope(Google_Service_Drive::DRIVE);
			$client->setAccessType("offline");
			$client->setAccessToken($data[0]['accesstoken']);
			$driveService = new Google_Service_Drive($client);
			
			
			$driveService->getClient()->setUseBatch(true);
			
			  $batch = $driveService->createBatch();

			  $userPermission = new Google_Service_Drive_Permission(array(
				'type' => 'user',
				'role' => $permission,
				'emailAddress' => $touseremail
			  ));
			  $request = $driveService->permissions->create(
				$fileId, $userPermission, array('fields' => 'id'));
			  $batch->add($request, 'user');
			  $domainPermission = new Google_Service_Drive_Permission(array(
				'type' => 'domain',
				'role' => 'reader',
				'domain' => 'google.com'
			  ));
			  $request = $driveService->permissions->create(
				$fileId, $domainPermission, array('fields' => 'id'));
			  $batch->add($request, 'domain');
			  $results = $batch->execute();

			  foreach ($results as $result) {
				if ($result instanceof Google_Service_Exception) {
				  // Handle error
				  echo $result;
				} else {
				  echo $result->id;
				}
			  }
			  
			    $t=time();
          updateLog('Sharing file',$t,$email,'Success',get_client_ip(),'File Shared to: '.  $filename.'--->'.$touseremail,$framework);
}
function movefile($email,$fileId,$folderId,$framework)
{
	updateToken($email,$framework);
	
	
	     
	         $db=$framework->get('DB');
	         $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='".$email."'"));
			
		    $client = new Google_Client();
			$client->setAuthConfigFile('ggl_conf.json');
			$client->addScope(Google_Service_Drive::DRIVE); 
			$client->setAccessType("offline");
			$client->setAccessToken($data[0]['accesstoken']);
			$driveService = new Google_Service_Drive($client);
			
	      $emptyFileMetadata = new Google_Service_Drive_DriveFile();
		// Retrieve the existing parents to remove
				$file = $driveService->files->get($fileId, array('fields' => 'parents'));
			$previousParents = join(',', $file->parents);
		// Move the file to the new folder
			$file = $driveService->files->update($fileId, $emptyFileMetadata, array(
		  'addParents' => $folderId,
		  'removeParents' => $previousParents,
		  'fields' => 'id, parents'));
		  $t=time();
          updateLog('Moving file',$t,$email,'Success',get_client_ip(),'File moved to: '.  $filename.'--->'.$tofolder,$framework);
		  
}
function getFileID($email,$filename,$framework)
{
	         updateTokenWeb($email,$framework);
			 $fileid=null;
			
			  $db=$framework->get('DB');
	          $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc")); 
			
		    $client = new Google_Client();
			$client->setAuthConfigFile('ggl_conf.json');
			$client->addScope(Google_Service_Drive::DRIVE);
			$client->setAccessType("offline");
			$client->setAccessToken($data[0]['accesstoken']);
			$service = new Google_Service_Drive($client);
			$pageToken = null;
			do {
			  $response = $service->files->listFiles(array(
				'q' => "name contains '".$filename."' ",
				'spaces' => 'drive',
				'pageToken' => $pageToken,
				'fields' => 'nextPageToken, files(id, name)',
                                 
                                
			  ));
			  foreach ($response->files as $file) {
				  
				$fileid[]=$file->id;
				  
			  }
			} while ($pageToken != null);
			
		return $fileid;			
}
function storeEmail($email,$framework)
{ 
	updateToken($email,$framework);
   
        $filename='';/*
	$server = 'imap.gmail.com';
	$login = 'bhavyae@smartmbbs.com'; 
	$password = 'echambadi@528';
	
	$inbox = imap_open('{imap.gmail.com:993/imap/ssl/novalidate-cert}', "bhavyae@smartmbbs.com", "echambadi@528");
*/
	
	$imapPath = '{imap.gmail.com:993/imap/ssl}INBOX';
	$username = 'bhavyae@smartmbbs.com';
	$password = 'echambadi@528';
	 
	// try to connect
	$inbox = imap_open($imapPath,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());


	
	$emails = imap_search($inbox,'ALL');

	$max_emails = 1;
	
	if($emails) {
 
			    $count = 1;
			 
			    /* put the newest emails on top */
			    rsort($emails);
			 
			    /* for every email... */
			    foreach($emails as $email_number) 
			    {
			 
			        /* get information specific to this email */
			        $overview = imap_fetch_overview($inbox,$email_number,0);
			 
			        /* get mail message */
			        $message = imap_fetchbody($inbox,$email_number,2);
			 					
			        /* get mail structure */
			        $structure = imap_fetchstructure($inbox, $email_number);
			 
			        $attachments = array();
			 
			        /* if any attachments found... */
			        if(isset($structure->parts) && count($structure->parts)) 
			        {
			            for($i = 0; $i < count($structure->parts); $i++) 
			            {
			                $attachments[$i] = array(
			                    'is_attachment' => false,
			                    'filename' => '',
			                    'name' => '',
			                    'attachment' => ''
			                );
			 
			                if($structure->parts[$i]->ifdparameters) 
			                {
			                    foreach($structure->parts[$i]->dparameters as $object) 
			                    {
			                        if(strtolower($object->attribute) == 'filename') 
			                        {
			                            $attachments[$i]['is_attachment'] = true;
			                            $attachments[$i]['filename'] = $object->value;
			                        }
			                    }
			                }
			 
			                if($structure->parts[$i]->ifparameters) 
			                {
			                    foreach($structure->parts[$i]->parameters as $object) 
			                    {
			                        if(strtolower($object->attribute) == 'name') 
			                        {
			                            $attachments[$i]['is_attachment'] = true;
			                            $attachments[$i]['name'] = $object->value;
			                        }
			                    }
			                }
			 
			                if($attachments[$i]['is_attachment']) 
			                {
			                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);
			 
			                    /* 4 = QUOTED-PRINTABLE encoding */
			                    if($structure->parts[$i]->encoding == 3) 
			                    { 
			                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
			                    }
			                    /* 3 = BASE64 encoding */
			                    elseif($structure->parts[$i]->encoding == 4) 
			                    { 
			                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
			                    }
			                }
			            }
			        }
			 
			      
			        foreach($attachments as $attachment)
			        {
			            if($attachment['is_attachment'] == 1)
			            {
			                $filename = $attachment['name'];
			                if(empty($filename)) $filename = $attachment['filename'];
			 
			                if(empty($filename)) $filename = time() . ".dat";
			 
			                /* prefix the email number to the filename in case two emails
			                 * have the attachment with the same file name.
			                 */
			                $fp = fopen($filename, "w+");
			                fwrite($fp, $attachment['attachment']);
			                fclose($fp);
							
							//echo $filename;
							//get folder id of EmailAttachements
							
							//$folderID=getFolderID($email,"EmailAttachments",$framework);
							//echo $folderID;
							//die();
							
							//if($folderID=='not found')
							//{
								//createFolder($email,"EmailAttachments",$framework);
								//$fid=getFolderID($email,"EmailAttachments",$framework);
								$fid='';
								inserIntoFolder($fid,$filename,$email,$framework);
							//}
							//else{
								//inserIntoFolder($folderID,$filename,$email,$framework);
							//}
							
			            }
			 
			        }
			 
			        if($count++ >= $max_emails) break;
			    }
			 
			} 
			else
			{
				echo "emails not found";
			}
 
 /* close the connection */
 
imap_close($inbox);
          //$server = new Email_reader();
	//$server->inbox();
	//read email content and attachment
	//save the attachment in local folder with unique id
	//update attachment to drive emails folder
	//unlink the local file	
	//done
	$t=time();
    updateLog('Save attachment in to drive',$t,$email,'Success',get_client_ip(),'Saved attachement: '.  $filename,$framework);
	
}

function getFileExtension($fileName){
   $parts=explode(".",$fileName);
   return $parts[count($parts)-1];
}
function updateToken($email,$framework)
{

        $db=$framework->get('DB');
        $data=$framework->set('result',$db->exec('select refresh_token from drive_users where email=? ',$email));
	
	//echo $data[0]['refresh_token'];

        $refreshToken=$data[0]['refresh_token'];
        $client = new Google_Client();
        $client->setApplicationName("DriveSync");
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAuthConfigFile('ggl_conf.json');
        $client->setAccessType('offline');
        $client->refreshToken($refreshToken);
        $newtoken=$client->getAccessToken();
		
        $data=$framework->set('result',$db->exec("UPDATE drive_users SET accesstoken='".$newtoken."' WHERE email='".$email."' "));

}
/* hema*/

function printMobileFile($email,$encodedurl,$fileName,$folderId,$framework)
{
        $tmpFile=uniqid().".pdf";
	
        $url=base64_decode($encodedurl);
        $web = \Web::instance();

        $overwrite = true; // set to true, to overwrite an existing file; Default: false
        $slug = true; // rename file to filesystem-friendly version
       
//         if($folderId == 0){
//            // create new folder for user
//            $emrdb=$f3->get('DB');						
//            $folderdata =$f3->set('result',$emrdb->exec('select provider_folder from  tbl_drivesync_authentication where email=?',$email));
//            $folderquery = $folderdata[0]['provider_folder'];
//
//            $foldernamequery = $f3->set('result',$emrdb->exec("$folderquery AND id=?",$userId));
//            $foldername = $foldernamequery[0]['provider_folder'];
//            $newfolderId = createFolder($email,$foldername,$f3); 
//            $newfolder = explode(":",$newfolderId);
//            $folderId  = $newfolder[0];
//         }
        
        $files = $web->receive(
                function($file,$formFieldName){
                     var_dump($formFieldName); exit();
                    // var_dump($file);
//                    $filename=explode("/",$file['name']);
//                    $filename=$filename[1];
//                    $originalFile=$filename;
//                    $_SESSION['originalFile_upload']=$originalFile;

                        /* looks like:
                          array(5) {
                              ["name"] =>     string(19) "csshat_quittung.png"
                              ["type"] =>     string(9) "image/png"
                              ["tmp_name"] => string(14) "/tmp/php2YS85Q"
                              ["error"] =>    int(0)
                              ["size"] =>     int(172245)
                            }
                        */
                        // $file['name'] already contains the slugged name now

                        // maybe you want to check the file size
            //                if($file['size'] > (2 * 1024 * 1024)) // if bigger than 2 MB
            //                    return false; // this file is not valid, return false will skip moving it

                        // everything went fine, hurray!
                    return true; // allows the file to be moved from php tmp dir to your defined upload dir
                },
                $overwrite,
                $slug
        );

//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        //return the transfer as a string
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        // $output contains the output string
//        $output = curl_exec($ch);
//        // close curl resource to free up system resources
//        curl_close($ch);  
//        include('mpdf/mpdf.php');
//        $mpdf=new mPDF('c','A4','','' , 0 , 0 , 0 , 0 , 0 , 0);
//         // $mpdf=new mPDF('win-1252','A4','','',15,10,16,10,10,10);
//        $mpdf->SetDisplayMode('fullpage');
//        $mpdf->list_indent_first_level = 0; 
//        $mpdf->WriteHTML($url);
//        $mpdf->Output($tmpFile, 'F');
//        chmod($tmpFile,0777);
        //upload to dirve
        updateToken($email,$framework);
        $db=$framework->get('DB');
        $data=$framework->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));
			
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);

        //$folderID=getFolderID($email,$foldername);

        $fileMetadata = new Google_Service_Drive_DriveFile(array(
          'name' => $fileName.'.pdf',
          'parents' => array($folderId)
        ));
        echo $data = html_entity_decode ($url);
//        echo $data = "<html><body><div id='show_div_cc7294' style='display:block'><div style='clear:both;'> <div id='1Mobile-Chief-Complaint1' style='clear:both;'><ul class='1Mobile-Chief-Complaint1' ><li><b>Chief Complaint File Link:</b>ZSXC</li><li><b>Chief Complaint Status:</b>pending</li><li>aZSXD</li><ul/></div></div></div></body></html>";


        $createdFile = $service->files->create($fileMetadata, array(
                    'data' => $data,
                    'mimeType' => 'application/pdf',
                    'uploadType' => 'multipart',
                    'fields' => 'id'
            ));
        printf("File ID: %s\n", $createdFile->id);
                //print_r($createdFile);
        unlink($tmpFile);
        $t=time();
        updateLog('Genrated file from html',$t,$email,'Success',get_client_ip(),'Created a new file from html, encoded url'. $encodedurl,$framework);

}

/* =================== */
function printFile($email,$encodedurl,$fileName,$folderId,$framework)
{
        $tmpFile=uniqid().".pdf";
	
        $url=base64_decode($encodedurl);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $output contains the output string
        echo $output = curl_exec($ch);
        // close curl resource to free up system resources
        curl_close($ch);  
        include('mpdf/mpdf.php');
        $mpdf=new mPDF('c','A4','','' , 0 , 0 , 0 , 0 , 0 , 0);
         // $mpdf=new mPDF('win-1252','A4','','',15,10,16,10,10,10);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->list_indent_first_level = 0; 
        $mpdf->WriteHTML($output);
        $mpdf->Output($tmpFile, 'F');
        chmod($tmpFile,0777);
        //upload to dirve
        updateToken($email,$framework);
        $db=$framework->get('DB');
        $data=$framework->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));
			
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);

        //$folderID=getFolderID($email,$foldername);

        $fileMetadata = new Google_Service_Drive_DriveFile(array(
          'name' => $fileName.'.pdf',
          'parents' => array($folderId)
        ));
        $data = file_get_contents($tmpFile);
//        print_r($data);
        //echo $data = "<html><body><div id='show_div_cc7294' style='display:block'><div style='clear:both;'> <div id='1Mobile-Chief-Complaint1' style='clear:both;'><ul class='1Mobile-Chief-Complaint1' ><li><b>Chief Complaint File Link:</b>ZSXC</li><li><b>Chief Complaint Status:</b>pending</li><li>aZSXD</li><ul/></div></div></div></body></html>";

        /*  $file = new Google_Service_Drive_DriveFile();
                $file->setName($fileName.'.pdf');
                $file->setDescription('A test document');
                $file->setMimeType('application/pdf');*/

                $createdFile = $service->files->create($fileMetadata, array(
                          'data' => $data,
                          'mimeType' => 'application/pdf',
                          'uploadType' => 'multipart',
                          'fields' => 'id'
                        ));
                        printf("File ID: %s\n", $createdFile->id);
                //print_r($createdFile);
        unlink($tmpFile);
        $t=time();
        updateLog('Genrated file from html',$t,$email,'Success',get_client_ip(),'Created a new file from html, encoded url'. $encodedurl,$framework);

        //listAllFolders($email);
                //upload file using REST API
        /*$googleurl="https://www.googleapis.com/upload/drive/v2/files?uploadType=multipart";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $googleurl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/pdf','Authorization: Bearer '. $accessValue));
        curl_setopt($ch, CURLOPT_POST, 1);
        $args['file'] = curl_file_create('test.pdf', 'application/pdf', 'test.pdf');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        echo $response;*/
}
function getFolderID($email,$foldername,$framework)
{

        //  updateToken($email);
        $db=$framework->get('DB');
        $data=$framework->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));
			
        $folderID="not found";
        //echo $data['refresh_token'];
        //$accessValue=explode(":",explode(",",$data[0])[0])[1];
        //$accessValue=str_replace('"','',$accessValue);
		
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);

        $optParams = array(
          'pageSize' => 1000,
          'fields' => "nextPageToken, files(id, name)",
          'orderBy' => "folder"

        );
        $results = $service->files->listFiles($optParams);
        //print "Files:\n";
        foreach ($results->getFiles() as $file) {

            if($file->getName()==$foldername)
            {
                $folderID=$file->getId();
            }
            printf("%s (%s)\n", $file->getName(), $file->getId());
        }
        return $folderID;
}
function createFolder($email,$folder,$framework)
{
	 
    $useremail=$email;
 
    //update key
		
    updateToken($useremail,$framework);
 
    $db=$framework->get('DB'); 
    $data=$framework->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));
			
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $driveService = new Google_Service_Drive($client);
		
    $fileMetadata = new Google_Service_Drive_DriveFile(array(
		  'name' => $folder,
		  'mimeType' => 'application/vnd.google-apps.folder'
    ));
    $file = $driveService->files->create($fileMetadata, array(
		  'fields' => 'id'
    ));
//    printf("%s\n", $file->id.":");

    $t=time();
    updateLog('Create a new folder',$t,$useremail,'Success',get_client_ip(),'Created a new folder in drive:'.  $folder,$framework);

  return $file->id;
}
function inserIntoFolder($folderid,$filename,$email,$framework)
{
		
        $db=$framework->get('DB');
        $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);


        $extension=getFileExtension($filename);
       
        if($folderid=='')
        {
                $fileMetadata = new Google_Service_Drive_DriveFile(array(
                    'name' => $filename
                ));

        }
        else
        {
                $fileMetadata = new Google_Service_Drive_DriveFile(array(
                    'name' => $filename,
                    'parents' => array($folderid)
                ));

        }
       
        

        
        chmod($filename,0777);

        $mime_types= array(
            "xls" =>'application/vnd.ms-excel',
            "xlsx" =>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            "xml" =>'text/xml',
            "ods"=>'application/vnd.oasis.opendocument.spreadsheet',
            "csv"=>'text/plain',
            "tmpl"=>'text/plain',
            "pdf"=> 'application/pdf',
            "php"=>'application/x-httpd-php',
            "jpg"=>'image/jpeg',
            "png"=>'image/png',
            "gif"=>'image/gif',
            "bmp"=>'image/bmp',
            "txt"=>'text/plain',
            "doc"=>'application/msword',
            "js"=>'text/js',
            "swf"=>'application/x-shockwave-flash',
            "mp3"=>'audio/mpeg',
            "zip"=>'application/zip',
            "rar"=>'application/rar',
            "tar"=>'application/tar',
            "arj"=>'application/arj',
            "cab"=>'application/cab',
            "html"=>'text/html',
            "htm"=>'text/html',
            "default"=>'application/octet-stream',
            "folder"=>'application/vnd.google-apps.folder'
        );

        $data = file_get_contents($filename);
	
	$createdFile = $service->files->create($fileMetadata, array(
					  'data' => $data,
					  'mimeType' => $mime_types[$extension],
					  'uploadType' => 'multipart',
					  'fields' => 'id'
                            ));
        //printf("File ID: %s\n", $createdFile->id);
				//print_r($createdFile);
        unlink($filename);
}
function updateLog($typeofrequest,$timeofrequest,$email,$status,$fromip,$description,$framework)
{
	
	 $db=$framework->get('DB');
	         $data=$framework->set('result',$db->exec("INSERT INTO drive_logs (requesttype,timeofrequest,user,status,ipaddress,description) 
VALUES ('".$typeofrequest."','".$timeofrequest."','". $email."','".$status."','".$fromip."','".$description."')"));

//echo "Log Updated";
}
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
 function chmod_r($dir, $dirPermissions, $filePermissions) {
      $dp = opendir($dir);
       while($file = readdir($dp)) {
         if (($file == ".") || ($file == ".."))
            continue;

        $fullPath = $dir."/".$file;

         if(is_dir($fullPath)) {
           // echo('DIR:' . $fullPath . "\n");
            chmod($fullPath, $dirPermissions);
            chmod_r($fullPath, $dirPermissions, $filePermissions);
         } else {
           // echo('FILE:' . $fullPath . "\n");
            chmod($fullPath, $filePermissions);
         }

       }
     closedir($dp);
  }
 
function listAllFolders($email,$framework)
{
	    //updateToken($email);
	     $db=$framework->get('DB');
	  $data=$framework->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));
		//echo $data['refresh_token'];
		//$accessValue=explode(":",explode(",",$data[0])[0])[1];
		//$accessValue=str_replace('"','',$accessValue);
		
			$client = new Google_Client();
			$client->setAuthConfigFile('ggl_conf.json');
			$client->addScope(Google_Service_Drive::DRIVE);
			$client->setAccessType("offline");
			$client->setAccessToken($data[0]['accesstoken']);
			$service = new Google_Service_Drive($client);
			
						$optParams = array(
			  'pageSize' => 100,
			  'fields' => "nextPageToken, files(id, name)",
			  'orderBy' => "folder"
			  
			);
			$results = $service->files->listFiles($optParams);
			
		  foreach ($results->getFiles() as $file) {
			  echo "{".$file->getName().":".$file->getId()."},";
			
		  }
}

function listAllFilesInFolder($email,$folderId,$filetype,$framework)
{
	updateToken($email,$framework);
    
        $db     = $framework->get('DB');
        $data   = $framework->set('result',$db->exec("select accesstoken from drive_users where email=? ",$email));
        echo $data['refresh_token'];
        //$accessValue=explode(":",explode(",",$data[0])[0])[1];
        //$accessValue=str_replace('"','',$accessValue);
		
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
	$emptyFile = new Google_Service_Drive_DriveFile();	
        $mimetypes='';
        if($filetype == 'Photos'){
            $mimetypes = " and mimeType contains 'image/jpeg'";
        }
        if($filetype == 'Records'){
            $mimetypes = " and mimeType contains 'audio/x-wav'";
        }
        if($filetype == 'folders'){
            $mimetypes = " and mimeType contains 'application/vnd.google-apps.folder'";
        } 
        
        $optParams = array(
            'pageSize' => 1000,
            'fields' => "nextPageToken, files(id, name)",
//            'projection' => "BASIC",
//            'corpus'=>'user',
            'q' => "'$folderId' in parents $mimetypes and trashed = false"

        );
        $results = $service->files->listFiles($optParams);
//        echo "<pre>"; print_r($results); echo "</pre>";
        $filearray = array();
        $i = 0;          
        foreach ($results->getFiles() as $file) {
//            $filearray[] = $service->files->get($file->getId(), array('fields' => 'fileExtension,fullFileExtension,id,imageMediaMetadata,mimeType,name,originalFilename,webContentLink,webViewLink,thumbnailLink'));
            $filearray[$i]['id'] = $file->getId();
            $filearray[$i]['name'] = $file->getName();
            $i++;
        }
        echo json_encode($filearray);
}

//function listAllFilesInFolder($email,$framework,$folderId)
//{
//	//updateToken($email);
//	$db=$framework->get('DB');
//	$data=$framework->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));
//        //echo $data['refresh_token'];
//        //$accessValue=explode(":",explode(",",$data[0])[0])[1];
//        //$accessValue=str_replace('"','',$accessValue);
//
//        $client = new Google_Client();
//        $client->setAuthConfigFile('ggl_conf.json');
//        $client->addScope(Google_Service_Drive::DRIVE);
//        $client->setAccessType("offline");
//        $client->setAccessToken($data[0]['accesstoken']);
//        $service = new Google_Service_Drive($client);
//
//        $optParams = array(
////          'pageSize' => 100,
////          'corpus' => 'DOMAIN',
//            'q' => "'$folderId' in parents"
////          'orderBy' => "createdDate"
//
//        );
//        $results = $service->files->listFiles($optParams);
//        
//        print_r($results->getFiles());
//        echo json_encode($results->getFiles());
//        foreach ($results->getFiles() as $file) {
//            echo $file->getId();
////                echo "{".$file->getName().":".$file->getId()."},";
//                 $service->files->get($file->getId(), 'application/pdf', array(
//        'alt' => 'media' ));
//
//        }
//}


/********    API's FOR MEDICAL RECORD WEB      **********/

$f3->route('GET /oauth2/@useremail/@instance',
    function($f3)
    {
        session_start();
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $_SESSION['useremail']=$f3->get('PARAMS.useremail');
        if($f3->get('PARAMS.instance')=='emr')
            $inst='emr';
        else 
            $inst='provider_portal';
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            //echo $_SESSION['access_token'];
//            $client->setAccessToken($_SESSION['access_token']);
//            $service = new Google_Service_Drive($client);
//            $results = $service->files->listFiles(); 
//            echo json_encode($results);
              $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/api/DriveSync/oauth2callback_web.php?inst='.$inst;
           
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));  
        } else {
            $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/api/DriveSync/oauth2callback_web.php?inst='.$inst;
           
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        } 
    }		
);


$f3->route('GET /createfolder_web/@useremail/@foldername',
    function($f3)
    {
        $useremail=$f3->get('PARAMS.useremail');
        $folderName=$f3->get('PARAMS.foldername');
        createFolderWeb($useremail,$folderName,$f3);
    }		 
);


$f3->route('GET /uploadHTMLWeb/@useremail/@filename/@folderid/@encodedURL',
function($f3)
{
    printFileWeb($f3->get('PARAMS.useremail'),$f3->get('PARAMS.encodedURL'),$f3->get('PARAMS.filename'),$f3->get('PARAMS.folderid'),$f3);			
		
});


$f3->route('GET /listall_web/@email',
function($f3)
{
	listAllFoldersWeb($f3->get('PARAMS.email'),$f3);
});

$f3->route('GET /listallfiles_web/@email/@folderid/@filetype',
function($f3)
{
	listAllFilesInFolder_web($f3->get('PARAMS.email'),$f3->get('PARAMS.folderid'),$f3->get('PARAMS.filetype'),$f3);
});
$f3->route('GET /listallfilespagetoken_web/@email/@folderid/@filetype/@pagetoken',
function($f3)
{
	listAllFilesInFolderPagetoken_web($f3->get('PARAMS.email'),$f3->get('PARAMS.folderid'),$f3->get('PARAMS.filetype'),$f3->get('PARAMS.pagetoken'),$f3);
});
$f3->route('GET /movefile_web/@useremail/@fileid/@tofolderid/@user/@filename/@foldername',
function($f3)
{
	movefile_web($f3->get('PARAMS.useremail'),$f3->get('PARAMS.fileid'),$f3->get('PARAMS.tofolderid'),$f3->get('PARAMS.user'),$f3->get('PARAMS.filename'),$f3->get('PARAMS.foldername'),$f3);
});

$f3->route('GET /movefile_web1/@useremail/@fileid/@tofolderid/@user',
function($f3)
{
	movefile_web1($f3->get('PARAMS.useremail'),$f3->get('PARAMS.fileid'),$f3->get('PARAMS.tofolderid'),$f3->get('PARAMS.user'),$f3);
});

$f3->route('GET /renameFile_web/@instance/@user/@category/@fileId/@newTitle/@email',
function($f3)
{
    renameFileWeb($f3->get('PARAMS.instance'),$f3->get('PARAMS.user'),$f3->get('PARAMS.category'),$f3->get('PARAMS.fileId'),$f3->get('PARAMS.newTitle'),$f3->get('PARAMS.email'),$f3);			
		
});
 
$f3->route('GET /searchFile_web/@email/@name',
function($f3)
{
    searchFileWeb($f3->get('PARAMS.email'),$f3->get('PARAMS.name'),$f3);			
		
});

$f3->route('GET /insert_folder_web/@email/@folderid/@foldername',
function($f3)
{
    insert_folder_web($f3->get('PARAMS.email'),$f3->get('PARAMS.folderid'),$f3->get('PARAMS.foldername'),$f3);			
		 
});

$f3->route('GET /test_pdf/@email/@folderid/@foldername',
function($f3)
{
    test_pdf($f3->get('PARAMS.email'),$f3->get('PARAMS.folderid'),$f3->get('PARAMS.foldername'),$f3);			
		 
});

//functions 
$f3->route('GET /listall_folderid/@email/@folderid/@filetype',
function($f3)
{
	listall_folderid($f3->get('PARAMS.email'),$f3->get('PARAMS.folderid'),$f3->get('PARAMS.filetype'),$f3);
});
//store email attachment
$f3->route('GET /storeEmailAttachment_web/@email/@pwd/@unique_ids',
function($f3)
{
	storeEmail_web($f3->get('PARAMS.email'),$f3->get('PARAMS.pwd'),$f3->get('PARAMS.unique_ids'),$f3);
});
$f3->route('GET /createMsgForEmail/@email',
function($f3)
{
	createMsgForEmail($f3->get('PARAMS.email'),$f3);
});
//store email attachment count
$f3->route('GET /storeEmailAttachmentCount/@email/@pwd',
function($f3)
{
	storeEmailCount($f3->get('PARAMS.email'),$f3->get('PARAMS.pwd'),$f3);
});
function updateTokenWeb($email,$framework)
{
	 
    $db=$framework->get('DB');
    $data=$framework->set('result',$db->exec("select refresh_token from drive_users where email='$email' order by id desc"));
    //echo $data[0]['refresh_token'];
    $refreshToken=$data[0]['refresh_token'];
    $client = new Google_Client();
    $client->setApplicationName("DriveSync");
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAuthConfigFile('ggl_conf.json');
    $client->setAccessType('offline');
    $client->refreshToken($refreshToken);
    $newtoken=$client->getAccessToken();
    $data=$framework->set('result',$db->exec("UPDATE drive_users SET accesstoken='".$newtoken."' WHERE email='".$email."'"));
}

function updateLogWeb($typeofrequest,$timeofrequest,$email,$status,$fromip,$description,$framework)
{
	
    $db=$framework->get('DB');
    $data=$framework->set('result',$db->exec("INSERT INTO drive_logs (requesttype,timeofrequest,user,status,ipaddress,description,domain) 
    VALUES ('".$typeofrequest."','".$timeofrequest."','". $email."','".$status."','".$fromip."','".$description."','web')"));

//echo "Log Updated";
}
$f3->route('GET|POST|PUT /downloadfile/@email/@fileid', 
   function($f3){
        $email= $f3->get('PARAMS.email');
        $mediaid = $f3->get('PARAMS.fileid');
        updateToken($email,$f3);
        $db=$f3->get('DB');      
        $data=$f3->set('result',$db->exec('select accesstoken from drive_users where email=?',$email));  
        $web = \Web::instance();
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
        $content = $service->files->get($mediaid, array('alt' => 'media' ));
        echo $content;

    }
);

function createFolderWeb($email,$folder,$framework)
{
	 
    $useremail=$email;
    //update key
    updateTokenWeb($useremail,$framework);
    $db=$framework->get('DB'); 
    $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
			
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $driveService = new Google_Service_Drive($client);
		
    $fileMetadata = new Google_Service_Drive_DriveFile(array(
		                                        'name' => $folder,
		                                        'mimeType' => 'application/vnd.google-apps.folder'));
    
    
     try {
        $file = $driveService->files->create($fileMetadata, array(
                                                        'fields' => 'id'));
			//printf("File ID: %s\n", $file->id); 
         printf("%s\n", $file->id.":");
    } catch (Exception $e) {
        if ($e->getCode() == 404) {
            // Apply exponential backoff.
           echo '404 ERROR:';

         } else {
//                    // Other error, re-throw. 
//                    throw;
         }
    }
    
    
    
    
    $t=time();
    updateLogWeb('Create a new folder',$t,$useremail,'Sucess',get_client_ip(),'Created a new folder in drive:'.  $folder,$framework);
    
  
}


function printFileWeb($email,$encodedurl,$fileName,$folderId,$framework)
{
    $tmpFile=uniqid().".pdf";	
    $url=base64_decode($encodedurl);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $output contains the output string
    $output = curl_exec($ch);
    // close curl resource to free up system resources
     curl_close($ch);  
    include('mpdf/mpdf.php');
    $mpdf=new mPDF('c','A4','','' , 0 , 0 , 0 , 0 , 0 , 0);
     // $mpdf=new mPDF('win-1252','A4','','',15,10,16,10,10,10);
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->list_indent_first_level = 0; 
    $mpdf->WriteHTML(utf8_encode($output));
    $mpdf->Output($tmpFile, 'F');
    chmod($tmpFile,0777);
    //upload to dirve
    updateTokenWeb($email,$framework);
    $db=$framework->get('DB');
    $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
			
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $service = new Google_Service_Drive($client);
    
    //$folderID=getFolderID($email,$foldername);
    $fileMetadata = new Google_Service_Drive_DriveFile(array(
      'name' => $fileName.'.pdf',
      'parents' => array($folderId)
    ));
    $data = file_get_contents($tmpFile);
//  echo base64_decode($data);	echo "test";	
    /*  $file = new Google_Service_Drive_DriveFile();
            $file->setName($fileName.'.pdf');
            $file->setDescription('A test document');
            $file->setMimeType('application/pdf');*/

    $createdFile = $service->files->create($fileMetadata, array(
              'data' => $data,
              'mimeType' => 'application/pdf',
              'uploadType' => 'multipart',
              'fields' => 'id'
            ));
    printf($createdFile->id);
				//print_r($createdFile);
    unlink($tmpFile);
    $t=time();
    updateLogWeb('Genrated file from html',$t,$email,'Sucess',get_client_ip(),'Created a new file from html, encoded url'. $encodedurl,$framework);
			
         
}

function AttachmentPdf($email,$msg,$fileName,$folderId,$framework)
{
    $tmpFile=uniqid().".pdf";	
//    $url=base64_decode($encodedurl);
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, $url);
//    //return the transfer as a string
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    // $output contains the output string
//    $output = curl_exec($ch);
//    // close curl resource to free up system resources
//     curl_close($ch);  
     
      $encoded_string =  "<html><style>@page { size 8.5in 11in; margin: 2cm; }
            body { font-family: sans-serif;font-size: 10.5;padding:10px;}
             
            ul{    list-style-type: none;   padding:0px;   -webkit-padding-start: 0px !important;}li { padding-right:40px; }ul{float:left;}</style>
            <body  style='border:1px solid black;width:980px;' cellspacing='0'>".str_replace('dir="rtl"','',str_replace('dir="ltr"','',$msg))."<footer style='clear:both;' align='right'>
            </footer></body></html>" ;
    
    include_once('mpdf/mpdf.php');
    $mpdf=new mPDF('c','A4','','' , 0 , 0 , 0 , 0 , 0 , 0);
     // $mpdf=new mPDF('win-1252','A4','','',15,10,16,10,10,10);
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->list_indent_first_level = 0; 
    $mpdf->WriteHTML($encoded_string);
    $mpdf->Output('email_attachments/'.$tmpFile, 'F');
    chmod('email_attachments/'.$tmpFile,0777);
    //upload to dirve
    updateTokenWeb($email,$framework);
    $db=$framework->get('DB');
    $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
			
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $service = new Google_Service_Drive($client);
    
    //$folderID=getFolderID($email,$foldername);
    $fileMetadata = new Google_Service_Drive_DriveFile(array(
      'name' => $fileName.'.pdf',
      'parents' => array($folderId)
    ));
    $data = file_get_contents('email_attachments/'.$tmpFile);
  //  $data = file_get_contents('email_attachments/2016-07-25_03_49_13_Subhan_SA__subhansa@smartmbbs.com_.html');
//  echo base64_decode($data);	echo "test";	
    /*  $file = new Google_Service_Drive_DriveFile();
            $file->setName($fileName.'.pdf');
            $file->setDescription('A test document');
            $file->setMimeType('application/pdf');*/

    $createdFile = $service->files->create($fileMetadata, array(
              'data' => $data,
              'mimeType' => 'application/pdf',
              'uploadType' => 'multipart',
              'fields' => 'id'
            ));
    printf($createdFile->id);
				//print_r($createdFile);
    unlink('email_attachments/'.$tmpFile);
   
    $t=time();
    updateLogWeb('Genrated file from html',$t,$email,'Sucess',get_client_ip(),'Created a new file from html, encoded url'. $encodedurl,$framework);
			
         
}

function listAllFoldersWeb($email,$framework)
{
     updateTokenWeb($email,$framework);
     $db=$framework->get('DB');
     $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
    //echo $data['refresh_token'];
    //$accessValue=explode(":",explode(",",$data[0])[0])[1];
    //$accessValue=str_replace('"','',$accessValue);

    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $service = new Google_Service_Drive($client);
			
    $optParams = array(
      'pageSize' => 1000,
      'fields' => "nextPageToken, files(id, name)",
      'q'=>"mimeType = 'application/vnd.google-apps.folder' and trashed = false and '$email' in owners ",
    // 'orderBy'=>"folder" ,
    );
    //'q'=>"mimeType = 'application/vnd.google-apps.folder' and trashed = false and '$email' in owners ",
    $results = $service->files->listFiles($optParams);
    foreach ($results->getFiles() as $file) {
	//echo "{".$file->getName().":".$file->getId()."},";
        $data1[$file->getName()]=$file->getId();
     }
     // echo "<pre>"; print_r($data1); echo "</pre>"; 
      echo json_encode($data1);
}


$f3->route('GET /getfileinfo_web/@useremail/@fileid',
function($f3)
{
	$email=$f3->get('PARAMS.useremail'); 
	$fileId=$f3->get('PARAMS.fileid');
	updateTokenWeb($email,$f3);
	
	 $db=$f3->get('DB');
	 
	 $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
			
		    $client = new Google_Client();
			$client->setAuthConfigFile('ggl_conf.json');
			$client->addScope(Google_Service_Drive::DRIVE);
			$client->setAccessType("offline");
			$client->setAccessToken($data[0]['accesstoken']);
			$service = new Google_Service_Drive($client);
			try {
                                $file = $service->files->get($fileId, array('fields' => 'appProperties,capabilities,contentHints,createdTime,description,explicitlyTrashed,fileExtension,folderColorRgb,fullFileExtension,headRevisionId,iconLink,id,imageMediaMetadata,kind,lastModifyingUser,md5Checksum,mimeType,modifiedByMeTime,modifiedTime,name,originalFilename,ownedByMe,owners,parents,permissions,properties,quotaBytesUsed,shared,sharedWithMeTime,sharingUser,size,spaces,starred,thumbnailLink,trashed,version,videoMediaMetadata,viewedByMe,viewedByMeTime,viewersCanCopyContent,webContentLink,webViewLink,writersCanShare'));
                                echo json_encode($file);
                         } catch (Exception $e) {
                             if ($e->getCode() == 403 && ($e->getErrors()[0]["reason"] == "rateLimitExceeded"|| $e->getErrors()[0]["reason"] == "userRateLimitExceeded")) {
                                // Apply exponential backoff.
                               echo '[{"error_code":"403"}]';

                             } else {
            //                    // Other error, re-throw. 
            //                    throw;
                             }
                         }
	
		  $t=time();
          updateLogWeb('File info captured',$t,$email,'Success',get_client_ip(),'File info captured: '.  $filename.'<---'.$useremail,$f3);
});


function listAllFilesInFolder_web($email,$folderId,$filetype,$framework) 
{
        updateTokenWeb($email,$framework);
        $db     = $framework->get('DB');
        $data   = $framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
        //echo $data['refresh_token'];
        //$accessValue=explode(":",explode(",",$data[0])[0])[1];
        //$accessValue=str_replace('"','',$accessValue);
		
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
	$emptyFile = new Google_Service_Drive_DriveFile();	
        $orderby='';
        if($filetype == 'pdf'){
            $mimetypes = " and mimeType contains 'application/pdf'";
        }
//        else if($filetype == 'modifiedTime'){
//            $orderby = $filetype .' desc';
//        }else if($filetype=='name') {
//            $orderby = $filetype . ' asc';
//        }else if($filetype=='createdTime') {
//            $orderby = $filetype .' desc';
//        }
        else if($filetype == 'folders'){
            $mimetypes = " and mimeType contains 'application/vnd.google-apps.folder'";
        }
        if($filetype == ''){
             $optParams = array(
            'pageSize' => 1000,
            'fields' => "nextPageToken, files(id, name,mimeType,thumbnailLink)",
//            'corpus'=>'user',
            'q' => "'$folderId' in parents and trashed = false ",
             'orderBy' => "folder,modifiedTime desc"      
         );
        }else {
            
            if($orderby==''){
                $optParams = array(
                    'pageSize' => 1000,
                    'fields' => "nextPageToken, files(id, name,mimeType,thumbnailLink)",
                    'q' => "'$folderId' in parents $mimetypes and trashed = false",
                    'orderBy' => "folder,modifiedTime desc"      
                );
            }else {
                $optParams = array(
                    'pageSize' => 1000,
                    'fields' => "nextPageToken, files(id, name,mimeType,thumbnailLink)",
                    'q' => "'$folderId' in parents and trashed = false ",
                    'orderBy' => "$orderby"
                );
            }
            
        }
        
       
//        $results = $service->files->listFiles($optParams);
//        $filearray = array();
//        $i = 0;          
//        foreach ($results->getFiles() as $file) {
//            $filearray[]= $service->files->get($file->getId(), array('fields' => 'fileExtension,fullFileExtension,id,imageMediaMetadata,mimeType,name,originalFilename,webContentLink,webViewLink,thumbnailLink'));
////            echo $downloadUrl = $file->getWebContentLink(); echo "====";
//            $content = $service->files->get($file->getId(), array(
//            'alt' => '' ));
//     
//            $i++;
//        }
//       
//        echo json_encode($filearray);
        
          try {
              
               $results = $service->files->listFiles($optParams);
              
                $filearray = array();
                $i = 0;          
                foreach ($results->getFiles() as $file) { 
                   
                     $filearray[$i]['id'] = $file->getId();
                     $filearray[$i]['name'] = $file->getName();
                     $filearray[$i]['mimeType'] = $file->mimeType;
                     $filearray[$i]['thumbnail'] = $file->thumbnailLink;
                    $i++;
                }
               
               echo json_encode($filearray);
           } catch (Exception $e) {
                if ($e->getCode() == 403 && ($e->getErrors()[0]["reason"] == "rateLimitExceeded"|| $e->getErrors()[0]["reason"] == "userRateLimitExceeded")) {
                    // Apply exponential backoff.
                   echo '[{"error_code":"403"}]';
                   
                 } else {
//                    // Other error, re-throw. 
//                    throw;
                 }
           }
}

function listAllFilesInFolderPagetoken_web($email,$folderId,$filetype,$pagetoken,$framework) 
{
        updateTokenWeb($email,$framework);
        $db     = $framework->get('DB');
        $data   = $framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
        
	$client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
	$emptyFile = new Google_Service_Drive_DriveFile();	
        $orderby='';
        if($pagetoken=='empty')
        $pageToken = null;
        else 
        $pageToken = $pagetoken;
        if($filetype == 'pdf'){
            $mimetypes = " and mimeType contains 'application/pdf'";
        }else if($filetype == 'folders'){
            $mimetypes = " and mimeType contains 'application/vnd.google-apps.folder'";
        }
        if($filetype == ''){
             $optParams = array(
                'pageSize' => 1000,
                'pageToken' => $pageToken,
                'fields' => "nextPageToken, files(id, name,mimeType,thumbnailLink)",
                'q' => "'$folderId' in parents and trashed = false ",
                'orderBy' => "folder,modifiedTime desc"      
            );
        }else {
            if($orderby==''){
                $optParams = array(
                    'pageSize' => 1000,
                    'pageToken' => $pageToken,
                    'fields' => "nextPageToken, files(id, name,mimeType,thumbnailLink)",
                    'q' => "'$folderId' in parents $mimetypes and trashed = false",
                    'orderBy' => "folder,modifiedTime desc"      
                );
            }else {
                $optParams = array(
                    'pageSize' => 1000,
                    'pageToken' => $pageToken,
                    'fields' => "nextPageToken, files(id, name,mimeType,thumbnailLink)",
                    'q' => "'$folderId' in parents and trashed = false ",
                    'orderBy' => "$orderby"
                );
            }
        }
        try {
            $results = $service->files->listFiles($optParams);
            $filearray = array();
            $i = 0;          
            foreach ($results->getFiles() as $file) { 
                $filearray[$i]['id'] = $file->getId();
                $filearray[$i]['name'] = $file->getName();
                $filearray[$i]['mimeType'] = $file->mimeType;
                $filearray[$i]['thumbnail'] = $file->thumbnailLink;
                $i++;
            }
            $filearray['nextpageToken'] = $results->nextPageToken;
            echo json_encode($filearray);
        } catch (Exception $e) {
            if ($e->getCode() == 403 && ($e->getErrors()[0]["reason"] == "rateLimitExceeded"|| $e->getErrors()[0]["reason"] == "userRateLimitExceeded")) {
                    // Apply exponential backoff.
                   echo '[{"error_code":"403"}]';
            } else {
                echo $e->getCode();
            }
        }
}
function movefile_web($email,$fileId,$folderId,$user,$filename,$foldername,$framework)
{
	updateTokenWeb($email,$framework);
	$db=$framework->get('DB');
	$data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));
        
	$client = new Google_Client();
	$client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE); 
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $driveService = new Google_Service_Drive($client);
	$emptyFileMetadata = new Google_Service_Drive_DriveFile();
	// Retrieve the existing parents to remove
        $file = $driveService->files->get($fileId, array('fields' => 'parents'));
        $previousParents = join(',', $file->parents);
	// Move the file to the new folder
        try{
	$file = $driveService->files->update($fileId, $emptyFileMetadata, array(
                                                                              'addParents' => $folderId,
                                                                              'removeParents' => $previousParents,
                                                                              'fields' => 'id, parents'));
        }catch (Exception $e) {
                if ($e->getCode() == 403 && ($e->getErrors()[0]["reason"] == "rateLimitExceeded"|| $e->getErrors()[0]["reason"] == "userRateLimitExceeded")) {
                    // Apply exponential backoff.
                   echo '[{"error_code":"403"}]';
                   
                 } else {
//                    // Other error, re-throw. 
//                    throw;
                 }
           }
         
        $id     = GibberishAES::enc($file->id, 'rotcoderaclla');
        $view='https://'.$_SERVER['HTTP_HOST'].'/interface/login/login_frame.php?param='.$id;
        $liview_linknk = "<a href=$view target=".'_blank'.">Document link</a>";
        $text='File moved to: '.  $filename.'--->'.$foldername;
        
        $user_cus=$framework->set('result',$db->exec("select movefile_settings from tbl_drivesync_authentication where email='$email'  order by id desc"));   
        $ser1=unserialize($user_cus[0]['movefile_settings']);                      
        $ser=explode(",",unserialize($ser1['user']));
   
        $myArray = array_filter( $ser );
        foreach($myArray as $val){
            $user_cus1=$framework->set('result',$db->exec("select email from tbl_user_custom_attr_1to1 where userid='".$val."'"));
            $user_cus2=$framework->set('result',$db->exec("select username from users where id='".$val."' and username!=''"));
            $user_nam=$user_cus2[0]['username'];
            $email_id=$user_cus1[0]['email'];
            /*********send Emr message notification******/
             $body="(Moved files )".$liview_linknk;
            $data3=$framework->set('result',$db->exec("INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                            values (date_format(now(), '%Y-%m-%d %H:%i'),'".$body."', 0, '$user_nam', 'Default', 1, 1, 'Moved files ', '".$user_nam."', 'new')"));
            $lastid =$framework->set('result',$db->exec("select LAST_INSERT_ID() as lastid")); 
            $data3  =$framework->set('result',$db->exec("INSERT INTO tbl_pnotes_file_relation (date, mid, doc_links, type)
                            values (NOW(),'".$lastid[0]['lastid']."','$link1','$category')"));
            
            
            $smtp_details  =$framework->set('result',$db->exec("select gl_name,gl_value from globals where gl_name IN('SMTP_HOST','SMTP_PORT','SMTP_PASS','SMTP_USER') "));
           
            /*********send email notification******/
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'html';
//            $mail->Host = "smtp.gmail.com";
//            $mail->Port = 465;
            if($smtp_details[0][gl_name]=='SMTP_HOST')
            $mail->Host = $smtp_details[0]['gl_value'];
            if($smtp_details[2][gl_name]=='SMTP_PORT')
            $mail->Port = $smtp_details[2]['gl_value'];
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
//            $mail->Username = "srinus@smartmbbs.com";
//            $mail->Password = "srinucnu@#";
           if($smtp_details[3][gl_name]=='SMTP_USER')
            $mail->Username = $smtp_details[3]['gl_value'];
            if($smtp_details[1][gl_name]=='SMTP_PASS')
            $mail->Password = $smtp_details[1]['gl_value'];
            $mail->setFrom($smtp_details[3]['gl_value'], 'Smart MBBS');

        //    $toEmails = explode(";",$to);
            $toEmails = $email_id;
            if(count($toEmails)>1){
                foreach($toEmails as $eachEmail){
                    $mail->addAddress($eachEmail);
                }
            }
            else
            {
                $mail->addAddress($toEmails);
            }


            $mail->Subject =  $ser1['text'];
            $mail->msgHTML($ser1['text']."<br>".$view);
            $mail->AltBody = 'This is a plain-text message body';
            if($ser1['attach']=='yes'){
                $mail->AddAttachment($_SERVER["DOCUMENT_ROOT"].'/api/DriveSync/uploads/'.$originalFile, '', $encoding = 'base64', $type = 'application/octet-stream');
            }
            //send the message, check for errors
            if (!$mail->send()) {
                echo "Mailer Error: " . $mail->ErrorInfo;
                $mstatus = false;
            } else {
                $mstatus = true;
            }
        }
           
           
           
	$t=time();
        $ins_log=$framework->set('result',$db->exec("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID)values(now(),'".$user."','".$email."','','','$view','','','$text','')"));
        updateLogWeb('Moving file',$t,$email,'Success',get_client_ip(),'File moved to: '.  $filename.'TO'.$foldername,$framework);
	
}


function movefile_web1($email,$fileId,$folderId,$user,$framework)
{
	updateTokenWeb($email,$framework);
	$db=$framework->get('DB');
	$data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));
        
	$client = new Google_Client();
	$client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE); 
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $driveService = new Google_Service_Drive($client);
	$emptyFileMetadata = new Google_Service_Drive_DriveFile();
	// Retrieve the existing parents to remove
        $file = $driveService->files->get($fileId, array('fields' => 'parents'));
        $previousParents = join(',', $file->parents);
	// Move the file to the new folder
        try{
	$file = $driveService->files->update($fileId, $emptyFileMetadata, array(
                                                                              'addParents' => $folderId,
                                                                              'removeParents' => $previousParents,
                                                                              'fields' => 'id, parents'));
        }catch (Exception $e) {
                if ($e->getCode() == 403 && ($e->getErrors()[0]["reason"] == "rateLimitExceeded"|| $e->getErrors()[0]["reason"] == "userRateLimitExceeded")) {
                    // Apply exponential backoff.
                   echo '[{"error_code":"403"}]';
                   
                 } else {
//                    // Other error, re-throw. 
//                    throw;
                 }
           }
         
        $id     = GibberishAES::enc($file->id, 'rotcoderaclla');
        $view='https://'.$_SERVER['HTTP_HOST'].'/interface/login/login_frame.php?param='.$id;
        $liview_linknk = "<a href=$view target=".'_blank'.">Document link</a>";
        $text='File moved to: '.  $filename.'--->'.$foldername;
        
        $user_cus=$framework->set('result',$db->exec("select movefile_settings from tbl_drivesync_authentication where email='$email'  order by id desc"));   
        $ser1=unserialize($user_cus[0]['movefile_settings']);                      
        $ser=explode(",",unserialize($ser1['user']));
   
        $myArray = array_filter( $ser );
        foreach($myArray as $val){
            $user_cus1=$framework->set('result',$db->exec("select email from tbl_user_custom_attr_1to1 where userid='".$val."'"));
            $user_cus2=$framework->set('result',$db->exec("select username from users where id='".$val."' and username!=''"));
            $user_nam=$user_cus2[0]['username'];
            $email_id=$user_cus1[0]['email'];
            /*********send Emr message notification******/
             $body="(Moved files )".$liview_linknk;
            $data3=$framework->set('result',$db->exec("INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                            values (date_format(now(), '%Y-%m-%d %H:%i'),'".$body."', 0, '$user_nam', 'Default', 1, 1, 'Moved files ', '".$user_nam."', 'new')"));
            $lastid =$framework->set('result',$db->exec("select LAST_INSERT_ID() as lastid")); 
            $data3  =$framework->set('result',$db->exec("INSERT INTO tbl_pnotes_file_relation (date, mid, doc_links, type)
                            values (NOW(),'".$lastid[0]['lastid']."','$link1','$category')"));
            
            
            //$smtp_details  =$f3->set('result',$db->exec("select gl_value from globals where gl_name IN('SMTP_HOST','SMTP_PORT','SMTP_PASS','SMTP_USER') "));
            
            /*********send email notification******/
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'html';
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465;
//            $mail->Host = $smtp_details[0]['SMTP_HOST'];
//            $mail->Port = $smtp_details[0]['SMTP_PORT'];
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
            $mail->Username = "srinus@smartmbbs.com";
            $mail->Password = "srinucnu@#";
//            $mail->Username = $smtp_details[0]['SMTP_USER'];
//            $mail->Password = $smtp_details[0]['SMTP_PASS'];
            $mail->setFrom($from, 'Smart MBBS');

        //    $toEmails = explode(";",$to);
            $toEmails = $email_id;
            if(count($toEmails)>1){
                foreach($toEmails as $eachEmail){
                    $mail->addAddress($eachEmail);
                }
            }
            else
            {
                $mail->addAddress($toEmails);
            }


            $mail->Subject =  $ser1['text'];
            $mail->msgHTML($ser1['text']."<br>".$view);
            $mail->AltBody = 'This is a plain-text message body';
            if($ser1['attach']=='yes'){
                $mail->AddAttachment($_SERVER["DOCUMENT_ROOT"].'/api/DriveSync/uploads/'.$originalFile, '', $encoding = 'base64', $type = 'application/octet-stream');
            }
            //send the message, check for errors
            if (!$mail->send()) {
                echo "Mailer Error: " . $mail->ErrorInfo;
                $mstatus = false;
            } else {
                $mstatus = true;
            }
        }
           
           
           
	$t=time();
//        $ins_log=$framework->set('result',$db->exec("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID)values(now(),'".$user."','".$email."','','','$view','','','$text','')"));
        updateLogWeb('Moving file',$t,$email,'Success',get_client_ip(),'File moved to: '.  $filename.'TO'.$foldername,$framework);
	
}
$f3->route('GET /deletefile_web/@useremail/@instance/@user/@category/@fileid',
function($f3)
{
	$email=$f3->get('PARAMS.useremail');
	$fileId=$f3->get('PARAMS.fileid');
        $user=$f3->get('PARAMS.user');
        $instance=$f3->get('PARAMS.instance');
        $category=$f3->get('PARAMS.category');
                
        
	updateTokenWeb($email,$f3);
	$db=$f3->get('DB');
        $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));
	$client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
        $deleted=$service->files->delete($fileId);
         if(empty($deleted))
            echo "Deleted";		
        else 
            echo "Failed";
	$t=time();
       
        updateLogWeb('File Deleted',$t,$email,'Success',get_client_ip(),'File Delete from: '.  $filename.'<---'.$useremail,$f3);
        $ins_log=$f3->set('result',$db->exec("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID,category)values(now(),'".$user."','".$email."','','','$fileId','','$fileId','Deleted from $category ','','".$category."')"));
			
});

function renameFileWeb($instance,$user,$category, $fileId, $newTitle,$email,$framework) {
    $useremail=$email;
 
    //update key
		
    updateTokenWeb($useremail,$framework);
 
    $db=$framework->get('DB'); 
    $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));
			
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $driveService = new Google_Service_Drive($client);

    $file = new Google_Service_Drive_DriveFile();
    $file->setName($newTitle);
		
    $updatedFile = $driveService->files->update($fileId, $file, array(
      'fields' => 'name'
    ));

    $t=time();
    
    if($updatedFile->name == $newTitle){
        echo "Updated";
        updateLogWeb('Renamed successfully',$t,$useremail,'Success',get_client_ip(),'Renamed file in drive:'.  $newTitle ,$framework);
        $rename=$framework->set('result',$db->exec("UPDATE tbl_pnotes_file_relation  SET file_name='$newTitle' where doc_links='$fileId'"));
        $ins_log=$framework->set('result',$db->exec("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID,category)values(now(),'".$user."','".$email."','','','$fileId','renamed as $newTitle','$fileId','Renamed from $category Updated','','".$category."')"));
    }else{
        echo "Failed";
        updateLogWeb('Rename failed',$t,$useremail,'Failed',get_client_ip(),'Renamed file in drive:'.  $newTitle ,$framework);
        $ins_log=$framework->set('result',$db->exec("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,watsID)values(now(),'".$user."','".$email."','','','$fileId','renamed as $newTitle','$fileId','Renamed from $category Failed','','".$category."')"));
    }

}

$f3->route('GET|POST|PUT /downloadfile_web/@email/@fileid', 
   function($f3){
        $email= $f3->get('PARAMS.email');
        $mediaid = $f3->get('PARAMS.fileid');
        updateTokenWeb($email,$f3);
        $db=$f3->get('DB');      
        $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));  
        $web = \Web::instance();
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
        $content = $service->files->get($mediaid, array('alt' => 'media' ));
        echo $content;
      //  file_put_contents("test.txt","Hello World. Testing!"); 
    }
);
$f3->route('GET|POST|PUT /getaccesstoken_web/@email', 
   function($f3){
        $email= $f3->get('PARAMS.email');
        $mediaid = $f3->get('PARAMS.fileid');
        updateTokenWeb($email,$f3);
        $db=$f3->get('DB');      
        $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));  
        $web = \Web::instance();
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
        $token=json_decode($data[0]['accesstoken']);
        echo $token->access_token;
//      $content = $service->files->export($mediaid, 'application/pdf', array(
//  'alt' => 'media' ));
        
       
      //  file_put_contents("test.txt","Hello World. Testing!"); 
        
        
       

    }
);

function searchFileWeb($email,$name,$framework) {
    $useremail=$email;
 
    //update key
		
    updateTokenWeb($useremail,$framework);
 
    $db=$framework->get('DB'); 
    $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));
			
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $driveService = new Google_Service_Drive($client);
    $$pageToken = null;
   
      $response = $driveService->files->listFiles(array(
        'q' => "name contains '$name'",
        'spaces' => 'drive',
       // 'pageToken' => $pageToken,
        'fields' => 'nextPageToken, files(id, name)',
      ));
      foreach ($response->files as $file) {
          printf("Found file: %s (%s)\n", $file->name, $file->id);
      }
    
}

function insert_folder_web($email,$folderId,$folder,$framework) {
    $useremail=$email;
 
    //update key
		
    updateTokenWeb($useremail,$framework);
 
    $db=$framework->get('DB'); 
    $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='" .$email."' order by id desc"));
			
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $driveService = new Google_Service_Drive($client);

    $folder_name=str_replace("_"," ",$folder);
    $fileMetadata = new Google_Service_Drive_DriveFile(array(
      'name' => $folder_name,
      'mimeType' => 'application/vnd.google-apps.folder',  
      'parents' => array($folderId)
    ));
   
  
    
    try {
        $file = $driveService->files->create($fileMetadata, array('fields' => 'id'));
        printf("%s\n", $file->id.":");
    } catch (Exception $e) {
        if ($e->getCode() == 404) {
            // Apply exponential backoff.
           echo '404 ERROR:';

         } else {
//                    // Other error, re-throw. 
//                    throw;
         }
    }
    
    $t=time();
    updateLogWeb('Create a new folder',$t,$useremail,'Sucess',get_client_ip(),'Created a new folder in drive:'.  $folder,$framework);
}

$f3->route('GET|POST|PUT /search_folder/@email/@foldername', 
   function($f3){
        $email= $f3->get('PARAMS.email');
        $foldername = $f3->get('PARAMS.foldername');
        updateTokenWeb($email,$f3);
        $db=$f3->get('DB');      
        $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));  
        $web = \Web::instance();
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);  
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
        $optParams = array(
          'pageSize' => 1000,
          'fields' => "nextPageToken, files(id, name,mimeType,parents)",     
//          'q' => "name contains '$foldername' and trashed = false and '$email' in owners and '0B_4Ba3GYJzSsZE43ZmN4bWpqY1k'  in parents",
           'q' => "name contains '$foldername' and trashed = false and '$email' in owners and starred = true ",
          
        );  
        
        //$folderID="not found";
        $results = $service->files->listFiles($optParams); 
         echo "<pre>"; print_r($results); echo "</pre>";    
        foreach ($results->getFiles() as $file) {
                
             $folderID[]=$file->getId();
        } 

        echo json_encode($folderID); 

    }
);
function listall_folderid($email,$folderId,$filetype,$framework)
{
        updateTokenWeb($email,$framework);
        $db     = $framework->get('DB');
        $data   = $framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
        
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
	$emptyFile = new Google_Service_Drive_DriveFile();	
        $orderby='';
        if($filetype == 'pdf'){
            $mimetypes = " and mimeType contains 'application/pdf'";
        }else if($filetype == 'folders'){
            $mimetypes = " and mimeType contains 'application/vnd.google-apps.folder'";
        }
        if($filetype == ''){
             $optParams = array(
            'fields' => "nextPageToken, files(id, name)",
            'q' => "'$folderId' in parents and trashed = false "
            
         );
        }else {
                    $optParams = array(
                            'fields' => "nextPageToken, files(id, name)",
                            'q' => "'$folderId' in parents and trashed = false ",
                            'orderBy' => "$orderby"
                        );
                }
        
        try {
              
               $results = $service->files->listFiles($optParams);
               
                $i = 0;          
                foreach ($results->getFiles() as $file) {
                    $filearray[$i] = $file->getId();
                    //$filearray[$i]['name'] = $file->getName();
                    $i++;
                }
             //  echo "<pre>"; print_r($filearray); echo "</pre>";
              echo json_encode($filearray);
           } catch (Exception $e) {
                if ($e->getCode() == 403 && ($e->getErrors()[0]["reason"] == "rateLimitExceeded"|| $e->getErrors()[0]["reason"] == "userRateLimitExceeded")) {
                    // Apply exponential backoff.
                   echo '[{"error_code":"403"}]';
                   
                 } else {
//                    // Other error, re-throw. 
//                    throw;
                 }
           }
}

function checkAttachments($estMsg)
  {
    if(($estMsg->type!=0) && ($estMsg->type!=1))
    {
      // Text and multipart parts will not be shown as attachments
      return(true);
    }
    else
    {
      // If there's no attachments, parts inside will be checked
      if($estMsg->parts)
      {
        $partMsg=$estMsg->parts;
        $i=0;
        // Parts will be checked while no attachments found or not all of them checked
        while(!(checkAttachments($partMsg[$i])) && ($i<sizeof($estMsg->parts)))
        {
          $i++;
        }

        // If any 'checkAttachment' calls returned 'true', 'i' should be
        // equal to number of parts(after increased in while). So, no
        // attachments found
        if($i==sizeof($estMsg->parts))
        {
          return(false);
        }
        else
        {
          return(true);
        }
      }
      else
      {
        // If no parts and text or multipart type, no attachments
        return(false);  
      }
    }
  }
function storeEmail_web_backup($email,$pwd,$framework)
{ 
	updateTokenWeb($email,$framework);
        $db=$framework->get('DB');
       
        $pwd=base64_decode($pwd); 
       
        $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
        $filename='';
          
        //parent folder to store email attachment
        $data1=$framework->set('result',$db->exec("select email_parent_folder from tbl_drivesync_authentication where email='$email'  order by id desc"));
        $parentfolderid=str_replace("https://drive.google.com/drive/folders/","",$data1[0]['email_parent_folder']);
        //$parentfolderid='root';

        
	$imapPath = '{imap.gmail.com:993/imap/ssl}INBOX';
//	$username = 'bhavyae@smartmbbs.com';
//	$password = 'echambadi@528';
	
	// try to connect
	$inbox = imap_open($imapPath,$email,$pwd) or die('Cannot connect to Gmail: ' . imap_last_error());
        $emails = imap_search($inbox,'UNSEEN');
        $em=count($emails);
        //$max_emails = 1;
	if($emails) {
            //$count = 1;
            /* put the newest emails on top */
            rsort($emails);
            $bh=0; $attach=0;
	    /* for every email... */
            foreach($emails as $email_number){
                 /* get information specific to this email */
                $overview = imap_fetch_overview($inbox,$email_number,0);
                /* get mail message */
                $message = imap_fetchbody($inbox,$email_number,1.1);
               
                /* get mail structure */
                $structure = imap_fetchstructure($inbox, $email_number);
                
                  if(checkAttachments($structure))
                  {
                     $attach++;
                       $folder_name_format=trim(str_replace(" ","_",date('Y-m-d H:i:s')."_".$overview[0]->from),"_"); 
                       if($parentfolderid==''){
                                $fileMetadata = new Google_Service_Drive_DriveFile(array(
                                    'name' => $folder_name_format
                                ));
                            }
                            else{
                                    $fileMetadata = new Google_Service_Drive_DriveFile(array(
                                        'name' => $folder_name_format,
                                        'mimeType' => 'application/vnd.google-apps.folder',
                                        'parents' => array($parentfolderid)
                                    ));
                            }
                   
                            //to create folder for each mail
                            $createdFolder = $service->files->create($fileMetadata, array('fields' => 'id' ));
                            if($message!=''){
                                $fileMetadata1 = new Google_Service_Drive_DriveFile(array(
                                        'name' => $folder_name_format,
                                        'mimeType' => 'application/pdf',
                                        'parents' => array($createdFolder->id)
                                    ));
                                $createdFile1 = $service->files->create($fileMetadata1, array(
                                                          'data' => $message,
                                                          'mimeType' => 'application/pdf',
                                                          'uploadType' => 'multipart',
                                                          'fields' => 'id'
                                            )); 
                        }
                  }else{
                      if($bh==$em){
                          echo "There is no attachments";
                      }
                      $bh++;
                  }
                $attachments = array();
                    /* if any attachments found... */
                if(isset($structure->parts) && count($structure->parts)){
                  
                    for($i = 0; $i < count($structure->parts); $i++) 
                    {
                        
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );
                       
                        if($structure->parts[$i]->ifdparameters) 
                        {
                           foreach($structure->parts[$i]->dparameters as $object) 
                            {
                                if(strtolower($object->attribute) == 'filename') 
                                {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                }
                                if($structure->parts[$i]->ifparameters) 
                                {
                                    foreach($structure->parts[$i]->parameters as $object) 
                                    {
                                        if(strtolower($object->attribute) == 'name') 
                                        {
                                            $attachments[$i]['is_attachment'] = true;
                                            $attachments[$i]['name'] = $object->value;
                                        }
                                    }
                                }
                                 if($attachments[$i]['is_attachment']) 
                                {
                                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);

                                    /* 4 = QUOTED-PRINTABLE encoding */
                                    if($structure->parts[$i]->encoding == 3) 
                                    { 
                                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                                    }
                                    /* 3 = BASE64 encoding */
                                    elseif($structure->parts[$i]->encoding == 4) 
                                    { 
                                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                    }
                                }
                            }
                           
                        }

                        
                    }
                     foreach($attachments as $attachment)
                    {
                       
                        if($attachment['is_attachment'] == 1)
                        {
                            $filename = $attachment['name'];
                            if(empty($filename)) $filename = $attachment['filename'];

                            if(empty($filename)) $filename = time() . ".dat";

                            /* prefix the email number to the filename in case two emails
                             * have the attachment with the same file name.
                             */
                             $fp = fopen($filename, "w+");
                            fwrite($fp, $attachment['attachment']);
                            fclose($fp);
                            //create folder id to save attachments
                            //echo $createdFolder->id;
                            inserIntoFolder($createdFolder->id,$filename,$email,$framework);


                        }
                       
                    }
                    
                }
                if(checkAttachments($structure))
                {
                     $user_cus=$framework->set('result',$db->exec("select userid,emr_msg,drive_email,email from tbl_user_custom_attr_1to1 where emr_msg!='' and drive_email!=''"));
                   
                    for($i=0;$i<count($user_cus);$i++){
                        $user_cus1=$framework->set('result',$db->exec("select username,email from users where id='".$user_cus[$i]['userid']."' and username!=''"));
                        $user_nam=$user_cus1[0]['username'];
                        $email_id=$user_cus[$i]['email'];
                        if($user_cus[$i]['emr_msg']=='YES'){
                            $data3=$framework->set('result',$db->exec("INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                            values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."($message)".' '."https://drive.google.com/drive/folders/$createdFolder->id'), 0, '$user_nam', 'Default', 1, 1, 'Store Email Attachment', '".$user_nam."', 'new')"));
                        }
                        if($user_cus[$i]['drive_email']=='YES'){
                            $to = $email_id;
                            $subject = "Email Attachment Folder Link";
                            $link='https://' . $_SERVER['HTTP_HOST'] . '/interface/patient_file/summary/ggl_drive_folders.php?folder='.$createdFolder->id;
                            $email_content=$message."<br>".$link;

                            // Always set content-type when sending HTML email
                            $headers = "MIME-Version: 1.0" . "\r\n"; 
                            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                            // More headers 
                           // $sender='bhavyae@smartmbbs.com';
                            $headers .= 'From: <'.$email.'>' . "\r\n";
                           $mail=mail($to,$subject,$email_content,$headers);  
                           if($mail){
                              //echo "Thank you for using our mail form";
                            }else{
                             // echo "Mail sending failed."; 
                            }
                        }
                    }
                     
                }
                  
            }
            
            $cn=$em-$bh; //remove non attachment emails from count of emails
            if($attach==$cn){
                 echo "Success fully stored email attachments";
            }
        } 
        else{
            echo "emails not found";
        }
 /* close the connection */
imap_close($inbox);
          //$server = new Email_reader();
	//$server->inbox();
	//read email content and attachment
	//save the attachment in local folder with unique id
	//update attachment to drive emails folder
	//unlink the local file	
	//done
	$t=time();
    updateLogWeb('Save attachment in to drive',$t,$email,'Success',get_client_ip(),'Saved attachement: '.  $filename,$framework);
	
}


  
  function flattenParts($messageParts, $flattenedParts = array(), $prefix = '', $index = 1, $fullPrefix = true) {
        echo "<pre>"; print_r($messageParts); echo "</pre>";
	foreach($messageParts as $part) {
		$flattenedParts[$prefix.$index] = $part;
		if(isset($part->parts)) {
			if($part->type == 2) {
				$flattenedParts = flattenParts($part->parts, $flattenedParts, $prefix.$index.'.', 0, false);
			}
			elseif($fullPrefix) {
				$flattenedParts = flattenParts($part->parts, $flattenedParts, $prefix.$index.'.');
			}
			else {
				$flattenedParts = flattenParts($part->parts, $flattenedParts, $prefix);
			}
			unset($flattenedParts[$prefix.$index]->parts);
		}
		$index++;
	}

	return $flattenedParts;
			
}
function getPart($connection, $messageNumber, $partNumber, $encoding) {
	
	$data = imap_fetchbody($connection, $messageNumber, $partNumber);
	switch($encoding) {
		case 0: return $data; // 7BIT
		case 1: return $data; // 8BIT
		case 2: return $data; // BINARY
		case 3: return base64_decode($data); // BASE64
		case 4: return quoted_printable_decode($data); // QUOTED_PRINTABLE
		case 5: return $data; // OTHER
	}
	
	
}

function getFilenameFromPart($part,$dposition) {

	$filename = '';
        if($part->disposition==$dposition){
            if($part->ifdparameters) {
		foreach($part->dparameters as $object) {
			if(strtolower($object->attribute) == 'filename') {
				$filename = $object->value;
			}
		}
	}

	if(!$filename && $part->ifparameters) {
		foreach($part->parameters as $object) {
			if(strtolower($object->attribute) == 'name') {
				$filename = $object->value;
			}
		}
	}
            
            
            
            
            
        }
	
	return $filename;
	
}
function storeEmail_web($email,$pwd,$unique_ids,$framework)
{ 
	updateTokenWeb($email,$framework);
        $db=$framework->get('DB');
       
        //$pwd=base64_decode($pwd); 
       // $pwd=GibberishAES::dec(str_replace('@','/',$pwd), 'rotcoderaclla');      
        $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
        $filename='';
          
        //parent folder to store email attachment
        $data1=$framework->set('result',$db->exec("select email_parent_folder,email_to_users,imap_pwd from tbl_drivesync_authentication where email='$email'  order by id desc"));
        $parentfolderid=str_replace("https://drive.google.com/drive/folders/","",$data1[0]['email_parent_folder']);
        
        $pwd=GibberishAES::dec($data1[0]['imap_pwd'], 'rotcoderaclla');
        //$parentfolderid='root';
        $imapPath = '{imap.gmail.com:993/imap/ssl}INBOX';

	
	// try to connect
	$inbox = imap_open($imapPath,$email,$pwd) or die('Cannot connect to Gmail: ' . imap_last_error());
        $emails = json_decode($unique_ids);
        $em=count($emails);
        //$max_emails = 1;
	if($emails) {
            /* put the newest emails on top */
            rsort($emails);
            $attach=0;
	    /* for every email... */
            foreach($emails as $email_number){
                /* get information specific to this email */
                $overview = imap_fetch_overview($inbox,$email_number,0);
                /* get mail message */
                $message = imap_fetchbody($inbox,$email_number,0); 
                           
                
                /* get mail structure */
                $structure = imap_fetchstructure($inbox, $email_number);
//                 echo "<pre>"; print_r($structure); echo "</pre>";
                
                //folder creation for each email
                        $folder_name_format=trim(str_replace(" ","_",date('Y-m-d H:i:s')."_".$overview[0]->from),"_"); 
                       if($parentfolderid==''){
                            $fileMetadata = new Google_Service_Drive_DriveFile(array(
                                'name' => $folder_name_format
                            ));
                        }
                        else{
                            $fileMetadata = new Google_Service_Drive_DriveFile(array(
                                'name' => $folder_name_format,
                                'mimeType' => 'application/vnd.google-apps.folder',
                                'parents' => array($parentfolderid)
                            ));
                        }
                   
                        
                        $createdFolder = $service->files->create($fileMetadata, array('fields' => 'id' ));
               // decode7Bit($message);
                $flattenedParts = flattenParts($structure->parts);
                //echo "<pre>"; print_r($flattenedParts); echo "</pre>"; 
                foreach($flattenedParts as $partNumber => $part) {
                    
                    switch($part->type) {

                            case 0:
                                    // the HTML or plain text part of the email

                                     $message = getPart($inbox, $email_number, $partNumber, $part->encoding); 
                                     
                                    // now do something with the message, e.g. render it
                            break;

                            case 1:
                                    // multi-part headers, can ignore

                            break;
                            case 2:
                                    // attached message headers, can ignore
                            break;

                            case 3: // application
                            case 4: // audio
                            case 5: // image
                            case 6: // video
                            case 7: // other
                                   $filename = getFilenameFromPart($part,'ATTACHMENT');
                                    if($filename) {
                                            // it's an attachment
                                            $attachment = getPart($inbox, $email_number, $partNumber, $part->encoding);
                                            $fp = fopen($filename, "w+");
                                            fwrite($fp, $attachment);
                                            fclose($fp);
                                            //create folder id to save attachments
                                            //echo $createdFolder->id;
                                            inserIntoFolder($createdFolder->id,$filename,$email,$framework);
                                            
                                    }
//                                    else {
//                                         preg_match_all('/src="cid:(.*)"/Uims', $message, $matches);
////                                         echo count($matches);
////                                         echo "<pre>"; print_r($matches); echo "</pre>";
//                                         
//                                        if(count($matches)) {
//                                                $search = array();
//                                                $replace = array();
//                                                foreach($matches[1] as $match) {
////                                                    $image=''; $imagename='';
////                                                    $imagename=explode("@",$match);
//                                                                                        
//                                                    echo  $filename = getFilenameFromPart($part,'INLINE');
////                                                  
////                                                    if($imagename[0]!='') $filename=$imagename[0];
//                                                   // if($filename){
//                                                    
//                                                        $image = getPart($inbox, $email_number, $partNumber, $part->encoding);
//                                                        $uniqueFilename = $filename;
//                                                        file_put_contents("email_attachments/$uniqueFilename", $image);
//                                                        $search[] = "src=\"cid:$match\"";
//                                                        $replace[] = "src=\"https://qa2allcare.texashousecalls.com/api/DriveSync/email_attachments/$uniqueFilename\"";
//                                                        $message= str_replace($search, $replace, $message);
//                                                    //}
//                                                }
//                                        } 
//                                    }
                                 
                            break;
                     }
                   
               }
                
                
                $attach++;
                if($message!=''){
                      
//                      $myfile = fopen("email_attachments/".$folder_name_format.".html", "w+") or die("Unable to open file!");
//                      fwrite($myfile, $message);
//                      fclose($myfile);
//                      $encurl=base64_encode("https://" . $_SERVER['HTTP_HOST'] . "api/DriveSync/email_attachments/" . $folder_name_format . '.html');
                   AttachmentPdf($email,$message,$folder_name_format,$createdFolder->id,$framework);
                   //to creaTE PDF FOR EMAIL MSG BODY
//                    $fileMetadata1 = new Google_Service_Drive_DriveFile(array(
//                                        'name' => $folder_name_format,
//                                        
//                                        'parents' => array($createdFolder->id)
//                                    ));
//                                $createdFile1 = $service->files->create($fileMetadata1, array(
//                                                          'data' => $message,
//                                                          'mimeType' => 'application/pdf',
//                                                          'uploadType' => 'multipart',
//                                                          'fields' => 'id'
//                                            )); 
                   $user_cus=$framework->set('result',$db->exec("select email_parent_folder,store_email_settings from tbl_drivesync_authentication where email='$email'  order by id desc"));
                   // $user_cus=$framework->set('result',$db->exec("select userid,emr_msg,drive_email,email from tbl_user_custom_attr_1to1 where emr_msg!='' and drive_email!=''"));
                  
                   $ser1=unserialize($user_cus[0]['store_email_settings']);                      
                   $ser=explode(",",unserialize($ser1['user']));        
                    foreach($ser as $val){
                        $user_cus1=$framework->set('result',$db->exec("select userid,email from tbl_user_custom_attr_1to1 where userid='$val'"));
                        $user=$framework->set('result',$db->exec("select username,concat(fname,',',lname) as name from users where id='$val'"));
                         if($user[0]['username']!=''){
                              $user_nam=$user[0]['username'];
                         }else { 
                              $user_nam=$user[0]['name'];
                         }
                         
                        $email_id=$user_cus[$i]['email'];
                        $id= GibberishAES::enc($createdFolder->id, 'rotcoderaclla');
                        $type= GibberishAES::enc('attachments', 'rotcoderaclla');
                        $individual_link='https://'.$_SERVER['HTTP_HOST'].'/interface/login/login_frame.php?param='.$id.'&type='.$type;  
                        $view_link = "<a href=$individual_link target=".'_blank'.">Folder link</a>";
                        $data3=$framework->set('result',$db->exec("INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
                        values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."(folder location)".' '."$view_link'), 0, '$user_nam', 'Default', 1, 1, 'Store Email Attachment', '".$user_nam."', 'new')"));
                        
                        
                            
                        $mail = new PHPMailer;
                           
                        $mail->isSMTP();
                        $mail->SMTPDebug = 0;
                        $mail->Debugoutput = 'html';
                        $mail->Host = "smtp.gmail.com";
                        $mail->Port = 465;
                        $mail->SMTPAuth = true;
                        $mail->SMTPSecure = "ssl";
                        $mail->Username = "srinus@smartmbbs.com";
                        $mail->Password = "srinucnu@#"; 
                        $mail->setFrom($from, 'Smart MBBS');

                        //    $toEmails = explode(";",$to);
                            $toEmails = $email;
                            if(count($toEmails)>1){
                                foreach($toEmails as $eachEmail){
                                    $mail->addAddress($eachEmail);
                                }
                            }
                            else
                            {
                                $mail->addAddress($toEmails);
                            }

                            $mail->Subject =$ser1['text'];
                            $mail->msgHTML($ser1['text']."<br>".$individual_link);
                            $mail->AltBody = 'This is a plain-text message body';

                            //send the message, check for errors
                            if (!$mail->send()) {
                                echo "Mailer Error: " . $mail->ErrorInfo;
                                $mstatus = false;
                            } else {
                                $mstatus = true;
                            }
                        }
                     }
            }
             if($attach==$em){
                $cnt=$attach-$em;
                 echo $cnt.":Success fully stored email attachments";
            }
        } 
        
 /* close the connection */
imap_close($inbox);
          //$server = new Email_reader(); 
	//$server->inbox();
	//read email content and attachment
	//save the attachment in local folder with unique id
	//update attachment to drive emails folder
	//unlink the local file	
	//done
	$t=time();
    updateLogWeb('Save attachment in to drive',$t,$email,'Success',get_client_ip(),'Saved attachement: '.  $filename,$framework);
	
}
function storeEmailCount($email,$pwd,$framework){
        updateTokenWeb($email,$framework);
        $db=$framework->get('DB');
        
       // $pwd=base64_decode($pwd); 
       // $pwd=GibberishAES::dec($pwd, 'rotcoderaclla');   
   
        $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
        $filename='';
          
       
        
	$imapPath = '{imap.gmail.com:993/imap/ssl}INBOX';
//	$username = 'bhavyae@smartmbbs.com';
//	$password = 'echambadi@528';
       
	 $data2=$framework->set('result',$db->exec("select imap_pwd from tbl_drivesync_authentication where email='$email'  order by id desc limit 0,1"));
         
       
        $pwd=GibberishAES::dec($data2[0]['imap_pwd'], 'rotcoderaclla');  
	// try to connect
	$inbox = imap_open($imapPath,$email,$pwd) or die('Cannot connect to Gmail: ' . imap_last_error());
        $emails = imap_search($inbox,'UNSEEN');
        $em=count($emails);
        //$max_emails = 1;
	if($emails) {
            /* put the newest emails on top */
            rsort($emails);
             $attach=0; 
	    /* for every email... */
            foreach($emails as $email_number){
                /* get mail structure */
                $structure = imap_fetchstructure($inbox, $email_number);
               // echo "<pre>"; print_r($structure); echo "</pre>";
                if(checkAttachments($structure))
                {
                     $attached_email[$attach]=$email_number;
                     $attach++;
                }
            }
           
            echo json_encode($attached_email);
          
       }else{
            echo "emails not found";
        }
 /* close the connection */
imap_close($inbox);
}

$f3->route('GET|POST|PUT /uploadfile_web/@email/@instance/@user/@folderId/@category',
    function($f3){
        $f3->set('UPLOADS','uploads/'); // don't forget to set an Upload directory, and make it writable!
        $email=	$f3->get('PARAMS.email');	
        $folderId   = $f3->get('PARAMS.folderId');
        $category   = $f3->get('PARAMS.category');
        $instance   = $f3->get('PARAMS.instance');
        $user   = $f3->get('PARAMS.user');
        
        updateTokenWeb($email,$f3);
        
        $db=$f3->get('DB');						
        $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));	 
        $web = \Web::instance();

        $overwrite = true; // set to true, to overwrite an existing file; Default: false
        $slug = true; // rename file to filesystem-friendly version
       
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);

        
        $mime_types= array(
                "xls" =>'application/vnd.ms-excel',
                "xlsx" =>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                "xml" =>'text/xml',
                "ods"=>'application/vnd.oasis.opendocument.spreadsheet',
                "csv"=>'text/plain',
                "tmpl"=>'text/plain',
                "pdf"=> 'application/pdf',
                "php"=>'application/x-httpd-php',
                "jpg"=>'image/jpeg',
                "png"=>'image/png',
                "gif"=>'image/gif',
                "bmp"=>'image/bmp',
                "txt"=>'text/plain',
                "doc"=>'application/msword',
                "js"=>'text/js',
                "swf"=>'application/x-shockwave-flash',
                "mp3"=>'audio/mpeg',
                "zip"=>'application/zip',
                "rar"=>'application/rar',
                "tar"=>'application/tar',
                "arj"=>'application/arj',
                "cab"=>'application/cab',
                "html"=>'text/html',
                "htm"=>'text/html',
                "default"=>'application/octet-stream',
                "folder"=>'application/vnd.google-apps.folder'
        );
        
        $files = $web->receive( 
                function($file,$formFieldName){
                        
                    return true; // allows the file to be moved from php tmp dir to your defined upload dir
                   
                },
                $overwrite,
                $slug
        );
                
        if($category=='patients'){
//                            echo "select * from patient_data where patient_folder LIKE '%$folderId%'";
            $sql=$f3->set('result',$db->exec("select * from patient_data where patient_folder LIKE '%$folderId%'"));
            
            $obj_id=$sql[0]['lname'].",".$sql[0]['fname'];
            $obj_id1=$sql[0]['pid'];
        }else if($category=='users'){
            $sql=$f3->set('result',$db->exec("select username from tbl_user_custom_attr_1to1 uc inner join users u ON u.id=uc.userid where user_drive_folder LIKE '%$folderId%'"));
            
            $obj_id=$sql[0]['username'];
            $obj_id1=$sql[0]['userid'];
        }else if($category=='insurance'){
            $sql=$f3->set('result',$db->exec("select name from tbl_inscomp_custom_attr_1to1 ic inner join  insurance_companies i on i.id=ic.insuranceid where payer_folder LIKE '%$folderId%'"));
            
            $obj_id=$sql[0]['name'];
            $obj_id1=$sql[0]['insuranceid'];
        }else if($category=='pharmacy'){
            $sql=$f3->set('result',$db->exec("select name from tbl_pharmacy_custom_attributes_1to1 pc inner join pharmacies p on p.id=pc.pharmacyid where pharmacy_folder LIKE '%$folderId%'"));
            
            $obj_id=$sql[0]['name'];
            $obj_id1=$sql[0]['pharmacyid'];
        }else if($category=='address_Book'){
            $sql=$f3->set('result',$db->exec("select addrbk_type_id from tbl_addrbk_custom_attr_1to1 where addrbk_folder LIKE '%$folderId%'"));
            
            $obj_id=$sql[0]['addrbk_type_id'];
            $obj_id1=$sql[0]['pid'];
        }else if($category=='facility'){
            $sql=$f3->set('result',$db->exec("select f.name from tbl_facility_custom_attr_1to1 fc inner join facility f on f.id=fc.facilityid where facilityfolder LIKE '%$folderId%'"));
            
            $obj_id=$sql[0]['name'];
            $obj_id1=$sql[0]['facilityid'];
        }              
                
       $link=''; $filename='';
       
       foreach($files as $key => $val){
            $filename=explode("/",$key);
            $filename=$filename[1];
            $originalFile=$filename;

            chmod_r("uploads", 0777, 0777);

            $data1 = file_get_contents("uploads/".$originalFile);

            $extension=getFileExtension($originalFile);
            
            $fileMetadata = new Google_Service_Drive_DriveFile(array(
                'name' => $originalFile,
                'parents' => array($folderId)
            ));
            $createdFile = $service->files->create($fileMetadata, array(
                'data' => $data1,
                'mimeType' => $mime_types[$extension],
                'uploadType' => 'multipart',
                'fields' => 'id, name'
             ));

             //$individual_link='https://'.$_SERVER['HTTP_HOST'].'/interface/login/login_frame.php?param='.$createdFile->id;
               $individual_link='https://'.$_SERVER['HTTP_HOST'].'/interface/main/allcarereports/view_file.php?file_id='.$createdFile->id;
            
               
             $ins_log=$f3->set('result',$db->exec("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name ,file_id,status,category)"
                                                 . "values(now(),'".$user."','".$email."','','$obj_id','$individual_link','$originalFile','$createdFile->id','files uploaded in $category','$category')"));
        } 
        
	$link1=trim($individual_link,"||");	
        $id     = GibberishAES::enc($createdFile->id, 'rotcoderaclla');
        if($instance=='emr'){
               $view='https://'.$_SERVER['HTTP_HOST'].'/interface/login/login_frame.php?param='.$id;
               $liview_linknk = "<a href=$view target=".'_blank'.">Document link</a>";
               $msg_view='https://'.$_SERVER['HTTP_HOST'].'/interface/main/allcarereports/view_file.php?file_id='.$createdFile->id;
               $msglink="<a href=$msg_view target=".'_blank'.">Document link</a>";
        }else if($instance=='provider_portal'){
               $view='https://'.$_SERVER['HTTP_HOST'].'/providers/index.php?param='.$id;
               $liview_linknk =  "<a href=$view target=".'_blank'.">Document link</a>";
               $msg_view='https://'.$_SERVER['HTTP_HOST'].'/interface/main/allcarereports/view_file.php?file_id='.$createdFile->id;
               $msglink="<a href=$msg_view target=".'_blank'.">Document link</a>";
        }else if($instance=='agency_portal'){
               $view='https://'.$_SERVER['HTTP_HOST'].'/agencies/index.php?param='.$id;
               $liview_linknk =  "<a href=$view target=".'_blank'.">Document link</a>";
               $msg_view='https://'.$_SERVER['HTTP_HOST'].'/interface/main/allcarereports/view_file.php?file_id='.$createdFile->id;
               $msglink="<a href=$msg_view target=".'_blank'.">Document link</a>";
        }
        
         $data3=$f3->set('result',$db->exec("INSERT INTO tbl_allcare_custom_messages (date, body, obj_id, user, groupname, 
                                            activity, authorized, title,assigned_to, message_status,object_type , priority)
                                            values (date_format(now(), '%Y-%m-%d %H:%i'),'', "
                                            . "'".$message_array['linkto']."', '$user', 'Default', 1, 1, "
                                            . "'".$message_array['Message_type']."', '".$message_array['assigned_to']."', "
                                            . "'no message','".$message_array['obj_type']."','".$message_array['priority']."')"));
        $lastid =$f3->set('result',$db->exec("select LAST_INSERT_ID() as lastid")); 
        $data3  =$f3->set('result',$db->exec("INSERT INTO tbl_pnotes_file_relation (date, mid, doc_links, type, file_name)
                            values (NOW(),'".$lastid[0]['lastid']."','$createdFile->id','$category','$createdFile->name')"));
        echo $createdFile->id.":".$lastid[0]['lastid'];
        unlink("uploads/".$originalFile);
    }
);

//get next page token (not working)
$f3->route('GET|POST|PUT /gettoken/@email', 
   function($f3){
        $email= $f3->get('PARAMS.email');
       
        updateTokenWeb($email,$f3);
        $db=$f3->get('DB');      
        $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));  
        $web = \Web::instance();
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline"); 
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
//        $response1 = $service->changes->getStartPageToken();
//       printf("next token: %s\n", $response1->startPageToken);
//        $savedStartPageToken=$response1->nextPageToken;
//       echo  $pageToken = $savedStartPageToken;
//        while ($pageToken != null) {
//            $response = $service->changes->listChanges($pageToken, array(
//              'fields' => "*",
//              'spaces' => 'drive'
//            ));
//            echo "<pre>"; print_r($response); echo "</pre>";
//            foreach ($response->changes as $change) {
//                // Process change
//                printf("Change found for file: %s", $change->fileId);
//            }
//            if ($response->newStartPageToken != null) {
//                // Last page, save this token for the next polling interval
//                $savedStartPageToken = $response->newStartPageToken;
//            }
//            $pageToken = $response->nextPageToken;
//        }
                function retrieveAllFiles($service) {
                      $result = array();
                      $pageToken = NULL;

                      //do {
                        try {
                          $parameters = array();
                          if ($pageToken) {
                            $parameters['pageToken'] = $pageToken;
                          }
                          print_r($parameters);
                          $files = $service->files->listFiles($parameters);
print_r($files);
                          //$result = array_merge($result, $files->getFiles());
                          //$pageToken = $files->getNextPageToken();
                        } catch (Exception $e) {
                          print "An error occurred: " . $e->getMessage();
                          $pageToken = NULL;
                        }
                    //  } while ($pageToken);
                      return $files;
                }
                echo retrieveAllFiles($service);
    }
);

function test_pdf($email,$folderId,$folder,$framework) {
    $useremail=$email;
 
    //update key
		
    updateTokenWeb($useremail,$framework);
 
    $db=$framework->get('DB'); 
    $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='" .$email."' order by id desc"));
			
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $driveService = new Google_Service_Drive($client);

    
    $fileMetadata = new Google_Service_Drive_DriveFile(array(
      'name' => $folder,
      'mimeType' => 'application/pdf',  
      'parents' => array($folderId)
    ));
   
  
    
    try {
        $file = $driveService->files->create($fileMetadata, array('fields' => 'id'));
        printf("%s\n", $file->id.":");
    } catch (Exception $e) {
        if ($e->getCode() == 404) {
            // Apply exponential backoff.
           echo '404 ERROR:';

         } else {
//                    // Other error, re-throw. 
//                    throw;
         }
    }
    
   
}

function sharefileAnyone($email,$fileId,$framework)
{
    updateTokenWeb($email,$framework);
    $db=$framework->get('DB');
    $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
			
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $driveService = new Google_Service_Drive($client);
			
    $driveService->getClient()->setUseBatch(true);
    $batch = $driveService->createBatch();
    $userPermission = new Google_Service_Drive_Permission(array(
				'type' => 'anyone',
				'role' => 'reader',
    ));
    $request = $driveService->permissions->create(
    $fileId, $userPermission, array('fields' => 'id'));
    $batch->add($request, 'anyone');
//    $domainPermission = new Google_Service_Drive_Permission(array(
//            'type' => 'domain',
//            'role' => 'reader',
//            'domain' => 'google.com'
//      ));
//    $request = $driveService->permissions->create(
//    $fileId, $domainPermission, array('fields' => 'id'));
//      $batch->add($request, 'domain');
    $results = $batch->execute();                      
    foreach ($results as $result) {
            if ($result instanceof Google_Service_Exception) {
              // Handle error
              echo $result;
            } else {
              echo $result->id;
            }
    }

	$t=time();
    updateLogWeb('Sharing file',$t,$email,'Success',get_client_ip(),'File Shared to: '.  $filename.'--->'.$touseremail,$framework);
}
//delete file permissions
$f3->route('GET|POST|PUT|DELETE /delete_permission/@email/@fileid/@permission', 
   function($f3){
        $email= $f3->get('PARAMS.email');
        $fileId = $f3->get('PARAMS.fileid');
        $Permission_id = $f3->get('PARAMS.permission');

        updateTokenWeb($email,$f3);
        $db=$f3->get('DB');      
        $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));  
        $web = \Web::instance();
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
         $service->permissions->delete($fileId, $Permission_id);
    }
);

//to check folder or files  exists 
$f3->route('GET /isfolder/@email/@foldername/@filetype',
function($f3)
{
	$email=$f3->get('PARAMS.email');
        $foldername=$f3->get('PARAMS.foldername');
        $type=$f3->get('PARAMS.filetype');
        updateTokenWeb($email,$f3);
        $db     = $f3->get('DB');
        $data   = $f3->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
        
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
        if($filetype == 'pdf'){
            $mimetypes = " and mimeType contains 'application/pdf'";
        }else if($filetype == 'folders'){
            $mimetypes = " and mimeType contains 'application/vnd.google-apps.folder'";
        }
        $optParams = array(
                            'fields' => "nextPageToken, files(id, name)",
                            'q' => "name='$foldername'  and trashed = false ",
                            'mimeType' => $mimetypes
                        );
          
        try {
               $i=0;
               $results = $service->files->listFiles($optParams);
               foreach ($results->getFiles() as $file) {
                    $filearray[$i] = $file->getId();
                    $i++;
                }
                echo json_encode($filearray);            
           } catch (Exception $e) {
                if ($e->getCode() == 403 && ($e->getErrors()[0]["reason"] == "rateLimitExceeded"|| $e->getErrors()[0]["reason"] == "userRateLimitExceeded")) {
                    // Apply exponential backoff.
                   echo '[{"error_code":"403"}]';
                   
                 } else {
//                    // Other error, re-throw. 
//                    throw;
                 }
           }
});
//to add comments
$f3->route('GET|POST|PUT /addcomment/@email/@fileid/@comments', 
   function($f3){
        $email= $f3->get('PARAMS.email');
        $fileId = $f3->get('PARAMS.fileid');
        $content = base64_decode($f3->get('PARAMS.comments'));

        updateTokenWeb($email,$f3);
        $db=$f3->get('DB');      
        $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));  
        $web = \Web::instance();
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
                            
        $newComment = new Google_Service_Drive_Comment();
        $newComment->setContent($content);
        $newComment->content = $content; 
        $newComment->htmlContent = $content; 
                            
         // $revisions = $service->revisions->listRevisions($fileId);

        try {
          return $service->comments->create($fileId, $newComment,array('fields'=>'htmlContent'));
        } catch (Exception $e) {
          print "An error occurred: " . $e->getMessage();
        }
            
});
//to get list of  comments 
$f3->route('GET|POST|PUT /commentlist/@email/@fileid', 
   function($f3){
        $email= $f3->get('PARAMS.email');
        $fileId = $f3->get('PARAMS.fileid');
        

        updateTokenWeb($email,$f3);
        $db=$f3->get('DB');      
        $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));  
        $web = \Web::instance();
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
       
        $newComment = new Google_Service_Drive_Comment();
          
        try {
             $new_arr=[];               
             $comments = $service->comments->listComments($fileId,array('fields'=>'comments'));
             for($i=0; $i<count($comments->comments); $i++){
                $new_arr[$comments->comments[$i]->id]=$comments->comments[$i]->content;
                $j=0; $replied=[];
                if(!empty($comments->comments[$i]->replies)){
                    for($j=0; $j<count($comments->comments[$i]->replies); $j++){
                      $replied[$comments->comments[$i]->replies[$j]->id]=$comments->comments[$i]->replies[$j]->htmlContent;
                      $new_arr[$comments->comments[$i]->id.'_replies']=$replied;
                    }
                }
            }
            
            return json_encode($new_arr);
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
});

//reply to comment
$f3->route('GET|POST|PUT /reply_comment/@email/@fileid/@commentid/@content', 
   function($f3){
        $email= $f3->get('PARAMS.email');
        $fileId = $f3->get('PARAMS.fileid');
        $commentid = $f3->get('PARAMS.commentid');
        $content = base64_decode($f3->get('PARAMS.content'));

        updateTokenWeb($email,$f3);
        $db=$f3->get('DB');      
        $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));  
        $web = \Web::instance();
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
       
        $newReply = new Google_Service_Drive_Reply();
          
        $newReply->setContent($content);
        $newReply->content = $content; 
        $newReply->htmlContent = $content; 
                            
         // $revisions = $service->revisions->listRevisions($fileId);

        try {
          return $service->replies->create($fileId, $commentid,$newReply,array('fields'=>'htmlContent'));
        } catch (Exception $e) {
          print "An error occurred: " . $e->getMessage();
        }
});
//to copy a file 
$f3->route('GET|POST|PUT /copyfile/@email/@originalfileid/@title/@folderid', 
    function($f3){
        $email= $f3->get('PARAMS.email');
        $fileId = $f3->get('PARAMS.originalfileid');
        $copied_file = $f3->get('PARAMS.title');
        $folderid=$f3->get('PARAMS.folderid');        
        
        updateTokenWeb($email,$f3);
        $db=$f3->get('DB');      
        $data=$f3->set('result',$db->exec("select accesstoken from drive_users where email='".$email."' order by id desc"));  
        $web = \Web::instance();
        $client = new Google_Client();
        $client->setAuthConfigFile('ggl_conf.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType("offline");
        $client->setAccessToken($data[0]['accesstoken']);
        $service = new Google_Service_Drive($client);
        
        $fileMetadata1 = new Google_Service_Drive_DriveFile(array(
            'name' => $copied_file,
            'parents' => array($folderid)
        ));
        $copiedFile = $service->files->copy($fileId, $fileMetadata1);
    });

function getSubtree($email,$parentId,$framework){
    updateTokenWeb($email,$framework);
    $db=$framework->get('DB');
    $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $service = new Google_Service_Drive($client);
    $optParams = array(
          'pageSize' => 1000,
//          'fields' => "nextPageToken, files(id, name,mimeType,parents)",     
          'q' => "trashed = false and '$email' in owners and '$parentId'  in parents ",
//           'q' => "name contains '$foldername' and trashed = false and '$email' in owners and starred = true ",
                            
    );  
   
    //$folderID="not found";
    $results = $service->files->listFiles($optParams); 
   $i=0;
    foreach ($results->getFiles() as $file) {
              
         $folderID[$i]=$file->getName();
    } 
   
   return json_encode($folderID);
    
}   

function getSubtreeForFolder($email,$parentId,$framework){
    updateTokenWeb($email,$framework);
    $db=$framework->get('DB');
    $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $service = new Google_Service_Drive($client);
    $optParams = array(
          'pageSize' => 1000,
//          'fields' => "nextPageToken, files(id, name,mimeType,parents)",     
          'q' => "trashed = false and '$email' in owners and '$parentId'  in parents ",
//           'q' => "name contains '$foldername' and trashed = false and '$email' in owners and starred = true ",
                            
    );  
   
    //$folderID="not found";
    $results = $service->files->listFiles($optParams); 
   
    foreach ($results->getFiles() as $file) {
              
         $folderID[$file->getName()]=json_decode(getSubtreeForFolder($email,$file->getId(),$framework));
    } 
    echo "<pre>"; print_r($folderID); echo "</pre>";
   return json_encode($folderID);
    
}   

function createMsgForEmail($email,$framework){
    updateTokenWeb($email,$framework);
    $db=$framework->get('DB');
       
        
    $data=$framework->set('result',$db->exec("select accesstoken from drive_users where email='$email' order by id desc"));
    $client = new Google_Client();
    $client->setAuthConfigFile('ggl_conf.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setAccessType("offline");
    $client->setAccessToken($data[0]['accesstoken']);
    $service = new Google_Service_Drive($client);
    $filename='';
          
    //parent folder to store email attachment
    $data1=$framework->set('result',$db->exec("select email_parent_folder,email_to_users,imap_pwd from tbl_drivesync_authentication where email='$email'  order by id desc"));
    $parentfolderid=str_replace("https://drive.google.com/drive/folders/","",$data1[0]['email_parent_folder']);
                            
    $pwd=GibberishAES::dec($data1[0]['imap_pwd'], 'rotcoderaclla');
    
    $imapPath = '{imap.gmail.com:993/imap/ssl}INBOX';
    // try to connect
    $inbox = imap_open($imapPath,$email,$pwd) or die('Cannot connect to Gmail: ' . imap_last_error());
    $emails = imap_search($inbox,'UNSEEN');
    $em=count($emails);    
    
    
   if($emails) {
            /* put the newest emails on top */
            rsort($emails);
            $attach=0;
            
           
	    /* for every email... */
            foreach($emails as $email_number){
               
                /* get information specific to this email */
                $overview = imap_fetch_overview($inbox,$email_number,0);
                /* get mail message */
                $message = imap_fetchbody($inbox,$email_number,0); 
                /* get mail structure */
                $structure = imap_fetchstructure($inbox, $email_number);
                //folder creation for each email
                $folder_name_format=trim(str_replace(" ","_",date('Y-m-d H:i:s')."_".$overview[0]->from),"_"); 
                if($parentfolderid==''){
                    $fileMetadata = new Google_Service_Drive_DriveFile(array(
                        'name' => $folder_name_format
                    ));
                }
                else{
                    $fileMetadata = new Google_Service_Drive_DriveFile(array(
                        'name' => $folder_name_format,
                        'mimeType' => 'application/vnd.google-apps.folder',
                        'parents' => array($parentfolderid)
                    ));
                }
                $createdFolder = $service->files->create($fileMetadata, array('fields' => 'id' ));
                // decode7Bit($message);
                if($structure->parts!=''){
                    $flattenedParts = flattenParts($structure->parts); 
                    foreach($flattenedParts as $partNumber => $part) {
                        switch($part->type) {
                        case 0:
                            // the HTML or plain text part of the email
                            $message = getPart($inbox, $email_number, $partNumber, $part->encoding); 
                            break;
                        case 1:
                            // multi-part headers, can ignore
                            break;
                        case 2:
                                // attached message headers, can ignore
                        break;

                        case 3: // application
                        case 4: // audio
                        case 5: // image
                        case 6: // video
                        case 7: // other
                            $filename = getFilenameFromPart($part,'ATTACHMENT');
                            if($filename) {
                                    // it's an attachment
                                    $attachment = getPart($inbox, $email_number, $partNumber, $part->encoding);
                                    $fp = fopen($filename, "w+");
                                    fwrite($fp, $attachment);
                                    fclose($fp);
                                    //create folder id to save attachments
                                    //echo $createdFolder->id;
                                    inserIntoFolder($createdFolder->id,$filename,$email,$framework);

                            }
                            break;
                    }
                    }
                }else{
                    $text = imap_fetchbody($inbox,$email_number, 1);
//                    $fp = fopen('texashousecalls.html', "w+");
//                    fwrite($fp, $text);
//                    fclose($fp);
                    include('mpdf/mpdf.php');
                    $mpdf=new mPDF('c', 'A4','','' , 2 , 2 , 2 , 2 , 2 , 2,'');
                     // $mpdf=new mPDF('win-1252','A4','','',15,10,16,10,10,10);
                    $mpdf->SetDisplayMode('fullpage');
                    $mpdf->list_indent_first_level = 0; 
                    $mpdf->WriteHTML($text);
                    $mpdf->Output('texashousecalls.pdf', 'F');
                    chmod('texashousecalls.pdf',0777);
                                    //create folder id to save attachments
                                    //echo $createdFolder->id;
                                    inserIntoFolder($createdFolder->id,'texashousecalls.pdf',$email,$framework);
                }
                
                 $attach++;
//                if($message!=''){
//                    AttachmentPdf($email,$message,$folder_name_format,$createdFolder->id,$framework);
//                    $user_cus=$framework->set('result',$db->exec("select email_parent_folder,store_email_settings from tbl_drivesync_authentication where email='$email'  order by id desc"));
//                   // $user_cus=$framework->set('result',$db->exec("select userid,emr_msg,drive_email,email from tbl_user_custom_attr_1to1 where emr_msg!='' and drive_email!=''"));
//                    $ser1=unserialize($user_cus[0]['store_email_settings']);                      
//                    $ser=explode(",",unserialize($ser1['user']));        
//                    foreach($ser as $val){
//                        $user_cus1=$framework->set('result',$db->exec("select userid,email from tbl_user_custom_attr_1to1 where userid='$val'"));
//                        $user=$framework->set('result',$db->exec("select username,concat(fname,',',lname) as name from users where id='$val'"));
//                         if($user[0]['username']!=''){
//                              $user_nam=$user[0]['username'];
//                         }else { 
//                              $user_nam=$user[0]['name'];
//                         }
//                         
//                        $email_id=$user_cus[$i]['email'];
//                        $id= GibberishAES::enc($createdFolder->id, 'rotcoderaclla');
//                        $type= GibberishAES::enc('attachments', 'rotcoderaclla');
//                        $individual_link='https://'.$_SERVER['HTTP_HOST'].'/interface/login/login_frame.php?param='.$id.'&type='.$type;  
//                        $view_link = "<a href=$individual_link target=".'_blank'.">Folder link</a>";
////                        $data3=$framework->set('result',$db->exec("INSERT INTO pnotes (date, body, pid, user, groupname, activity, authorized, title, assigned_to, message_status)
////                        values (NOW(), CONCAT(date_format(now(), '%Y-%m-%d %H:%i'),'".' '."(folder location)".' '."$view_link'), 0, '$user_nam', 'Default', 1, 1, 'Store Email Attachment', '".$user_nam."', 'new')"));
//                        $data3=$framework->set('result',$db->exec("INSERT INTO tbl_allcare_custom_messages (date, body, obj_id, user, groupname, activity, authorized, title, assigned_to, message_status,object_type , priority)
//                        values (date_format(now(), '%Y-%m-%d %H:%i'),'".$view_link."', '5937', '$user_nam', 'Default', 1, 1, 'from mail', '".$user_nam."', 'new','patients','Very_High')"));
//                        
//                            
//                        $mail = new PHPMailer;
//                        $mail->isSMTP();
//                        $mail->SMTPDebug = 0;
//                        $mail->Debugoutput = 'html';
//                        $mail->Host = "smtp.gmail.com";
//                        $mail->Port = 465;
//                        $mail->SMTPAuth = true;
//                        $mail->SMTPSecure = "ssl";
//                        $mail->Username = "srinus@smartmbbs.com";
//                        $mail->Password = "srinucnu@#"; 
//                        $mail->setFrom($from, 'Smart MBBS');
//
//                        //$toEmails = explode(";",$to);
//                        $toEmails = $email;
//                        if(count($toEmails)>1){
//                            foreach($toEmails as $eachEmail){
//                                $mail->addAddress($eachEmail);
//                            }
//                        }
//                        else
//                        {
//                            $mail->addAddress($toEmails);
//                        }
//
//                        $mail->Subject =$ser1['text'];
//                        $mail->msgHTML($ser1['text']."<br>".$individual_link);
//                        $mail->AltBody = 'This is a plain-text message body';
//
//                        //send the message, check for errors
//                        if (!$mail->send()) {
//                            echo "Mailer Error: " . $mail->ErrorInfo;
//                            $mstatus = false;
//                        } else {
//                            $mstatus = true;
//                        }
//                    }
//                }
            }
             if($attach==$em){
                $cnt=$attach-$em;
                 echo $cnt.":Success fully stored email attachments";
            }
        } 
} 
$f3->run();                              
?>  