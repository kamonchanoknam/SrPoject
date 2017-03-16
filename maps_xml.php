<?php
$username="root";
$password="";
$database="templeincm";
$servername= "localhost";

// Start XML file, create parent node
//$doc = domxml_new_doc("1.0");

$doc = new DOMDocument("1.0"); 
$node = $doc->createElement("temple");
$parnode = $doc->appendChild($node);
 
// Opens a connection to a MySQL server

$connection = new mysqli($servername, $username, $password);
// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
mysqli_set_charset($connection,"utf8");

// Set the active MySQL database
$db_selected = mysqli_select_db( $connection, $database);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysqli_error());
}


// Select all the rows in the markers table
$query = "SELECT `Temp_name`,`Temp_address`,`Temp_latitude`,`Temp_longitude` FROM `temple` WHERE 1";
$result = mysqli_query($connection, $query);
if (!$result) {
  die('Invalid query: ' . mysqli_error());
}

header("Content-type: text/xml"); 

// Iterate through the rows, adding XML nodes for each
while ($row = mysqli_fetch_assoc($result)){
  // Add to XML document node
  $node = $doc->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("name",$row['Temp_name']);
  $newnode->setAttribute("address", $row['Temp_address']);
  $newnode->setAttribute("lat", $row['Temp_latitude']);
  $newnode->setAttribute("lng", $row['Temp_longitude']);
  
}

echo $doc->saveXML();

?>