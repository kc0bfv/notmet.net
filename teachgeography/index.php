<?php
	//Include necessary stuff
	include_once "../../teachgeography/databaseFuncs.php";

	//Read in the default variable values
	include_once "../../teachgeography/teachGeographySettings.php";

	//Prep the json weather to be inserted
	$db = openDatabase( $dbInfo );
	$databaseData = getDatabaseData( $db );
	$headerJS = '	<script type="text/javascript" src="teachgeography.js"></script>';
	$headerJS .= '<script type="text/javascript">var databaseData=' . json_encode( $databaseData ) . ';</script>' . "\n";
	closeDatabase( $db );

	//Prep the code which will be executed after loading
	$JSonload = "afterLoad();";

	//Set all that stuff before including head
	include "head.php";
?>
		<div class="span-15"> <!--full image-->
			<img id="fullImg" src="fullMap.png" width="590" alt="Political Map of the World" onmousemove="updateZoom(event);" onmouseup="freezeImage(event);">
		</div>

		<div id="zoomImg" class="span-9 rounded last"> <!--zoomed image-->
		</div>

		<div id="weatherbar" class="span-24">
		</div>
<?php
	include "foot.php";
?>
