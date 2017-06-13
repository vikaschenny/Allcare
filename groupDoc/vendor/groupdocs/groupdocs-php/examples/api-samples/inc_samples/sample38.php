<?php

//### This sample will show how to create new user and add him as collaborator to doc with annotations
// Set variables and get POST data
F3::set('userId', '');
F3::set('privateKey', '');
F3::set('fileId', '');
F3::set('collaborations', '');
$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$email = F3::get('POST["email"]');
$firstName = F3::get('POST["firstName"]');
$lastName = F3::get('POST["lastName"]');
//Check is all required data is entered
if (empty($clientId) || empty($privateKey) || empty($email) || empty($firstName) || empty($lastName)) {
    $error = 'Please enter all required parameters';
    F3::set('error', $error);
} else {
    //Get base path
    $basePath = F3::get('POST["basePath"]');
    //Get entered by user data
    $fileGuId = F3::get('POST["fileId"]');
    $url = F3::get('POST["url"]');
    $file = $_FILES['file'];
    F3::set('userId', $clientId);
    F3::set('privateKey', $privateKey);
    F3::set('email', $email);
    F3::set('firstName', $firstName);
    F3::set('lastName', $lastName);
    // Create signer object
    $signer = new GroupDocsRequestSigner($privateKey);
    // Create apiClient object
    $apiClient = new ApiClient($signer);
    // Create Annotation object
    $antApi = new AntApi($apiClient);
    //Create Storage Api object
    $storageApi = new StorageApi($apiClient);
    //Create MgmtApi object (this class allow manipulations with User account)
    $mgmtApi = new MgmtApi($apiClient);
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    //Build propper basePath
    if (substr($basePath, -3) != "2.0") {
        if (substr($basePath, -1) != "/") {
            $basePath = $basePath . "/v2.0";
        } else {
            $basePath = $basePath . "v2.0";
        }
    }
    //Set base path
    $antApi->setBasePath($basePath);
    $storageApi->setBasePath($basePath);
    $mgmtApi->setBasePath($basePath);
    //Check if user choose upload file from URL
    if ($url != "") {
        $fileGuId = "";
        //Upload file from URL
        $uploadResult = $storageApi->UploadWeb($clientId, $url);
        //Check is file uploaded
        try {
            if ($uploadResult->status == "Ok") {
                //Get file GUID
                $fileId = $uploadResult->result->guid;
                //If it isn't uploaded throw exception to template
            } else {
                throw new Exception($uploadResult->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    }
    //Check is user choose upload local file
    if ($_FILES['file']["name"] != "") {
        $fileGuId = "";
        //Temp name of the file
        $tmpName = $file['tmp_name'];
        //Original name of the file
        $name = $file['name'];
        //Creat file stream
        $fs = FileStream::fromFile($tmpName);
        //###Make a request to Storage API using clientId
        //Upload file to current user storage
        try {
            $uploadResult = $storageApi->Upload($clientId, $name, 'uploaded', "", $fs);

            //###Check if file uploaded successfully
            if ($uploadResult->status == "Ok") {
                //Get file GUID
                $fileId = $uploadResult->result->guid;
                //If it isn't uploaded throw exception to template
            } else {
                throw new Exception($uploadResult->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    }
    if ($fileGuId != "") {
        $fileId = $fileGuId;
    }
    F3::set('fileId', $fileId);
    //Generating iframe for template
    if ($basePath == "https://api.groupdocs.com/v2.0") {
        $url = 'https://apps.groupdocs.com/document-annotation2/embed/' . $fileId;
        //iframe to dev server
    } elseif ($basePath == "https://dev-api-groupdocs.dynabic.com/v2.0") {
        $url = 'https://dev-apps-groupdocs.dynabic.com/document-annotation2/embed/' . $fileId;
        //iframe to test server
    } elseif ($basePath == "https://stage-api-groupdocs.dynabic.com/v2.0") {
        $url = 'https://stage-apps-groupdocs.dynabic.com/document-annotation2/embed/' . $fileId;
    } elseif ($basePath == "http://realtime-api.groupdocs.com") {
        $url = 'http://realtime-apps-groupdocs.dynabic.com/document-annotation2/embed/' . $fileId;
    }

    try {
        //Get all users from accaunt
        $allUsers = $mgmtApi->GetAccountUsers($clientId);
        if ($allUsers->status == "Ok") {
            //Loop for all users
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
                //Set nick name as entered first name
                $user->nickname = $firstName;
                //Set first name as entered first name
                $user->firstname = $firstName;
                //Set last name as entered last name
                $user->lastname = $lastName;
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
            $getCollaborators = $antApi->GetAnnotationCollaborators($clientId, $fileId);
            if ($getCollaborators->status == "Ok") {
                //Loop for checking all collaborators
                for ($n = 0; $n < count($getCollaborators->result->collaborators); $n++) {
                    //Check is user with entered email already in collaborators
                    if ($getCollaborators->result->collaborators[$n]->guid == $userGuid) {
                        //Add user GUID as "uid" parameter to the iframe URL
                        $url = $url . "?uid=" . $userGuid;
                        //Sign iframe URL
                        $url = $signer->signUrl($url);
                        break;
                    }
                }
                //Check whether user was founded in collaborators list
                if (strpos($url, "?uid=")) {
                    //If was set variable with URL for iframe
                    F3::set("url", $url);
                //If user wasn't founded in collaborators list - add him to it
                } else {
                    //Create array with entered email for SetAnnotationCollaborators method 
                    $arrayEmail = array($email);
                    //Add user as collaborators for the document
                    $setCollaborator = $antApi->SetAnnotationCollaborators($clientId, $fileId, "v2.0", $arrayEmail);
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
                }
            } else {
                throw new Exception($getCollaborators->error_message);
            }
        } else {
            throw new Exception($allUsers->error_message);
        }
    } catch (Exception $e) {
        $error = 'ERROR: ' . $e->getMessage() . "\n";
        F3::set('error', $error);
    }
}
// Process template
echo Template::serve('sample38.htm');
