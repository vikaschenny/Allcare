<html>
    <head>
        <script src="https://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzLfZ_qAqcFD_6m-WPm-bcEp6fR5bGdLs&sensor=false"></script>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
        <script>
        var addr = '<?php echo $_REQUEST['hdnAddressBack1']; ?>';
        addr = addr.split('~~~').join('#');
        </script>
    </head>
    <body>
        <?php 
            require_once("verify_session.php");
            $google_api_key = '';
            $get_google_api_key = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='GoogleMapApiKey'");
            while($set_google_api_key = sqlFetchArray($get_google_api_key)){
            $google_api_key = $set_google_api_key['title'];
            }
        ?>
        <div id='divGoogleMap' style='float:right;height:700px;width:100%;border:1px solid;'></div>
        <input type='hidden' id="hdnAddressBackId" name='hdnAddressBack' />
        <input type='hidden' id="pid" name='pid' value="<?php echo $_GET['pid']; ?>" />
        <script>
            $('#hdnAddressBackId').val(addr);
            google.maps.event.addDomListener(window, 'load', initialize);
            google.maps.event.addDomListener('#divGoogleMap', 'load', initialize);

            function initialize()
            {
            //var latlng = new google.maps.LatLng(100,100);
            var latlng = new google.maps.LatLng(32.780140, -96.800451);
            var mapOptions = {
                             zoom: 12,
                             center: latlng
                     }

            map = new google.maps.Map(document.getElementById("divGoogleMap"), mapOptions);
            showLocations();
            }
            
        
        
        function showLocations()
        {
        var marker = new google.maps.Marker({
           map: map, position: ''
        });     
        var markersArray = [];
        //var addresses=document.getElementsByName("hdnAddress");
        var addressesback = $("#hdnAddressBackId").val();
        addressesback = addressesback.split("||||");
        for (var i=0;i<markersArray.length;i++) 
        {
            markersArray[i].setMap(null);
        }
        // alert('ADDRS LrL= '+addresses.length);   
        var get_address=0;
        var get_addressBack=0;
        //for(var i=0;i<addresses.length;i++)
        //{
        //$('#AddrVal').dataTable().fnClearTable();
        //alert(addressesback.length);
        $.each(addressesback,function(i,val){

        /**************************************************************************/
        //alert('add '+i+':'+jQuery("#hdnAddressBack_"+i).val());
        //var geocoder = new google.maps.Geocoder();
        var address_patient = val;
        var show_address= address_patient.split("$*$").join("<br/>");
        //var show_address= val;
        //alert('ADDRS_D = '+jQuery("#hdnDetails_"+i).val());

        //setTimeout( function () {
        //var address = encodeURIComponent(address_patient);
        
        var address = address_patient.split("$*$");
        var street = encodeURIComponent(address[0]);
        var city = encodeURIComponent(address[1]);
        var state = encodeURIComponent(address[2]);
        var zip = encodeURIComponent(address[3]);
        var country = encodeURIComponent(address[4]);
        var latitude = encodeURIComponent(address[5] || "");
        var longitude = encodeURIComponent(address[6] || "");
        var pid = encodeURIComponent(address[7]);
        var name = encodeURIComponent(address[8]);
        var phome = encodeURIComponent(address[9]);
        var pbiz = encodeURIComponent(address[10]);
        var pcontact = encodeURIComponent(address[11]);
        var pcell = encodeURIComponent(address[12]);
        var popendate = encodeURIComponent(address[13]);
        
        
        var show_address = name + "<br/>" + street +"$$"+ city +"$$"+ state +"$$"+ zip +"$$"+ country + "<br />Home Phone: " + phome +"<br />Business Phone: "+ pbiz +"<br />Contact: "+ pcontact +"<br />Cell: "+ pcell + "<br />Open Appointment: " + popendate;
        show_address = show_address.split("$$").join(" ");
        show_address = show_address.split("%24%24").join(" ");
        show_address = show_address.split("%20S%20").join(" ");
        show_address = show_address.split("%2C").join(" ");
        show_address = show_address.split("%20").join(" ");
        
        //alert(address);
        //            jQuery.getJSON('geocode.php?addr='+address, function(data) { 
        //                var results = data.results;
        //                status = data.status;

        
        if(address_patient != "$*$$*$$*$$*$$*$$*$$*$"){
            if(latitude!="" && longitude!=""){
                var lat = latitude;
                var lon = longitude;
                var latlon = lat +","+lon;
                if (lat != "" & lon != "") 
                    {
                        $.ajax({
                            type: 'get',
                            cache: false,
                            url: "https://maps.googleapis.com/maps/api/geocode/json?latlng="+latlon+"&sensor=false",
                            success: function(response) {
                               // alert(JSON.stringify(response));
                                var obj = JSON.parse(JSON.stringify(response) );
                                //alert(JSON.stringify(obj["results"][0]["formatted_address"]));
                                //alert(address_patient);
                                patientAddSplit = show_address.split('-<br>');
                                patientName = patientAddSplit[0];
                                correctAddress = JSON.stringify(obj["results"][0]["formatted_address"]);
                                correctAddress = correctAddress.substring(1,correctAddress.length-1);
                                //alert(correctAddress +"!="+ address_patient);
                                var exactaddress_patient = address_patient.split("$*$").join(", ");
                                //alert(exactaddress_patient);
                                var formatedAddr = exactaddress_patient.split(",");
                                // Eliminate firt word from address
                                //formatcorrectAddress = correctAddress.substring( correctAddress.indexOf(" ") + 1, correctAddress.length );
                                formatcorrectAddress = correctAddress;
                                var streetformat = formatedAddr[0].substring( formatedAddr[0].indexOf(" ") + 1, formatedAddr[0].length );
                                var completeAddr = streetformat+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                var displayAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                var cAdd = formatcorrectAddress.split(" ").join("");
                                var comAdd = completeAddr.split(" ").join("");
                                
                            },
                            error: "Unknown Place"
                        });
                        var myLatlng = new google.maps.LatLng(lat, lon);
                        map.setCenter(myLatlng);
                        marker = new google.maps.Marker({
                                   map: map,
                                   position: myLatlng

                        });

                        //marker.setTitle(jQuery("#hdnAddress_"+get_address).val());
                        marker.setTitle(exactaddress_patient);

                        //marker.setIcon('https://maps.google.com/mapfiles/ms/icons/yellow.png');
                        if($('#pid').val() == pid){
                            marker.setIcon('images/green.png');
                        }
                        else{
                            marker.setIcon('images/red.png');
                        }    
                        markersArray.push(marker);
                        var infowindow = new google.maps.InfoWindow();
                        var service = new google.maps.places.PlacesService(map);

                        //var show_address=jQuery("#hdnDetails_"+get_address).val();
        //alert('add '+i+':'+jQuery("#hdnAddress_"+i).val());
        //alert('show_address '+i+':'+jQuery("#hdnDetails_"+i).val());
                        google.maps.event.addListener(marker, 'click', function() {

                              infowindow.setContent(show_address.toString());
                              infowindow.open(map, this);

                            //get_address++;
                        });

                        google.maps.event.addListener(marker, 'dblclick', function() {

                        //map.setZoom(3);

                        });


                        get_addressBack++;

                    } 
                    else{
                        var exactaddress_patient = address_patient.split("$*$").join(", ");
                        var formatedAddr = exactaddress_patient.split(",");
                        var completeAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                        patientAddSplit = show_address.split('-<br>');
                        patientName = patientAddSplit[0];
                        
                    }
            }
            else{
                    $.ajax({
                            type: 'get',
                            cache: false,
                            url: "https://maps.googleapis.com/maps/api/geocode/json?address="+street+","+city+","+state+","+zip+","+country+"&sensor=false&key=<?php echo $google_api_key; ?>",
                            success: function(response) {
                                var coordinateobj = JSON.parse(JSON.stringify(response) );
                                if(coordinateobj['status'] != 'ZERO_RESULTS'){
                                var coordinates = coordinateobj["results"][0]["geometry"];
                                var lat = coordinates.location.lat;
                                var lon = coordinates.location.lng;
                                var latlon = lat +","+lon;
                                

                    if (lat != "" & lon != "") 
                    {
                        $.ajax({
                            type: 'get',
                            cache: false,
                            url: "https://maps.googleapis.com/maps/api/geocode/json?latlng="+latlon+"&sensor=false",
                            success: function(response) {
                               // alert(JSON.stringify(response));
                                var obj = JSON.parse(JSON.stringify(response) );
                                //alert(JSON.stringify(obj["results"][0]["formatted_address"]));
                                //alert(address_patient);
                                patientAddSplit = show_address.split('-<br>');
                                patientName = patientAddSplit[0];
                                correctAddress = JSON.stringify(obj["results"][0]["formatted_address"]);
                                correctAddress = correctAddress.substring(1,correctAddress.length-1);
                                //alert(correctAddress +"!="+ address_patient);
                                var exactaddress_patient = address_patient.split("$*$").join(", ");
                                var formatedAddr = exactaddress_patient.split(",");
                                // Eliminate firt word from address
                                //formatcorrectAddress = correctAddress.substring( correctAddress.indexOf(" ") + 1, correctAddress.length );
                                formatcorrectAddress = correctAddress;
                                var streetformat = formatedAddr[0].substring( formatedAddr[0].indexOf(" ") + 1, formatedAddr[0].length );
                                var completeAddr = streetformat+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                var displayAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                                var cAdd = formatcorrectAddress.split(" ").join("");
                                var comAdd = completeAddr.split(" ").join("");
                                
                            },
                            error: "Unknown Place"
                        });
                        var myLatlng = new google.maps.LatLng(lat, lon);
                        map.setCenter(myLatlng);
                        marker = new google.maps.Marker({
                                   map: map,
                                   position: myLatlng

                        });

                        //marker.setTitle(jQuery("#hdnAddress_"+get_address).val());
                        marker.setTitle(exactaddress_patient);

                        //marker.setIcon('https://maps.google.com/mapfiles/ms/icons/yellow.png');
                        marker.setIcon('images/yellow.png');
                        markersArray.push(marker);
                        var infowindow = new google.maps.InfoWindow();
                        var service = new google.maps.places.PlacesService(map);

                        //var show_address=jQuery("#hdnDetails_"+get_address).val();
        //alert('add '+i+':'+jQuery("#hdnAddress_"+i).val());
        //alert('show_address '+i+':'+jQuery("#hdnDetails_"+i).val());
                        google.maps.event.addListener(marker, 'click', function() {

                              infowindow.setContent(show_address.toString());
                              infowindow.open(map, this);

                            //get_address++;
                        });

                        google.maps.event.addListener(marker, 'dblclick', function() {

                        //map.setZoom(3);

                        });


                        get_addressBack++;

                    } 
                    else{
                        var exactaddress_patient = address_patient.split("$*$").join(", ");
                        var formatedAddr = exactaddress_patient.split(",");
                        var completeAddr = formatedAddr[0]+","+formatedAddr[1]+","+formatedAddr[2]+" "+formatedAddr[3]+","+formatedAddr[4];
                        patientAddSplit = show_address.split('-<br>');
                        patientName = patientAddSplit[0];
                        
                    }
                            }
                }});
            }

          }  

        //}, i * 500);
        /*****************************************************/
        //if(i == 15) return false;
        });

        map.setZoom(12);

        //}            

        }
        
        </script>
        
    </body>
</html>
