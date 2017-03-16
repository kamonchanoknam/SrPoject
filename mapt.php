<!DOCTYPE html>

<html>
  <head>
    <title>Place ID Finder</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
       #directionsPanel{
        width:600px;
        margin:auto;
        clear:both; 
/*        background-color:#F1FEE9;*/
    }
      #map {
        height: 400px;
        width: 600px;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      .controls {
        background-color: #fff;
        border-radius: 2px;
        border: 1px solid transparent;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        box-sizing: border-box;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        height: 29px;
        margin-left: 17px;
        margin-top: 10px;
        outline: none;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
      }

      .controls:focus {
        border-color: #4d90fe;
      }

       .textbox {
        background-color: #fff;
        border-radius: 2px;
        border: 1px solid transparent;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        box-sizing: border-box;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        height: 29px;
        margin-left: 17px;
        margin-top: 10px;
        outline: none;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
      }

      .textbox:focus {
        border-color: #4d90fe;
      }
      .title {
        font-weight: bold;
      }
      #infowindow-content {
        display: none;
      }
      #map #infowindow-content {
        display: inline;
      }

    </style>
  </head>
  <body>
  <?php

  $db         = mysqli_connect('localhost', 'root', '', 'templeincm');
  $db->set_charset("utf8");

  
  $sql        = "SELECT * FROM temple ORDER BY Temp_name";

  
  $res        = $db->query($sql);
  
 
   
    
?>
      <div id="infowindow-content">
        <span id="place-name"  class="title"></span><br>
        Place ID <span id="place-id"></span><br>
        <span id="place-address"></span>
      </div>

    <input id="pac-input" class="controls" type="text"
        placeholder="Enter a location">
<button onclick="setMap()">กด</button>

         ปลายทาง :
    <input class="textbox" type="text" list="myCompanies" name="tempName" id="tempName" >
            <datalist id='myCompanies'>
        <?php 
        while( $row = $res->fetch_object() ){
          echo "<option value='".$row->Temp_latitude.",".$row->Temp_longitude."'>".$row->Temp_name."</option>";
        }
        ?>
            </datalist>
    
    
    <div id="map"></div>
    <div id="directionsPanel"></div>
    <script>
      // This sample uses the Place Autocomplete widget to allow the user to search
      // for and select a place. The sample then displays an info window containing
      // the place ID and other information about the place that the user has
      // selected.
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
      var pointA;
      var pointB;
      var directionsService;
      var directionsDisplay;
      var directionShow;
      var lat;
      var long;
      var map;
      var infowindow;
      var markerList = [];

      function createRadius(lat, long, rad) {

          // Add the circle for this city to the map.
          var cityCircle = new google.maps.Circle({
              strokeColor: '#FF0000',
              strokeOpacity: 0.8,
              strokeWeight: 2,
              fillColor: '#FF0000',
              fillOpacity: 0.35,
              map: map,
              center: new google.maps.LatLng(lat, long),
              radius: rad * 1000
          });

      }

      function newMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: 18.799534,
                lng: 98.979538
            },
            zoom: 13
        });

        var input = document.getElementById('pac-input');

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        infowindow = new google.maps.InfoWindow();
        var infowindowContent = document.getElementById('infowindow-content');
        infowindow.setContent(infowindowContent);

        autocomplete.addListener('place_changed', function() {
            infowindow.close();
            place = autocomplete.getPlace();
            var location = place.geometry.location;
            lat = location.lat();
            long = location.lng();
            if (!place.geometry) {
                return;
            }

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            // Set the position of the marker using the place ID and location.
            // marker.setPlace({
            //   placeId: place.place_id,
            //   location: place.geometry.location
            // });
            // marker.setVisible(true);

            infowindowContent.children['place-name'].textContent = place.name;
            infowindowContent.children['place-id'].textContent = place.place_id;
            infowindowContent.children['place-address'].textContent =
                place.formatted_address;

            pointA = new google.maps.LatLng(lat, long);
            setDirection(map, pointA);
        });
      }

      function initMap() {
          newMap();
      }

      
function dist($lat1, $long1, $lat2, $long2) {
          $pi = Math.PI;
          $dist = 0;

          $theta = $long1 - $long2;
          $dist = Math.sin($lat1 * $pi / 180) * Math.sin($lat2 * $pi / 180) + Math.cos($lat1 * $pi / 180) * Math.cos($lat2 * $pi / 180) * Math.cos($theta * $pi / 180);
          $dist = Math.acos($dist);
          $dist = ($dist * 180) / $pi;
          $dist = $dist * 60 * 1.1515 * 1.609344; //Km.

          return $dist;
      }

      function setDirection(map, pointA) {
          var latlong = document.getElementById('tempName').value.split(",");

          pointB = new google.maps.LatLng(latlong[0], latlong[1]);
          myOptions = {
                  zoom: 7,
                  center: pointA
              },
              map,
              // Instantiate a directions service.
              directionsService = new google.maps.DirectionsService,
              directionsDisplay = new google.maps.DirectionsRenderer({
                  map: map
              });
          // }),
          // markerA = new google.maps.Marker({
          //   position: pointA,
          //   title: "point A",
          //   label: "A",
          //   map: map
          // }),
          // markerB = new google.maps.Marker({
          //   position: pointB,
          //   title: "point B",
          //   label: "B",
          //   map: map
          // });

          // get route from A to B


      }

      function setMap() {
          // Clear Marker
          for (var i = 0; i < markerList.length; i++) {
              markerList[i].setMap(null);
          }
          markerList = [];

          calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB);
      }

      function callback(results, status) {
          if (status === google.maps.places.PlacesServiceStatus.OK) {
              for (var i = 0; i < results.length; i++) {
                  createMarker(results[i]);
              }
          }
      }

      function createMarker(place) {
          var placeLoc = place.geometry.location;
          var marker = new google.maps.Marker({
              map: map,
              position: place.geometry.location
          });

          markerList.push(marker);

          google.maps.event.addListener(marker, 'click', function() {
              infowindow.setContent(place.name);
              infowindow.open(map, this);
          });
      }

      function calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB) {
          var latlong = document.getElementById('tempName').value.split(",");
          var distance = dist(latlong[0], latlong[1], lat, long);
          var radius = distance / 2;
          var latitude = (+latlong[0] + (+lat)) / 2.0;
          var longtitude = (+latlong[1] + (+long)) / 2.0;

          window.alert("latG=" + latlong[0] + ", longG=" + latlong[1] + ",lat=" + lat + ",long=" + long + "C1=" + latitude + "C2=" + longtitude);
          createRadius(latitude, longtitude, radius);


          directionsService.route({
              origin: pointA,
              destination: pointB,
              travelMode: google.maps.TravelMode.DRIVING
          }, function(response, status) {
              if (status == google.maps.DirectionsStatus.OK) {

                  directionsDisplay.setDirections(response);

              }
          });

          var service = new google.maps.places.PlacesService(map);
          service.nearbySearch({
              location: {
                  lat: latitude,
                  lng: longtitude
              },
              radius: radius * 1000,
              type: ['resturant']
          }, callback);

      }

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1wd_ZjcGOeUA3Z8PTTArFp2oSiCGd3KQ&libraries=places&callback=initMap"
        async defer></script>
  </body>
</html>