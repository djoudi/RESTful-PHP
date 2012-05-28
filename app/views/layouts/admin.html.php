<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<title><?= RESTful_Response::title() ?></title>
		<link rel="stylesheet" href="<?= BASE_URL ?>css/960.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="<?= BASE_URL ?>css/fluid.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="<?= BASE_URL ?>css/template.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="<?= BASE_URL ?>css/colour.css" type="text/css" media="screen" charset="utf-8" />
		
		<link rel="stylesheet" href="<?= BASE_URL ?>css/jquery-ui-1.8.16.custom.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="<?= BASE_URL ?>css/admin.css" type="text/css" media="screen" charset="utf-8" />
	</head>
	<body>
		
		<h1 id="head">OLBG API Admin</h1>

		<ul id="navigation">
			<li>
        <? if ( RESTful_Application::getRequest()->getController() == 'admin/bookies' ): ?>
        <span class="active">Bookies</span>
        <? else: ?>
        <a href="<?= BASE_URL ?>admin/bookies">Bookies</a>
        <? endif; ?>
      </li>
      <li>
        <? if ( RESTful_Application::getRequest()->getController() == 'admin/events' ): ?>
        <span class="active">Events</span>
        <? else: ?>
        <a href="<?= BASE_URL ?>admin/events">Events</a>
        <? endif; ?>
      </li>
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
		
		<script type="text/javascript" src="<?= BASE_URL ?>js/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="<?= BASE_URL ?>js/jquery-ui-1.8.16.custom.min.js"></script>
		<script type="text/javascript" src="<?= BASE_URL ?>js/admin.js"></script>
	</body>
</html>