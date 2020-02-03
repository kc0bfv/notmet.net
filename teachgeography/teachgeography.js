		//Global variables
		var preGenerate = true; //Generate each of the city block in advance
		var frozen = false;
		var curZoomExtents = { 'leftX': 0, 'topY': 0, 'rightX': 0, 'bottomY': 0 };
		var updateWeatherOnMove = true;

		//Pads a number to at least two digits
		function pad( num ) {
			return (num<10) ? ( "0" + num ) : num; 
		}

		//Swap the keys and values of an array
		//Makes it easy to search an array for a value
		function swapArrayKeysValues( arr ) {
			var swapped=[];
			for( var i in arr ) {
				swapped[arr[i]]=i;
			}
			return swapped;
		}

		//take an associative array describing a bounding box on the map
		//return a list of city names, or something that we'll use to look them up in display weather
		function findVisibleCities( pixelExtents ) {
			var visibleCities = [];
			for( var zmw in databaseData ) {
				city = databaseData[zmw];
				if( city['pixelsXPos'] > pixelExtents['leftX'] && city['pixelsXPos'] < pixelExtents['rightX'] &&
				 city['pixelsYPos'] > pixelExtents['topY'] && city['pixelsYPos'] < pixelExtents['bottomY'] ) {
					visibleCities.push( zmw );
				}
			}
			return visibleCities;
		}
	
		function getIconFilename( iconStr ) {
			var icon = "icons/";
			switch( iconStr ) {
				case "clear": case "sunny": icon+="clear"; break;
				case "cloudy": icon+="cloudy"; break;
				case "flurries": icon+="flurries"; break;
				case "fog": icon+="fog"; break;
				case "hazy": icon+="hazy"; break;
				case "mostlycloudy": case "partlysunny": icon+="mostlycloudy"; break;
				case "partlycloudy": case "mostlysunny": icon+="partlycloudy"; break;
				case "rain": icon+="rain"; break;
				case "sleet": icon+="sleet"; break;
				case "snow": icon+="snow"; break;
				case "tstorms": icon+="tstorms"; break;
				case "chanceflurries": icon+="chanceflurries"; break;
				case "chancerain": icon+="chancerain"; break;
				case "chancesleet": icon+="chancesleet"; break;
				case "chancesnow": icon+="chancesnow"; break;
				case "chancetstorms": icon+="chancetstorms"; break;
				default: icon+="unknown";
			}
			icon += ".jpg";
			return icon;
		}

		function genCityDiv( weather, display ) {
			var newdiv = document.createElement( 'div' );
			newdiv.setAttribute('class','cityWeather rounded');
			newdiv.setAttribute('id','weatherDiv'+weather["zmw"]);
			if( display ) { //if we want to display this, or if it's not specified
				newdiv.setAttribute('display','block');
			} else {
				newdiv.setAttribute('display','none');
			}
			newdiv.innerHTML = '<span class="cityName">' + weather["name"] + '</span><br><img class="rounded" src="' + getIconFilename(weather["icon"]) + '"><br><span class="cityCondition">' + weather["conditions"] + '</span><br><span class="cityTemp">' + weather["lowF"] + '&deg;F - ' + weather["highF"] + '&deg;F</span><br><span class="cityTemp">' + weather["lowC"] + '&deg;C - ' + weather["highC"] + '&deg;C</span>';
			return newdiv;
		}

		//take an array of cities to look up
		//get the weather for the cities and display it
		function displayWeather( cities ) {
			var weatherBar = document.getElementById('weatherbar');
			if( !preGenerate ) {
				//remove all current divs in the weatherBar
				while( weatherBar.childNodes[0] ) {
					weatherBar.removeChild( weatherBar.childNodes[0] );
				}

				//Generate and add the new divs to weatherBar
				for( var cityIndex in cities ) {
					weather = databaseData[cities[cityIndex]];
					cityDiv=genCityDiv( weather );
					weatherBar.appendChild(cityDiv);
				}
			} else {
				cityEasySearch = swapArrayKeysValues( cities );
				for( var zmw in databaseData ) {
					var div=document.getElementById('weatherDiv' + zmw );
					if( cityEasySearch[zmw] ) {
						div.style.display = "block";
					} else {
						div.style.display = "none";
					}
				}
			}
		}

		function updateZoom( event ) {
			if( frozen ) {
				return;
			}

			//Do some setup
			var zoomImg = document.getElementById('zoomImg');
			var fullImg = document.getElementById('fullImg');
			var zoomImgWidth = zoomImg.offsetWidth;
			var zoomImgHeight = zoomImg.offsetHeight;
			var scaling = 3.88;
			var origImgWidth = 2289;
			var origImgHeight = 1155;

			//Get the cursor position relative to the image position
			var mouseX = Math.round( event.pageX ) - fullImg.offsetLeft;
			var mouseY = Math.round( event.pageY ) - fullImg.offsetTop;

			//Scale the cursor position up from the full image to the zoomed one
			var offsetX = mouseX * scaling;
			var offsetY = mouseY * scaling;

			//Center the zoom image on the cursor
			offsetX = (offsetX < (zoomImgWidth/2)) ? 0 : (offsetX-(zoomImgWidth/2));
			offsetY = (offsetY < (zoomImgHeight/2)) ? 0 : (offsetY-(zoomImgHeight/2));
			if( (offsetX + zoomImgWidth) > origImgWidth ) {
				offsetX = origImgWidth - zoomImgWidth;
			}
			if( (offsetY + zoomImgHeight) > origImgHeight ) {
				offsetY = origImgHeight - zoomImgHeight;
			}
			offsetX = Math.floor( offsetX );
			offsetY = Math.floor( offsetY );

			zoomImg.style.background = 'transparent url(fullMap.png) -' + offsetX + 'px -' + offsetY + 'px no-repeat';
			curZoomExtents={ 'leftX': offsetX, 'topY': offsetY, 'rightX': offsetX+zoomImgWidth, 'bottomY': offsetY+zoomImgHeight };

			if( updateWeatherOnMove ) {
				var cities = findVisibleCities( curZoomExtents );
				displayWeather( cities );
			}
		}

		function freezeImage( event ) {
			frozen = !frozen;
			if( !frozen ) {
				updateZoom( event );
			} else {
				var cities = findVisibleCities( curZoomExtents );
				displayWeather( cities );
			}
		}

		function afterLoad() {
			var fullImg = document.getElementById('fullImg');
			var initialX = 290;
			var initialY = 70;

			if( preGenerate ) {
				var weatherBar = document.getElementById('weatherbar');
				//Generate and add the new divs to weatherBar
				for( var zmw in databaseData ) {
					city=databaseData[zmw];
					cityDiv=genCityDiv( city, false );
					weatherBar.appendChild(cityDiv);
				}
			}

			updateZoom({'pageX': initialX+fullImg.offsetLeft, 'pageY': initialY+fullImg.offsetTop});
		}

