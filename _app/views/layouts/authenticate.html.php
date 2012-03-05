<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>OLBG API - Auth</title>
<link rel="stylesheet" href="<?=BASE_URL?>css/screen.css" type="text/css" media="screen" title="default" />
<!--  jquery core -->
<script src="<?=BASE_URL?>js/jquery/jquery-1.4.1.min.js" type="text/javascript"></script>

<!-- Custom jquery scripts -->
<script src="<?=BASE_URL?>js/jquery/custom_jquery.js" type="text/javascript"></script>

<!-- MUST BE THE LAST SCRIPT IN <HEAD></HEAD></HEAD> png fix -->
<script src="<?=BASE_URL?>js/jquery/jquery.pngFix.pack.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(document).pngFix( );
	});
</script>
</head>

<body id="login-bg"> 
 
 <? RESTful_Response::yeld() ?>
 
</body>
</html>