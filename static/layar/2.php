<?php
//---------------Functions Used-----------------

//This changes Goog supplied (decimal) lat or lon into a Layar-type int lat lon
function DecimalPOSToInt( $decVal ) {
	return (int)($decVal * 1000000);
}

//Converts a string to an int or returns NULL
function SafeStrToInt( $strVal ) {
	if( is_numeric( trim( $strVal ) ) ) {
		return (int)$strVal;
	} else {
		return NULL;
	}
}

//Converts a string to a float or returns NULL
function SafeStrToFlt( $strVal ) {
	if( is_numeric( trim( $strVal ) ) ) {
		return (float)$strVal;
	} else {
		return NULL;
	}
}

//Calculate the box surrounding the points we want to search
function CalcLatLonBoundingBox( $userLat, $userLon, $userRadius) {
	//Convert from deg to rads
	$latR = (float) $userLat * pi() / 180;
	$lonR = (float) $userLon * pi() / 180;
	$radius = (float) $userRadius;
	$R = 6371000; //Earth radius in m

	//Calculate!
	$lon1 = $lonR - acos( (1/cos($latR))*(1/cos($latR))*(cos($radius/$R)-sin($latR)*sin($latR)) );
	$lon2 = $lonR + acos( (1/cos($latR))*(1/cos($latR))*(cos($radius/$R)-sin($latR)*sin($latR)) );
	$lat1 = $latR - acos(cos($radius/$R));
	$lat2 = $latR + acos(cos($radius/$R));

	//Convert from radians to deg
	$lat1 = $lat1*180/pi();
	$lat2 = $lat2*180/pi();
	$lon1 = $lon1*180/pi();
	$lon2 = $lon2*180/pi();

	//Check the ranges
	$lat1 = ($lat1<=90)? $lat1 : 90;
	$lat1 = ($lat1>=-90)? $lat1 : -90;
	$lat2 = ($lat2<=90)? $lat2 : 90;
	$lat2 = ($lat2>=-90)? $lat2 : -90;
	$lon1 = ($lon1<=180)? $lon1 : 180;
	$lon1 = ($lon1>=-180)? $lon1 : -180;
	$lon2 = ($lon2<=180)? $lon2 : 180;
	$lon2 = ($lon2>=-180)? $lon2 : -180;

	//Drop into an output array
	$box = array();
	$box["latmin"] = ($lat1>$lat2)? $lat2 : $lat1;
	$box["latmax"] = ($lat1<$lat2)? $lat2 : $lat1;
	$box["lonmin"] = ($lon1>$lon2)? $lon2 : $lon1;
	$box["lonmax"] = ($lon1<$lon2)? $lon2 : $lon1;

	return $box;
}

//Calculate distance between points
function CalcDistance( $userLat, $userLon, $poiLat, $poiLon ) {
	//Calculate actual distance
	$latR = (float) $userLat * pi() / 180;
	$lonR = (float) $userLon * pi() / 180;
	$latP = (float) $poiLat * pi() / 180;
	$lonP = (float) $poiLon * pi() / 180;
	$R = 6371000; //Earth Radius in m
	$distance = acos(sin($latR)*sin($latP) + cos($latR)*cos($latP)*cos($lonP-$lonR)) * $R;

	return $distance;
}

//Build a WHERE filter for the classes, build it so it can be inserted behind an existing WHERE filter (put an AND at the front)
function BuildClassFilter( $usrReq ) {
	$classFilter="";
	$classCol="`usgs_class`";
	if( strpos( $usrReq, "moun" ) !== false ) {
		if( $classFilter != "" )
			$classFilter.= " OR ";
		$classFilter.="$classCol='Glacier' OR $classCol='Summit'";
	}
	if( strpos( $usrReq, "lake" ) !== false ) {
		if( $classFilter != "" )
			$classFilter.= " OR ";
		$classFilter.="$classCol='Lake' OR $classCol='Stream'";
	}
	if( strpos( $usrReq, "park" ) !== false ) {
		if( $classFilter != "" )
			$classFilter.= " OR ";
		$classFilter.="$classCol='Forest' OR $classCol='Park' OR $classCol='Reserve'";
	}
	if( strpos( $usrReq, "misc" ) !== false ) {
		if( $classFilter != "" )
			$classFilter.= " OR ";
		$classFilter.="$classCol='Arch' OR $classCol='Cave' OR $classCol='Cliff' OR $classCol='Crater' OR $classCol='Falls' OR $classCol='Gap' OR $classCol='Lava' OR $classCol='Pillar' OR $classCol='Ridge'";
	}

	if( $classFilter != "" ) {
		$classFilter = "AND (" . $classFilter . ")";
	}

	return $classFilter;
}

