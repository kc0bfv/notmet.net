<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	<meta name="description" content="<?php if( isset($metaDesc) ) echo $metaDesc; ?>">

	<title><?php if( isset( $mainTitle ) ) { echo $mainTitle; } if( isset($subTitle) ) { echo " - $subTitle"; } ?></title>
	
	<!--Blueprint CSS Framework Includes-->
	<link rel="stylesheet" href="css/blueprint/screen.css" type="text/css" media="screen, projection">
	<link rel="stylesheet" href="css/blueprint/print.css" type="text/css" media="print"> 
	<!--[if lt IE 8]>
		<link rel="stylesheet" href="/css/blueprint/ie.css" type="text/css" media="screen, projection">
	<![endif]-->

	<!--My CSS-->
	<link rel="stylesheet" href="css/myscreen.css" type="text/css" media="screen, projection">
	<link rel="stylesheet" href="css/myprint.css" type="text/css" media="print">
	<link rel="stylesheet" href="css/forTeach.css" type="text/css" media="screen, projection">

	<!--Favicon-->
	<link rel="icon" href="favicon.ico">

	<!--Useful JS-->
	<?php if( isset($headerJS) ) echo $headerJS; ?>
</head>
<body <?php if( isset($JSonload) ) echo 'onload="' . $JSonload . '"'; ?>>
	<div class="container">
<?php
	if( isset($displayHeadImg) && $displayHeadImg ) {
		echo '		<div class="span-24 top append-bottom"> <!--header-->' . "\n";
		$num=sprintf( "%02u", mt_rand(1,1) );
		echo '			<img class="veryrounded" src="images/header' . $num . '.jpg" alt="notmet.net">' . "\n";
		echo "		</div>\n";
	}
?>

