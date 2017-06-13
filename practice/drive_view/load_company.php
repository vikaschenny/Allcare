<?php require_once("verify_session.php");
$subpage = "Zirmed Company Load";
$pagename = "insurance"; 
if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer']; 
}else {
   $provider                     = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}

$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id = sqlFetchArray($sql);
$id1    = $id['id'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Care Central</title>
    <link href='//fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Dosis:300,400,500,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="./../../library/customselect/css/select2.css"/>
    <link rel="stylesheet" href="./../../library/customselect/css/select2-bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/jquery.webui-popover.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">
    <link rel="stylesheet" type="text/css" href="assets/css/customize.css">
    <link rel="stylesheet" type="text/css" href="assets/css/jquery.alerts.css">
    <link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
    
    <style>
        .entry {
            border-radius: 3px;
            box-shadow: 0 0 8px 0 #d9d9d3;
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            margin: 5px;
            padding: 5px 10px;
            position: relative;
            transition: all 0.2s ease-in-out 0s;
            vertical-align: top;
            height: 35px;
        }
        .entry:hover{
            background-color:#29ADE2;
        }
        .entry:hover .entry_name{
            color: #fff;
        }
        .costlabel{
            display: block;
            font-weight: normal;
        }
        .entry_name {
            margin-left: 6px;
            margin-right: 5px;
            overflow: hidden;
            overflow-wrap: break-word;
            text-align: left;
            line-height: 21px;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding-right: 13px;
            margin-right: 12px;
        }
        .entry_checkbox {
            display: block;
            float: right;
            margin-top: -21px;
            margin-right: 5px;
            position: relative;
        }
        .leftpanbody,.rightpanbody{
            overflow-y:auto;
        }
        .select2{
            width: 100%;
        }
        .navbar-nav > li > .dropdown-menu{
            /*margin-top: 4px !important;*/
        }
        .uyd-grid .dropdown-menu{
            background-color: #fff;
            padding: 5px 0px;
        }
        .webui-popover .dropdown-menu {
            background-color: #fff;
            padding: 5px 0px;
        }

        .webui-popover-content .dropdown-menu > li > a {
            padding: 3px 10px;
        }
        .webui-popover-content .dropdown-menu > li > a:hover {
            color:#fff;
            background-color:#0E92C7;
        }
        .webui-popover-content .dropdown-menu{
           min-width: 0px;
        }
        .webui-popover-content .dropdown-menu > li > a span.glyphicon{
            padding-right: 8px;
        }
        .movepanel{
            text-align: right;
        }

        /*search box inneraddon*/

        /* enable absolute positioning */
        .searchdrive {
            padding: 0 15px;
        }

        .inner-addon {
          position: relative;
        }

        /* style glyph */


        /* align glyph */
        .left-a.glyphicon {
          position: absolute;
          padding: 10px;
          pointer-events: none;
          left:  0px;
          z-index: 4;
        }
        .right-a.glyphicon{
            right: 0;
            cursor: pointer;
        }
        .input-group-addon{
            cursor:pointer;
        }

        /* add padding  */
        .left-addon input  { padding-left:  30px; }
        .right-addon input { padding-right: 30px; }
        .column_names {
            background-color: #f1f1ef;
            border-color: #e4e4e4;
            border-style: solid;
            border-width: 1px 1px 0;
            clear: both;
            font-size: 80% !important;
            min-height: 30px;
            padding: 5px 20px;
        }
        .column_names {
            display: inline-block;
            width: 100%;
        }
        .all {
            float: right;
        }
        /*loader css*/

        .loader{
             background: rgba(0, 0, 0, 0.56) none repeat scroll 0 0;
            border-radius: 4px;
            color: #fff;
            display: table;
            height: 48px;
            left: 0;
            margin: 0 auto;
            position: absolute;
            right: 0;
            top: 50%;
            width: 242px;
        }

        .ajax-spinner-bars {
            height: 48px;
            left: 23px;
            position: relative;
            top: 20px;
            width: 35px;
            display: table-cell;
         }
         #loadertitle {
            display: table-cell;
            font-size: 17px;
            padding-left: 14px;
            vertical-align: middle;
         }

        .ajax-spinner-bars > div {
            position: absolute;
            width: 2px;
            height: 8px;
            background-color: #fff;
            opacity: 0.05;
            animation: fadeit 0.8s linear infinite;
        }
        .ajax-spinner-bars > .bar-1 {
        transform: rotate(0deg) translate(0, -12px);
        animation-delay:0.05s;
        }
        .ajax-spinner-bars > .bar-2 {
        transform: rotate(22.5deg) translate(0, -12px);
        animation-delay:0.1s;
        }
        .ajax-spinner-bars > .bar-3 {
        transform: rotate(45deg) translate(0, -12px);
        animation-delay:0.15s;
        }
        .ajax-spinner-bars > .bar-4 {
        transform: rotate(67.5deg) translate(0, -12px);
        animation-delay:0.2s;
        }
        .ajax-spinner-bars > .bar-5 {
        transform: rotate(90deg) translate(0, -12px);
        animation-delay:0.25s;
        }
        .ajax-spinner-bars > .bar-6 {
        transform: rotate(112.5deg) translate(0, -12px);
        animation-delay:0.3s;
        }
        .ajax-spinner-bars > .bar-7 {
        transform: rotate(135deg) translate(0, -12px);
        animation-delay:0.35s;
        }
        .ajax-spinner-bars > .bar-8 {
        transform: rotate(157.5deg) translate(0, -12px);
        animation-delay:0.4s;
        }
        .ajax-spinner-bars > .bar-9 {
        transform: rotate(180deg) translate(0, -12px);
        animation-delay:0.45s;
        }
        .ajax-spinner-bars > .bar-10 {
        transform: rotate(202.5deg) translate(0, -12px);
        animation-delay:0.5s;
        }
        .ajax-spinner-bars > .bar-11 {
        transform: rotate(225deg) translate(0, -12px);
        animation-delay:0.55s;
        }
        .ajax-spinner-bars > .bar-12 {
        transform: rotate(247.5deg) translate(0, -12px);
        animation-delay:0.6s;
        }
        .ajax-spinner-bars> .bar-13 {
        transform: rotate(270deg) translate(0, -12px);
        animation-delay:0.65s;
        }
        .ajax-spinner-bars > .bar-14 {
        transform: rotate(292.5deg) translate(0, -12px);
        animation-delay:0.7s;
        }
        .ajax-spinner-bars > .bar-15 {
        transform: rotate(315deg) translate(0, -12px);
        animation-delay:0.75s;
        }
        .ajax-spinner-bars> .bar-16 {
        transform: rotate(337.5deg) translate(0, -12px);
        animation-delay:0.8s;
        }

        @keyframes fadeit{
              0%{ opacity:1; }
              100%{ opacity:0;}
        }
        .entry{
            background-color: #337ab7;
            border: 2px solid white;
            border-radius: 14px;
            box-shadow: 0 0 3px #444;
            box-sizing: content-box;
            color: white;
            content: "+";
            display: block;
            font-family: "Courier New",Courier,monospace;
            height: 14px;
            left: 4px;
            line-height: 14px;
            position: absolute;
            text-align: center;
            top: 9px;
            width: 14px;
            cursor: pointer;
            padding-left: 30px;
            position: relative;
        }
     </style>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.js"></script>-->
