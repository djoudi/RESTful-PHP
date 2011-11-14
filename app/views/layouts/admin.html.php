<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<title><?= RESTful_Response::title() ?></title>
		<link rel="stylesheet" href="../css/960.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="../css/fluid.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="../css/template.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="../css/colour.css" type="text/css" media="screen" charset="utf-8" />
		
		<link rel="stylesheet" href="../css/jquery-ui-1.8.16.custom.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="../css/admin.css" type="text/css" media="screen" charset="utf-8" />
	</head>
	<body>
		
		<h1 id="head">OLBG API Admin</h1>
		
		<ul id="navigation">
			<li><span class="active">Bookies</span></li>
			<li><a href="../authentications/log_off.html">Log out</a></li>
		</ul>
		
		<div id="content" class="container_16 clearfix round_corners">
			<div class="grid_16">
				<? RESTful_Response::yeld() ?>
			</div>
		</div>
		
		<div id="foot" class="round_corners">
			<a href="http://olbg.com">OLBG.com</a>
			<a href="http://invendium.co.uk">Invendium.co.uk</a>
		</div>
		
		<script type="text/javascript" src="../js/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery-ui-1.8.16.custom.min.js"></script>
		<script type="text/javascript" src="../js/admin.js"></script>
	</body>
</html>