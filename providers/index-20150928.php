<?php
$ignoreAuth = true;
include_once ("../interface/globals.php");
?>
<HTML>
<head>
<?php html_header_show(); ?>
<TITLE><?php xl ('Login','e'); ?></TITLE>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel=stylesheet href="<?php echo $css_header;?>" type="text/css">
<link rel=stylesheet href="../interface/themes/login.css" type="text/css">

</HEAD>

<frameset rows="*" cols="*" frameborder="NO" border="0" framespacing="0">
<!--  <frame class="logobar" src="<?php echo $rootdir;?>/login/filler.php" name="Filler Top" scrolling="no" noresize frameborder="NO">-->
<!--  <frame class="titlebar" src="<?php echo $rootdir;?>/login/login_title.php" name="Title" scrolling="no" noresize frameborder="NO">-->
  <frame  src="login_provider.php" name="Login" scrolling="auto" frameborder="NO">
  <!--<frame src="<?php echo $rootdir;?>/login/filler.php" name="Filler Bottom" scrolling="no" noresize frameborder="NO">-->
</frameset>
<?php// $_SESSION['login_test'] = 1; ?>
<noframes><body bgcolor="#FFFFFF">

</body></noframes>

</HTML>