<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
        <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
	<title>Nested Layouts</title> 

	<script src="../../../../library/bootstrap/docs/js/jquery-2.1.1.min.js"></script>
        <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="./js/jquery.layout.js"></script> 
	<script type="text/javascript" src="./js/jquery.ui.all.js"></script> 

	<script> 

	var outerLayout, middleLayout, innerLayout; 

	$(document).ready(function () { 

		outerLayout = $('body').layout({ 
			center__paneSelector:	".outer-center" 
		,	west__paneSelector:		".outer-west" 
		,	east__paneSelector:		".outer-east" 
		,	west__size:				200 
		,	east__size:				125 
		,	spacing_open:			8 // ALL panes
		,	spacing_closed:			12 // ALL panes
		,	north__spacing_open:	0
		,	south__spacing_open:	0
		,	center__onresize:		"middleLayout.resizeAll" 
		}); 

		middleLayout = $('div.outer-center').layout({ 
			center__paneSelector:	".middle-center" 
		,	west__paneSelector:		".middle-west" 
		,	east__paneSelector:		".middle-east" 
		,	west__size:				100 
		,	east__size:				100 
		,	spacing_open:			8  // ALL panes
		,	spacing_closed:			12 // ALL panes
		,	center__onresize:		"innerLayout.resizeAll" 
		}); 

		innerLayout = $('div.middle-center').layout({ 
			center__paneSelector:	".inner-center" 
		,	west__paneSelector:		".inner-west" 
		,	east__paneSelector:		".inner-east" 
		,	west__size:				75 
		,	east__size:				75 
		,	south__size:			250 
		,	spacing_open:			8  // ALL panes
		,	spacing_closed:			8  // ALL panes
		,	west__spacing_closed:	12
		,	east__spacing_closed:	12
		}); 

	}); 


	</script> 

	<style type="text/css"> 

	.ui-layout-pane { /* all 'panes' */ 
		padding: 10px; 
		background: #FFF; 
		border-top: 1px solid #BBB;
		border-bottom: 1px solid #BBB;
		}
		.ui-layout-pane-north ,
		.ui-layout-pane-south {
			border: 1px solid #BBB;
		} 
		.ui-layout-pane-west {
			border-left: 1px solid #BBB;
		} 
		.ui-layout-pane-east {
			border-right: 1px solid #BBB;
		} 
		.ui-layout-pane-center {
			border-left: 0;
			border-right: 0;
			} 
			.inner-center {
				border: 1px solid #BBB;
			} 

		.outer-west ,
		.outer-east {
			background-color: #EEE;
		}
		.middle-west ,
		.middle-east {
			background-color: #F8F8F8;
		}

	.ui-layout-resizer { /* all 'resizer-bars' */ 
		background: #DDD; 
		}
		.ui-layout-resizer:hover { /* all 'resizer-bars' */ 
			background: #FED; 
		}
		.ui-layout-resizer-west {
			border-left: 1px solid #BBB;
		}
		.ui-layout-resizer-east {
			border-right: 1px solid #BBB;
		}

	.ui-layout-toggler { /* all 'toggler-buttons' */ 
		background: #AAA; 
		} 
		.ui-layout-toggler:hover { /* all 'toggler-buttons' */ 
			background: #FC3; 
		} 

	.outer-center ,
	.middle-center {
		/* center pane that are 'containers' for a nested layout */ 
		padding: 0; 
		border: 0; 
	} 

	</style> 

</head> 
<body> 

<div class="outer-center">
	<div class="middle-center">
		<div class="inner-center">Inner Center</div> 
		<div class="ui-layout-south">Inner South</div> 
	</div> 
</div> 
<div class="outer-west">Outer West</div> 

<div class="ui-layout-north">Outer North</div> 

</body> 
</html> 

