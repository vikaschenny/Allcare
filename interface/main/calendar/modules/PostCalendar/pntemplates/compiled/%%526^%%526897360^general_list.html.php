<?php /* Smarty version 2.6.2, created on 2016-12-05 11:56:25
         compiled from /var/www/html/allcaretphc/templates/practice_settings/general_list.html */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'xl', '/var/www/html/allcaretphc/templates/practice_settings/general_list.html', 20, false),)), $this); ?>
<html>
<head>
<?php html_header_show(); ?>

<link rel="stylesheet" href="<?php echo $this->_tpl_vars['CSS_HEADER']; ?>
" type="text/css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['rootdir'] . '/../library/bootstrap/docs/css/bootstrap-3.2.0.min.css'; ?>" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['rootdir'] . '/../library/js/fancybox/jquery.fancybox-1.2.6.css'; ?>" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['rootdir'] . '/../library/popover/css/jquery.webui-popover.min.css'; ?>" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['rootdir'] . '/../library/jalert/css/jquery-confirm.css'; ?>" media="screen" />
<script type="text/javascript" src="<?php echo $GLOBALS['rootdir'] . '/../library/js/jquery.1.3.2.js'; ?>"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['rootdir'] . '/../library/popover/js/jquery.webui-popover.js'; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['rootdir'] . '/../library/js/common.js'; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['rootdir'] . '/../library/bootstrap/docs/js/bootstrap-3.2.0.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['rootdir'] . '/../library/jalert/js/jquery-confirm.js'; ?>"></script>
</head>
<body class="body_top">

<div>
    <b><?php echo smarty_function_xl(array('t' => 'Practice Settings'), $this);?>
</b>
</div>

<div>
    <div class="small">
        <a href="<?php echo $this->_tpl_vars['TOP_ACTION']; ?>
pharmacy&action=list"><?php echo smarty_function_xl(array('t' => 'Pharmacies'), $this);?>
</a> |
        <a href="<?php echo $this->_tpl_vars['TOP_ACTION']; ?>
insurance_company&action=list"><?php echo smarty_function_xl(array('t' => 'Insurance Companies'), $this);?>
</a> |
<!--        <a href="../templates/insurance_companies/insurance_custom.php"><?php echo smarty_function_xl(array('t' => 'Insurance Companies'), $this);?>
</a> |-->
        <a href="<?php echo $this->_tpl_vars['TOP_ACTION']; ?>
insurance_numbers&action=list"><?php echo smarty_function_xl(array('t' => 'Insurance Numbers'), $this);?>
</a> |
        <a href="<?php echo $this->_tpl_vars['TOP_ACTION']; ?>
x12_partner&action=list"><?php echo smarty_function_xl(array('t' => 'X12 Partners'), $this);?>
</a> |
        <a href="<?php echo $this->_tpl_vars['TOP_ACTION']; ?>
document&action=queue"><?php echo smarty_function_xl(array('t' => 'Documents'), $this);?>
</a> |
        <a href="<?php echo $this->_tpl_vars['TOP_ACTION']; ?>
hl7&action=default"><?php echo smarty_function_xl(array('t' => 'HL7 Viewer'), $this);?>
</a>
    </div>

    <br/>
    <div class="section-header">
        <b><?php echo $this->_tpl_vars['ACTION_NAME']; ?>
</b>
    </div>
    <div class="tabContainer">
        <div class="tab current">
            <?php echo $this->_tpl_vars['display']; ?>

        </div>
    </div>
</div>
</body>
</html>