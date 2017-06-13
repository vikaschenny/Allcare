Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.chooser', '../ux/chooser');
Ext.Loader.setPath('Ext.ux', '../ux');

Ext.require([
	 'Ext.tree.*',
	 'Ext.data.*',
	 'Ext.grid.*',
	 'Ext.button.Button',
	 'Ext.data.proxy.Ajax',
	 'Ext.chooser.InfoPanel',
	 'Ext.chooser.IconBrowser',
	 'Ext.chooser.Window',
	 'Ext.ux.DataView.Animated',
	 'Ext.toolbar.Spacer'
 ]);


Ext.onReady(function() {
	var windowURL = window.location.href;       	
	//gUrl = getQuerystring('url', windowURL);
    Ext.QuickTips.init();    
    var signatureData = "data";
    var addText = "signedText";
    var pageNO = document.getElementById('pageNumber').value;
    var windowHeight = window.innerHeight;
    var state = "NEW";
    var signoverlay = Ext.create('Ext.window.Window', {
	    layout: 'fit',
	    resizable: true,
	    title: 'Signture',
	    closable: true,
	    closeAction: 'hide',
	    id: 'ext-signoverlay',
	    contentEl: 'signatureHolder'
	});
    
    var signatureImageId = "signpad";
    //load mask
    var myMask;
    /*
     * This button just opens the window. We render it into the 'buttons' div and set its
     * handler to simply show the window
     */
    var insertButton = Ext.create('Ext.button.Button', {
        text: "Insert Image",
        handler : function() {
        	var drive_Image_Store = Ext.data.StoreManager.lookup('Drive_Images_Store');
        	drive_Image_Store.load();
            win.show();
        }
    });
    
    /*
     * Here is where we create the window from which the user can select images to insert into the 'images' div.
     * This window is a simple subclass of Ext.window.Window, and you can see its source code in Window.js.
     * All we do here is attach a listener for when the 'selected' event is fired - when this happens it means
     * the user has double clicked an image in the window so we call our insertSelectedImage function to add it
     * to the DOM (see below).
     */
    var win = Ext.create('Ext.chooser.Window', {
        id: 'img-chooser-dlg',
        animateTarget: insertButton.getEl(),
        closable: true,
        listeners: {
            selected: insertSelectedImage
        }
    });
    
    function GetElementInsideContainer(containerID) {
        var elm = {};
        var elms = document.getElementById(containerID).getElementsByTagName("*");
        	elm = elms[0];
    	if(elm != null){
    		return elm.id;
    	} else{
    		return "noImageTag";
    	}
        
    }
    /*
     * This function is called whenever the user double-clicks an image inside the window. It creates
     * a new <img> tag inside the 'images' div and immediately hides it. We then call the show() function
     * with a duration of 500ms to fade the image in. At the end we call .frame() to give a visual cue
     * to the user that the image has been inserted
     */
    function insertSelectedImage(images) {
    	
    	//remove the child nodes before adding new child nodes
    	var myNode = document.getElementById("images");
    	myNode.innerHTML = '';
    	
        //create the new image tag
        var image = Ext.fly('images').createChild({
            tag: 'img',
            src: images.get('thumbnailLink'),
            id: images.get('id')
        });
        
        //alert(images.get('id'));
        //var imageID = document.getElementById("images").children[0].id;
        //document.getElementById(imageID).innerHTML.style.opacity = 1;
        Ext.getDom(images.get('id')).style.opacity = 1;
        //hide it straight away then fade it in over 500ms, finally use the frame animation to give emphasis
        image.hide().show({duration: 500}).frame();
        
        //this will make the window animate back to the newly inserted image element
        //win.animateTarget = image;
        signoverlay.removeAll();
    	//signoverlay.add([{ html:'<img src="'+images.get('thumbnailLink')+'" id="'+images.get('id')+'" width="140" height="60" />'}]);
    	//signatureData = "data";
        setSignatureImage(images.get('thumbnailLink'), images.get('id'));
    }
    
    
    
    var pageX;
    var pageY;
    var txt_x, txt_y;
    
    
    var addTextWindow = Ext.create('Ext.window.Window', {
    	layout      : 'fit',
        closeAction :'hide',
        hidden: true,
        resizable: false,
        title: 'Text',
        width: 150,
        items:
    	[
    	 	{
    	 		xtype: 'textfield',
    	 		id: 'textData',
    	 		name: 'textdata',
    	 		grow: true,
    	 		emptyText: 'Enter Text Here!!!'
    	 		
    	 	}
	    ]
    });
    
    var sendFax = new Ext.Window({
    	title: 'FAX',
        layout      : 'fit',
        closeAction :'hide',
        plain       : true,
        styleHtmlContent: true,
        autoScroll : true,
        id: 'ext-sendFaxWindow',
        items:
    	[
    	 	{
    	 		xtype:'form',
    	 		
    	 		bodyPadding: 5,
    	 	    
    	 		// Fields will be arranged vertically, stretched to full width
    	 	    layout: 'anchor',
    	 	    
    	 	    defaultType: 'textfield',
    	 	    items: 
	 	    	[
			 	    {
		 	        	fieldLabel: 'Recipient:',
			 	        name: 'recipient',
			 	        allowBlank: true,
			 	        value:'18775197473'
			 	    }
    	 	    ],
    	 	   buttons: 
	 		   [
	 		    	{
	 		    		text: 'Reset',
	 		    		handler: function() {
	 		    			this.up('form').getForm().reset();
	 		    		}
	 		    	},
	 		    	{
	 		    		text: 'FAX',
 		    			formBind: true, //only enabled once the form is valid
 		    			disabled: true,
 		    			handler: function() {
 		    				myMask.show();
 		    				//alert(fileID);
 		    				var form = this.up('form').getForm();
 		    				//make an ajax call
 		    				Ext.Ajax.request({
 		    					url: g_preURL+'/fax',
 	                		    method: 'GET',
 	                		    disableCaching: false,
 	                		    timeout: 60000,
 	                		    //success
 	                		    success: function(response) {
 	                		    	myMask.hide();
 	                		        alert("FAX Sent!!!"+response.responseText);
 	                		        Ext.getCmp('ext-sendFaxWindow').hide();
 	                		    },
 	                		    //failure
 	                		    failure: function(response) {
 	                		    	myMask.hide();
 	                		        alert("Error !!!"+response.responseText);
 	                		    },
 	                		    //send params
 	                		    params: {
 	                		    	fileID: fileID,
 	                		    	recipient: form.getValues().recipient
 	                		    }
 	                	  });
 		    			}
	 		    	}
 		    	]
    	 	}
	    ]
    });
    
    var mail = new Ext.Window({
    	title: 'Send Email',
        layout      : 'fit',
        closeAction : 'hide',
        plain       : false,
        styleHtmlContent: true,
        autoScroll : true,
        hidden: true,
        draggable: true,
        id:'ext-mailWindow',
        items:
    	[	
    	 	{
    	 		xtype:'form',
    	 		
    	 		bodyPadding: 5,
    	 		// Fields will be arranged vertically, stretched to full width
    	 	    layout: 'anchor',
    	 	    defaultType: 'textfield',
    	 	    items: 
	 	    	[
	 	    	 	{
			            xtype      : 'fieldcontainer',
			            fieldLabel : 'Size',
			            //defaultType: 'radiofield',
			            defaults: {
			                flex: 1
			            },
			            layout: 'hbox',
			            items: [
			                {
			                    xtype     : 'radiofield',
			                    boxLabel  : 'Signed',
			                    name      : 'driveFolder',
			                    inputValue: 'signed',
			                    id        : 'radio1'
			                }, {
			                	xtype	  :	'radiofield',
			                    boxLabel  : 'Processed',
			                    name      : 'driveFolder',
			                    inputValue: 'processed',
			                    id        : 'radio2'
			                }
			            ]
			        },
					{
			 			name: 'to',
			 			fieldLabel: 'To Address: ',
			 			vtype: 'email'
					},
	 	    	 	{
	 	    	 		name: 'subject',
		 	        	fieldLabel: 'Subject:',
			 	        allowBlank: true
			 	    },
			 	    {
			 	    	name: 'message',
		 	        	fieldLabel: 'Message:',
			 	        allowBlank: true,
			 	        //value: 'Link to the file:'+link,
			 	    }
    	 	    ],
    	 	   buttons: 
	 		   [
	 		    	{
	 		    		text: 'Reset',
	 		    		handler: function() {
	 		    			this.up('form').getForm().reset();
	 		    		}
	 		    	},
	 		    	{
	 		    		text: 'Submit',
 		    			formBind: true, //only enabled once the form is valid
 		    			disabled: true,
 		    			handler: function() {
 		    				Ext.getCmp('ext-mailWindow').hide();//hide the mail window
 		    				myMask.show();//show the mask
 		    				
 		    				
 		    				var form = this.up('form').getForm();
 		    				var message = form.getValues().message;
 		    				var radio1 = Ext.getCmp('radio1').getValue();
 		    				var radio2 = Ext.getCmp('radio2').getValue();
 		    				if(radio1){
 		    					state = "SIGNED";
 		    				} else{
 		    					if(radio2){
 		    						state = "PROCESSED";
 		    					}
 		    				}
 		    				var email_message = message.concat("\nFile Link: "+link);
 		    				//add permission to the user
 		    				Ext.Ajax.request({
 	                		    //url: 'http://ec2-23-21-48-239.compute-1.amazonaws.com/DocumentViewer_02/drive',
 		    					url: g_preURL+'/drive',
 	                		    method: 'GET',
 	                		    disableCaching: false,
 	                		    //success
 	                		    success: function(response) {
 	                		        alert("permission granted");
 	                		        //send the mail now
 	    		    				Ext.Ajax.request({
 	    	                		    //url: 'http://ec2-23-21-48-239.compute-1.amazonaws.com/DocumentViewer_02/mail',
 	    	                		    url: g_preURL+'/mail',
 	    	                		    method: 'GET',
 	    	                		    disableCaching: false,
 	    	                		    //success
 	    	                		    success: function(response) {
 	    	                		    	myMask.hide();
 	    	                		        alert("Email Sent!!!");
 	    	                		        updateDocStore();
 	    	                		    },
 	    	                		    //failure
 	    	                		    failure: function(response) {
 	    	                		    	myMask.hide();
 	    	                		        alert("Error !!!"+response.responseText);
 	    	                		    },
 	    	                		    //callback
 	    	                		    callback: function(response) {
 	    	                		        //alert("It is what it is");
 	    	                		    },
 	    	                		    //send params
 	    	                		    params: {
 	    	                		    	//username: form.getValues().username,
 	    	                		    	//password: form.getValues().password,
 	    	                		    	to: form.getValues().to,
 	    	                		    	subject: form.getValues().subject,
 	    	                		    	message: email_message,//form.getValues().message
 	    	                		    	//add state params
 	    	                		    },		    
 	    	                	  });
 	                		        //PDFpanel.doLayout();
 	                		    },
 	                		    //failure
 	                		    failure: function(response) {
 	                		    	myMask.hide();
 	                		        alert("permission Error !!!"+response.responseText);
 	                		    },
 	                		    //callback
 	                		    callback: function(response) {
 	                		        //alert("It is what it is");
 	                		    },
 	                		    //send params
 	                		    params: 
 	                		    {               		    	
 	                		    	fileID: fileID,
 	                		    	emailID: form.getValues().to,
 	                		    	state: state
                		    	},		    
 		    				});
 		    				
 		    				
 		    			}
	 		    	}
 		    	],
 		    	//renderTo: Ext.getBody()
 		    	//renderTo: Ext.get('SENDEMAIL')
    	 	}
	    ]
    });
    
    
    Ext.define('Task', {
        extend: 'Ext.data.Model',
        fields: [
             //drive metadata
             {name: 'user',     type: 'string'},
             {name: 'duration', type: 'string'},
             {name: 'done',     type: 'boolean'},
             //drive metadata
             {name: 'title',     type: 'string'},
             {name: 'alternateLink', type: 'string'},
             {name: 'fileSize', type: 'string'},
             {name: 'ownerNames', type: 'string'},
             {name: 'lastModifyingUserName', type: 'string'},
             {name: 'leaf', type: 'boolean'}
        ]
    });

    var store = Ext.create('Ext.data.TreeStore', {
        model: 'Task',
        proxy: {
            type: 'ajax',
            //the store will get the content from the .json file
            url: g_preURL+'/drive?fileID=LIST',
            //url: 'treePanel.json',
            reader: {
                type: 'json',
                root: 'countries'
            }
        },
        root: {leaf: false},
        folderSort: true
        
    });
    
  //Ext.ux.tree.TreeGrid is no longer a Ux. You can simply use a tree.TreePanel
    var fileID = null;
    var link = "this is a link";
    var tree = Ext.create('Ext.tree.Panel', {
    	title: 'Drive Folder/Files',
    	id: 'DocTreePanel',
        width: '100%',
		height: window.innerHeight,
        collapsible: false,
        useArrows: true,
        rootVisible: false,
        store: store,
        multiSelect: true,
        singleExpand: false,
        //the 'columns' property is now 'headers'
        columns: [{
            xtype: 'treecolumn', //this is so we know which column will show the tree
            text: 'Title',
            flex: 2,
            sortable: true,
            dataIndex: 'title'
        },{
            text: 'lastModifyingUserName',
            flex: 1,
            dataIndex: 'lastModifyingUserName',//'user',
            sortable: true
        },{
            text: 'fileSize',
            flex: 1,
            dataIndex: 'fileSize',//'user',
            sortable: true
        },{
        	text: 'ownerNames',
        	flex: 1,
        	dataIndex: 'ownerNames',
        	sortable: true
        }],
        listeners: {
        	itemclick: {
        	    fn: function(self, record, html_element, node_index, event) {
        	    	//alert(record.get('title'));
        	    	fileID = record.get('id');
        	    	link = record.get('alternateLink');
        	    	//alert(record.get('leaf'));
        	    	if(record.get('leaf')){
        	    		PDFView.loadingBar = new ProgressBar('#loadingBar', {});
        	    		PDFView.open("/DocumentViewer_02/drive?fileID="+fileID);
            	    	document.getElementById('pageNumber').value;
            	    	myMask.show();
            	    	setTimeout(function(){myMask.hide();},4000)
            	    	
        	    	} else{
        	    		//Ext.Msg.alert('you have selected '+ record.get('title')+' folder\nSelect a File');
        	    	}
        	    }
        	},
        	render: function(me){
        		//me.setLoading(true);
        		if(me.getStore().isLoading()){
        			me.setLoading(true);
        		}
        	},
        	load: function(me, node, records, successful, eOpts){
        		Ext.getCmp('DocTreePanel').setLoading(true);
        		if(successful){
        			//alert('data loaded');
        			//Ext.getCmp('DocTreePanel').setLoading(false);
        			setTimeout(function(){
        				//This simulates a long-running operation like a database save or XHR call.
   		             	//In real code, this would be in a callback function.
        				Ext.getCmp('DocTreePanel').setLoading(false);
   		            	//Ext.example.msg('Done', 'Your fake data was saved!');
   		         }, 1000);
        		}
        	}
        	
        }
    });
    
    var fileUpload = new Ext.Window({
        layout      : 'fit',
        closeAction :'hide',
        plain       : false,
        //renderTo: Ext.getBody(),
        hidden: true,
        draggable: true,
        frame: true,
        title: 'Upload Signature Image',
        closable: true,
        id: 'ext-fileUploadWindow',
        items:
    	[
    	 	{
    	 		xtype:'form',
    	 		
    	 		bodyPadding: 5,
    	 		// Fields will be arranged vertically, stretched to full width
    	 	    layout: 'anchor',
    	 	    defaultType: 'textfield',
    	 	    items: 
	 	    	[
					{
						xtype: 'filefield',
			            emptyText: 'Select an image',
			            fieldLabel: 'Photo',
			            msgTarget: 'side',
			            name: 'photo',
			            buttonText: 'Select Photo...',
			            allowBlank: false,
			            anchor: '100%'  
					}
    	 	    ],
    	 	   buttons: 
	 		   [
					{
						text: 'Upload',
						handler: function() {
							
                        	Ext.getCmp('ext-fileUploadWindow').hide();//hide the window
							myMask.show();
							
							var form = this.up('form').getForm();
							var fileName = form.findField('photo').getValue();
							//validate if the selected file is an image file
							var fileExt = fileName.substring(fileName.lastIndexOf('.') + 1).toLowerCase();
							//alert(fileExt+" file extension");
							if(form.isValid()){
								if(fileExt == "gif" || fileExt == "png" || fileExt == "jpg" || fileExt == "jpeg"){
									form.submit({
				                        //url: 'http://ec2-23-21-48-239.compute-1.amazonaws.com/DocumentViewer_02/fileupload',
				                        url: g_preURL+'/fileupload',
				                        //waitMsg: 'Uploading your file...',
				                        success: function(form, action) {
				                        	myMask.hide();
				                        	//alert('success');
				                        	Ext.Msg.alert('Success', 'Uploaded file "' + action.result.file + '" to GDrive');
				                        	
				                         },
				                         failure: function(form, action) {
				                        	 myMask.hide();
				                        	 Ext.Msg.alert('Failed', action.result.file);
				                         }
				                    });
								} else{
									Ext.Msg.alert('Failure','Select an image file');
								}
							}
						}
					},
					{
						text: 'close',
						handler: function(){
							Ext.getCmp('ext-fileUploadWindow').hide();
						}
					}
 		       ]
    	 	}
	    ]
    });
    
    var toolbar = {
		xtype:'toolbar',
	    items: [
	        {
	            xtype: 'button', // default for Toolbars
	            text: 'Submit',
	            handler:function(){
	            	alert("sry!!!");
	            }
	        },
	        {
	            xtype    : 'textfield',
	            name     : 'field1',
	            emptyText: 'enter search term'
	        },
	    ]	
    };
    
    var spacer = { xtype: 'tbspacer', width: 100 };
    
    var x = 5;
    var y = 5;
    var actionToolbar = {
					        xtype: 'toolbar',
					        dock: 'top',
					        items: 
				        	[	
				        	 	insertButton,
				        	 	{
				        	 		text: 'upload',
				        	 		handler: function(){
				        	 			fileUpload.show();
				        	 		}
				        	 	
				        	 	},
				        	 	{
				        	 		text: 'Signature',
				        	 		handler:function(){
				        	 			try{
				        	 				
				        	 				document.getElementById('Signature').style.display='block';
					        	 			$('.sigPad').signaturePad().clearCanvas();//clear the signature image data
					        	 			signPopUP();
				        	 			} catch(err){
				        	 				alert(err);
				        	 			}
				        	 			
				        	 			//sign.show();
				        	 			//var pageNumber = document.getElementById('pageNumber').value;
		    	    	    			//pageX = Ext.get('page'+pageNumber+'').getX();
		    	    	    			//pageY = Ext.get('page'+pageNumber+'').getY();
				        	 		}
				        	 	},
				        	 	{
				        	 		text: 'AddText',
				        	 		handler: function(){
				        	 			alert('add your text here');
				        	 			addTextWindow.show();
				        	 		},
				        	 	},
				        	 	{
				        	 		text:'Fax',
				        	 		handler:function(){
				        	 			sendFax.show();
				        	 		}
				        	 	
				        	 	},
				        	 	{
				        	 		text:'Email',
				        	 		handler: function(){
				        	 			mail.show();
				        	 		}
				        	 	},
				        	 	{
				        	 		text: 'PlaceSign',
				        	 		handler: function(){
				        	 			//sign.show();
				        	 			var pageNumber = document.getElementById('pageNumber').value;
		    	    	    			pageX = Ext.get('page'+pageNumber+'').getX();
		    	    	    			pageY = Ext.get('page'+pageNumber+'').getY();
		    	    	    			
			        	 				//set the signoverlay position
			        	 				signoverlay.setPosition(pageX, pageY,true);
				        	 			signoverlay.show();
				        	 			addTextWindow.setPosition(pageX, pageY,true);
				        	 			addTextWindow.show();
				        	 		}
				        	 	},
				        	 	{
				        	 		text:'Save',
				        	 		handler: function(){
				        	 			
				        	            myMask.show();				        	          
				        	 			var pageNumber = document.getElementById('pageNumber').value;//get the page number
				                		var c_width = document.getElementById('page'+pageNumber+'').width;//get page width
				                		var c_height = document.getElementById('page'+pageNumber+'').height;//              		
				                		pageX = Ext.get('page'+pageNumber+'').getX();
		    	    	    			pageY = Ext.get('page'+pageNumber+'').getY();
				        	 			var signPosition = signoverlay.getPosition();
				        	 			x = (signPosition[0]) - pageX; y = c_height - (signPosition[1] - pageY);
				        	 			//hide the overlays
				        	 			signoverlay.hide();
		                		        addTextWindow.hide();
				        	 			var textPosition = addTextWindow.getPosition();
				        	 			txt_x = textPosition[0] - pageX;
				        	 			txt_y = c_height- (textPosition[1] - pageY);
				        	 			//alert(txt_x+";"+txt_y);
				        	 			//make an ajax request`
				        	 			var jq_signValue;
				        	 			var driveImageID;
				        	 			if(signatureImageId != 'signpad'){
				        	 				driveImageID = signatureImageId;
				        	 			}else{
				        	 				jq_signValue = document.getElementById("signpad").src;
				        	 				jq_signValue = jq_signValue.split("base64,");
					    	            	jq_signValue = jq_signValue[1];
				        	 			}
				    	            	
				    	            	//alert(fileID);
				    	            	addText = Ext.getCmp('textData').getValue(); 
				    	            	//alert(addText);
				    	            	//send the image fileID
				    	            	//var imageID = GetElementInsideContainer("images");
				        	 			
				    	            	
				        	 			//check if user has selected a signature
				        	 			if(jq_signValue != "data" || imageID != "noImageTag")
			        	 				{
				        	 				//var imageID = div_image.id;
				        	 				Ext.Ajax.request({
					    	            		url: g_preURL+'/drive',
					                		    method: 'POST',
					                    		disableCaching: false,
					                    		timeout: 120000,
					                    		//success
					                    		success: function(response) {
					                		        //alert("Success: Image submitted");
					                		        //show the message box
					                		        myMask.hide();
					                		        
					                		        Ext.MessageBox.show({
					                		            msg: 'Success: Image submitted',
					                		            width:300,
					                		        });
					                		         setTimeout(function(){
					                		             //This simulates a long-running operation like a database save or XHR call.
					                		             //In real code, this would be in a callback function.
					                		             Ext.MessageBox.hide();
					                		             //Ext.example.msg('Done', 'Your fake data was saved!');
					                		         }, 2000);
					                		        
					                		       //close the overlays
						                		    signoverlay.removeAll();
					                		        
					                		       
					                		        //update the viewer with newerversion
					                		        updateDocStore();
					                		        //PDFView.open("http://ec2-23-21-48-239.compute-1.amazonaws.com/DocumentViewer_02/drive?fileID="+fileID);
					                		        PDFView.open("http://localhost:8080/DocumentViewer_02/drive?fileID="+fileID);
					                		        
					                		    },
					                		    //failure
					                		    failure: function(response) {
					                		    	myMask.hide();
					                		    	//close the overlays
					                		        signoverlay.removeAll();
					                		    	Ext.MessageBox.show({
					                		            msg: 'Error: Image not submitted',
					                		            width:300,
					                		        });
					                		        setTimeout(function(){Ext.MessageBox.hide();}, 2000);
					                		    },
					                		    //callback
					                		    callback: function(response) {
					                		        //alert("It is what it is");
					                		    },
					                		    //send params
					                		    params: {
					                		    	fileID: fileID,
					                		    	page: pageNumber, 
					                		    	x_cordinate : x,
					                		    	y_cordinate : y,
					                		    	v_width: c_width,
					                		    	v_height: c_height,
					                		    	imageData: jq_signValue,
					                		    	imageID: imageID,
					                		    	textData: addText,
					                		    	textX: txt_x,
					                		    	textY: txt_y
					                		    },		    
						            		});
			        	 				} else{
			        	 					Ext.Msg.alert('Select Signature','please select a signature image or place your signature');
			        	 				}
				    	            	
				        	 		}
				        	 	}
				    	 	]
					    };
    
    //
    function setSignatureImage(imgSrc, id){
    	document.getElementById("signpad").src = imgSrc;
    	signatureImageId = id;
    }
  //sign window
    function signPopUP()
    {
    	var sign;
    	if(Ext.getCmp('ext-SignWindow')!= null)
    	{
        	Ext.getCmp('ext-SignWindow').show();
        } else{
        	sign = new Ext.Window({
                layout      : 'fit',
                closeAction :'hide',
                plain       : true,
                contentEl   : 'Signature',
                styleHtmlContent: true,
                autoScroll : true,
                resizable: false,
                closable: true,
                id: 'ext-SignWindow',
                buttons:
            	[
            	 	{
        	            text     : 'save',
        	            handler  : function()
        	            {
        	            	signatureData =  $('.sigPad').signaturePad().getSignatureImage();
        	            	addText = $('.typed').text();
        	            	signoverlay.removeAll();
        	            	setSignatureImage(signatureData, "signpad");
        	            	
        	            	//signoverlay.add([{ html:'<img src="'+signatureData+'" width="140" height="60" />'}]);
        	            	//var myNode = document.getElementById("images");
        	            	//myNode.innerHTML = '';
        	            	Ext.getCmp('ext-SignWindow').hide();
        	            }
        	 		},
        	 		{
        	 			text 	: 'close',
        	 			handler : function(){
        	 				//clear the image data
        	 				$('.sigPad').signaturePad().clearCanvas();
        	 				try{
        	 					Ext.getCmp('ext-SignWindow').hide();
            	 				document.getElementById('Signature').style.display='hide';
        	 				}catch(err){
        	 					alert(err);
        	 				}
        	 				
        	 			}
        	 		}
        	 	]
            }).show();
        }
    }
    
    var viewport = Ext.create('Ext.Viewport', {
    		layout:'hbox',
	    	fullscreen: true,
	        items: 
	    	[
	    	 	{
	    	 		xtype:'panel',
	    	 		flex:1,
	    	 		layout:'fit',
	    	 		items:
    	 			[
    	 			 	tree
	 			    ]
	    	 	},
	    	 	{
	    	 		//pdf Viewer Panel
	    	 		xtype:'panel',
	    	 		flex:2,
	    	 		id: 'pdfViewerPanel',
	    	 		contentEl:'outerContainer',
	    	 		width: '100%',
    	    		height: window.innerHeight,
    	    		layout:'fit',
    	    		dockedItems: [actionToolbar]
	    	 	}
	        ],
	        listeners:{
	        	resize:function(vp, width, height, oldWidth, oldHeight, eOpts ){
	        		//alert('dolayout');
	        		vp.doLayout();
	        	},
	        	render:function(vp, eOpts){
	        		//alert('viewport');
	        		myMask = new Ext.LoadMask(vp, {msg:"Please wait..."});
	        	}
	        }
	    }); 
});

function updateDocStore(){
	var tg = Ext.getCmp('DocTreePanel');
	//alert('tg'+ tg);
	var store = tg.getStore();
	//store.removeAll();
	store.load();
	tg.setLoading(true);
	//tg.doLayout();	
}

