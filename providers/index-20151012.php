<?php
/**
 * Login screen.
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Rod Roark <rod@sunsetsystems.com>
 * @author  Brady Miller <brady@sparmy.com>
 * @author  Kevin Yeh <kevin.y@integralemr.com>
 * @author  Scott Wakefield <scott.wakefield@gmail.com>
 * @author  ViCarePlus <visolve_emr@visolve.com>
 * @author  Julia Longtin <julialongtin@diasp.org>
 * @author  cfapress
 * @author  markleeds
 * @link    http://www.open-emr.org
 */

$fake_register_globals=false;
$sanitize_all_escapes=true;

$ignoreAuth=true;
include_once("../interface/globals.php");
include_once("$srcdir/sql.inc");
$provider=$_POST['authUser'];
session_start();
?>
<html>
<head>
<?php html_header_show();?>
 <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="assets/css/customizealerts.css">
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script language='JavaScript'>
function transmit_form()
{
    document.forms[0].submit();
}
function imsubmitted() {
<?php if (!empty($GLOBALS['restore_sessions'])) { ?>
 // Delete the session cookie by setting its expiration date in the past.
 // This forces the server to create a new session ID.
 var olddate = new Date();
 olddate.setFullYear(olddate.getFullYear() - 1);
 document.cookie = '<?php echo session_name() . '=' . session_id() ?>; path=/; expires=' + olddate.toGMTString();
<?php } ?>
    return false; //Currently the submit action is handled by the encrypt_form(). 
}
$(function(){
    /* center modal */
function centerModals($element) {
    var windowheight = $(window).height();
    var penalheight = $element.height();
    var margintop = ((windowheight/2) - penalheight/2);
    $element.css({marginTop:margintop});
  }
  centerModals($('.panel'));
  $(window).on('resize',function(){
      centerModals($('.panel'));
  });
    
})
</script>
<style>
body {
  background: #46a1b4;
}
.vertical-offset-100{
    padding-top:100px;
}
</style>
</head>
<body onload="javascript:document.login_form1.authUser.focus();" bgcolor="#638fd0" >
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading panel-heading-custom"><h3 class="panel-title">Provider Portal login</h3></div>
                    <div class="panel-body">
                        <form method="POST" action="home.php?auth=login&site=<?php echo attr($_SESSION['site_id']); ?>" target="_top" name="login_form1" accept-charset="UTF-8" role="form" onsubmit="">
                            <input type='hidden' name='new_login_session_management' value='1' />
                                <?php
                                // collect groups
                                $res = sqlStatement("select distinct name from groups");
                                for ($iter = 0;$row = sqlFetchArray($res);$iter++)
                                        $result[$iter] = $row;
                                if (count($result) == 1) {
                                        $resvalue = $result[0]{"name"};
                                        echo "<input type='hidden' name='authProvider' value='" . attr($resvalue) . "' />\n";
                                }
                                // collect default language id
                                $res2 = sqlStatement("select * from lang_languages where lang_description = ?",array($GLOBALS['language_default']));
                                for ($iter = 0;$row = sqlFetchArray($res2);$iter++)
                                          $result2[$iter] = $row;
                                if (count($result2) == 1) {
                                          $defaultLangID = $result2[0]{"lang_id"};
                                          $defaultLangName = $result2[0]{"lang_description"};
                                }
                                else {
                                          //default to english if any problems
                                          $defaultLangID = 1;
                                          $defaultLangName = "English";
                                }
                                // set session variable to default so login information appears in default language
                                $_SESSION['language_choice'] = $defaultLangID;
                                // collect languages if showing language menu
                                if ($GLOBALS['language_menu_login']) {

                                        // sorting order of language titles depends on language translation options.
                                        $mainLangID = empty($_SESSION['language_choice']) ? '1' : $_SESSION['language_choice'];
                                        if ($mainLangID == '1' && !empty($GLOBALS['skip_english_translation']))
                                        {
                                          $sql = "SELECT *,lang_description as trans_lang_description FROM lang_languages ORDER BY lang_description, lang_id";
                                          $res3=SqlStatement($sql);
                                        }
                                        else {
                                          // Use and sort by the translated language name.
                                          $sql = "SELECT ll.lang_id, " .
                                            "IF(LENGTH(ld.definition),ld.definition,ll.lang_description) AS trans_lang_description, " .
                                            "ll.lang_description " .
                                            "FROM lang_languages AS ll " .
                                            "LEFT JOIN lang_constants AS lc ON lc.constant_name = ll.lang_description " .
                                            "LEFT JOIN lang_definitions AS ld ON ld.cons_id = lc.cons_id AND " .
                                            "ld.lang_id = ? " .
                                            "ORDER BY IF(LENGTH(ld.definition),ld.definition,ll.lang_description), ll.lang_id";
                                          $res3=SqlStatement($sql, array($mainLangID));
                                        }

                                        for ($iter = 0;$row = sqlFetchArray($res3);$iter++)
                                               $result3[$iter] = $row;
                                        if (count($result3) == 1) {
                                               //default to english if only return one language
                                               echo "<input type='hidden' name='languageChoice' value='1' />\n";
                                        }
                                }
                                else {
                                        echo "<input type='hidden' name='languageChoice' value='".attr($defaultLangID)."' />\n";   
                                }
                                ?>
                            <fieldset>
                                <?php if (isset($_SESSION['loginfailure']) && ($_SESSION['loginfailure'] == 1)): ?>
                                    <div class="alert alert-error">
                                        <a class="close" data-dismiss="alert" onclick="<?php unset($_SESSION['loginfailure']); ?>">&times;</a>
                                        <strong>Error! </strong> <?php echo xlt('Invalid username or password'); ?>.
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($_SESSION['relogin']) && ($_SESSION['relogin'] == 1)): ?>
                                    <div class="alert alert-warning">
                                        <a class="close" data-dismiss="alert">&times;</a>
                                        <strong>Note: </strong> <?php echo xlt('Password security has recently been upgraded.'); ?><br />
                                        <?php echo xlt('Please login again.'); ?>
                                        <?php unset($_SESSION['relogin']); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-group left-inner-addon">
                                    <i class="glyphicon glyphicon-user"></i>
                                    <input class="form-control" placeholder="<?php echo xlt('Username'); ?>" name="authUser" id="authUser" type="text">
                                </div>
                                <div class="inner-addon left-inner-addon">
                                    <i class="glyphicon glyphicon-lock"></i>
                                    <input class="form-control" placeholder="<?php echo xlt('Password'); ?>" name="clearPass" id="clearPass"  type="password" value="">
                                </div>
                                <div class="checkbox">
                                <label>
                                    <input name="remember" type="checkbox" value="Remember Me"> Remember Me
                                </label>
                                </div>
                                <input class="btn btn-md btn-success btn-block" onClick="transmit_form()" type="submit" value="<?php echo xla('Login');?>">
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
</body>
</html>