<option>
<?php

	$db         = mysqli_connect('localhost', 'root', '', 'templeincm');
	$db->set_charset("utf8");
	$company    = $_GET['company'];
	
	$sql        = "SELECT Temp_name FROM temple WHERE Temp_name like '$company%' ORDER BY Temp_name";

	
	$res        = $db->query($sql);
	
	if(!$res)
		echo mysqli_error($db);
	else
		while( $row = $res->fetch_object() )
			echo "<option value='".$row->Temp_name."'>";
		
?>
</option>