<script src="assets/js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
<script src="assets/js/jquery.alerts.js"></script>
<script>
//Declaration of function that will insert data into database
 var callcount = 0; 
 function senddata(filename,row,tccount,logid,type){ 
        var file = filename;
        $.ajax({
            type: "POST",
            url: "senddata.php?row="+row+"&logid="+logid+"&type="+type,
            data: {file},
//            async: true,
            success: function(html){ //console.log(html);
                callcount++
                var obj = JSON.parse(html);
                if(callcount == tccount){
                    $(".loader").hide();
                    window.location.href = "load_company.php";
                }
                    
//                console.log(JSON.stringify(obj));
                var append = '';
                var i = 1;
                $.each(obj.csvpayers,function(index,value){
                    var name = value[0].replace(/'/g,"");
                    var payerid = value[1];
                    var content = this;
                    append+= '<label class="costlabel" for="lch'+i+'-'+payerid+'"><div class="entry"><div class="entry_name"><span>'+content+'</span></div><div class="entry_checkbox"><input data-value="'+content+'" class="checkboxselected-files" id="lch'+i+'-'+payerid+'" name="selected-list[]" type="checkbox"></div></div></label>';
                    i++;
                });
                //console.log(append);
                setTimeout(function(){
                 $(".leftpanbody").append(append);
                 }, 1000);
                
                if(row == 0){
                    //console.log(obj.dbpayers);
                    var dbpayerslist = '';
                    var i = 1;
                    $.each(obj.dbpayers,function(index,value){
                        var name = value['name'].replace(/'/g,"");
                        var payerid = value['payerid'].replace(/'/g,"");
                        var content = name+":"+payerid;
                        dbpayerslist+= '<label class="costlabel" for="rch'+i+'-'+payerid+'"><div class="entry"><div class="entry_name"><span>'+content+'</span></div><div class="entry_checkbox"><input data-value="'+content+'" class="radioselected-files" id="rch'+i+'-'+payerid+'" name="selected-upd-list" type="radio"></div></div></label>';
                        i++;
                    });
                    //console.log(append);
                    setTimeout(function(){
                     $(".rightpanbody").append(dbpayerslist);
                     }, 1000);
                }
            }
        });
        }
 </script>
<?php
$csv = array();
$batchsize = 1000; //split huge CSV file by 1,000, you can modify this based on your needs

if($_FILES['csv']['error'] == 0 && sizeof($_FILES) > 0){
    $name = $_FILES['csv']['name'];
    $ext = strtolower(end(explode('.', $_FILES['csv']['name'])));
    $tmpName = $_FILES['csv']['tmp_name'];
    if($ext === 'csv'){ //check if uploaded file is of CSV format
         
        if(($handle = fopen($tmpName, 'r')) !== FALSE) {
            set_time_limit(0);
            $row = 0;
            $cond = 0;
            $milliseconds = round(microtime(true) * 1000).".csv";
            move_uploaded_file($_FILES["csv"]["tmp_name"], "csv_uploads/" . $milliseconds);
            
            $logQry = "INSERT INTO `import_log` (`user`,`filename`,`upload_datetime`) VALUES ('".$_SESSION['portal_username']."','".$milliseconds."',NOW())";
            mysql_query($logQry);
            $logid = mysql_insert_id();
            
                $check_head_row = array('Payer Name','Payer ID','External Payer Indentifier','Apps','Enroll Required','Prof Electronic Secondary','Outbound Format','Dual CH Allowed','Claims Attachments');
                
                while(($data = fgetcsv($handle)) !== FALSE) {
                    $col_count = count($data);
                    //splitting of CSV file :
                    if ($row % $batchsize == 0):
                        
                        $file = fopen("minpoints$row.csv","w");
                        
                    endif;
                    
                    if($data[1] != ''){
                        if($cond == 0){
                            if($check_head_row != $data){
                                echo "<script> alert('Content Mis Match. Please check the file contents.'); window.location.href = 'import_csv.php'; </script>";
                            }
                        }
                        $cnt = 1;
                        $json = '';
                        foreach($data as $each){
                            $csv[$row]['col'.$cnt] = rtrim($each," ");
                            $json.="'".  str_replace(',', '.', rtrim($each," "))."',";
                            $cnt++;
                        }

                        $json = rtrim($json,",");
                        fwrite($file,$json.PHP_EOL);
                        $cond++;
                    }
                    //sending the splitted CSV files batch by batch..
                    if ($row % $batchsize == 0):
                        echo "<script> senddata('minpoints$row.csv',$row,6,$logid,'zirmed'); </script>";
                    endif;
                    $row++; 
                }
                fclose($file);
                fclose($handle);
//                header("Location:load_company.php");
//                echo "<script> window.location.href = 'load_company.php'; </script>";
        }
        //echo "<script> window.location.href = 'load_company.php'; </script>";
        
    }
    else
    {
        echo "<script> alert('Only CSV files allowed'); window.location.href = 'import_csv.php'; </script>";
    } ?>
 <div class="loader" style="display: block;">
        <div class="ajax-spinner-bars">
            <div class="bar-1"></div>
            <div class="bar-2"></div>
            <div class="bar-3"></div>
            <div class="bar-4"></div>
            <div class="bar-5"></div>
            <div class="bar-6"></div>
            <div class="bar-7"></div>
            <div class="bar-8"></div>
            <div class="bar-9"></div>
            <div class="bar-10"></div>
            <div class="bar-11"></div>
            <div class="bar-12"></div>
            <div class="bar-13"></div>
            <div class="bar-14"></div>
            <div class="bar-15"></div>
            <div class="bar-16"></div>
        </div>
        <div id="loadertitle">Processing Zirmed Payers...</div>
    </div>
<?php }
else{
    ?>
    
                
   <script language="javascript"> 
           function DoPost(page_name, provider,refer) {
                method = "post"; // Set method to post by default if not specified.

                var form = document.createElement("form");
                form.setAttribute("method", method);
                form.setAttribute("action", page_name);
                var key='provider';
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", key);
                hiddenField.setAttribute("value", provider);
                form.appendChild(hiddenField);
                
                var key1='refer';
                var hiddenField1 = document.createElement("input");
                hiddenField1.setAttribute("type", "hidden");
                hiddenField1.setAttribute("name", key1);
                hiddenField1.setAttribute("value", refer);
                form.appendChild(hiddenField1);

               document.body.appendChild(form);
                form.submit();
        } 
        
        </script>       

	</head>

<body><?php include 'header_nav.php'; ?>
    <section id= "services"  style=''>
    <div class= "container-fluid">
        <div class= "row">
            <div class= "col-sm-5"><!-- start col1 -->
                <div class="panel panel-default">
                    <div class="panel-heading movepanel">
                        <h3 class="panel-title" style="float: left;font-weight: 600;">Source Zirmed Payers</h3>
                        <a href="javascript:void(0)" class="nav-search"  data-placement="bottom-left"><span class="glyphicon glyphicon-search" ></span></i></a>
                        <div  class="webui-popover-content">
                            <ul class="dropdown-menu" >
                                <li class="searchdrive">
                                    <div class="input-group inner-addon left-addon">
                                        <i class="left-a glyphicon glyphicon-search"></i>      
                                        <input type="text" class="form-control searchentery" placeholder="Search filenames" data-item="leftpanbody"/><span class="input-group-addon clearinput" data-item="leftpanbody"><i class="glyphicon glyphicon-remove-sign"></i> </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="leftpanbody">
<!--                        <div class="column_names">
                            <div class="all">
                                <label for="selectall">Select All</label>
                                <input type="checkbox" name="selectall" id="selectall"/>
                            </div>
                        </div>-->
                        
                        
                        
                        
                        
                        <div class="loader">
                            <div class="ajax-spinner-bars">
                                <div class="bar-1"></div>
                                <div class="bar-2"></div>
                                <div class="bar-3"></div>
                                <div class="bar-4"></div>
                                <div class="bar-5"></div>
                                <div class="bar-6"></div>
                                <div class="bar-7"></div>
                                <div class="bar-8"></div>
                                <div class="bar-9"></div>
                                <div class="bar-10"></div>
                                <div class="bar-11"></div>
                                <div class="bar-12"></div>
                                <div class="bar-13"></div>
                                <div class="bar-14"></div>
                                <div class="bar-15"></div>
                                <div class="bar-16"></div>
                            </div>
                            <div id="loadertitle">Zirmed Payers Loading...</div>
                        </div>
                    </div>
                </div>  
            </div> <!-- end col1 -->
            <div class= "col-sm-2"><!-- start col2 -->
                <div>
                    <input type="button" class="btn action_btn btn-primary btn-block" data-logid="<?php echo $logid; ?>" data-action="add" value="Add" >
                    <input type="button" class="btn action_btn btn-primary btn-block" data-logid="<?php echo $logid; ?>" data-action="update" value="Update">
                    <input type="button" class="btn action_btn btn-primary btn-block" data-logid="<?php echo $logid; ?>" data-action="reupload" value="Re-Upload">
                    <input type="button" class="btn action_btn btn-primary btn-block" data-logid="<?php echo $logid; ?>" data-action="update_logs" value="Report">
                </div>
            </div> <!-- end col2 -->
            <div class= "col-sm-5"><!-- start col3 -->
                <div class="panel panel-default">
                    <div class="panel-heading movepanel">
                        <h3 class="panel-title" style="float: left;font-weight: 600;">Central Zirmed Payers</h3>
                        <a href="javascript:void(0)" class="nav-search"  data-placement="bottom-left"><span class="glyphicon glyphicon-search" ></span></i></a>
                        <div  class="webui-popover-content">
                            <ul class="dropdown-menu" >
                                <li class="searchdrive">
                                    <div class="input-group inner-addon left-addon">
                                        <i class="left-a glyphicon glyphicon-search"></i>      
                                        <input type="text" class="form-control searchentery" placeholder="Search filenames" data-item="rightpanbody" /><span class="input-group-addon clearinput" data-item="rightpanbody"><i class="glyphicon glyphicon-remove-sign"></i> </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="rightpanbody">
                        <div class="loader">
                            <div class="ajax-spinner-bars">
                                <div class="bar-1"></div>
                                <div class="bar-2"></div>
                                <div class="bar-3"></div>
                                <div class="bar-4"></div>
                                <div class="bar-5"></div>
                                <div class="bar-6"></div>
                                <div class="bar-7"></div>
                                <div class="bar-8"></div>
                                <div class="bar-9"></div>
                                <div class="bar-10"></div>
                                <div class="bar-11"></div>
                                <div class="bar-12"></div>
                                <div class="bar-13"></div>
                                <div class="bar-14"></div>
                                <div class="bar-15"></div>
                                <div class="bar-16"></div>
                            </div>
                            <div id="loadertitle">Central Zirmed Payers Loading...</div>
                        </div>
                    </div>
                </div>  
            </div> <!-- end col3 -->
        </div>
    </div>
     <br><br>
     <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="viewinfomodal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Update Logs</h4>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs responsive" id="myTab">
                     <li class="active"><a href="#direct">Direct Updates</a></li>
                     <li><a href="#new">New</a></li>
                     <li><a href="#update">Manual Updates</a></li>
                   </ul>
                   <div class="tab-content responsive">
                       
                   </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    </section>
    <?php include 'footer.php'; ?> 
        <script src="assets/js/jquery.webui-popover.min.js"></script>
        <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
        <script src="./../../library/customselect/js/select2.js"></script>
<!--        <script src="./js/jquery.webui-popover.min.js"></script>-->
        <script type='text/javascript' src='assets/js/responsive-tabs.js'></script>
        <script type='text/javascript'>
            $(function(){
                
                $( 'ul.nav.nav-tabs a' ).click( function ( e ) {
                    e.preventDefault();
                    $( this ).tab( 'show' );
                 });
                
                $.ajax({
                    type: "POST",
                    url: "ajaxgetrawdata.php",
                    data: {},
//                    async: true,
                    success: function(html){
                        var obj = JSON.parse(html);
                        var dbpayerslist = '';
                        var i = 1;
                        
                        if(obj.dbpayers.length == 0){
                            window.location.href = "import_csv.php";
                        }
                        
                        $.each(obj.dbpayers,function(index,value){
                            //var name = value['name'].replace(/'/g,"");
                            var name = value.payer_name;
                            //name = JSON.stringify(name);
                            //var payerid = value['payerid'].replace(/'/g,"");
                            //var content = name+":"+payerid;
                            var payerid = value.payer_id;
                            var content = JSON.stringify(value);
                            dbpayerslist+= '<label class="costlabel" for="rch'+i+'-'+payerid+'"><div class="entry"><div class="entry_name"><span>'+name+' : '+payerid+'</span></div><div class="entry_checkbox"><input data-value="'+content+'" class="source-radio" id="rch'+i+'-'+payerid+'" name="zirmed-upd-list" type="radio"></div></div></label>';
                            i++;
                        });
                        setTimeout(function(){
                         $(".loader").hide();
                         $(".leftpanbody").append(dbpayerslist);
                         }, 1000);
                        
                        var emrpayerslist = '';
                         $.each(obj.emrpayers,function(index,value){
                            var name = value['name'].replace(/'/g,"");
                            var payerid = value['payerid'].replace(/'/g,"");
                            var content = name+":"+payerid;
                            emrpayerslist+= '<label class="costlabel" for="rch'+i+'-'+payerid+'"><div class="entry"><div class="entry_name"><span>'+name+':'+payerid+'</span></div><div class="entry_checkbox"><input data-value="'+content+'" class="radioselected-files" id="rch'+i+'-'+payerid+'" name="selected-upd-list" type="radio"></div></div></label>';
                            i++;
                        });
                        setTimeout(function(){
                         $(".loader").hide();
                         $(".rightpanbody").append(emrpayerslist);
                        }, 1000);
                         
                    }
                });
                
               //$('.select2').select2({ placeholder : 'Select option' });
               $('a.nav-search').webuiPopover('destroy').webuiPopover({trigger:'click',padding:0,width:215});
               $('.clearinput').click(function(){
                    var itemcls = $(this).data("item")
                    $(this).parent().find("input").val("");
                     $('.'+itemcls+" .costlabel").show();
                });
                
                $(".searchentery").on("keyup",function(){
                    var itemcls = $(this).data("item");
                    var inputval = $(this).val().trim();
                    $('.'+itemcls+" .costlabel").hide();
                    $('.'+itemcls+" .costlabel").filter(function(){
                        var patt = new RegExp(inputval,"i");
                        var res = patt.test($(this).find(".entry_name span").text());
                        return res;
                    }).show();
                });
        
               var body = window.innerHeight;//document.body.clientHeight;
               $('.leftpanbody').height(body-180);
               $('.rightpanbody').height(body-180);
               $('#selectall').click(function(evt){
                   console.log();
                   if($(this).prop("checked"))
                       $(this).parents('.leftpanbody').find('.costlabel .checkboxselected-files').prop("checked",true)
                    else
                       $(this).parents('.leftpanbody').find('.costlabel .checkboxselected-files').prop("checked",false)
               })
               
               $(".action_btn").click(function(){
                  var action = $(this).data('action');
                  var logid = $(this).data('logid');
                  
                  if(action == 'update_logs'){
                      $.ajax({
//                        type: "POST",
                        url: "updated_logs.php",
                        data: {},
//                        async: true,
                        success: function(html){
                            
                            $(".tab-content").html(html);
                            
                            $("#viewinfomodal").modal('show');
                        }
                    });  
                  }
                  else if(action == 'reupload'){
                      $.jAlert({'type': 'confirm','confirmQuestion':'Do you want to terminate the partial import in process?', 'onConfirm': function(){
                              
                        $.ajax({
                            type: "POST",
                            url: "delpartialimport.php",
                            data: {},
//                            async: true,
                            success: function(html){
                                if(html == "1"){
                                    window.location.href = "import_csv.php";
                                }
                            }
                        });      
                      }, 'onDeny': function(){
                      } });
                      
                  }
                  else{
//                    var checkCnt = $('[name="selected-list[]"]:checked').length;
//                    var checkData = [];
                    var zirmedlist = false;
                    $('.source-radio').each(function(){
                        if($(this).prop("checked") == true){
                            zirmedlist = true;
                        }
                    });
                      if(zirmedlist){
                          if(action == 'add'){
//                              $('[name="source-radio"]:checked').each(function(){
//                                 checkData.push($(this).data('value'));
//                                 $(this).closest('label').remove();
//                              });
                              
                              var checkData = $('input[name=zirmed-upd-list]:checked').data('value');

                              $.ajax({
                                  type: "POST",
                                  url: "updatedata.php?action=add&logid="+logid,
                                  data: {checkdata: checkData},
//                                  async: true,
                                  success: function(html){ alert(html);
                                      $('input[name=zirmed-upd-list]:checked').closest('label').remove();
                                  }
                              });
                          }
                          else if(action =='update'){
                              var centerlist = false;
                              $('.radioselected-files').each(function(){
                                  if($(this).prop("checked") == true){
                                      centerlist = true;
                                  }
                              });
                              if(!centerlist){
                                  $.jAlert({
                                    'title': 'Alert',
                                    'content': 'Please select one Central Payer List.',
                                    'theme': "blue",
                                    'closeOnClick': true,
                                    'backgroundColor': 'white',
                                    'btns': [
                                          {'text':'OK', 'theme':"blue"}
                                       ]
                                   });
                              }
                              else{
                                  $('[name="selected-list[]"]:checked').each(function(){
                                     checkData.push($(this).data('value'));
                                     $(this).closest('label').remove();
                                  });
                                  var toupdate = $('[name="selected-upd-list"]:checked').data('value');
                                  $.ajax({
                                      type: "POST",
                                      url: "updatedata.php?action=update&logid="+logid,
                                      data: {checkdata: checkData,toupdate:toupdate},
//                                      async: true,
                                      success: function(html){
                                         // alert(html);
                                      }
                                  });
                              }
                          }
                      }
                      else{
                          $.jAlert({
                            'title': 'Alert',
                            'content': 'Please select atleast one payer.',
                            'theme': "blue",
                            'closeOnClick': true,
                            'backgroundColor': 'white',
                            'btns': [
                                  {'text':'OK', 'theme':"blue"}
                               ]
                           });
                      }
                  }
               });
               
               
            });
        </script>

 
<?php }
?>
</body>
</html>