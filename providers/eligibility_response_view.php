<?php
//
require_once("verify_session.php");
 
if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}else {
    $provider                    = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}

//

 require_once("../interface/globals.php");
 
$pid=$_REQUEST['pid'];
$get_fields = '';
$gettitles = '';
 ?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.1.2/css/buttons.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.1.2/css/select.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="../library/edittable/css/editor.dataTables.min.css">
        <style>
        div.DTTT_container {
                float: none;
        }
        </style>
       
    </head>
    <body style="background-color:#FFFFCC;">
        <section>	
                <table id="example" class="display" cellspacing="0" width="100%">
                        <thead>
                                <tr>
                                    <?php 
                                        $getFields = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id='ELIGIBILITY' AND uor <> 0 ORDER BY group_name, seq"); 
                                        while($rowfields = sqlFetchArray($getFields)){
                                            echo "<th>".$rowfields['title']."</th>";
                                            $get_fields .= $rowfields['field_id'].",";
                                            $gettitles .= $rowfields['title'].",";
                                        }

                                    ?>
                                    
                                </tr>
                        </thead>
                </table>
        <section>
            <script type='text/javascript'>
            var editor; // use a global for the submit and return data rendering in the examples
            var editerfields=[];
            var datatablecolumns=[];
            var advancedsetting=["datetime0"];
        $(document).ready(function() {
                function createfieldsdata(fields,titles){
                    var fieldval = fields.split(",");
                    var titleval = titles.split(",");
                    fieldval.pop();
                    titleval.pop();
                    var titlelength = titleval.length;
                    for(var i=0; i<titlelength; i++){
                        if(advancedsetting.indexOf("datetime"+i)!=-1){
                            editerfields[i] = {label: titleval[i]+":",name: fieldval[i],type:"datetime"}
                            datatablecolumns[i] =  {data: fieldval[i]}
                        }else{
                            editerfields[i] = {label: titleval[i]+":",name: fieldval[i]}
                            datatablecolumns[i] =  {data: fieldval[i]}
                        }
                        
                    }
                };
                createfieldsdata("<?php echo $get_fields ?>","<?php echo $gettitles ?>");
                editor = new $.fn.dataTable.Editor( {
                        ajax: "elig_autoupdate.php?pid="+<?php echo $pid; ?>,
                        table: "#example",
                        fields: editerfields
                } );

                var table = $('#example').DataTable( {
                        dom: "Bfrtip",
                        ajax: "elig_autoupdate.php?pid="+<?php echo $pid; ?>,
                        columns: datatablecolumns,
                        order: [ 0, 'dsc' ],
                        keys: {
                                columns: '',
                                keys: [ 9 ]
                        },
                        select:false,
                        buttons: [{
				extend: 'collection',
				text: 'Export',
				buttons: [
					'copy',
					'excel',
					'csv',
					'pdf',
					'print'
				]
			}]
                } );

                // Inline editing on click
                $('#example').on( 'click', 'tbody td', function (e) {
                       // editor.inline( this );
                       editor.inline( this, {
                            onBlur: 'submit'
                        } );
                } );

                // Inline editing on tab focus
                /*table.on( 'key-focus', function ( e, datatable, cell ) {
                        editor.inline( cell.index(), {
                                onBlur: 'submit'
                        } );
                } );*/
            } );
    </script>
    </body>
</html>