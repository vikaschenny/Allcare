/*
 * Element ids:
 * id = userSignature - draw sign
 * id = userSignatureText - enter name
 */

Ext.Loader.setConfig({enabled: true});
//Ext.Loader.setPath('Ext.chooser', '../ux/chooser');
//Ext.Loader.setPath('Ext.ux', '../ux');

Ext.require([
	 'Ext.tree.*',
	 'Ext.data.*',
	 'Ext.grid.*',
	 'Ext.button.Button',
	 'Ext.data.proxy.Ajax',
	 //'Ext.chooser.InfoPanel',
	 //'Ext.chooser.IconBrowser',
	 //'Ext.chooser.Window',
	 //'Ext.ux.DataView.Animated',
	 'Ext.toolbar.Spacer'
 ]);


Ext.onReady(function() {

	Ext.QuickTips.init();
	
    var signatureData = null;
    var signatureText = null;
    var addText = "";
    var state = "NEW";
    
    //initialize the page co-ordinates
    var pageX;
    var pageY;
    //text window(left top) co-ordinates
    var txt_x;
    var txt_y;
    //Signature co-ordinates
    var x = 5;
    var y = 5;
    
    /*
     * This is a popup window that loads the user signature.
     * The window has a single input element, that would be added when
     * user puts his signature in the singature pad
     */
    var signoverlay = Ext.create('Ext.window.Window', {
		width: 180,
		height: 60,
	    layout: 'fit',
	    resizable: true,
	    title: 'Signature',
	    closable: true,
	    closeAction: 'hide',
	    id: Ext.id(),
		listeners:{
			resize:function(signoverlay, width, height, eOpts) {
				//alert("Resized window \nwidth: "+width+"	height: "+height);
				//alert(document.getElementById("userSignature").width);
				//resize the signature to fit the window
				if(document.getElementById("userSignature") != undefined) {
					document.getElementById("userSignature").width = (width-12);
					document.getElementById("userSignature").height = (height-32);
				}
			}
		}
    });
    
    var clone1 = Ext.create('Ext.window.Window', {
		width: 180,
		height: 60,
                layout: 'fit',
                resizable: true,
                title: 'Signature',
                closable: true,
                closeAction: 'hide',
                id: 'clone1',
                    listeners:{
                            resize:function(clone1, width, height, eOpts) {
                                    //alert("Resized window \nwidth: "+width+"	height: "+height);
                                    //alert(document.getElementById("userSignature").width);
                                    //resize the signature to fit the window
                                    if(document.getElementById("userSignature1") != undefined) {
                                            document.getElementById("userSignature1").width = (width-12);
                                            document.getElementById("userSignature1").height = (height-32);
                                    }
                            }
                    }
    });
    
    
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
    
	//Email button to get recipient email id from the user and send ajax request to <host>interface/email_doc.php 
	//Date Added: 17th October, 2013
	var email = Ext.create('Ext.Window',{
		  title: 'Email',
		  width: 400,
		  layout: 'fit',
		  closeAction : 'hide',
		  plain: false,
				styleHtmlContent: true,
				autoScroll : true,
				hidden: true,
				draggable: true,
		  id:'ext-mailwindow',
		  items:[
		   {
			xtype:'form',
				bodyPadding: 5,
				// Fields will be arranged vertically, stretched to full width
				   layout: 'anchor',
				   defaultType: 'textfield',
				   defaults: {
					anchor: '100%'
				   },
			items:[
			 {
			  name:'recipient',
			  fieldLabel:'Recipient Mail',
			  id:'recipient',
			  allowBlank: false,
			  emptyText: 'email address',
			  //vtype:'email',
			  /*regex: /^(([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+([;,.](([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+)*$/
			  */ 
			 }
			],
			buttons:[
			 {
			  text:'Reset',
			  handler:function(){
			   alert(docid + pid);
			   this.up('form').getForm().reset();
			  }
			 },
			 {
			  text:'Mail',
			  formBind:true,
			  disabled:true,
			  handler:function(){
			   //alert(docId);
			   var form = this.up('form').getForm();
			   Ext.Ajax.request({
				url:'http://emrsb.risecorp.com/interface/email_doc.php',
				method: 'POST',
				params:{
				 docid : docid,
				 recipient : form.getValues().recipient,
				 pid : patientId,
				},
				success:function(response){
					signoverlay.removeAll();
                    Ext.MessageBox.show({
                            msg: 'Email Sent',
                            width:300,
                         });
					setTimeout(function(){Ext.MessageBox.hide();}, 2000);
					email.close();
				},
				failure:function(response){
				 Ext.Msg.alert('Status','Failed');
				}
			   
			   });
			  }
			 }
			]
		   }
		  ]
		 
		 });
	 
    var spacer = { xtype: 'tbspacer', width: 50 };
    
    var incre = 0;
    var clones = ['clone1','clone2','clone3','clone4','clone5','clone6','clone7','clone8','clone9','clone10'];
    
    var actionToolbar = {
					        xtype: 'toolbar',
					        dock: 'top',
					        items: 
				        	[
			        	 	/*
			        	 		{
			        	 			text: 'upload',
			        	 			handler: function() {
			        	 				fileUpload.show();
			        	 			}
	
			        	 		},
		        	 		*/
				        	 	{
				        	 		text: 'Signature',
				        	 		handler:function(){
				        	 			try{
				        	 				document.getElementById('Signature').style.display='block';
					        	 			$('.sigPad').signaturePad().clearCanvas();//clear the signature image data
					        	 			$('.name').val("");//clear the typed signature data
					        	 			$('.typed').empty();
											//signatureText = "signText";
					        	 			signPopUP();
				        	 			} catch(err){
				        	 				alert(err);
				        	 			}
				        	 		}
				        	 	},spacer,
				        	 	{
				        	 		text: 'AddText',
				        	 		handler: function(){
				        	 			//alert('add your text here');
				        	 			addTextWindow.show();
				        	 		},
				        	 	},spacer,				        	 	
				        	 	{
				        	 		text:'Email',
				        	 		handler: function(){
				        	 			email.show();
				        	 		}
				        	 	},spacer,
				        	 	{
				        	 		text: 'PlaceSign',
				        	 		handler: function(){
				        	 			//sign.show();
				        	 			var pageNumber = document.getElementById('pageNumber').value;
                                                                        pageX = Ext.get('page'+pageNumber+'').getX();
                                                                        pageY = Ext.get('page'+pageNumber+'').getY();
		    	    	    			
			        	 				//set the signoverlay position
			        	 				//signoverlay.setPosition(pageX, pageY,true);
				        	 			signoverlay.show();
                                                                        // clone the signature
                                                                        signatureData =  $('.sigPad').signaturePad().getSignatureImage();
                                                                        incre++;
                                                                        // Subhan //
                                                                        clone1 = signoverlay.cloneConfig();
                                                                        clone1.add([{ html:'<img src="'+signatureData+'" width="180" height="60"/>'}]);
                                                                        clone1.doLayout();
                                                                        //clone.setPosition(pageX, pageY,true);
                                                                        clone1.show(); 
                                                                        /* --------------- */
									}
				        	 	},spacer,
				        	 	{
				        	 		text: 'PlaceText',
				        	 		handler: function(){
				        	 			//sign.show();
				        	 			var pageNumber = document.getElementById('pageNumber').value;
                                                                        pageX = Ext.get('page'+pageNumber+'').getX();
                                                                        pageY = Ext.get('page'+pageNumber+'').getY();
		    	    	    			
			        	 				//set the signoverlay position
			        	 				addTextWindow.setPosition(pageX, pageY,true);
				        	 			addTextWindow.show();
				        	 		}
				        	 	},spacer,								
				        	 	{
				        	 		text:'Save',
				        	 		handler: function(){
				        	 			
				        	 			//get the signature value
				        	 			var jq_signValue;
				        	 			if(signatureData != null){
				        	 				jq_signValue = signatureData.split("base64,");
                                                                                jq_signValue = jq_signValue[1];
				        	 			}else{
				        	 				jq_signValue = "data";
				        	 			}
				    	            	
				        	 			//get the text value
				        	 			addText = Ext.getCmp('textData').getValue();
				        	 			
                                                                        var userTextSign = signatureText;
                                                                        //var imageID = GetElementInsideContainer("images");
				        	 			//check if user has selected a signature
				        	 			if(jq_signValue != "data" || userTextSign != "signText")// || userTextSign != "signText"
			        	 				{
				        	 				//start
				        	 				myMask.show();
				        	 				//hide the overlays
                                                                                // Subhan
					        	 			//signoverlay.hide();
                                                                                // ---------------- //
                                                                                addTextWindow.hide();
                                                                                //get page details
					        	 			var pageNumber = document.getElementById('pageNumber').value;   //get the page number
                                                                                var c_width = document.getElementById('page' + pageNumber + '').width;  //get page width
                                                                                var c_height = document.getElementById('page' + pageNumber + '').height;    //              		
                                                                                pageX = Ext.get('page'+pageNumber+'').getX();
                                                                                pageY = Ext.get('page'+pageNumber+'').getY();
                                                                                var dateValue = Ext.getElementById("datefield").value;
                                                                                //get the signature position
					        	 			var signPosition = signoverlay.getPosition();
					        	 			//x = (signPosition[0]) - pageX; y = c_height - (signPosition[1] - pageY);
                                                                                x = (signPosition[0]) - pageX; y = c_height - (signPosition[1] - pageY);
                                                                                
                                                                                // For clones
                                                                                
                                                                                var clone1Position = clone1.getPosition();
                                                                                x1 = (clone1Position[0] - pageX); y1 = c_height - (clone1Position[1] - pageY);
                                                                                
                                                                                alert(x + " --- " + y);
                                                                                alert(x1 + " --- " + y1);
                                                                                
                                                                                // --------- //
                                                                                
                                                                                //x = signoverlay.pageX - pageX;
                                                                                //y = signoverlay.pageY - pageY;
					        	 			
                                                                                //alert(signPosition[0] + ' --- ' + pageX);
					        	 			//get the text position
					        	 			//var textPosition = addTextWindow.getPosition();
					        	 			//txt_x = textPosition[0] - pageX;
					        	 			//txt_y = c_height- (textPosition[1] - pageY);
					        	 			txt_x = addTextWindow.pageX - pageX;
					        	 			txt_y = addTextWindow.pageY - pageY;
			    	    	    			
			    	    	    			
					        	 			//make an ajax request
					    	            	
					    	            	//remove the image in the div tag
					    	            	var myNode = document.getElementById("images");
					    	            	myNode.innerHTML = '';
					    	            	//end
				        	 				//reset the signature and signtext
					    	            	signatureText = "signText";
								signatureData = "data";
					    	            	
                                                                //call the FDF library file to save the image and text on to pdf file
                                                                Ext.Ajax.request({
                                                                    url: 'http://emrsb.risecorp.com/interface/patient_file/SaveDoc.php',
                                                                    method: 'POST',
                                                                    disableCaching: false,
                                                                    timeout: 120000,
                                                                    //success
                                                                    success: function(response) {
                                                                            //show the message box
                                                                            myMask.hide();
                                                                            alert(response.responseText);
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

                                                                                //make a call to the gDrive to insert the pdf file
                                                                                uploadFile(response.responseText);//'http://emrsb.risecorp.com/interface/patient_file/drive2357.pdf');//response.responseText
                                                                        },
                                                                    //failure
                                                                    failure: function(response) {
                                                                        myMask.hide();
                                                                        //close the overlays
                                                                        signoverlay.removeAll();
                                                                        Ext.MessageBox.show({
                                                                            msg: 'Failure: Image not submitted',
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
                                                                        page: pageNumber, 
                                                                        x_cordinate : x,
                                                                        y_cordinate : y,
                                                                        v_width: c_width,
                                                                        v_height: c_height,
                                                                        imageData: jq_signValue,
                                                                        textData: addText,
                                                                        textX: txt_x,
                                                                        textY: txt_y,
                                                                        textSignature: userTextSign,
                                                                        fileName: originalFile.title,
                                                                        imageWidth: document.getElementById("userSignature").width,
                                                                        imageHeight: document.getElementById("userSignature").height,
                                                                        date: dateValue
                                                                    },		    
                                                                });
                                                                } else{
                                                                        myMask.hide();
                                                                        Ext.Msg.alert('Select Signature','please select a signature image or place your signature');
                                                                }
				    	            	
				        	 		}
				        	 	}
				    	 	]
					    };
    
	//signature popup window
    var signPopUP = function()
    {
    	var sign;
    	if(Ext.getCmp('ext-SignWindow')!= null)
    	{
        	Ext.getCmp('ext-SignWindow').show();
        } else{
        	sign = new Ext.Window({
                //layout      : 'fit',
                closeAction :'hide',
                plain       : true,
                contentEl   : 'Signature',
                styleHtmlContent: true,
                autoScroll : true,
                resizable: true,
                closable: true,
                id: 'ext-SignWindow',
                width: 220,
                height: 250,
                buttons:
            	[
            	 	{
        	            text     : 'save',
        	            handler  : function()
        	            {
        	            	signoverlay.removeAll();//remove elements from the overlay window
        	            	var myNode = document.getElementById("images");
        	            	//Commented 'type it' as 'sign it' alone is required 	18th Oct, 2013
        	            	//check if the user has enter name or has drawn his signature
        	            	/*if($('.name').val() != "") {
        	            		//save the text and add this as image
        	            		signatureText = $('.name').val();
        	            		signoverlay.add([{ html:'<input value="'+signatureText+'" type="text" id="userSignatureText"/>'}]);
								signoverlay.doLayout();
        	            		myNode.innerHTML = '';
        	            		Ext.getCmp('ext-SignWindow').hide();
        	            	} else {*/
        	            		//check if user has drawn his signed
        	            		if($('.output').val() != "") {
        	            		//save this signature
        	            		signatureData =  $('.sigPad').signaturePad().getSignatureImage();
                	            	signoverlay.add([{ html:'<img src="'+signatureData+'" width="180" height="60" id="userSignature"/>'}]);
					signoverlay.doLayout();
                                                                                                                                                                             
                	            	myNode.innerHTML = '';
                	            	Ext.getCmp('ext-SignWindow').hide();
        	            		} else {
        	            			Ext.Msg.alert('signature','Enter your Signature');
        	            		}
        	            	/*}*/
        	            }
        	 		},
        	 		{
        	 			text 	: 'close',
        	 			handler : function(){
        	 				//clear the image data
							//alert($('.output').val());
        	 				$('.sigPad').signaturePad().clearCanvas();        	 				
        	 				try{
        	 					Ext.getCmp('ext-SignWindow').hide();
            	 				document.getElementById('Signature').style.display='hide';
        	 				}catch(err){
        	 					//alert(err);
								Ext.Msg.alert('status', err);
        	 				}
        	 				
        	 			}
        	 		}
        	 	]
            }).show();
        }
    };
	
	//simple form
	var patientDetailsForm = Ext.create('Ext.form.Panel', {
		title: 'Patient',
		bodyPadding: 5,
		width: '100%',
		height: window.innerHeight,
	
		// The form will submit an AJAX request to this URL when submitted
		url: 'save-form.php',

		// Fields will be arranged vertically, stretched to full width
		layout: 'anchor',
		defaults: {
			anchor: '100%'
		},

		// The fields
		defaultType: 'displayfield',
		items: [{
			//xtype: 'combo',
			fieldLabel: 'Patient Name',
			name: 'patientname',
			value: openEMR_patientName
		},{
			fieldLabel: 'Category',
			name: 'category',
			value: openEMR_category
		},{
			fieldLabel: 'Status',
			name: 'status',
			value: openEMR_status
		},{
			fieldLabel: 'History',
			name: 'history',
			value: openEMR_history
		}]
	});
    
	//check for the status and load the appropriate viewer components
	var dynamicViewer = function(){
		if(openEMR_status === "tobesigned" || openEMR_status === "signed"){
			alert("hello");
			actionToolbar.hidden = false;
		}else if(openEMR_status === "processed"){
			actionToolbar.hidden = true;
		}
	};
	dynamicViewer();
	
    var viewport = Ext.create('Ext.Viewport', {
    		layout:'hbox',
	    	fullscreen: true,
	        items: 
	    	[
	    	 	/*Hide left panel. Show the document viewer panel alone
				{
					//this panel shows the patient details
	    	 		xtype:'panel',
	    	 		//flex:1,
					width: '20%',
	    	 		layout:'fit',
	    	 		items:
    	 			[
    	 			 	patientDetailsForm
	 			    ]
	    	 	},*/
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
	        	resize:function(vp, width, height, oldWidth, oldHeight, eOpts ) {
	        		//alert('dolayout');
	        		vp.doLayout();
	        	},
	        	render:function(vp, eOpts) {
	        		//alert('viewport');
	        		myMask = new Ext.LoadMask(vp, {msg:"Please wait..."});
	        	}
	        }
	    }); 
});

function updateDocStore() {
	var tg = Ext.getCmp('DocTreePanel');
	//alert('tg'+ tg);
	var store = tg.getStore();
	//store.removeAll();
	store.load();
	tg.setLoading(true);
}


