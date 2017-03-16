<!DOCTYPE>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Waypoints in directions</title>
    <style>
      #right-panel {
        font-family: 'Roboto','sans-serif';
        line-height: 30px;
        padding-left: 10px;
      }

      #right-panel select, #right-panel input {
        font-size: 15px;
      }

      #right-panel select {
        width: 100%;
      }

      #right-panel i {
        font-size: 12px;
      }
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 100%;
        float: left;
        width: 70%;
        height: 100%;
      }
      #right-panel {
        margin: 20px;
        border-width: 2px;
        width: 20%;
        height: 400px;
        float: left;
        text-align: left;
        padding-top: 0;
      }
      #directions-panel {
        margin-top: 10px;
        background-color: #FFEE77;
        padding: 10px;
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
    <div id="map"></div>
    <div id="right-panel">
    <div>
    <b>จุดเริ่มต้น:</b>
    <input type="text" id="start" style="width: 250px" placeholder="Enter a location" />

    <!-- <select id="start">
      <option value="Halifax, NS">Halifax, NS</option>
      <option value="Boston, MA">Boston, MA</option>
      <option value="New York, NY">New York, NY</option>
      <option value="chicago, il">Chicago</option>
    </select> -->
    <br>
    
    <b>จุดสิ้นสุด:</b><br>
    <input class="textbox" type="text" list="myCompanies" name="tempName" id="end" >
            <datalist id='myCompanies'>
        <?php 
        while( $row = $res->fetch_object() ){
          echo "<option value='".$row->Temp_latitude.",".$row->Temp_longitude."'>".$row->Temp_name."</option>";
        }
        ?>
    </datalist><br><br>
    <!-- <input type="submit" id="submit" value="ค้นหาเส้นทางหลัก"><br> -->
    <b>เลือกวัดที่ต้องการ:</b> <br>
    <i>(Ctrl+Click สำหรับเลือกได้หลายวัด)</i> <br>
    <select multiple id="waypoints">
      <option value="18.78824,98.96776">วัดสวนดอก</option>
      <option value="18.80088,98.95910">วัดประทานพร</option>
      <option value="chicago, il">Chicago</option>
      <option value="winnipeg, mb">Winnipeg</option>
      <option value="fargo, nd">Fargo</option>
      <option value="calgary, ab">Calgary</option>
      <option value="spokane, wa">Spokane</option>
    </select>
    <br><br>
    
   <!--  <select id="end">
      <option value="Vancouver, BC">Vancouver, BC</option>
      <option value="Seattle, WA">Seattle, WA</option>
      <option value="San Francisco, CA">San Francisco, CA</option>
      <option value="Los Angeles, CA">Los Angeles, CA</option>
    </select> -->
    
      <input type="submit" id="submit" value="ดูเส้นทาง">
    </div>
    <div id="directions-panel"></div>
    </div>
    <script>
  
      var count=0;
      var latitude;
      var longitude;

      var cityCircle;
      var latitude_af;
      var longitude_af;
      var radius_af;
      var covered;

      var map;


      function initMap() {

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer;
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 15,
          center: {lat: 18.78775, lng: 98.99312}
        });

        directionsDisplay.setMap(map);

        document.getElementById('submit').addEventListener('click', function() {
          calculateAndDisplayRoute(directionsService, directionsDisplay);

          

        });


        google.maps.event.addDomListener(window, 'load', function () {
            var places = new google.maps.places.Autocomplete(document.getElementById('start'));
            google.maps.event.addListener(places, 'place_changed', function () {
                var place = places.getPlace();
                var address = place.formatted_address;
                latitude = place.geometry.location.lat();
                longitude = place.geometry.location.lng();
                var mesg = "Address: " + address;
                mesg += "\nLatitude: " + latitude;
                mesg += "\nLongitude: " + longitude;
                alert(mesg);
            });
        });
      }

      
      function createRadius(lat,long,radius) {
        
          covered={
              strokeColor: '#FF0000',
              strokeOpacity: 0.8,
              strokeWeight: 2,
              fillColor: '#FF0000',
              fillOpacity: 0.35,
              map: map,
              center: new google.maps.LatLng(lat, long),
              radius: radius * 1000
          }
          // Add the circle for this city to the map.
          if(count>0)
          {
              cityCircle.setMap(null);

          }

         cityCircle = new google.maps.Circle(covered);

        count=count+1;
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
      

      function calculateAndDisplayRoute(directionsService, directionsDisplay) {
        var waypts = [];
        var checkboxArray = document.getElementById('waypoints');

        for (var i = 0; i < checkboxArray.length; i++) {
          if (checkboxArray.options[i].selected) {
            waypts.push({
              location: checkboxArray[i].value,
              stopover: true
            });
          }
        }
        alert(latitude+""+longitude);
        // var latlng = document.getElementById('start').value.split(",");
        // var lat1 = parseFloat(latlng[0]);
        // var lng1 = parseFloat(latlng[1]);
        

        var latlng = document.getElementById('end').value.split(",");
        var lat2 = parseFloat(latlng[0]);
        var lng2 = parseFloat(latlng[1]);


        var distance = dist(latitude, longitude, lat2, lng2);
        var radius = distance / 2;
        var latitude1 = (+latitude + (+lat2)) / 2.0;
        var longtitude1 = (+longitude + (+lng2)) / 2.0;

        window.alert("latG=" + latitude + ", longG=" + longitude + ",lat=" + lat2 + ",long=" + lng2 + "C1=" + latitude1 + "C2=" + longtitude1);

       
        createRadius(latitude1,longtitude1,radius);


        directionsService.route({
          origin: document.getElementById('start').value,
          destination: document.getElementById('end').value,
          waypoints: waypts,
          optimizeWaypoints: true,
          travelMode: 'DRIVING'
        }, function(response, status) {
          if (status === 'OK') {
            directionsDisplay.setDirections(response);
            var route = response.routes[0];
            var summaryPanel = document.getElementById('directions-panel');
            summaryPanel.innerHTML = '';
            // For each route, display summary information.
            for (var i = 0; i < route.legs.length; i++) {
              var routeSegment = i + 1;
              summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
                  '</b><br>';
              summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
              summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
              summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
            }
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1wd_ZjcGOeUA3Z8PTTArFp2oSiCGd3KQ&sensor=false&libraries=places&callback=initMap">
    </script>
  </body>
</html>