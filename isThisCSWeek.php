<html>
<head>
	<title>Is this a CS Week?</title>
</head>
<body bgcolor="White">
<h1>
<?php

$indicator="/home/finity/.csweek";
$negindicator="/home/finity/.notcsweek";
$olderThanLimit=60*60*6;

$CSWeek="This is the week!\n";
$NotCSWeek="This isn't the week...\n";

if( file_exists($indicator) ) {
	if( (time() - filemtime($indicator)) > $olderThanLimit ) {
		# The indicator file exists and is old, it is a CS week!
		print($CSWeek);
	} else {
		# The indicator exists but isn't old, this is too early to say it's the CS week, it would confuse people
		print($NotCSWeek);
	}
} else if( file_exists($negindicator) ) {
	if( (time() - filemtime($negindicator)) > $olderThanLimit ) {
		# The negative-indicator file exists and is old, no CS...
		print($NotCSWeek);
	} else {
		print($CSWeek);
	}
} else {
	print("I had trouble telling if this was a CS week, but I don't think it is.\n");
}
?>
</h1>
</body>
</html>
