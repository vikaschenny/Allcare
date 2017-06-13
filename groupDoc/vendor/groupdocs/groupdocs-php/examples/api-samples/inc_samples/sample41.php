<?php

//###<i>This sample will show how to set callback for Annotation and manage user rights using PHP SDK </i>
//Set variables and get POST data
F3::set('userId', '');
F3::set('privateKey', '');
F3::set('fileId', '');
F3::set('message', '');
F3::set('iframe', '');
$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$emailsArray = F3::get('POST["email"]');
$callbackUrl = F3::get('POST["callbackUrl"]');
F3::set("callbackUrl", $callbackUrl);
//###Check clientId and privateKey
if (empty($clientId) || empty($privateKey) || empty($emailsArray[0])) {
    $error = 'Please enter all required parameters';
    F3::set('error', $error);
} else {
    //Add all emails to array
   if (empty($emailsArray[1])) {
       unset($emailsArray[1]);
   };

    //path to settings file - temporary save userId and apiKey like to property file
    $infoFile = fopen(__DIR__ . '/../user_info.txt', 'w');
    fwrite($infoFile, $clientId . "\r\n" . $privateKey);
    fclose($infoFile);
    //Delete temporary file which content callback data
    if (file_exists(__DIR__ . '/../callback_info.txt')) {
        unlink(__DIR__ . '/../callback_info.txt');
    }
    //Deleting of tags, slashes and  space from clientId and privateKey
    $clientID = strip_tags(stripslashes(trim($clientId))); //ClientId==UserId
    $apiKey = strip_tags(stripslashes(trim($privateKey))); //ApiKey==PrivateKey
    //###Create Signer, ApiClient and Storage Api objects
    //Create signer object
    $signer = new GroupDocsRequestSigner($apiKey);
    //Create apiClient object
    $apiClient = new APIClient($signer);
    //Create Storage Api object
    $storageApi = new StorageApi($apiClient);
    //Create MgmtApi object (this class allow manipulations with User account)
    $mgmtApi = new MgmtApi($apiClient);
    $basePath = F3::get('POST["basePath"]');
    //Declare which Server to use
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    //Set base path
    $storageApi->setBasePath($basePath);
    $mgmtApi->setBasePath($basePath);
    //Get entered by user data
    $name = "";
    $fileGuId = "";
    $url = F3::get('POST["url"]');
    $file = $_FILES['file'];
    $fileId = F3::get('POST["fileId"]');
    //Check is URL entered
    if ($url != "") {
        //Upload file from URL
        try {
            $uploadResult = $storageApi->UploadWeb($clientID, $url);
            //Check is file uploaded
            if ($uploadResult->status == "Ok") {
                //Get file GUID
                $fileGuId = $uploadResult->result->guid;
                //###Make a request to Storage API using clientId
                //Obtaining all Entities from current user
                try {
                    $files = $storageApi->ListEntities($clientID, 'My Web Documents', 0);
                    //Obtaining file name and id by fileGuID
                    if ($files->status == "Ok") {
                        foreach ($files->result->files as $item) {
                            if ($item->guid == $fileGuId) {
                                $name = $item->name;
                            }
                        }
                    } else {
                        throw new Exception($uploadResult->error_message);
                    }
                } catch (Exception $e) {
                    $error = 'ERROR: ' . $e->getMessage() . "\n";
                    F3::set('error', $error);
                }
                //If it isn't uploaded throw exception to template
            } else {
                throw new Exception($uploadResult->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    }
    //Check is local file chosen
    if ($file["name"] != "") {
        //Get uploaded file
        $uploadedFile = $_FILES['file'];
        //###Check uploaded file
        if (null === $uploadedFile) {
            return new RedirectResponse("/sample41");
        }
        //Temp name of the file
        $tmpName = $uploadedFile['tmp_name'];
        //Original name of the file
        $name = $uploadedFile['name'];
        //Create file stream
        $fs = FileStream::fromFile($tmpName);
        //###Make a request to Storage API using clientId
        //Upload file to current user storage
        try {
            $uploadResult = $storageApi->Upload($clientID, $name, 'uploaded', "", $fs);
            //###Check if file uploaded successfully
            if ($uploadResult->status == "Ok") {
                $fileGuId = $uploadResult->result->guid;
                $name = $uploadResult->result->adj_name;
            } else {
                throw new Exception($uploadResult->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    }
    //Check is user choose file GUID
    if ($fileId != "") {
        //Get entered by user file GUID
        $fileGuId = $fileId;
        //###Make a request to Storage API using clientId
        //Obtaining all Entities from current user
        try {
            $files = $storageApi->ListEntities($clientID, '', 0);
            if ($files->status == "Ok") {
                //Obtaining file name and id by fileGuID
                foreach ($files->result->files as $item) {
                    if ($item->guid == $fileGuId) {
                        $name = $item->name;
                    }
                }
            } else {
                throw new Exception($uploadResult->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    }
    F3::set("fileId", $fileGuId);
    //Create Annotation Api object
    $antApi = new AntApi($apiClient);
    $antApi->setBasePath($basePath);

    try {

        //Set file sesion callback - will be trigered when user add, remove or edit commit for annotation
        $setCallback = $antApi->SetSessionCallbackUrl($clientID, $fileGuId, $callbackUrl);
        if ($setCallback->status == 'Ok'){
            //Generate iframe URL for iframe
            if ($basePath == "https://api.groupdocs.com/v2.0") {
                //iframe to prodaction server
                $url = "https://apps.groupdocs.com/document-annotation2/embed/" . $fileGuId;
                //iframe to dev server
            } elseif ($basePath == "https://dev-api.groupdocs.com/v2.0") {
                $url = 'https://dev-apps.groupdocs.com/document-annotation2/embed/' . $fileGuId;
                //iframe to test server
            } elseif ($basePath == "https://stage-apps-groupdocs.dynabic.com/v2.0") {
                $url = 'https://stage-apps-groupdocs.dynabic.com/document-annotation2/embed/' . $fileGuId;
            } elseif ($basePath == "http://realtime-api.groupdocs.com") {
                $url = 'http://realtime-apps.groupdocs.com/document-annotation2/embed/' . $fileGuId;
            }
            //Get all users from accaunt
            $allUsers = $mgmtApi->GetAccountUsers($clientId);
            $collaborator = array();
            if ($allUsers->status == "Ok" && $allUsers->result->users != null) {
                //Loop for all users
                foreach($emailsArray as $item) {
                    //Get current user email
                    $email = $item;
                    //Loop to get user GUID if user with same email already exist
                    for ($i = 0; $i < count($allUsers->result->users); $i++) {
                        //Check whether there is a user with entered email
                        if ($email == $allUsers->result->users[$i]->primary_email) {
                            //Get user GUID
                            $userGuid = $allUsers->result->users[$i]->guid;
                            break;
                        }
                    }
                    //Check is user with entered email was founded in GroupDocs account, if not user will be created
                    if (!isset($userGuid)) {
                        //###Create User info object
                        //Create User info object
                        $user = new UserInfo ();
                        //Create Role info object
                        $role = new RoleInfo ();
                        //Set user role Id. Can be: 1 -  SysAdmin, 2 - Admin, 3 - User, 4 - Guest
                        $role->id = "3";
                        //Set user role name. Can be: SysAdmin, Admin, User, Guest
                        $role->name = "User";
                        //Create array of roles.
                        $roles = array($role);
                        //Set first name as entered first name
                        $user->firstname = $email;
                        //Set last name as entered last name
                        $user->lastname = $email;
                        $user->roles = $roles;
                        //Set email as entered email
                        $user->primary_email = $email;
                        //Creating of new user. $clientId - user id, $firstName - entered first name, $user - object with new user info
                        $newUser = $mgmtApi->UpdateAccountUser($clientId, $email, $user);
                        //Check the result of the request
                        if ($newUser->status == "Ok") {
                            //Get user GUID
                            $userGuid = $newUser->result->guid;
                        } else {
                            //Throw error message
                            throw new Exception($newUser->error_message);
                        }
                    }
                    //Get all collaborators for current document
                    $getCollaborators = $antApi->GetAnnotationCollaborators($clientId, $fileGuId);

                    if ($getCollaborators->status == "Ok") {
                        //Loop for checking all collaborators
                        for ($n = 0; $n < count($getCollaborators->result->collaborators); $n++) {
                            //Check is user with entered email already in collaborators
                            if ($getCollaborators->result->collaborators[$n]->primary_email == $email) {
                                $collaborator[$n] = $getCollaborators->result->collaborators[$n]->guid;
                            }
                        }
                    }
                }
                //Check whether user was founded in collaborators list
                if (count($collaborator) < 2) {
                    //Add user as collaborators for the document
                    $setCollaborator = $antApi->SetAnnotationCollaborators($clientId, $fileGuId, "v2.0", $emailsArray);
                    if ($setCollaborator->status == "Ok") {
                        // Check the result of the request
                        if (isset($setCollaborator->result)) {

                            //Add user GUID as "uid" parameter to the iframe URL
                            $url = $url . "?uid=" . $userGuid;
                            //Sign iframe URL
                            $url = $signer->signUrl($url);
                            // If request was successfull - set variables for template
                            F3::set('result', $setCollaborator->result);
                            F3::set("url", $url);
                        }
                    } else {
                        throw new Exception($setCollaborator->error_message);
                    }
                } else {
                    //Add user GUID as "uid" parameter to the iframe URL
                    $url = $url . "?uid=" . $userGuid;
                    //Sign iframe URL
                    $url = $signer->signUrl($url);
                    F3::set("url", $url);
                }
            }
        }
    } catch (Exception $e) {
        $error = 'ERROR: ' . $e->getMessage() . "\n";
        F3::set('error', $error);
    }
}



//Process template
F3::set('userId', $clientId);
F3::set('privateKey', $privateKey);
echo Template::serve('sample41.htm');