//<script type="text/javascript">
      // Enter a client ID for a web application from the Google Developer Console.
      // The provided clientId will only work if the sample is run directly from
      // https://google-api-javascript-client.googlecode.com/hg/samples/authSample.html
      // In your Developer Console project, add a JavaScript origin that corresponds to the domain
      // where you will be running the script.
      //var clientId = '384887784012-jlgr2m8dqau19jt5uli71mkefs6dfarh.apps.googleusercontent.com';
	  var clientId = '298984030540.apps.googleusercontent.com';

      // Enter the API key from the Google Develoepr Console - to handle any unauthenticated
      // requests in the code.
      // The provided key works for this sample only when run from
      // https://google-api-javascript-client.googlecode.com/hg/samples/authSample.html
      // To use in your own application, replace this API key with your own.
      //var apiKey = 'AIzaSyA89YhlGIUFnJaYYuXq9Cnepmip4JlcQqM';
	  var apiKey = 'AIzaSyDP54iAuYO-EHGI63rJGJE4fr3EZn4Ds5U';
      // To enter one or more authentication scopes, refer to the documentation for the API.
      var scopes = 'https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/drive';
      
      //get the fileId to download and load in the pdfviewer
      var drive_fileId = getUrlVars()["fileId"];
      var patientId = getUrlVars()["patient"];//use this to get the id/create new folder
      var docid = getUrlVars()["docid"];
	  //The global variables
      var rootFolderId = '';
      var patientFolderId = '';
      var signedFolderId = '';
      var processedFolderId = '';
      var originalFile = '';
      var signedFile = '';
	  var docTitle = '';	//To save the document title which is loaded and to be provided as the signed file title
      function setPatientFolderId(value) {
    	  patientFolderId = value.id;
      }
      
      function setSignedFolderId(value) {
    	  signedFolderId = value.id;
      }
      
      function setProcessedFolderId(value) {
    	  processedFolderId = value.id;
      }
	  
	  function setRootFolderId(value) {
    	  rootFolderId = value.rootFolderId;;
      }
      
       /**
        * Retrieve a list of files belonging to a folder.
        *
        * @param {String} folderId ID of the folder to retrieve files from.
        * @param {Function} callback Function to call when the request is complete.
        *
        */
       function retrieveAllFilesInFolder(folderTitle, folderId, callback) {
  		   
		   var retrievePageOfChildren = function(request, result) {
	           request.execute(function(resp) {
	             result = result.concat(resp.items);
	             var nextPageToken = resp.nextPageToken;
	             if (nextPageToken) {
	               request = gapi.client.drive.children.list({
	                 'folderId' : folderId,//folderId,
	                 'pageToken': nextPageToken
	               });
	               retrievePageOfChildren(request, result);
	             } else {
    	               console.log('result '+result);
    	               if(result[0] == undefined) {
    	            	   //create a new folder with title patient id
    	            	   console.log('no results found');
    	            	   //create patient folder and its sub folders
    	            	   insertFolder(folderTitle, rootFolderId);
    	               } else {
	            		   callback(result[0]);
    	            	   //alert('patientFolderId '+patientFolderId);
    	            	   if(signedFolderId == '') {
    	            		   retrieveAllFilesInFolder('Signed', patientFolderId, setSignedFolderId);
    	            	   } else {
    	            		   if(processedFolderId == '') {
    	            			   retrieveAllFilesInFolder('Processed', patientFolderId, setProcessedFolderId);
    	            		   }
    	            	   }
    	            	   
    	               }
	             }
	           });
		   }
		   
		   var initialRequest = gapi.client.drive.children.list({
			   'folderId' : folderId,
			   'q': "title = '"+folderTitle+"' and mimeType = 'application/vnd.google-apps.folder'"
           });
		   retrievePageOfChildren(initialRequest, []);
       }
       
      /**
       * Print information about the current user along with the Drive API
       * settings.
	   * Retrieve the rootFolderId and assign it to the global variable
       */
      function printAbout() {    	  
    	  gapi.client.load('drive', 'v2', function () {
    		  var request = gapi.client.drive.about.get();
    		  request.execute(function(resp) {
	    		    rootFolderId = resp.rootFolderId;
	    		    //alert('rootFolderId: '+rootFolderId);
	    		    //after you have retrieved the root folder id
	    		    //if a folder with the patientId is already present get the id
	    		    //else create the folder and its subfolders
	    		    //retrieveAllFilesInFolder(patientId, rootFolderId, setPatientFolderId);
    		  });
    		});
      }
      
      
      
      // Use a button to handle authentication the first time.
      function handleClientLoad() {
    	  gapi.client.setApiKey(apiKey);
    	  window.setTimeout(checkAuth,1);
      }
      
      function checkAuth() {
		//alert('drive fileID: '+drive_fileId);
		//alert("check auth");
        gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: true}, handleAuthResult);
      }
      
	  /*
	   * Handles the authentication Result. On successful login the drive file 
	   * will be loaded into the viewer and save on the server.
	   * This file has to be deleted once the user has successfully updated the file.
	   */
      function handleAuthResult(authResult) {
        //var authorizeButton = document.getElementById('authorize-button');
        if (authResult && !authResult.error) {
        	
	    	//call the drive api to download the drive file
	      	downloadDriveFile();
	      	
	     	//get the google user details
	    	printAbout();
        	
        } else {
		  	handleAuthClick();
        }
      }
      
      function handleAuthClick() {
    	  gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: false}, handleAuthResult);
    	  //return false;
      }
	  
      var accessToken = 'token';
      
      // Load the API and make an API call.  Display the results on the screen.
      function downloadDriveFile() {
		//drive call
		accessToken = gapi.auth.getToken().access_token;
		//console.log("The authenticated token is: "+accessToken);
		
		gapi.client.load('drive', 'v2', function() {
			
			var request = gapi.client.drive.files.get({
				'fileId': drive_fileId//'0BwVED7ZAOe8NOGxWSUNTbERTS2M'//
			});
			
			request.execute(function(resp) {
				//alert(resp.title);
				console.log(resp.title);
				//Modification done on 21/10/2013
				if(resp.title == undefined){
				//console.log('Title: ' + resp.title);
				//console.log('Description: ' + resp.description);
				//console.log('MIME type: ' + resp.mimeType);
				var url = "https://docs.google.com/file/d/"+ drive_fileId+"/edit?usp=drive_web";
				window.location.assign(url);//It Will automatically redirect the url that was specified
				}
				else if(resp.mimeType != "application/pdf")
				{
					alert("Only PDF file types are supported");
					//var iframe = window.parent.document.getElementById('doceditor');
					//iframe.parentNode.removeChild(iframe);
				}
				else{
				originalFile = resp;
				docTitle = resp.title;
				//console.log('Doc Title: '+resp.title);
				//console.log('MIME type: ' + resp.mimeType);
				var fileDownloadUrl = resp.downloadUrl;
				//alert(resp.downloadUrl);
				var xhr = new XMLHttpRequest();
				xhr.open('GET', resp.downloadUrl);
				xhr.setRequestHeader('Authorization', 'Bearer ' + accessToken);
				xhr.responseType = 'arraybuffer';//<!--'blob';-->
				//xhr.onload = function(e) {	alert("callback"+xhr.responseText); };
				xhr.onerror = function() {
					alert('File could not be fetched');
				};
				//handle successful response
				xhr.onreadystatechange=function(e)
				{
					if (xhr.readyState==4 && xhr.status==200)
					{						
						var blob = new Blob([xhr.response], {
							type: "application/pdf",
						});
						
						var arrayBuffer = xhr.response;
						var fileLength = arrayBuffer.byteLength;
						var byteArray = new Uint8Array(arrayBuffer);
						//console.log(byteArray);
						
						//save the pdf file on the server by passing the stream data to the php file
						var xhr2 = new XMLHttpRequest();
						var url = 'http://'+window.location.host+'/DocumentViewer_02/WebContent/web/savePDF.php';
						xhr2.open('POST', url);
						xhr2.setRequestHeader("Content-type","application/pdf");
						//xhr2.setRequestHeader('Authorization', 'Bearer ' + accessToken);
						xhr2.onreadystatechange=function() {
							if (xhr2.readyState==4 && xhr2.status==200)
							{
								//alert('file saved: '+xhr2.responseText);
							}
						}
						xhr2.send(byteArray);
						//var a = saveAs(blob, "thing.pdf"); //this shows up the window asking user to save the file
						PDFView.open(xhr.response);
						
						retrieveAllFilesInFolder(patientId, rootFolderId, setPatientFolderId);
					}
				};
				//send the request
				xhr.send();
          }
		  });
        });
	
		
      }
      
      /**
       * Start the file upload.
       *
       * @param filePath
       */
      function uploadFile(filePath) {
    	  
        gapi.client.load('drive', 'v2', function() {
          insertFile(filePath, signedFolderId);
        });
      }
       
      /*
      	convert array buffer to base 64
      */
      function arrayBuffToBase64(arrayBuffer) {
    	//convert the arrayBuffer to binary
		var binary = ''
		var bytes = new Uint8Array( arrayBuffer );
		var len = bytes.byteLength;
		for (var i = 0; i < len; i++) {
			binary += String.fromCharCode( bytes[ i ] )
		}
		
		return btoa(binary);
	  }
      
      /**
       * Insert new file.
       * This 
       * @param {File} fileData File object to read data from.
       * @param {Function} callback Function to call when the request is complete.
       */
      function insertFile(filePath, parentId, callback)
	  {
			const boundary = '-------314159265358979323846';
			const delimiter = "\r\n--" + boundary + "\r\n";
			const close_delim = "\r\n--" + boundary + "--";
			//alert('File path parameter value in insertFile() '+filePath);
			console.log(filePath.name);
			var contentType = "application/pdf";
			var metadata = {
				//'title': 'TestDriveUpload',//fileData.name,				
				'title': docTitle,
				'mimeType': contentType,
				//by adding the parents will insert the file to the respective folder
				'parents': 	[
				             	{
			             			'id': parentId//'0BwVED7ZAOe8NUlozckoycUJRc3c'
		             			}
			             	]
			};
			
			//get the file by making an ajax call
			var xhr = new XMLHttpRequest();
			//xhr.open('GET', 'http://localhost:83/Blob/file.pdf');
			xhr.open('GET', filePath);
			xhr.responseType = 'arraybuffer';//'blob', this will set the response type for the ajax call
				
			xhr.onerror = function() {
				//alert('xhr failed');
				callback(null);
			};
			
			xhr.onreadystatechange=function()
			{
				if (xhr.readyState==4 && xhr.status==200)
				{
					//alert("file Length: "+xhr.response.byteLength);
					var arrayBuffer = xhr.response;
						
					var base64 = arrayBuffToBase64(arrayBuffer);	

					var base64Data = base64;//btoa(reader.result);
					var multipartRequestBody =
						delimiter +
						'Content-Type: application/json\r\n\r\n' +
						JSON.stringify(metadata) +
						delimiter +
						'Content-Type: ' + contentType + '\r\n' +
						'Content-Transfer-Encoding: base64\r\n' +
						'\r\n' +
						base64Data +
						close_delim;

					var request = gapi.client.request({
						'path': '/upload/drive/v2/files',
						'method': 'POST',
						'params': {'uploadType': 'multipart'},
						'headers': {
							'Content-Type': 'multipart/mixed; boundary="' + boundary + '"'
						},
						'body': multipartRequestBody
					});
				  
					if (!callback) {
						callback = function(file) {
							console.log("The uploaded file id is: "+file.id);
						};
					}
					//request.execute(updateFile(filePath));
					request.execute(function(resp) { 
						console.log(resp); 
						console.log("Uploaded File ID: "+resp.id);
						signedFile = resp;
						updateFile(filePath)
					});					
				}
			}
			xhr.send();
      }
       
       /*
		Insert folder to Drive
       */
       function insertFolder(folderTitle, parentFolderId) {
    	   
    	   var request = gapi.client.request({
   	   			'path': '/drive/v2/files',
   	   			'method': 'POST',
   	   			'body':{
   	   				"title" : folderTitle,
   	   				"mimeType" : "application/vnd.google-apps.folder",
   	   				"parents": 	[{"id": parentFolderId}]
   	   			}
	       });
	       
	      request.execute(function(resp) {
	    	  	console.log('created Folder'+resp.title);
	    	  	if(resp.title != 'Signed' && resp.title != 'Processed') {
	    	  		setPatientFolderId(resp);
	    	  		//alert(patientFolderId);
	    	  		insertFolder('Signed', patientFolderId);
	    	  	} else {
	    	  		if(resp.title == 'Signed') {
	    	  			insertFolder('Processed', patientFolderId);
	    	  		}
	    	  	}
	      });
       }
       
       /*
        * Move the file to the patient processed folder
        */
       function updateFile(path) {
		   //alert('updateFile '+ path);
	   
    	   var folderId = processedFolderId;
    	   var fileId = originalFile.id;
			//gapi.client.load('drive', 'v2', function () {
    		  insertFileIntoFolder(folderId, fileId, path); 
			//});   			
       }
       
	   /*
		Delete the file from the server
	   */
	   function deleteFile(path){
			//alert('delete file');
			var delFileUrl = 'http://'+window.location.host+'/interface/patient_file/deletePDF.php';
			var xhr = new XMLHttpRequest();
			xhr.open('GET', delFileUrl+"?path="+path+"&signedDocUrl="+encodeURIComponent(signedFile.webContentLink));
			xhr.responseType = 'text';
			xhr.onerror = function() {
				//alert('file not downloaded');
			};
			//handle successful response
			xhr.onreadystatechange=function()
			{
				if (xhr.readyState==4 && xhr.status==200)
				{
					//once the file has been successfully removed
					console.log('the file has been deleted');
					//alert('Signed file has been deleted from the Server.');
				}
			};
			//send the request
			xhr.send();
	   }
	   
       function removeFile() {
    	   var folderId = originalFile.parents[0].id;
    	   var fileId = originalFile.id;
    	   //gapi.client.load('drive', 'v2', function () {
    		   removeFileFromFolder(folderId, fileId);
    	   //});
    	   
       }
       
       /**
        * Insert a file into a folder.
        *
        * @param {String} folderId ID of the folder to insert the file into.
        * @param {String} fileId ID of the file to insert.
        */
       function insertFileIntoFolder(folderId, fileId, path) {
         var body = {'id': folderId};
         var request = gapi.client.drive.parents.insert({
           'fileId': fileId,
           'resource': body
         });
         request.execute(function(resp) { 
			console.log(resp);
		 });
		 deleteFile(path);
		 /*var request = gapi.client.drive.files.get({
				'fileId': fileId//'0BwVED7ZAOe8NOGxWSUNTbERTS2M'//
			});
			
			request.execute(function(resp) {
				originalFile = resp;
				var fileDownloadUrl = resp.downloadUrl;
				console.log('Signed file download url: '+ fileDownloadUrl);
				});*/
       }
       
       /**
        * Remove a file from a folder.
        *
        * @param {String} folderId ID of the folder to remove the file from.
        * @param {String} fileId ID of the file to remove from the folder.
        */
       function removeFileFromFolder(folderId, fileId) {
         var request = gapi.client.drive.parents.delete({
           'parentId': folderId,
           'fileId': fileId
         });
         request.execute(function(resp) { console.log(resp); });
       }
       