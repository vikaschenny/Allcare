<?php
require_once("../../globals.php");

$org=$_REQUEST['org'];
if($org!=''){
$users=sqlStatement("select abook_type,organization,assistant,title,fname,lname,mname,phone,phonecell,phonew1,phonew2,fax,email,street,city  from users where id=$org");
$res=sqlFetchArray($users);
}
?>
<html>
    <head>
    </head>
    <body>
        <div>
            <fieldset style="width:400px;">
                <legend>Address Book Details:</legend>
            <table>
                <tr><td><b>Address book Type:</b></td><td><?php  $type_sql_row = sqlQuery("SELECT `title` FROM `list_options` WHERE `list_id` = 'abook_type' AND `option_id` = ?", array(trim($res['abook_type'])));echo $type_sql_row['title']; ?></td></tr>
                <tr><td><b>Organization:</b></td><td><?php echo $res['organization']; ?></td></tr>
                <tr><td><b>Name:</b></td><td><?php echo $res['title']." ".$res['fname']." ".$res['lname']." ".$res['mname']; ?></td></tr>
                <tr><td><b>Home Phone:</b></td><td><?php echo $res['phone']; ?></td></tr>
                <tr><td><b>Mobile:</b></td><td><?php echo $res['phonecell']; ?></td></tr>
                <tr><td><b>Work Phone:</b></td><td><?php echo $res['phonew1']; ?></td></tr>
                <tr><td><b>Assistant:</b></td><td><?php echo $res['assistant']; ?></td></tr>
                <tr><td><b>Email:</b></td><td><?php echo $res['email']; ?></td></tr>
                <tr><td><b>Fax:</b></td><td><?php echo $res['fax']; ?></td></tr>
                <tr><td><b>Street:</b></td><td><?php echo $res['street']; ?></td></tr>
                <tr><td><b>City:</b></td><td><?php echo $res['city']; ?></td></tr>
                
            </table>
           </fieldset>     
            
        </div>
    </body>
</html>