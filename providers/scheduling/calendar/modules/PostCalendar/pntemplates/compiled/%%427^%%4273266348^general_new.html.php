<?php /* Smarty version 2.6.2, created on 2014-08-06 07:34:52
         compiled from /mnt/stor10-wc2-dfw1/551939/551948/emrsb.risecorp.com/web/content/interface/forms/leg_length/templates/general_new.html */ ?>
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
<p><span class="title">Circumferential and leg length measurements.</span></p>
<form name="soap" method="post" action="<?php echo $this->_tpl_vars['FORM_ACTION']; ?>
/interface/forms/leg_length/save.php"
 onsubmit="return top.restoreSession()">

<table width="194" border="1" bordercolor="#000000" cellpadding="7" cellspacing="0">
		<col width="40">
		<col width="54">
		<col width="56">
		<tbody><tr valign="top">
			<td width="40" height="1">
				<p class="western" align="justify">&nbsp;</p>

			</td>
			<td width="54">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><b>RIGHT</b></font></font></p>
			</td>
			<td width="56">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><b>LEFT</b></font></font></p>
			</td>
		</tr>

		<tr valign="top">
			<td width="40" height="2">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><b>AE</b></font></font></p>
			</td>
			<td width="54">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="AE_left" value="<?php echo $this->_tpl_vars['data']->get_AE_left(); ?>
"></font></font></p>
			</td>
			<td width="56">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="AE_right" value="<?php echo $this->_tpl_vars['data']->get_AE_right(); ?>
"></font></font></p>
			</td>
		</tr>
		<tr valign="top">
			<td width="40" height="2">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><b>BE</b></font></font></p>
			</td>
			<td width="54">

				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="BE_left" value="<?php echo $this->_tpl_vars['data']->get_BE_left(); ?>
"></font></font></p>
			</td>
			<td width="56">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="BE_right" value="<?php echo $this->_tpl_vars['data']->get_BE_right(); ?>
"></font></font></p>
			</td>
		</tr>
		<tr valign="top">
			<td width="40" height="3">

				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><b>AK</b></font></font></p>
			</td>
			<td width="54">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="AK_left" value="<?php echo $this->_tpl_vars['data']->get_AK_left(); ?>
"></font></font></p>
			</td>
			<td width="56">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="AK_right" value="<?php echo $this->_tpl_vars['data']->get_AK_right(); ?>
"></font></font></p>

			</td>
		</tr>
		<tr valign="top">
			<td width="40" height="2">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><b>K</b></font></font></p>
			</td>
			<td width="54">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="K_left" value="<?php echo $this->_tpl_vars['data']->get_K_left(); ?>
"></font></font></p>

			</td>
			<td width="56">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="K_right" value="<?php echo $this->_tpl_vars['data']->get_K_right(); ?>
"></font></font></p>
			</td>
		</tr>
		<tr valign="top">
			<td width="40" height="2">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><b>BK</b></font></font></p>

			</td>
			<td width="54">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="BK_left" value="<?php echo $this->_tpl_vars['data']->get_BK_left(); ?>
"></font></font></p>
			</td>
			<td width="56">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="BK_right" value="<?php echo $this->_tpl_vars['data']->get_BK_right(); ?>
"></font></font></p>
			</td>
		</tr>

		<tr valign="top">
			<td width="40" height="2">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><b>ASIS</b></font></font></p>
			</td>
			<td width="54">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="ASIS_left" value="<?php echo $this->_tpl_vars['data']->get_ASIS_left(); ?>
"></font></font></p>
			</td>
			<td width="56">

				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="ASIS_right" value="<?php echo $this->_tpl_vars['data']->get_ASIS_right(); ?>
"></font></font></p>
			</td>
		</tr>
		<tr valign="top">
			<td width="40">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><b>UMB</b></font></font></p>
			</td>
			<td width="54">

				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="UMB_left" value="<?php echo $this->_tpl_vars['data']->get_UMB_left(); ?>
"></font></font></p>
			</td>
			<td width="56">
				<p class="western" align="justify"><font face="Verdana, sans-serif"><font style="font-size: 11pt;" size="2"><input type="text" name="UMB_right" value="<?php echo $this->_tpl_vars['data']->get_UMB_right(); ?>
"></font></font></p>
			</td>
		</tr>
	</tbody>
</table>

</p>
<table border='0' cellpadding='0' cellspacing='0' class='text'>
	<tr class='text'>
		<td><b>NOTES</b></td>
	</tr>
	<tr class='text'>
		<td><textarea cols='80' rows='5' name='notes'><?php echo $this->_tpl_vars['data']->get_notes(); ?>
</textarea></td>
	</tr>
</table>

<table>
	<tr>
		<td>
		</td>
	</tr>
	
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