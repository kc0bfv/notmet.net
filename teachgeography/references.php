<?php
	//Read in the default variable values
	include_once "../../teachgeography/teachGeographySettings.php";

	$displayHeadImg = true;
	$subTitle = "References";

	//Set all that stuff before including head
	include "head.php";
?>
	<div class="span-24">
		<p>I used the following resources to create this site:
		<ul>
			<li>Weather Underground - My weather data comes from these guys.</li>
			<li>NOAA - My weather icons come from (or were slightly modified from) these guys' icons</li>
			<li>Wikipedia - The map background came from these guys</li>
		</ul>
		<p>I've attempted to select resources that were licensed in such a way to enable me to create this site free of charge, and appreciate the things that the above-linked folks have provided.  Thanks.
	</div>
<?php
	include "foot.php";
?>
