<?php /* Smarty version 2.6.2, created on 2014-08-01 05:26:24
         compiled from /mnt/stor10-wc2-dfw1/551939/551948/emrsb.risecorp.com/web/content/interface/forms/additional_studies/templates/general_new.html */ ?>
<html>
<head>
<?php html_header_show();  echo '

 <style type="text/css" title="mystyles" media="all">
<!--
td {
	font-size:12pt;
	font-family:helvetica;
}
li{
	font-size:11pt;
	font-family:helvetica;
	margin-left: 15px;
}
a {
	font-size:11pt;
	font-family:helvetica;
}
.title {
	font-family: sans-serif;
	font-size: 12pt;
	font-weight: bold;
	text-decoration: none;
	color: #000000;
}

.form_text{
	font-family: sans-serif;
	font-size: 9pt;
	text-decoration: none;
	color: #000000;
}

-->
</style>
'; ?>

</head>
<body bgcolor="<?php echo $this->_tpl_vars['STYLE']['BGCOLOR2']; ?>
">
<p><span class="title">Additional Studies</span></p>
<form name="soap" method="post" action="<?php echo $this->_tpl_vars['FORM_ACTION']; ?>
/interface/forms/additional_studies/save.php"
 onsubmit="return top.restoreSession()">
<table>
	<tr>
		<td width="90%">
			<textarea name="additional_studies" cols="60" rows="18"><?php echo $this->_tpl_vars['data']->get_additional_studies(); ?>
</textarea>
		</td>
	</tr>
</table>
<table>
	<tr>
		<td>
			<input type="submit" name="Submit" value="Save Form">
		</td>
		<td>
			<a href="<?php echo $this->_tpl_vars['DONT_SAVE_LINK']; ?>
" class="link">[Don't Save]</a>
		</td>
	</tr>
</table>
<input type="hidden" name="id" value="<?php echo $this->_tpl_vars['data']->get_id(); ?>
" />
<input type="hidden" name="pid" value="<?php echo $this->_tpl_vars['data']->get_pid(); ?>
">
<input type="hidden" name="process" value="true">
</form>
</body>
</html>