<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title><?=$CFG->siteTitle?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="<? echo $CFG->admin_dir?>/style.css" rel="stylesheet" type="text/css">
		<?if(isset($includeOverLib)){?>
		<script type="text/javascript" src="overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
		<?}?>
		<script src="http://bogota.promoambientaldistrito.com/JSCal2-1.9/src/js/jscal2.js"></script>
		<script src="http://bogota.promoambientaldistrito.com/JSCal2-1.9//src/js/lang/es.js"></script>
		<link rel="stylesheet" type="text/css" href="http://bogota.promoambientaldistrito.com/JSCal2-1.9/src/css/jscal2.css" />
		<link rel="stylesheet" type="text/css" href="http://bogota.promoambientaldistrito.com/JSCal2-1.9/src/css/border-radius.css" />
		<link rel="stylesheet" type="text/css" href="http://bogota.promoambientaldistrito.com/JSCal2-1.9/src/css/steel/steel.css" />

	</head>
	<body style="margin: 8" bgcolor="<?if(isset($entidad)) echo $entidad->get("pageBgColor"); else echo "#ffffff";?>">
