---
title: "Explore Outdoors"
date: 2019-06-09T16:25:58-04:00
buttonimage: "/img/eobutton.jpg"
images: ["/img/exploreoutdoors01.jpg", "/img/exploreoutdoors02.jpg", "/img/exploreoutdoors03.jpg", "/img/exploreoutdoors04.jpg"]
imagealt: "Explore Outdoors Screenshot"
draft: false
weight: 6
---

Explore Outdoors was a free layer for the Android and iPhone augmented reality app "Layar."  It displayed the names of mountains, lakes, parks, streams and a whole bunch of other good stuff.  Explore Outdoors was helpful for those times when you're sitting on top of a mountain and you wonder about the names of other nearby mountains.  There's an example screenshot of it to the right.

Unfortunately, the Layar app was discontinued, and therefore this layer no longer works.  Further, I don't know of any technology that's quite like it that's actively being developed.  Layar would request data from a URL endpoint, then display it in context of where you were.  It had a great selection system for choosing those URL endpoints, and standardized request and data formats.  That's really all that's needed to make this work again...

The data currently covered the US, and comes from the [USGS Geographical Names Database](http://geonames.usgs.gov/) which is published by the authority which official names these things.  The database was served to users from this website, which is in no way affiliated with the US government.  I also took all the icons and images used in the layer from public domain sources (or sources which I hope are public domain).  My list of image sources is at the bottom of this page.

I'd like to rebuild this as a standalone app, with a built-in places database, so no data connection is required.

Click, tap or use your phone's QR-code reader to try it out: 

[![A QR-code which will link your phone to the explore outdoors layer.](/img/eoqrcode.png)](http://m.layar.com/open/exploreoutdoors)

{{< dropdown summary="Image sources:" >}}
* [Earth](http://nssdc.gsfc.nasa.gov/photo_gallery/photogallery-earth.html)
* [Summit](http://en.wikipedia.org/wiki/Siniolchu)
* [Stream](http://education.usgs.gov/schoolyard/RockDescription.html)
* [Lake](http://ndep.nv.gov/photo/tahoe_emerald.htm)
* [Forest](http://www.nasa.gov/vision/earth/environment/0624_hanpp.html)
* [Arch](http://travel.utah.gov/posters/expanded/arches_poster.htm)
* [Falls](http://next.nasa.gov/alsj/a16/a16.sta11.html)
* [Falls](http://www.nps.gov/piro/planyourvisit/nearbyattractions.htm)
* [Cliff](http://coastal.er.usgs.gov/navassa/sail/nw1.html)
* [Cave](http://sbsc.wr.usgs.gov/cprs/research/projects/caves/wildlife.asp)
* [Lava](http://hvo.wr.usgs.gov/kilauea/update/archive/2008/2008_Jan.html)
* [Pillar](http://visitmt.com/history/Montana_the_Magazine_of_Western_History/Winter02/yellowstone.htm)
{{< /dropdown >}}
