<?php

//### This sample will show how create or update user and add him to collaborators using PHP SDK
// Set variables and get POST data
F3::set('userId', '');
F3::set('privateKey', '');
F3::set('email', '');
F3::set('firstName', '');
F3::set('fileId', '');
F3::set('lastName', '');
$clientId = F3::get('POST["clientId]');
$privateKey = F3::get('POST["privateKey"]');
$email = F3::get('POST["email"]');
$firstName = F3::get('POST["firstName"]');
$lastName = F3::get('POST["lastName"]');
$basePath = F3::get('POST["basePath"]');

try {
    //Check if all requared parameters were transferred
    if (empty($clientId) || empty($privateKey) || empty($email) || empty($firstName) || empty($lastName)) {
        //if not send error message
        throw new Exception('Please enter all required parameters');
    } else {
        //Set variables for template "You are entered" block
        F3::set('userId', $clientId);
        F3::set('privateKey', $privateKey);
        F3::set('email', $email);
        F3::set('firstName', $firstName);
        F3::set('lastName', $lastName);
        //### Create Signer, ApiClient and Mgmt Api objects
        // Create signer object
        $signer = new GroupDocsRequestSigner($privateKey);
        // Create apiClient object
        $apiClient = new ApiClient($signer);
        // Create MgmtApi object
        $mgmtApi = new MgmtApi($apiClient);
        //Create Storage Api object
        $storageApi = new StorageApi($apiClient);
        //Declare which Server to use
        if ($basePath == "") {
            //If base base is empty seting base path to prod server
            $basePath = 'https://api.groupdocs.com/v2.0';
        }
        //Set base path
        $mgmtApi->setBasePath($basePath);
        $storageApi->setBasePath($basePath);
        //Get entered by user data
        $url = F3::get('POST["url"]');
        $file = $_FILES['file'];
        $fileGuId = F3::get('POST["fileId"]');
        $fileId = "";
        //Check is file GUID entered
        if ($fileGuId != "") {
            $fileId = $fileGuId;
        }
        if ($url != "") {
            //Upload file from URL
            $uploadResult = $storageApi->UploadWeb($clientId, $url);
            //Check is file uploaded
            if ($uploadResult->status == "Ok") {
                //Get file GUID
                $fileId = $uploadResult->result->guid;

                //If it isn't uploaded throw exception to template
            } else {
                throw new Exception($uploadResult->error_message);
            }
        }
        //Check is local file chosen
        if ($file["name"] != "") {
            //Get uploaded file
            $uploadedFile = $_FILES['file'];


            //###Check uploaded file
            if (null === $uploadedFile) {
                return new RedirectResponse("/sample22");
            }
            //Temp name of the file
            $tmpName = $uploadedFile['tmp_name'];
            //Original name of the file
            $name = $uploadedFile['name'];
            //Creat file stream
            $fs = FileStream::fromFile($tmpName);


            //###Make a request to Storage API using clientId
            //Upload file to current user storage
            $uploadResult = $storageApi->Upload($clientId, $name, 'uploaded', "", $fs);
            //###Check if file uploaded successfully
            if ($uploadResult->status == "Ok") {
                $fileId = $uploadResult->result->guid;
            } else {
                throw new Exception($uploadResult->error_message);
            }
        }
        //###Create User info object
        //Create User info object
        $user = new UserInfo();
        //Create Role info object
        $role = new RoleInfo();
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
            //### If request was successfull
            //Create Annotation api object
            $ant = new AntApi($apiClient);
            $ant->setBasePath($basePath);
            //Create array with entered email for SetAnnotationCollaborators method 
            $arrayEmail = array($email);
            //Make request to Ant api for set new user as annotation collaborator
            $addCollaborator = $ant->SetAnnotationCollaborators($clientId, $fileId, "2.0", $arrayEmail);
            if ($addCollaborator->status == "Ok") {
                //Make request to Annotation api to receive all collaborators for entered file id
                $getCollaborators = $ant->GetAnnotationCollaborators($clientId, $fileId);
                if ($getCollaborators->status == "Ok") {
                    //Set reviewers rights for new user. $newUser->result->guid - GuId of created user, $fileId - entered file id, 
                    //$getCollaborators->result->collaborators - array of collabotors in which new user will be added
                    $setReviewer = $ant->SetReviewerRights($newUser->result->guid, $fileId, $getCollaborators->result->collaborators);
                    if ($setReviewer->status == "Ok") {
                        //Generating iframe for template
                        if ($basePath == "https://api.groupdocs.com/v2.0") {
                            $iframe = 'https://apps.groupdocs.com//document-annotation2/embed/' .
                                    $fileId . '?&uid=' . $newUser->result->guid . '&download=true';
                            //iframe to dev server
                        } elseif ($basePath == "https://dev-api.groupdocs.com/v2.0") {
                            $iframe = 'https://dev-apps.groupdocs.com//document-annotation2/embed/' .
                                    $fileId . '?&uid=' . $newUser->result->guid . '&download=true ';
                            //iframe to test server
                        } elseif ($basePath == "https://stage-api-groupdocs.dynabic.com/v2.0") {
                            $iframe = 'https://stage-apps-groupdocs.dynabic.com/document-annotation2/embed/' .
                                    $fileId . '?&uid=' . $newUser->result->guid . '&download=true ';
                        } elseif ($basePath == "http://realtime-api.groupdocs.com") {
                            $iframe = 'http://realtime-apps.groupdocs.com/document-annotation2/embed/' .
                                    $fileId . '?&uid=' . $newUser->result->guid . '&download=true ';
                        }
                        $iframe = $signer->signUrl($iframe);
                    } else {
                        throw new Exception($setReviewer->error_message);
                    }
                } else {
                    throw new Exception($getCollaborators->error_message);
                }
            } else {
                throw new Exception($addCollaborator->error_message);
            }
            //Set variable with work results for template
            F3::set('fileId', $fileId);
            F3::set('url', $iframe);
        } else {
            F3::set("message", $newUser->error_message);
        }
    }
} catch (Exception $e) {
    $error = 'ERROR: ' . $e->getMessage() . "\n";
    F3::set('error', $error);
}

// Process template
echo Template::serve('sample22.htm');