//Choose the closest 50 pois and return them
function ChooseClosest( $pois ) {
		//Setup two arrays for array_multisort - one of distance, the other of which poi that distance went to
		$indexes=array();
		$distances=array();
		$i=0;
		foreach( $pois as $poikey=>$poival) {
			$indexes[$i]=$poikey;
			$distances[$i]=$poival["distance"];
			$i++;
		}

		//Do multisort, sorting by distance and taking index along with for the ride
		array_multisort( $distances, $indexes );

		//Replace response hotspots with the closest 50
		$response=array();
		for( $i=0; $i<50; $i++ ) {
			$response[$i] = $pois[$indexes[$i]];
		}

		return $response;
}




//---------------Main Code-----------------

//Default Settings
include_once "../../exploreOutdoorsConfig.php";
$defaultLayerName = "exploreoutdoors";

//Determine what the user is requesting
try {
	$request = array( "layerName"=>$defaultLayerName, "lat"=>"0", "lon"=>"0", "radius"=>"10000", "CHECKBOXLIST"=>"moun,lake,park,misc", "CUSTOM_SLIDER"=>"0", "pageKey"=>"0" ); //Potential user-supplied keys and reasonable defaults
	foreach( $request as $key => $value ) {
		if( isset( $_GET[$key] )) {
			$request[$key] = $_GET[$key]; //I do input validation next
		}
	}

	//Verify a few inputs, throw a fit if they aren't right
	if( !is_numeric( $request["lat"] ) || !is_numeric( $request["lon"] )) {
		throw new Exception( "Latitude and longitude must be numeric." );
	}
	$request["lat"] = ($request["lat"]<=90)? (float) $request["lat"] : 90;
	$request["lat"] = ($request["lat"]>=-90)? (float) $request["lat"] : -90;
	$request["lon"] = ($request["lon"]<=180)? (float) $request["lon"] : 180;
	$request["lon"] = ($request["lon"]>=-180)? (float) $request["lon"] : -180;

	if( !is_numeric( $request["radius"] ) ) {
		throw new Exception( "Radius must be numeric." );
	}
	$request["radius"] = ($request["radius"]<=20000)? (int) $request["radius"] : 20000;
	$request["radius"] = ($request["radius"]>=0)? (int) $request["radius"] : 0;

	//These two don't always show up right, so if they're wrong just set them right
 	if( !is_numeric( $request["CUSTOM_SLIDER"] ) ) {
		$request["CUSTOM_SLIDER"] = 0;
	}
	$request["CUSTOM_SLIDER"] = ($request["CUSTOM_SLIDER"]<=20000)? (float) $request["CUSTOM_SLIDER"] : 20000;
	$request["CUSTOM_SLIDER"] = ($request["CUSTOM_SLIDER"]>=0)? (float) $request["CUSTOM_SLIDER"] : 0;

	//Make sure the checkboxlist is a string, then pass it to the parser
	if( !is_string( $request["CHECKBOXLIST"] )) {
		$request["CHECKBOXLIST"] = "moun,lake,park,misc";
	}
	$classFilter=BuildClassFilter( $request["CHECKBOXLIST"] );

	if( !is_numeric( $request["pageKey"] )) {
		$request["pageKey"] = 0;
	}
	$request["pageKey"] = ($request["pageKey"]<=40) ? (int) $request["pageKey"] : 40;
	$request["pageKey"] = ($request["pageKey"]>=0) ? (int) $request["pageKey"] : 0;

	//Calculations for appropriate lat/lon search area
	$box = CalcLatLonBoundingBox( $request["lat"], $request["lon"], $request["radius"] );

	$response = array();

	//Connect to DB
	$db = new PDO( "mysql:host=$dbhost; dbname=$dbname;", $dbuser, $dbpass, array( PDO::ATTR_PERSISTENT => true, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8" ));
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	//Query DB for relevant POI
	$sqlStmt = $db->prepare( "SELECT my_id, title, attribution, lat, lon, alt, imageURL, line2, line3, line4, type, dimension, usgs_id, usgs_name, usgs_class, usgs_state, usgs_county FROM POI_Table USE INDEX (lonlatclass) WHERE lat>=:latmin AND lat<=:latmax AND lon>=:lonmin AND lon<=:lonmax $classFilter ORDER BY my_id ASC LIMIT 0, 200" ); //TODO: use limit to implement paging?
	$sqlStmt->bindParam(":latmin", $box["latmin"]);
	$sqlStmt->bindParam(":latmax", $box["latmax"]);
	$sqlStmt->bindParam(":lonmin", $box["lonmin"]);
	$sqlStmt->bindParam(":lonmax", $box["lonmax"]);
//	echo "$sqlStmt->queryString\n<br>";
//	echo $box["latmin"] . " ". $box["latmax"] . " ".  $box["lonmin"] . " ".  $box["lonmax"] . "\n";
//	exit;
	$sqlStmt->execute();
	$pois = array(); //each member will be an array representing a poi
	$pois = $sqlStmt->fetchAll( PDO::FETCH_ASSOC );

	//Close DB
	$db = NULL;

	//Format DB response into final list of pois
	foreach( $pois as $poikey=>&$poi ) {
		if( isset($poi["usgs_name"]) && $poi["usgs_name"] != "" )
			$poi["title"] = $poi["usgs_name"];
		$poi["actions"] = array();

		$poi["distance"] = CalcDistance( $request["lat"], $request["lon"], $poi["lat"], $poi["lon"] );

		$poi["id"] = $poi["my_id"];

		switch( strtolower(trim( $poi["usgs_class"] ))) {
			case "glacier":
			case "summit":
				$poi["imageURL"] = "http://notmet.net/layar/icons/summit.png";
				break;
			case "lake":
				$poi["imageURL"] = "http://notmet.net/layar/icons/lake.png";
				break;
			case "stream":
				$poi["imageURL"] = "http://notmet.net/layar/icons/stream.png";
				break;
			case "forest":
			case "park":
			case "reserve":
				$poi["imageURL"] = "http://notmet.net/layar/icons/forest.png";
				break;
			case "pillar":
				$poi["imageURL"] = "http://notmet.net/layar/icons/pillar.png";
				break;
			case "arch":
				$poi["imageURL"] = "http://notmet.net/layar/icons/arch.png";
				break;
			case "cave":
				$poi["imageURL"] = "http://notmet.net/layar/icons/cave.png";
				break;
			case "crater":
				$poi["imageURL"] = "http://notmet.net/layar/icons/crater.png";
				break;
			case "gap":
			case "ridge":
			case "cliff":
				$poi["imageURL"] = "http://notmet.net/layar/icons/cliff.png";
				break;
			case "falls":
				$poi["imageURL"] = "http://notmet.net/layar/icons/falls.png";
				break;
			case "lava":
				$poi["imageURL"] = "http://notmet.net/layar/icons/lava.png";
				break;

			default:
				$poi["imageURL"] = "";
				break;
		}

		//Final in-place formatting.  Do this last so calculations can work
		$poi["lat"] = DecimalPOSToInt( $poi["lat"] );
		$poi["lon"] = DecimalPOSToInt( $poi["lon"] );
		$poi["type"] = SafeStrToInt( $poi["type"] );
		$poi["dimension"] = SafeStrToInt( $poi["dimension"] );
		$poi["alt"] = SafeStrToInt( $poi["alt"] );
		//$poi["relativeAlt"] = SafeStrToInt( $poi["relativeAlt"] );
		if( !isset( $poi["line4"] )) $poi["line4"]="";

		//The user doesn't need to receive these pieces of data
		unset($poi["my_id"]);
		unset($poi["usgs_name"]);
		unset($poi["usgs_id"]);
		unset($poi["usgs_class"]);
		unset($poi["usgs_state"]);
		unset($poi["usgs_county"]);

		//If the poi is within the minimum distance, go ahead and delete it
		if( $poi["distance"] < (float) $request["CUSTOM_SLIDER"] ) {
			unset( $pois[$poikey] );
		}
	}

	$pois = array_values( $pois ); //Reindex pois - json_encode has trouble if we deleted any in that last step. 

	//Setup the response to be passed to JSON formatter
	//We're just ignoring whatever layer the user requested for now
	//When there's more than one layer served from here, we'll not be able to
	$response["layer"] = $defaultLayerName;

	//Handle too many/too few/just right number of POIs
	if( count($pois) > 50 ) {
		//Too many POIs?  Sort by distance, then return the 50 closest and send an error to the user
		//TODO: Paging!
		$response["hotspots"] = ChooseClosest( $pois );
		$tempHotspots = ChooseClosest( $pois );
		$response["errorCode"] = 20;
		$response["errorString"] = "Too many points of interest!  Only 50 are displayed.";
	} elseif( empty( $pois ) ) {
		$tempHotspots = $pois;
		$response["errorCode"] = 20;
		$response["errorString"] = "No points of interest!  Please adjust the range or objects displayed.";
	} else {
		$tempHotspots = $pois;
		$response["errorCode"] = 0;
		$response["errorString"] = "ok";
	}

	//Handle Pagination
	$response["hotspots"] = array_slice( $tempHotspots, $request["pageKey"], 10 );
	$nextKey = $request["pageKey"] + 10;
	if( (count($response["hotspots"])<count($tempHotspots)) && ($nextKey < 50) ) {
		$response["nextPageKey"] = $nextKey;
		$response["morePages"] = true;
	}

} catch( PDOException $e ) {
	$response["layer"] = $defaultLayerName;
	$response["errorCode"] = 28;
	$response["errorString"] = "Database error: " . $e->getMessage();
} catch( Exception $e ) {
	$response["layer"] = $defaultLayerName;
	$response["errorCode"] = 29;
	$response["errorString"] = "Generic error: " . $e->getMessage();
}

//JSON Formatting
$jsoned_response = json_encode( $response );
//$jsoned_response = str_replace( "\\/", "/", $jsoned_response );//Have to turn off escaped slashes, but that feature isn't working in json_encode.  This is a hack
header( "Content-type: application/json; charset=utf-8" );
echo $jsoned_response;

?>
